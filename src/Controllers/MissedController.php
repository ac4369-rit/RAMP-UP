<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\GithubService;
use PDO;
use PDOException;

/** Pantalla 01 — What was missed: lo más relevante para mi rol, desde la BD y desde GitHub */
class MissedController extends Controller
{
    private const PER_GROUP = 5;

    public function index(): void
    {
        $user = $this->requireUser();
        $pdo  = $this->pdo();

        $roles = $pdo->query('SELECT code FROM roles ORDER BY code')->fetchAll(PDO::FETCH_COLUMN);
        $role  = (string) ($_GET['role'] ?? $user['role']);
        if (!in_array($role, $roles, true)) {
            $role = $user['role'];
        }

        $state   = 'ready';
        $forRole = $others = [];
        $github  = ['stale' => false, 'error' => false];

        try {
            $st = $pdo->prepare(
                'SELECT kind, title, detail, area, happened_at, "manual" AS source
                   FROM change_events
                  WHERE project_id = :pid AND happened_at >= NOW() - INTERVAL 30 DAY'
            );
            $st->execute(['pid' => $user['project_id']]);
            $events = $st->fetchAll();

            // API externa: los commits se asignan a un rol y se mezclan con los eventos
            $repo = $pdo->prepare('SELECT repo FROM projects WHERE id = :pid');
            $repo->execute(['pid' => $user['project_id']]);
            $gh = (new GithubService())->recentCommits($repo->fetchColumn() ?: null);
            $github = ['stale' => $gh['stale'], 'error' => $gh['error']];
            foreach ($gh['items'] as $c) {
                $events[] = [
                    'kind'        => 'changed',
                    'title'       => $c['message'],
                    'detail'      => $c['author'],
                    'area'        => GithubService::guessArea($c['message']),
                    'happened_at' => date('Y-m-d H:i:s', (int) strtotime($c['date'])),
                    'source'      => 'github',
                ];
            }

            usort($events, fn ($a, $b) => strcmp($b['happened_at'], $a['happened_at']));
            foreach ($events as $ev) {
                if ($ev['area'] === $role) {
                    $forRole[] = $ev;
                } else {
                    $others[] = $ev;
                }
            }
            $forRole = array_slice($forRole, 0, self::PER_GROUP);
            $others  = array_slice($others, 0, self::PER_GROUP);

            if (!$forRole && !$others) {
                $state = 'empty';
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $state = 'error';
        }

        $this->render(
            'missed',
            compact('user', 'role', 'roles', 'forRole', 'others', 'state', 'github'),
            tab: 'pinned',
            titleKey: 'missed.title',
            status: $state === 'error' ? 500 : 200,
        );
    }
}
