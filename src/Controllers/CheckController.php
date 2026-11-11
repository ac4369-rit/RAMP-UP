<?php
declare(strict_types=1);

namespace App\Controllers;

use PDO;
use PDOException;

/** Pantalla 10 — Catch-up check: ¿estoy listo para empezar? (lecturas reales + resultado del quiz) */
class CheckController extends Controller
{
    public function index(): void
    {
        $user  = $this->requireUser();
        $state = 'ready';
        $data  = [];

        try {
            $pdo = $this->pdo();
            $uid = $user['id'];

            // Decisiones que afectan a mi rol, y cuántas he leído
            $st = $pdo->prepare(
                'SELECT d.id, d.title,
                        EXISTS (SELECT 1 FROM decision_reads r WHERE r.decision_id = d.id AND r.user_id = :uid) AS is_read
                   FROM decisions d
                   JOIN decision_roles dr ON dr.decision_id = d.id AND dr.role = :role
                  WHERE d.project_id = :pid
                  ORDER BY d.decided_at DESC'
            );
            $st->execute(['uid' => $uid, 'role' => $user['role'], 'pid' => $user['project_id']]);
            $relevant = $st->fetchAll();
            $total = count($relevant);
            $read  = count(array_filter($relevant, fn ($d) => (int) $d['is_read'] === 1));
            $gaps  = array_slice(array_values(array_filter($relevant, fn ($d) => (int) $d['is_read'] === 0)), 0, 5);
            $readingPct = $total === 0 ? 100 : (int) round($read / $total * 100);

            $st = $pdo->prepare(
                'SELECT score_percent, taken_at FROM quiz_attempts
                  WHERE user_id = :uid AND project_id = :pid ORDER BY taken_at DESC, id DESC LIMIT 1'
            );
            $st->execute(['uid' => $uid, 'pid' => $user['project_id']]);
            $quiz = $st->fetch() ?: null;

            $st = $pdo->prepare(
                'SELECT COUNT(*) FROM questions q
                  WHERE q.author_id = :uid AND NOT EXISTS (SELECT 1 FROM answers a WHERE a.question_id = q.id)'
            );
            $st->execute(['uid' => $uid]);
            $unanswered = (int) $st->fetchColumn();

            $st = $pdo->prepare('SELECT quiz_url FROM projects WHERE id = :pid');
            $st->execute(['pid' => $user['project_id']]);
            $quizUrl = $st->fetchColumn() ?: null;

            $readiness = $quiz !== null ? (int) round(($readingPct + (int) $quiz['score_percent']) / 2) : $readingPct;
            $level = $readiness >= 80 ? 'ready' : ($readiness >= 50 ? 'almost' : 'notYet');

            $data = compact('total', 'read', 'gaps', 'readingPct', 'quiz', 'unanswered', 'quizUrl', 'readiness', 'level');
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $state = 'error';
        }

        $this->render('check', $data + ['state' => $state], tab: 'todo', titleKey: 'check.title',
            status: $state === 'error' ? 500 : 200);
    }

    /** Registrar el resultado del formulario externo */
    public function saveScore(): void
    {
        $user  = $this->requireUser();
        $score = filter_var($_POST['score'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 100]]);

        if (!csrf_valid()) {
            flash_set('flash.csrf');
        } elseif ($score === false) {
            flash_set('flash.scoreInvalid');
        } else {
            $this->pdo()->prepare('INSERT INTO quiz_attempts (user_id, project_id, score_percent) VALUES (:u, :p, :s)')
                ->execute(['u' => $user['id'], 'p' => $user['project_id'], 's' => $score]);
            flash_set('flash.scoreSaved');
        }
        $this->redirect('/check');
    }
}
