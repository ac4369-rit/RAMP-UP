<?php
declare(strict_types=1);

namespace App\Controllers;

use PDO;
use PDOException;

class BuddyController extends Controller
{
    private const WORK_DAYS = [1, 2, 3, 4, 5];

    public function index(): void
    {
        $user  = $this->requireUser();
        $state = 'ready';
        $buddy = null;
        $buddyDays = [];
        $call = null;

        try {
            $buddy = $this->findBuddy($user);
            if ($buddy === null) {
                $state = 'empty';
            } else {
                $st = $this->pdo()->prepare('SELECT weekday FROM user_availability WHERE user_id = :u ORDER BY weekday');
                $st->execute(['u' => $buddy['id']]);
                $buddyDays = array_map('intval', $st->fetchAll(PDO::FETCH_COLUMN)) ?: self::WORK_DAYS;

                $st = $this->pdo()->prepare(
                    "SELECT id, weekday, minutes, status FROM intro_calls
                      WHERE project_id = :p AND newcomer_id = :u AND status <> 'cancelled'
                      ORDER BY created_at DESC LIMIT 1"
                );
                $st->execute(['p' => $user['project_id'], 'u' => $user['id']]);
                $call = $st->fetch() ?: null;
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $state = 'error';
        }

        $days = self::WORK_DAYS;
        $this->render('buddy', compact('buddy', 'buddyDays', 'days', 'call', 'state'),
            tab: 'todo', titleKey: 'buddy.title', status: $state === 'error' ? 500 : 200);
    }

    public function book(): void
    {
        $user = $this->requireUser();
        $day  = (int) ($_POST['weekday'] ?? 0);

        if (!csrf_valid()) {
            flash_set('flash.csrf');
        } elseif (!in_array($day, self::WORK_DAYS, true)) {
            flash_set('buddy.error.day');
        } elseif (($buddy = $this->findBuddy($user)) === null) {
            flash_set('buddy.empty');
        } else {
            $this->pdo()->prepare(
                'INSERT INTO intro_calls (project_id, newcomer_id, buddy_id, weekday) VALUES (:p, :n, :b, :d)'
            )->execute(['p' => $user['project_id'], 'n' => $user['id'], 'b' => $buddy['id'], 'd' => $day]);
            flash_set('flash.callBooked', ['name' => $buddy['name'], 'day' => weekday_name($day)]);
        }
        $this->redirect('/buddy');
    }

    public function confirmCancel(string $id): void
    {
        $user = $this->requireUser();
        $call = $this->loadOwnCall((int) $id, $user);
        $this->render('buddy_cancel', compact('call'), tab: 'todo', titleKey: 'buddy.cancelTitle');
    }

    public function cancel(string $id): void
    {
        $user = $this->requireUser();
        $call = $this->loadOwnCall((int) $id, $user);
        if (!csrf_valid()) {
            flash_set('flash.csrf');
        } else {
            $this->pdo()->prepare(
                "UPDATE intro_calls SET status = 'cancelled' WHERE id = :id AND newcomer_id = :u"
            )->execute(['id' => $call['id'], 'u' => $user['id']]);
            flash_set('flash.callCancelled');
        }
        $this->redirect('/buddy');
    }

    private function loadOwnCall(int $id, array $user): array
    {
        $st = $this->pdo()->prepare(
            "SELECT c.id, c.weekday, c.minutes, u.name AS buddy_name
               FROM intro_calls c
               JOIN users u ON u.id = c.buddy_id
              WHERE c.id = :id AND c.newcomer_id = :u AND c.status <> 'cancelled'"
        );
        $st->execute(['id' => $id, 'u' => $user['id']]);
        $call = $st->fetch();
        if (!$call) {
            $this->notFound();
        }
        return $call;
    }

    private function findBuddy(array $user): ?array
    {
        $st = $this->pdo()->prepare(
            'SELECT u.id, u.name, u.job_title
               FROM memberships m
               JOIN users u ON u.id = m.user_id
              WHERE m.project_id = :p AND m.is_buddy = TRUE AND u.id <> :me
              LIMIT 1'
        );
        $st->execute(['p' => $user['project_id'], 'me' => $user['id']]);
        return $st->fetch() ?: null;
    }
}
