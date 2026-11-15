<?php
declare(strict_types=1);

namespace App\Controllers;

use PDOException;

/** Pantalla 05 — Team Glossary */
class GlossaryController extends Controller
{
    public function index(): void
    {
        $user = $this->requireUser();
        $q    = trim((string) ($_GET['q'] ?? ''));
        $state = 'ready';
        $terms = [];

        try {
            $sql = 'SELECT term, definition, used_in FROM glossary_terms WHERE project_id = :pid';
            $params = ['pid' => $user['project_id']];
            if ($q !== '') {
                $sources = tc_sources($q);   // terms whose translation contains the words
                $sql .= ' AND (term LIKE :q1 OR definition LIKE :q2 OR '
                      . sql_in('term', $sources, 't', $params) . ' OR '
                      . sql_in('definition', $sources, 'd', $params) . ')';
                $like = '%' . addcslashes($q, '%_\\') . '%';
                $params['q1'] = $like;
                $params['q2'] = $like;
            }
            $sql .= ' ORDER BY term';

            $st = $this->pdo()->prepare($sql);
            $st->execute($params);
            $terms = $st->fetchAll();
            sort_by_translated($terms, 'term');   // A–Z in the reader's language, not in English
            if (!$terms) {
                $state = 'empty';
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $state = 'error';
        }

        $this->render(
            'glossary',
            compact('terms', 'q', 'state'),
            tab: 'understand',
            titleKey: 'glossary.title',
            status: $state === 'error' ? 500 : 200,
        );
    }
}
