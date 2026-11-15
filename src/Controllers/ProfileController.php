<?php
declare(strict_types=1);

namespace App\Controllers;

use PDO;
use PDOException;

class ProfileController extends Controller
{
    private const MAX_SKILLS = 12;
    public function skills(): void
    {
        $user = $this->requireUser();
        $this->showSkills($user, [], 200);
    }

    public function saveSkills(): void
    {
        $user = $this->requireUser();

        $keep = array_values(array_filter(
            array_map(fn ($s) => trim((string) $s), (array) ($_POST['keep'] ?? [])),
            fn ($s) => $s !== ''
        ));
        $new  = trim((string) ($_POST['new_skill'] ?? ''));
        $days = array_values(array_unique(array_filter(
            array_map('intval', (array) ($_POST['days'] ?? [])),
            fn ($d) => $d >= 1 && $d <= 7
        )));

        $errors = [];
        if (!csrf_valid()) {
            $errors[] = 'flash.csrf';
        }
        if (mb_strlen($new) > 60) {
            $errors[] = 'profile.error.tooLong';
        }
        $skills = $new !== '' ? array_unique([...$keep, $new]) : $keep;
        if (count($skills) > self::MAX_SKILLS) {
            $errors[] = 'profile.error.tooMany';
        }
        if ($errors) {
            $this->showSkills($user, $errors, 422);
            return;
        }

        $pdo = $this->pdo();
        $pdo->beginTransaction();
        $pdo->prepare('DELETE FROM user_skills WHERE user_id = :u')->execute(['u' => $user['id']]);
        $add = $pdo->prepare('INSERT INTO user_skills (user_id, skill) VALUES (:u, :s)');
        foreach ($skills as $skill) {
            $add->execute(['u' => $user['id'], 's' => mb_substr($skill, 0, 60)]);
        }
        $pdo->prepare('DELETE FROM user_availability WHERE user_id = :u')->execute(['u' => $user['id']]);
        $addDay = $pdo->prepare('INSERT INTO user_availability (user_id, weekday) VALUES (:u, :d)');
        foreach ($days as $day) {
            $addDay->execute(['u' => $user['id'], 'd' => $day]);
        }
        $pdo->commit();

        flash_set('flash.profileSaved');
        $this->redirect('/profile');
    }

    private function showSkills(array $user, array $errors, int $status): void
    {
        $state  = 'ready';
        $skills = [];
        $days   = [];

        try {
            $st = $this->pdo()->prepare('SELECT skill FROM user_skills WHERE user_id = :u ORDER BY skill');
            $st->execute(['u' => $user['id']]);
            $skills = $st->fetchAll(PDO::FETCH_COLUMN);
            // A–Z by the name the reader sees (skills like "Accessibility" are translated)
            $collator = new \Collator(\App\Translator::instance()->locale());
            usort($skills, fn ($a, $b) => $collator->compare(tc($a), tc($b)));

            $st = $this->pdo()->prepare('SELECT weekday FROM user_availability WHERE user_id = :u');
            $st->execute(['u' => $user['id']]);
            $days = array_map('intval', $st->fetchAll(PDO::FETCH_COLUMN));
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $state = 'error';
        }

        $this->render(
            'profile',
            compact('user', 'skills', 'days', 'errors', 'state'),
            tab: 'todo',
            titleKey: 'profile.title',
            status: $state === 'error' ? 500 : $status,
        );
    }
    public function notifications(): void
    {
        $user  = $this->requireUser();
        $state = 'ready';
        $settings = ['only_mentions' => 1, 'daily_summary' => 1, 'daily_summary_time' => '18:00:00', 'all_channel_messages' => 0];

        try {
            $st = $this->pdo()->prepare(
                'SELECT only_mentions, daily_summary, daily_summary_time, all_channel_messages
                   FROM notification_settings WHERE user_id = :u AND project_id = :p'
            );
            $st->execute(['u' => $user['id'], 'p' => $user['project_id']]);
            $settings = $st->fetch() ?: $settings;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $state = 'error';
        }

        $errors = [];
        $this->render(
            'notifications',
            compact('settings', 'state', 'errors'),
            tab: 'todo',
            titleKey: 'notify.title',
            status: $state === 'error' ? 500 : 200,
        );
    }

    public function saveNotifications(): void
    {
        $user = $this->requireUser();
        $time = (string) ($_POST['daily_summary_time'] ?? '18:00');

        if (!csrf_valid()) {
            flash_set('flash.csrf');
            $this->redirect('/notifications');
        }
        if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $time)) {
            $time = '18:00';
        }

        $this->pdo()->prepare(
            'INSERT INTO notification_settings
                    (user_id, project_id, only_mentions, daily_summary, daily_summary_time, all_channel_messages)
             VALUES (:u, :p, :m, :d, :t, :a)
             ON DUPLICATE KEY UPDATE only_mentions = VALUES(only_mentions), daily_summary = VALUES(daily_summary),
                    daily_summary_time = VALUES(daily_summary_time), all_channel_messages = VALUES(all_channel_messages)'
        )->execute([
            'u' => $user['id'],
            'p' => $user['project_id'],
            'm' => isset($_POST['only_mentions']) ? 1 : 0,
            'd' => isset($_POST['daily_summary']) ? 1 : 0,
            't' => $time . ':00',
            'a' => isset($_POST['all_channel_messages']) ? 1 : 0,
        ]);

        flash_set('flash.notificationsSaved');
        $this->redirect('/notifications');
    }
}
