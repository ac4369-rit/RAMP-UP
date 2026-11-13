<?php
declare(strict_types=1);

namespace App\Controllers;

use PDO;
use PDOException;

/** Pantallas 03 (Decision log) y 04 (Decision detail) */
class DecisionController extends Controller
{
    public function index(): void
    {
        $user = $this->requireUser();
        $q    = trim((string) ($_GET['q'] ?? ''));
        $area = trim((string) ($_GET['area'] ?? ''));

        $state = 'ready';
        $decisions = $areas = [];

        try {
            $pdo = $this->pdo();
            $sql = 'SELECT d.id, d.title, d.area, u.name AS decided_by, d.decided_at, d.status,
                           EXISTS (SELECT 1 FROM decision_roles dr
                                    WHERE dr.decision_id = d.id AND dr.role = :role) AS affects_me,
                           EXISTS (SELECT 1 FROM decision_reads r
                                    WHERE r.decision_id = d.id AND r.user_id = :uid)  AS is_read
                      FROM decisions d
                      JOIN users u ON u.id = d.decided_by_id
                     WHERE d.project_id = :pid';
            $params = ['role' => $user['role'], 'uid' => $user['id'], 'pid' => $user['project_id']];
            if ($q !== '') {
                $sql .= ' AND d.title LIKE :q';
                $params['q'] = '%' . addcslashes($q, '%_\\') . '%';
            }
            if ($area !== '') {
                $sql .= ' AND d.area = :area';
                $params['area'] = $area;
            }
            $sql .= ' ORDER BY affects_me DESC, d.decided_at DESC';

            $st = $pdo->prepare($sql);
            $st->execute($params);
            $decisions = $st->fetchAll();

            $a = $pdo->prepare('SELECT DISTINCT area FROM decisions WHERE project_id = :pid ORDER BY area');
            $a->execute(['pid' => $user['project_id']]);
            $areas = $a->fetchAll(PDO::FETCH_COLUMN);

            if (!$decisions) {
                $state = 'empty';
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $state = 'error';
        }

        $this->render(
            'decisions',
            compact('decisions', 'areas', 'q', 'area', 'state'),
            tab: 'understand',
            titleKey: 'decisions.title',
            status: $state === 'error' ? 500 : 200,
        );
    }

    public function show(string $id): void
    {
        $user = $this->requireUser();
        $id   = (int) $id;
        $state = 'ready';
        $decision = null;
        $options = $roles = [];

        try {
            $pdo = $this->pdo();
            $st = $pdo->prepare(
                'SELECT d.*, u.name AS decided_by FROM decisions d
                   JOIN users u ON u.id = d.decided_by_id
                  WHERE d.id = :id AND d.project_id = :pid'
            );
            $st->execute(['id' => $id, 'pid' => $user['project_id']]);
            $decision = $st->fetch() ?: null;
            if ($decision === null) {
                $this->notFound();
            }

            $st = $pdo->prepare('SELECT label, description, chosen FROM decision_options WHERE decision_id = :id ORDER BY position');
            $st->execute(['id' => $id]);
            $options = $st->fetchAll();

            $st = $pdo->prepare('SELECT role FROM decision_roles WHERE decision_id = :id ORDER BY role');
            $st->execute(['id' => $id]);
            $roles = $st->fetchAll(PDO::FETCH_COLUMN);

            // Abrirla = leerla: alimenta la preparación de la pantalla 10
            $pdo->prepare('INSERT IGNORE INTO decision_reads (user_id, decision_id) VALUES (:u, :d)')
                ->execute(['u' => $user['id'], 'd' => $id]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $state = 'error';
        }

        $affectsMe = in_array($user['role'], $roles, true);
        $this->render(
            'decision_detail',
            compact('decision', 'options', 'roles', 'affectsMe', 'state'),
            tab: 'understand',
            titleKey: 'decisions.detail',
            status: $state === 'error' ? 500 : 200,
        );
    }
}
