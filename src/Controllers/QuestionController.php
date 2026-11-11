<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\QuestionMatcher;
use PDO;
use PDOException;

/** Pantallas 08 (Ask a question) y 09 (Question thread) · CRUD completo de preguntas */
class QuestionController extends Controller
{
    // ---------- 08: crear ----------
    public function create(): void
    {
        $user = $this->requireUser();
        $form = ['body' => trim((string) ($_GET['q'] ?? '')), 'area' => $user['role'], 'to' => '', 'related' => ''];

        if (ctype_digit((string) ($_GET['decision'] ?? ''))) {
            $d = $this->decisionTitle((int) $_GET['decision'], $user);
            if ($d !== null) {
                $form['related'] = (string) (int) $_GET['decision'];
            }
        }
        $this->showForm($user, $form, [], null);
    }

    public function store(): void
    {
        $user = $this->requireUser();
        [$form, $errors] = $this->readForm($user);

        if ($errors === [] && !isset($_POST['force'])) {
            $match = (new QuestionMatcher())->find($this->pdo(), (int) $user['project_id'], $form['body']);
            if ($match !== null) {              // ya respondida: avisamos antes de duplicar
                $this->showForm($user, $form, [], $match);
                return;
            }
        }
        if ($errors) {
            $this->showForm($user, $form, $errors, null, 422);
            return;
        }

        $st = $this->pdo()->prepare(
            'INSERT INTO questions (project_id, author_id, to_user_id, area, body, related_decision_id)
             VALUES (:pid, :author, :to, :area, :body, :rel)'
        );
        $st->execute([
            'pid' => $user['project_id'], 'author' => $user['id'],
            'to'  => $form['to'] !== '' ? (int) $form['to'] : null,
            'area' => $form['area'], 'body' => $form['body'],
            'rel' => $form['related'] !== '' ? (int) $form['related'] : null,
        ]);
        flash_set('flash.questionPosted');
        $this->redirect('/questions/' . $this->pdo()->lastInsertId());
    }

    // ---------- 09: leer ----------
    public function show(string $id): void
    {
        $user = $this->requireUser();
        $question = $this->loadQuestion((int) $id, $user);

        $st = $this->pdo()->prepare(
            'SELECT a.id, a.body, a.created_at, u.name FROM answers a
               JOIN users u ON u.id = a.author_id
              WHERE a.question_id = :id ORDER BY a.created_at, a.id'
        );
        $st->execute(['id' => $question['id']]);
        $answers  = $st->fetchAll();
        $isAuthor = (int) $question['author_id'] === (int) $user['id'];

        $this->render('thread', compact('question', 'answers', 'isAuthor'),
            tab: 'search', titleKey: 'thread.title');
    }

    public function answer(string $id): void
    {
        $user = $this->requireUser();
        $question = $this->loadQuestion((int) $id, $user);
        $body = trim((string) ($_POST['body'] ?? ''));

        if (!csrf_valid()) {
            flash_set('flash.csrf');
        } elseif ($body === '' || mb_strlen($body) > 2000) {
            flash_set('flash.answerInvalid');
        } else {
            $this->pdo()->prepare('INSERT INTO answers (question_id, author_id, body) VALUES (:q, :u, :b)')
                ->execute(['q' => $question['id'], 'u' => $user['id'], 'b' => $body]);
            flash_set('flash.answerPosted');
        }
        $this->redirect('/questions/' . $question['id']);
    }

    // ---------- editar ----------
    public function edit(string $id): void
    {
        $user = $this->requireUser();
        $q = $this->loadOwnQuestion((int) $id, $user);
        $form = [
            'body' => $q['body'], 'area' => $q['area'],
            'to' => $q['to_user_id'] !== null ? (string) $q['to_user_id'] : '',
            'related' => $q['related_decision_id'] !== null ? (string) $q['related_decision_id'] : '',
        ];
        $this->showEdit($user, $q, $form, []);
    }

    public function update(string $id): void
    {
        $user = $this->requireUser();
        $q = $this->loadOwnQuestion((int) $id, $user);
        [$form, $errors] = $this->readForm($user);
        if ($errors) {
            $this->showEdit($user, $q, $form, $errors, 422);
            return;
        }
        $this->pdo()->prepare(
            'UPDATE questions SET body = :body, area = :area, to_user_id = :to WHERE id = :id AND author_id = :me'
        )->execute([
            'body' => $form['body'], 'area' => $form['area'],
            'to' => $form['to'] !== '' ? (int) $form['to'] : null,
            'id' => $q['id'], 'me' => $user['id'],
        ]);
        flash_set('flash.questionUpdated');
        $this->redirect('/questions/' . $q['id']);
    }

    // ---------- borrar (con confirmación) ----------
    public function confirmDelete(string $id): void
    {
        $user = $this->requireUser();
        $question = $this->loadOwnQuestion((int) $id, $user);
        $this->render('question_delete', compact('question'), tab: 'search', titleKey: 'delete.title');
    }

