<?php
declare(strict_types=1);

namespace App\Controllers;

use PDOException;

class HelloController extends Controller
{
    private const MAX_LENGTH = 1000;

    public function index(): void
    {
        $user = $this->requireUser();
        $this->showWall($user, '', [], 200);
    }

    public function store(): void
    {
        $user = $this->requireUser();
        $body = trim((string) ($_POST['body'] ?? ''));

        $errors = [];
        if (!csrf_valid()) {
            $errors[] = 'flash.csrf';
        } elseif ($body === '') {
            $errors[] = 'hello.error.empty';
        } elseif (mb_strlen($body) > self::MAX_LENGTH) {
            $errors[] = 'hello.error.tooLong';
        }
        if ($errors) {
            $this->showWall($user, $body, $errors, 422);
            return;
        }

        $this->pdo()->prepare('INSERT INTO team_posts (project_id, user_id, body) VALUES (:p, :u, :b)')
            ->execute(['p' => $user['project_id'], 'u' => $user['id'], 'b' => $body]);
        flash_set('flash.helloPosted');
        $this->redirect('/hello');
    }

    public function confirmDelete(string $id): void
    {
        $user = $this->requireUser();
        $post = $this->loadOwnPost((int) $id, $user);
        $this->render('hello_delete', compact('post'), tab: 'todo', titleKey: 'hello.deleteTitle');
    }

    public function destroy(string $id): void
    {
        $user = $this->requireUser();
        $post = $this->loadOwnPost((int) $id, $user);

        if (!csrf_valid()) {
            flash_set('flash.csrf');
        } else {
            $this->pdo()->prepare('DELETE FROM team_posts WHERE id = :id AND user_id = :u')
                ->execute(['id' => $post['id'], 'u' => $user['id']]);
            flash_set('flash.helloDeleted');
        }
        $this->redirect('/hello');
    }

    private function showWall(array $user, string $body, array $errors, int $status): void
    {
        $state = 'ready';
        $posts = [];

        try {
            $st = $this->pdo()->prepare(
                'SELECT p.id, p.body, p.created_at, p.user_id, u.name
                   FROM team_posts p
                   JOIN users u ON u.id = p.user_id
                  WHERE p.project_id = :p
                  ORDER BY p.created_at DESC, p.id DESC'
            );
            $st->execute(['p' => $user['project_id']]);
            $posts = $st->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $state = 'error';
        }

        $me  = (int) $user['id'];
        $max = self::MAX_LENGTH;
        $this->render('hello', compact('posts', 'body', 'errors', 'state', 'me', 'max'),
            tab: 'todo', titleKey: 'hello.title', status: $state === 'error' ? 500 : $status);
    }

    private function loadOwnPost(int $id, array $user): array
    {
        $st = $this->pdo()->prepare(
            'SELECT id, body FROM team_posts WHERE id = :id AND user_id = :u AND project_id = :p'
        );
        $st->execute(['id' => $id, 'u' => $user['id'], 'p' => $user['project_id']]);
        $post = $st->fetch();
        if (!$post) {
            $this->notFound();
        }
        return $post;
    }
}
