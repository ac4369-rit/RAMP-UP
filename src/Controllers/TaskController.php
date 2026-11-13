<?php
declare(strict_types=1);

namespace App\Controllers;

use PDOException;


class TaskController extends Controller
{
    private const MAX_LENGTH = 1000;

    private const RISK_DAYS = 2;

    private const PROGRESS_STATUSES = ['todo', 'in_progress'];


    public function index(): void
    {
        $user  = $this->requireUser();
        $state = 'ready';
        $tasks = $toReview = [];

        try {
            $st = $this->pdo()->prepare(
                "SELECT id, title, status, priority, due_date
                   FROM tasks
                  WHERE project_id = :p AND assigned_to = :u
                  ORDER BY status = 'done', due_date IS NULL, due_date, id"
            );
            $st->execute(['p' => $user['project_id'], 'u' => $user['id']]);
            $tasks = array_map([$this, 'withDeadline'], $st->fetchAll());

            $st = $this->pdo()->prepare(
                "SELECT t.id, t.title, t.status, t.priority, t.due_date, u.name AS assignee
                   FROM tasks t
                   JOIN users u ON u.id = t.assigned_to
                  WHERE t.project_id = :p AND t.status = 'review' AND t.assigned_to <> :u
                  ORDER BY t.due_date IS NULL, t.due_date"
            );
            $st->execute(['p' => $user['project_id'], 'u' => $user['id']]);
            $toReview = $st->fetchAll();

            if (!$tasks) {
                $state = 'empty';
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $state = 'error';
        }

        $this->render('tasks', compact('tasks', 'toReview', 'state'),
            tab: 'todo', titleKey: 'tasks.title', status: $state === 'error' ? 500 : 200);
    }

    public function starter(): void
    {
        $user  = $this->requireUser();
        $state = 'ready';
        $tasks = [];

        try {
            $st = $this->pdo()->prepare(
                "SELECT id, title, description, priority, due_date
                   FROM tasks
                  WHERE project_id = :p AND is_starter = TRUE AND assigned_to IS NULL AND status <> 'done'
                  ORDER BY due_date IS NULL, due_date, id"
            );
            $st->execute(['p' => $user['project_id']]);
            $tasks = $st->fetchAll();
            if (!$tasks) {
                $state = 'empty';
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $state = 'error';
        }

        $this->render('task_starter', compact('tasks', 'state'),
            tab: 'todo', titleKey: 'tasks.starter.title', status: $state === 'error' ? 500 : 200);
    }

    public function take(string $id): void
    {
        $user = $this->requireUser();
        $task = $this->loadTask((int) $id, $user);

        if (!csrf_valid()) {
            flash_set('flash.csrf');
            $this->redirect('/tasks/starter');
        }

        $st = $this->pdo()->prepare(
            'UPDATE tasks SET assigned_to = :u
              WHERE id = :id AND project_id = :p AND is_starter = TRUE AND assigned_to IS NULL'
        );
        $st->execute(['u' => $user['id'], 'id' => $task['id'], 'p' => $user['project_id']]);

        if ($st->rowCount() === 1) {
            flash_set('flash.taskTaken', ['title' => $task['title']]);
            $this->redirect('/tasks/' . $task['id']);
        }
        flash_set('flash.taskTakenAlready');
        $this->redirect('/tasks/starter');
    }

    public function show(string $id): void
    {
        $user = $this->requireUser();
        $this->showDetail($this->loadTask((int) $id, $user), $user, '', [], 200);
    }

    public function decide(string $id): void
    {
        $user    = $this->requireUser();
        $task    = $this->loadTask((int) $id, $user);
        $action  = (string) ($_POST['action'] ?? '');
        $comment = trim((string) ($_POST['comment'] ?? ''));

        if ($task['status'] !== 'review' || (int) $task['assigned_to'] === (int) $user['id']) {
            $this->redirect('/tasks/' . $task['id']);
        }

        $errors = [];
        if (!csrf_valid()) {
            $errors[] = 'flash.csrf';
        } elseif (!in_array($action, ['accept', 'changes'], true)) {
            $errors[] = 'tasks.decide.error.action';
        } elseif ($action === 'changes' && $comment === '') {
            $errors[] = 'tasks.decide.error.comment';
        } elseif (mb_strlen($comment) > self::MAX_LENGTH) {
            $errors[] = 'tasks.error.tooLong';
        }
        if ($errors) {
            $this->showDetail($task, $user, $comment, $errors, 422);
            return;
        }

        if ($action === 'accept') {
            $this->changeStatus($task, $user, 'done', 'accepted', $comment);
            flash_set('flash.taskAccepted', ['title' => $task['title']]);
        } else {
          
            $this->changeStatus($task, $user, 'in_progress', 'feedback', $comment);
            flash_set('flash.changesRequested');
        }
        $this->redirect('/tasks/' . $task['id']);
    }


    public function handoff(string $id): void
    {
        $user  = $this->requireUser();
        $task  = $this->loadTask((int) $id, $user);
        $notes = $this->updates((int) $task['id'], ['handoff']);

        $this->render('task_handoff', compact('task', 'notes'), tab: 'todo', titleKey: 'tasks.handoff.title');
    }


    public function blockedForm(string $id): void
    {
        $user = $this->requireUser();
        $task = $this->loadOwnTask((int) $id, $user);
        $this->showForm('task_blocked', 'tasks.blocked.title', $task, ['reason' => ''], [], 200);
    }

    public function markBlocked(string $id): void
    {
        $user   = $this->requireUser();
        $task   = $this->loadOwnTask((int) $id, $user);
        $reason = trim((string) ($_POST['reason'] ?? ''));

        $errors = $this->validateText($reason, required: true, emptyKey: 'tasks.blocked.error.empty');
        if (!$errors && $task['status'] === 'done') {
            $errors[] = 'tasks.error.alreadyDone';
        }
        if ($errors) {
            $this->showForm('task_blocked', 'tasks.blocked.title', $task, ['reason' => $reason], $errors, 422);
            return;
        }

        $this->changeStatus($task, $user, 'blocked', 'blocked', $reason);
        flash_set('flash.taskBlocked');
        $this->redirect('/tasks/' . $task['id']);
    }

    public function progressForm(string $id): void
    {
        $user = $this->requireUser();
        $task = $this->loadOwnTask((int) $id, $user);
        // A blocked task is shown as "in progress" here: saving it means "I can continue".
        $status = in_array($task['status'], self::PROGRESS_STATUSES, true) ? $task['status'] : 'in_progress';
        $this->showForm('task_progress', 'tasks.progress.title', $task, ['status' => $status, 'note' => ''], [], 200);
    }

    public function saveProgress(string $id): void
    {
        $user   = $this->requireUser();
        $task   = $this->loadOwnTask((int) $id, $user);
        $status = (string) ($_POST['status'] ?? '');
        $note   = trim((string) ($_POST['note'] ?? ''));

        $errors = $this->validateText($note, required: false);
        if (!$errors && !in_array($status, self::PROGRESS_STATUSES, true)) {
            $errors[] = 'tasks.progress.error.status';
        }
        if (!$errors && $task['status'] === 'done') {
            $errors[] = 'tasks.error.alreadyDone';
        }
        if ($errors) {
            $this->showForm('task_progress', 'tasks.progress.title', $task, ['status' => $status, 'note' => $note], $errors, 422);
            return;
        }

        $this->changeStatus($task, $user, $status, $note !== '' ? 'progress' : null, $note);
        flash_set('flash.progressSaved');
        $this->redirect('/tasks/' . $task['id']);
    }


    public function reviewForm(string $id): void
    {
        $user = $this->requireUser();
        $task = $this->loadOwnTask((int) $id, $user);
        $this->showForm('task_review', 'tasks.review.title', $task, ['body' => ''], [], 200);
    }

    public function submitReview(string $id): void
    {
        $user = $this->requireUser();
        $task = $this->loadOwnTask((int) $id, $user);
        $body = trim((string) ($_POST['body'] ?? ''));

        $errors = $this->validateText($body, required: true, emptyKey: 'tasks.review.error.empty');
        if (!$errors && in_array($task['status'], ['review', 'done'], true)) {
            $errors[] = $task['status'] === 'done' ? 'tasks.error.alreadyDone' : 'tasks.review.error.already';
        }
        if ($errors) {
            $this->showForm('task_review', 'tasks.review.title', $task, ['body' => $body], $errors, 422);
            return;
        }

        $this->changeStatus($task, $user, 'review', 'review', $body);
        flash_set('flash.reviewSubmitted');
        $this->redirect('/tasks/' . $task['id']);
    }

    public function feedback(string $id): void
    {
        $user     = $this->requireUser();
        $task     = $this->loadOwnTask((int) $id, $user);
        $feedback = array_reverse($this->updates((int) $task['id'], ['feedback'])); // newest first

        $this->render('task_feedback', compact('task', 'feedback'), tab: 'todo', titleKey: 'tasks.feedback.title');
    }

    public function deadline(string $id): void
    {
        $user = $this->requireUser();
        $task = $this->withDeadline($this->loadOwnTask((int) $id, $user));

        $this->render('task_deadline', compact('task'), tab: 'todo', titleKey: 'tasks.deadline.title');
    }


    public function accepted(string $id): void
    {
        $user = $this->requireUser();
        $task = $this->loadOwnTask((int) $id, $user);
        if ($task['status'] !== 'done') {
            $this->redirect('/tasks/' . $task['id']);
        }

        $st = $this->pdo()->prepare(
            "SELECT COUNT(*) FROM tasks WHERE project_id = :p AND assigned_to = :u AND status = 'done'"
        );
        $st->execute(['p' => $user['project_id'], 'u' => $user['id']]);
        $isFirst = (int) $st->fetchColumn() === 1;

        $comments = array_reverse($this->updates((int) $task['id'], ['accepted']));
        $comment  = $comments[0] ?? null;

        $this->render('task_accepted', compact('task', 'isFirst', 'comment'),
            tab: 'todo', titleKey: $isFirst ? 'tasks.accepted.firstTitle' : 'tasks.accepted.title');
    }


    private function showDetail(array $task, array $user, string $comment, array $errors, int $status): void
    {
        $task     = $this->withDeadline($task);
        $isMine   = (int) $task['assigned_to'] === (int) $user['id'];
        $canReview = !$isMine && $task['status'] === 'review';
        $history  = array_reverse($this->updates((int) $task['id'])); // newest first
        $hasHandoff  = (bool) array_filter($history, fn ($u) => $u['kind'] === 'handoff');
        $hasFeedback = (bool) array_filter($history, fn ($u) => $u['kind'] === 'feedback');

        $this->render('task_detail',
            compact('task', 'isMine', 'canReview', 'history', 'hasHandoff', 'hasFeedback', 'comment', 'errors'),
            tab: 'todo', titleKey: 'tasks.detail.title', status: $status);
    }

    private function showForm(string $view, string $titleKey, array $task, array $form, array $errors, int $status): void
    {
        $max = self::MAX_LENGTH;
        $statuses = self::PROGRESS_STATUSES;
        $this->render($view, compact('task', 'form', 'errors', 'max', 'statuses'),
            tab: 'todo', titleKey: $titleKey, status: $status);
    }

    /** @return list<string> translation keys of the errors */
    private function validateText(string $text, bool $required, string $emptyKey = ''): array
    {
        if (!csrf_valid()) {
            return ['flash.csrf'];
        }
        if ($required && $text === '') {
            return [$emptyKey];
        }
        if (mb_strlen($text) > self::MAX_LENGTH) {
            return ['tasks.error.tooLong'];
        }
        return [];
    }

    private function changeStatus(array $task, array $user, string $status, ?string $kind, string $body): void
    {
        $pdo = $this->pdo();
        $pdo->beginTransaction();
        try {
            $pdo->prepare('UPDATE tasks SET status = :s WHERE id = :id')
                ->execute(['s' => $status, 'id' => $task['id']]);
            if ($kind !== null) {
                $pdo->prepare('INSERT INTO task_updates (task_id, user_id, kind, body) VALUES (:t, :u, :k, :b)')
                    ->execute(['t' => $task['id'], 'u' => $user['id'], 'k' => $kind, 'b' => $body]);
            }
            $pdo->commit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    private function loadTask(int $id, array $user): array
    {
        $st = $this->pdo()->prepare(
            'SELECT t.*, u.name AS assignee
               FROM tasks t
               LEFT JOIN users u ON u.id = t.assigned_to
              WHERE t.id = :id AND t.project_id = :p'
        );
        $st->execute(['id' => $id, 'p' => $user['project_id']]);
        $task = $st->fetch();
        if (!$task) {
            $this->notFound();
        }
        return $task;
    }

    private function loadOwnTask(int $id, array $user): array
    {
        $task = $this->loadTask($id, $user);
        if ((int) $task['assigned_to'] !== (int) $user['id']) {
            $this->notFound();
        }
        return $task;
    }

    private function updates(int $taskId, array $kinds = []): array
    {
        $sql = 'SELECT tu.kind, tu.body, tu.created_at, u.name
                  FROM task_updates tu
                  JOIN users u ON u.id = tu.user_id
                 WHERE tu.task_id = ?';
        if ($kinds) {
            $sql .= ' AND tu.kind IN (' . implode(',', array_fill(0, count($kinds), '?')) . ')';
        }
        $sql .= ' ORDER BY tu.created_at, tu.id';

        $st = $this->pdo()->prepare($sql);
        $st->execute([$taskId, ...$kinds]);
        return $st->fetchAll();
    }

    private function withDeadline(array $task): array
    {
        $task['days_left'] = null;
        $task['at_risk']   = false;
        if (!empty($task['due_date'])) {
            $today = new \DateTimeImmutable('today', new \DateTimeZone('UTC'));
            $due   = new \DateTimeImmutable($task['due_date'], new \DateTimeZone('UTC'));
            $diff  = $today->diff($due);
            $task['days_left'] = $diff->invert ? -$diff->days : $diff->days;
            $task['at_risk']   = $task['days_left'] <= self::RISK_DAYS
                && !in_array($task['status'], ['review', 'done'], true);
        }
        return $task;
    }
}