    public function destroy(string $id): void
    {
        $user = $this->requireUser();
        $q = $this->loadOwnQuestion((int) $id, $user);
        if (!csrf_valid()) {
            flash_set('flash.csrf');
            $this->redirect('/questions/' . $q['id']);
        }
        $this->pdo()->prepare('DELETE FROM questions WHERE id = :id AND author_id = :me')
            ->execute(['id' => $q['id'], 'me' => $user['id']]);
        flash_set('flash.questionDeleted');
        $this->redirect('/ask');
    }

    // ---------- utilidades ----------
    private function showForm(array $user, array $form, array $errors, ?array $match, int $status = 200): void
    {
        $pdo = $this->pdo();
        $roles = $pdo->query('SELECT code FROM roles ORDER BY code')->fetchAll(PDO::FETCH_COLUMN);
        $team  = $this->team($user);

        $st = $pdo->prepare(
            'SELECT q.id, q.body, q.created_at,
                    (SELECT COUNT(*) FROM answers a WHERE a.question_id = q.id) AS replies
               FROM questions q WHERE q.author_id = :me AND q.project_id = :pid
              ORDER BY q.created_at DESC LIMIT 10'
        );
        $st->execute(['me' => $user['id'], 'pid' => $user['project_id']]);
        $mine = $st->fetchAll();

        $decisionTitle = $form['related'] !== '' ? $this->decisionTitle((int) $form['related'], $user) : null;

        $this->render('ask', compact('form', 'errors', 'match', 'roles', 'team', 'mine', 'decisionTitle'),
            tab: 'search', titleKey: 'ask.title', status: $status);
    }

    private function showEdit(array $user, array $question, array $form, array $errors, int $status = 200): void
    {
        $roles = $this->pdo()->query('SELECT code FROM roles ORDER BY code')->fetchAll(PDO::FETCH_COLUMN);
        $team  = $this->team($user);
        $this->render('question_edit', compact('question', 'form', 'errors', 'roles', 'team'),
            tab: 'search', titleKey: 'ask.edit', status: $status);
    }

    private function team(array $user): array
    {
        $st = $this->pdo()->prepare(
            'SELECT u.id, u.name, m.role FROM memberships m JOIN users u ON u.id = m.user_id
              WHERE m.project_id = :pid AND u.id <> :me ORDER BY u.name'
        );
        $st->execute(['pid' => $user['project_id'], 'me' => $user['id']]);
        return $st->fetchAll();
    }

    private function decisionTitle(int $id, array $user): ?string
    {
        $st = $this->pdo()->prepare('SELECT title FROM decisions WHERE id = :id AND project_id = :pid');
        $st->execute(['id' => $id, 'pid' => $user['project_id']]);
        $t = $st->fetchColumn();
        return $t === false ? null : (string) $t;
    }

    /** @return array{0: array, 1: list<string>} formulario limpio y claves de error */
    private function readForm(array $user): array
    {
        $form = [
            'body'    => trim((string) ($_POST['body'] ?? '')),
            'area'    => (string) ($_POST['area'] ?? ''),
            'to'      => (string) ($_POST['to'] ?? ''),
            'related' => (string) ($_POST['related'] ?? ''),
        ];
        $errors = [];

        if (!csrf_valid()) {
            $errors[] = 'flash.csrf';
        }
        if ($form['body'] === '') {
            $errors[] = 'ask.error.empty';
        } elseif (mb_strlen($form['body']) > 1000) {
            $errors[] = 'ask.error.tooLong';
        }
        $roles = $this->pdo()->query('SELECT code FROM roles')->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array($form['area'], $roles, true)) {
            $errors[] = 'ask.error.area';
        }
        if ($form['to'] !== '') {
            $ids = array_map(fn ($m) => (string) $m['id'], $this->team($user));
            if (!in_array($form['to'], $ids, true)) {
                $errors[] = 'ask.error.to';
            }
        }
        if ($form['related'] !== '' && $this->decisionTitle((int) $form['related'], $user) === null) {
            $form['related'] = '';
        }
        return [$form, $errors];
    }

    private function loadQuestion(int $id, array $user): array
    {
        $st = $this->pdo()->prepare(
            'SELECT q.*, a.name AS author_name, t.name AS to_name, d.title AS decision_title
               FROM questions q
               JOIN users a ON a.id = q.author_id
               LEFT JOIN users t ON t.id = q.to_user_id
               LEFT JOIN decisions d ON d.id = q.related_decision_id
              WHERE q.id = :id AND q.project_id = :pid'
        );
        $st->execute(['id' => $id, 'pid' => $user['project_id']]);
        $q = $st->fetch();
        if (!$q) {
            $this->notFound();
        }
        return $q;
    }

    /** Solo el autor puede editar o borrar; para los demás la pregunta "no existe". */
    private function loadOwnQuestion(int $id, array $user): array
    {
        $q = $this->loadQuestion($id, $user);
        if ((int) $q['author_id'] !== (int) $user['id']) {
            $this->notFound();
        }
        return $q;
    }
}
