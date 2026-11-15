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

                // Texts whose translation contains the search words (e.g. "račun"), so people can search in their own language.
                $sources = tc_sources($q);

                if ($type === 'all' || $type === 'decisions') {
                    $params = ['pid' => $pid, 'a' => $like, 'b' => $like, 'c' => $like];
                    $st = $pdo->prepare(
                        'SELECT id, title, context AS snippet FROM decisions
                          WHERE project_id = :pid
                            AND (title LIKE :a OR context LIKE :b OR consequences LIKE :c
                                 OR ' . sql_in('title', $sources, 'd1_', $params) . '
                                 OR ' . sql_in('context', $sources, 'd2_', $params) . '
                                 OR ' . sql_in('consequences', $sources, 'd3_', $params) . ')
                          ORDER BY decided_at DESC LIMIT 20'
                    );
                    $st->execute($params);
                    foreach ($st->fetchAll() as $r) {
                        $results[] = ['type' => 'decisions', 'href' => '/decisions/' . $r['id']] + $r;
                    }
                }
                if ($type === 'all' || $type === 'glossary') {
                    $params = ['pid' => $pid, 'a' => $like, 'b' => $like];
                    $st = $pdo->prepare(
                        'SELECT id, term AS title, definition AS snippet FROM glossary_terms
                          WHERE project_id = :pid
                            AND (term LIKE :a OR definition LIKE :b
                                 OR ' . sql_in('term', $sources, 'g1_', $params) . '
                                 OR ' . sql_in('definition', $sources, 'g2_', $params) . ')
                          ORDER BY term LIMIT 20'
                    );
                    $st->execute($params);
                    foreach ($st->fetchAll() as $r) {
                        $results[] = ['type' => 'glossary', 'href' => '/glossary?q=' . rawurlencode(tc($r['title']))] + $r;
                    }
                }
                if ($type === 'all' || $type === 'questions') {
                    $params = ['pid' => $pid, 'a' => $like];
                    $st = $pdo->prepare(
                        'SELECT id, body AS title, "" AS snippet FROM questions
                          WHERE project_id = :pid
                            AND (body LIKE :a OR ' . sql_in('body', $sources, 'q1_', $params) . ')
                          ORDER BY created_at DESC LIMIT 20'
                    );
                    $st->execute($params);
                    foreach ($st->fetchAll() as $r) {
                        $results[] = ['type' => 'questions', 'href' => '/questions/' . $r['id']] + $r;
                    }
                }

                // Translate first, then shorten (a shortened English text would never match its translation).
                foreach ($results as &$r) {
                    $r['title']   = mb_strimwidth(tc($r['title']), 0, 90, '…');
                    $r['snippet'] = mb_strimwidth(tc($r['snippet']), 0, 140, '…');
                }
                unset($r);

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
