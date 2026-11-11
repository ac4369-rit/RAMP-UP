<?php
declare(strict_types=1);

namespace App\Services;

use App\Database;
use PDOException;

/**
 * API externa: commits recientes del repositorio del proyecto (pantalla 01).
 * - El token vive en .env y se usa solo desde el servidor.
 * - Caché de 15 min en la tabla github_cache (límite de peticiones de la API).
 * - Si GitHub falla, se sirve la copia antigua; si no hay copia, la pantalla sigue sin esos datos.
 */
final class GithubService
{
    private const TTL = 900;
    private const API = 'https://api.github.com';
    private const HINTS = [
        'backend'  => ['backend', 'api', 'auth', 'auth0', 'auth2', 'server', 'db', 'database', 'migration'],
        'frontend' => ['frontend', 'ui', 'css', 'react', 'nextjs', 'next', 'component', 'dark'],
        'design'   => ['design', 'figma', 'ux', 'mockup'],
        'product'  => ['product', 'release', 'scope', 'roadmap', 'launch'],
    ];

    /** @return array{items: list<array{message:string,author:string,date:string,url:string}>, stale: bool, error: bool} */
    public function recentCommits(?string $repo): array
    {
        if ($repo === null || !preg_match('#^[\w.-]+/[\w.-]+$#', $repo)) {
            return ['items' => [], 'stale' => false, 'error' => false];
        }

        $key    = 'commits:' . $repo;
        $cached = $this->readCache($key);
        if ($cached !== null && $cached['age'] < self::TTL) {
            return ['items' => $cached['items'], 'stale' => false, 'error' => false];
        }

        $items = $this->fetch($repo);
        if ($items !== null) {
            $this->writeCache($key, $items);
            return ['items' => $items, 'stale' => false, 'error' => false];
        }
        if ($cached !== null) {
            return ['items' => $cached['items'], 'stale' => true, 'error' => false];
        }
        return ['items' => [], 'stale' => false, 'error' => true];
    }

    /** Asigna un commit a un rol por prefijo ("backend: ...") o palabra clave; null = todo el proyecto. */
    public static function guessArea(string $message): ?string
    {
        $m = mb_strtolower($message);
        if (preg_match('/^(\w+)[:(]/u', $m, $p) && isset(self::HINTS[$p[1]])) {
            return $p[1];
        }
        foreach (self::HINTS as $role => $words) {
            foreach ($words as $w) {
                if (preg_match('/\b' . preg_quote($w, '/') . '\b/u', $m)) {
                    return $role;
                }
            }
        }
        return null;
    }

    private function fetch(string $repo): ?array
    {
        $since = rawurlencode(gmdate('c', strtotime('-14 days')));
        $ch = curl_init(self::API . "/repos/{$repo}/commits?per_page=20&since={$since}");
        $headers = ['User-Agent: rampup-app', 'Accept: application/vnd.github+json'];
        if ($token = env('GITHUB_TOKEN')) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_HTTPHEADER     => $headers,
        ]);
        $body = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($body === false || $code !== 200) {      // incluye límite de peticiones (403/429)
            return null;
        }
        $data = json_decode((string) $body, true);
        if (!is_array($data)) {
            return null;
        }
        $items = [];
        foreach ($data as $c) {
            $items[] = [
                'message' => mb_substr((string) strtok((string) ($c['commit']['message'] ?? ''), "\n"), 0, 120),
                'author'  => (string) ($c['commit']['author']['name'] ?? ''),
                'date'    => (string) ($c['commit']['author']['date'] ?? ''),
                'url'     => (string) ($c['html_url'] ?? ''),
            ];
        }
        return $items;
    }

    private function readCache(string $key): ?array
    {
        try {
            $st = Database::getConnection()->prepare(
                'SELECT payload, TIMESTAMPDIFF(SECOND, fetched_at, NOW()) AS age FROM github_cache WHERE cache_key = :k'
            );
            $st->execute(['k' => $key]);
            $row = $st->fetch();
            if (!$row) {
                return null;
            }
            $items = json_decode((string) $row['payload'], true);
            return is_array($items) ? ['items' => $items, 'age' => (int) $row['age']] : null;
        } catch (PDOException) {
            return null;
        }
    }

    private function writeCache(string $key, array $items): void
    {
        try {
            Database::getConnection()->prepare(
                'INSERT INTO github_cache (cache_key, payload, fetched_at) VALUES (:k, :p, NOW())
                 ON DUPLICATE KEY UPDATE payload = VALUES(payload), fetched_at = NOW()'
            )->execute(['k' => $key, 'p' => json_encode($items, JSON_UNESCAPED_UNICODE)]);
        } catch (PDOException) {
            // la caché es opcional
        }
    }
}
