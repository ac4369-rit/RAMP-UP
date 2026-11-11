<?php
declare(strict_types=1);

namespace App\Controllers;

use PDO;
use PDOException;

/** Pantalla 07 — Search project knowledge (decisiones, glosario y preguntas) */
class SearchController extends Controller
{
    private const TYPES = ['all', 'decisions', 'glossary', 'questions'];

    public function index(): void
    {
        $user = $this->requireUser();
        $q    = trim((string) ($_GET['q'] ?? ''));
        $type = (string) ($_GET['type'] ?? 'all');
        if (!in_array($type, self::TYPES, true)) {
            $type = 'all';
        }

        $state = $q === '' ? 'idle' : 'ready';
        $results = [];

        if ($q !== '') {
            try {
                $pdo  = $this->pdo();
                $like = '%' . addcslashes($q, '%_\\') . '%';
                $pid  = $user['project_id'];

                if ($type === 'all' || $type === 'decisions') {
                    $st = $pdo->prepare(
                        'SELECT id, title, LEFT(context, 140) AS snippet FROM decisions
                          WHERE project_id = :pid AND (title LIKE :a OR context LIKE :b OR consequences LIKE :c)
                          ORDER BY decided_at DESC LIMIT 20'
                    );
                    $st->execute(['pid' => $pid, 'a' => $like, 'b' => $like, 'c' => $like]);
                    foreach ($st->fetchAll() as $r) {
                        $results[] = ['type' => 'decisions', 'href' => '/decisions/' . $r['id']] + $r;
                    }
                }
                if ($type === 'all' || $type === 'glossary') {
                    $st = $pdo->prepare(
                        'SELECT id, term AS title, definition AS snippet FROM glossary_terms
                          WHERE project_id = :pid AND (term LIKE :a OR definition LIKE :b)
                          ORDER BY term LIMIT 20'
                    );
                    $st->execute(['pid' => $pid, 'a' => $like, 'b' => $like]);
                    foreach ($st->fetchAll() as $r) {
                        $results[] = ['type' => 'glossary', 'href' => '/glossary?q=' . rawurlencode($r['title'])] + $r;
                    }
                }
                if ($type === 'all' || $type === 'questions') {
                    $st = $pdo->prepare(
                        'SELECT id, LEFT(body, 90) AS title, "" AS snippet FROM questions
                          WHERE project_id = :pid AND body LIKE :a
                          ORDER BY created_at DESC LIMIT 20'
                    );
                    $st->execute(['pid' => $pid, 'a' => $like]);
                    foreach ($st->fetchAll() as $r) {
                        $results[] = ['type' => 'questions', 'href' => '/questions/' . $r['id']] + $r;
                    }
                }
                if (!$results) {
                    $state = 'empty';
                }
            } catch (PDOException $e) {
                error_log($e->getMessage());
                $state = 'error';
            }
        }

        $this->render(
            'search',
            compact('q', 'type', 'results', 'state'),
            tab: 'search',
            titleKey: 'search.title',
            status: $state === 'error' ? 500 : 200,
        );
    }
}
