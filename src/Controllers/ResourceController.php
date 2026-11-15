<?php
declare(strict_types=1);

namespace App\Controllers;

use PDOException;

/** Pantalla 06 — Resources for my role (¿puedo fiarme de este documento?) */
class ResourceController extends Controller
{
    private const FRESH_DAYS = 30;   // pasado este plazo, un recurso verificado pasa a "outdated"

    public function index(): void
    {
        $user  = $this->requireUser();
        $state = 'ready';
        $groups = [];

        try {
            $st = $this->pdo()->prepare(
                'SELECT r.title, r.url, r.kind, r.verified_at, u.name AS verified_by
                   FROM resources r
                   LEFT JOIN users u ON u.id = r.verified_by_id
                  WHERE r.project_id = :pid AND r.role = :role
                  ORDER BY FIELD(r.kind, "guide", "repo", "design", "doc"), r.title'
            );
            $st->execute(['pid' => $user['project_id'], 'role' => $user['role']]);

            foreach ($st->fetchAll() as $r) {
                if ($r['verified_at'] === null) {
                    $r['trust'] = 'unverified';
                } else {
                    $r['trust'] = days_ago($r['verified_at']) > self::FRESH_DAYS ? 'outdated' : 'verified';
                }
                $groups[$r['kind']][] = $r;
            }
            foreach ($groups as &$items) {
                sort_by_translated($items, 'title');   // A–Z in the reader's language
            }
            unset($items);
            if (!$groups) {
                $state = 'empty';
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $state = 'error';
        }

        // Momento de la última sincronización: se guarda en la página, así se ve también offline
        $syncedAt = date('Y-m-d H:i:s');
        $role     = $user['role'];

        $this->render(
            'resources',
            compact('groups', 'state', 'syncedAt', 'role'),
            tab: 'understand',
            titleKey: 'resources.title',
            status: $state === 'error' ? 500 : 200,
        );
    }
}
