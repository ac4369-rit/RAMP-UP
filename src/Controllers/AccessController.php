<?php
declare(strict_types=1);

namespace App\Controllers;

use PDOException;

class AccessController extends Controller
{
    public function index(): void
    {
        $user  = $this->requireUser();
        $state = 'ready';
        $tools = [];

        try {
            $st = $this->pdo()->prepare(
                'SELECT t.id, t.name, t.url, a.status
                   FROM project_tools t
                   LEFT JOIN tool_access a ON a.tool_id = t.id AND a.user_id = :u
                  WHERE t.project_id = :p
                  ORDER BY t.id'
            );
            $st->execute(['u' => $user['id'], 'p' => $user['project_id']]);
            $tools = $st->fetchAll();
            if (!$tools) {
                $state = 'empty';
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $state = 'error';
        }

        $granted = count(array_filter($tools, fn ($t) => $t['status'] === 'granted'));

        $this->render(
            'access',
            compact('tools', 'granted', 'state'),
            tab: 'todo',
            titleKey: 'access.title',
            status: $state === 'error' ? 500 : 200,
        );
    }

    public function update(string $id): void
    {
        $user   = $this->requireUser();
        $action = (string) ($_POST['action'] ?? '');

        if (!csrf_valid()) {
            flash_set('flash.csrf');
            $this->redirect('/access');
        }

        $st = $this->pdo()->prepare('SELECT id, name FROM project_tools WHERE id = :id AND project_id = :p');
        $st->execute(['id' => (int) $id, 'p' => $user['project_id']]);
        $tool = $st->fetch();
        if (!$tool) {
            $this->notFound();
        }

        if ($action === 'request') {
            $this->pdo()->prepare(
                "INSERT IGNORE INTO tool_access (tool_id, user_id, status) VALUES (:t, :u, 'pending')"
            )->execute(['t' => $tool['id'], 'u' => $user['id']]);
            flash_set('flash.accessRequested', ['tool' => $tool['name']]);
        } elseif ($action === 'works') {
            $this->pdo()->prepare(
                "INSERT INTO tool_access (tool_id, user_id, status) VALUES (:t, :u, 'granted')
                 ON DUPLICATE KEY UPDATE status = 'granted'"
            )->execute(['t' => $tool['id'], 'u' => $user['id']]);
            flash_set('flash.accessConfirmed', ['tool' => $tool['name']]);
        }

        $this->redirect('/access');
    }
}
