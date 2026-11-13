<?php
declare(strict_types=1);

namespace App\Controllers;

use PDOException;

class HomeController extends Controller
{
    public function index(): void
    {
        $user  = $this->requireUser();
        $state = 'ready';
        $project = null;
        $task = null;

        try {
            $st = $this->pdo()->prepare('SELECT name FROM projects WHERE id = :p');
            $st->execute(['p' => $user['project_id']]);
            $project = $st->fetch() ?: null;

            $st = $this->pdo()->prepare(
                "SELECT id, title, due_date FROM tasks
                  WHERE project_id = :p AND is_starter = TRUE AND status <> 'done'
                    AND (assigned_to = :u OR assigned_to IS NULL)
                  ORDER BY assigned_to IS NULL, due_date
                  LIMIT 1"
            );
            $st->execute(['p' => $user['project_id'], 'u' => $user['id']]);
            $task = $st->fetch() ?: null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $state = 'error';
        }

        $this->render('home', compact('user', 'project', 'task', 'state'),
            tab: 'pinned', titleKey: 'home.title', status: $state === 'error' ? 500 : 200);
    }
    public function project(): void
    {
        $user  = $this->requireUser();
        $state = 'ready';
        $project = null;
        $daysLeft = 0;

        try {
            $st = $this->pdo()->prepare(
                'SELECT name, description, audience, current_status, sprint_starts_on, sprint_ends_on
                   FROM projects WHERE id = :p'
            );
            $st->execute(['p' => $user['project_id']]);
            $project = $st->fetch() ?: null;

            if ($project === null) {
                $state = 'empty';
            } else {
                $diff = (new \DateTimeImmutable('today'))->diff(new \DateTimeImmutable($project['sprint_ends_on']));
                $daysLeft = $diff->invert ? 0 : (int) $diff->days;
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $state = 'error';
        }

        $this->render('project', compact('project', 'daysLeft', 'state'),
            tab: 'pinned', titleKey: 'project.title', status: $state === 'error' ? 500 : 200);
    }
    public function team(): void
    {
        $user  = $this->requireUser();
        $state = 'ready';
        $members = [];

        try {
            $st = $this->pdo()->prepare(
                'SELECT u.id, u.name, u.job_title, m.role, m.is_buddy, m.replaces_name
                   FROM memberships m
                   JOIN users u ON u.id = m.user_id
                  WHERE m.project_id = :p'
            );
            $st->execute(['p' => $user['project_id']]);
            $members = sort_by_name($st->fetchAll());
            if (!$members) {
                $state = 'empty';
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $state = 'error';
        }

        $me = (int) $user['id'];
        $this->render('team', compact('members', 'me', 'state'),
            tab: 'understand', titleKey: 'team.title', status: $state === 'error' ? 500 : 200);
    }
    public function rules(): void
    {
        $user  = $this->requireUser();
        $state = 'ready';
        $rules = [];

        try {
            $st = $this->pdo()->prepare('SELECT rule_text FROM team_rules WHERE project_id = :p ORDER BY position');
            $st->execute(['p' => $user['project_id']]);
            $rules = $st->fetchAll();
            if (!$rules) {
                $state = 'empty';
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $state = 'error';
        }

        $this->render('rules', compact('rules', 'state'),
            tab: 'understand', titleKey: 'rules.title', status: $state === 'error' ? 500 : 200);
    }
}
