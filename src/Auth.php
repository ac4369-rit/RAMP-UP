<?php
declare(strict_types=1);

namespace App;

final class Auth
{
    private static ?array $cache = null;

    public static function attempt(string $email, string $password): ?array
    {
        $st = Database::getConnection()->prepare(
            'SELECT id, name, email, locale, password_hash FROM users WHERE email = :email'
        );
        $st->execute(['email' => $email]);
        $user = $st->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return null;
        }
        unset($user['password_hash']);
        return $user;
    }

    public static function login(int $userId, ?int $projectId = null): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;
        if ($projectId !== null) {
            $_SESSION['project_id'] = $projectId;
        } else {
            unset($_SESSION['project_id']);
        }
        self::$cache = null;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
        self::$cache = null;
    }

    public static function user(): ?array
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        if (self::$cache !== null) {
            return self::$cache;
        }

        $st = Database::getConnection()->prepare(
            'SELECT u.id, u.name, u.email, u.locale, m.project_id, m.role
               FROM users u
               LEFT JOIN memberships m
                      ON m.user_id = u.id AND (:pid IS NULL OR m.project_id = :pid2)
              WHERE u.id = :id
              ORDER BY m.joined_at DESC
              LIMIT 1'
        );
        $pid = $_SESSION['project_id'] ?? null;
        $st->execute(['pid' => $pid, 'pid2' => $pid, 'id' => $_SESSION['user_id']]);
        $user = $st->fetch();

        if (!$user) {
            self::logout();
            return null;
        }
        return self::$cache = $user;
    }
}
