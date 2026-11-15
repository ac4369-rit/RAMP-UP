<?php
declare(strict_types=1);

namespace App\Controllers;

use PDOException;

/** Pantalla 02 — Where are we now? */
class StatusController extends Controller
{
    public function index(): void
    {
        $user  = $this->requireUser();
        $state = 'ready';
        $project = null;
        $milestones = ['done' => [], 'current' => [], 'upcoming' => []];
        $events = [];
        $daysLeft = 0;

        try {
            $pdo = $this->pdo();

            $st = $pdo->prepare('SELECT * FROM projects WHERE id = :pid');
            $st->execute(['pid' => $user['project_id']]);
            $project = $st->fetch() ?: null;
            if ($project === null) {
                // The user has no project yet: show the empty state instead of "page not found".
                throw new \DomainException('no project');
            }

            $st = $pdo->prepare('SELECT name, status, due_on FROM milestones WHERE project_id = :pid ORDER BY position');
            $st->execute(['pid' => $user['project_id']]);
            foreach ($st->fetchAll() as $m) {
                $milestones[$m['status']][] = $m;
            }

            $st = $pdo->prepare(
                'SELECT kind, title, detail, happened_at FROM change_events
                  WHERE project_id = :pid AND happened_at >= :since
                  ORDER BY happened_at DESC LIMIT 5'
            );
            $st->execute(['pid' => $user['project_id'], 'since' => $project['sprint_starts_on']]);
            $events = $st->fetchAll();

            $end = new \DateTimeImmutable($project['sprint_ends_on']);
            $diff = (new \DateTimeImmutable('today'))->diff($end);
            $daysLeft = $diff->invert ? 0 : (int) $diff->days;

            if (!array_filter($milestones)) {
                $state = 'empty';
            }
        } catch (\DomainException) {
            $state = 'empty';
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $state = 'error';
        }

        $this->render(
            'status',
            compact('project', 'milestones', 'events', 'daysLeft', 'state'),
            tab: 'pinned',
            titleKey: 'status.title',
            status: $state === 'error' ? 500 : 200,
        );
    }
}
