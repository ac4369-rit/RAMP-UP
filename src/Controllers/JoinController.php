<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Auth;
use App\Translator;

class JoinController extends Controller
{
    public function login(): void
    {
        if (Auth::user() !== null) {
            $this->redirect('/home');
        }
        $this->showForm(null, '', []);
    }

    public function doLogin(): void
    {
        $email = trim((string) ($_POST['email'] ?? ''));

        if (!csrf_valid()) {
            $this->showForm(null, $email, ['flash.csrf'], 400);
            return;
        }
        $user = Auth::attempt($email, (string) ($_POST['password'] ?? ''));
        if ($user === null) {
            $this->showForm(null, $email, ['join.error.credentials'], 422);
            return;
        }

        Auth::login((int) $user['id']);
        $this->rememberLocale($user['locale']);
        $this->redirect('/home');
    }

    public function invite(string $code): void
    {
        $invite = $this->loadInvite($code);
        $this->showForm($invite, Auth::user()['email'] ?? $invite['email'], []);
    }

    public function accept(string $code): void
    {
        $invite = $this->loadInvite($code);
        $email  = trim((string) ($_POST['email'] ?? ''));
        $state  = $this->inviteState($invite);
        $errors = [];
        $user   = Auth::user();

        if (!csrf_valid()) {
            $errors[] = 'flash.csrf';
        } elseif ($state !== 'open') {
            $errors[] = 'join.error.' . $state;
        } else {
            if ($user === null) {
                $user = Auth::attempt($email, (string) ($_POST['password'] ?? ''));
                if ($user === null) {
                    $errors[] = 'join.error.credentials';
                }
            }
            if ($user !== null && mb_strtolower($user['email']) !== mb_strtolower($invite['email'])) {
                $errors[] = 'join.error.wrongAccount';
            }
        }

        if ($errors) {
            $this->showForm($invite, $email, $errors, 422);
            return;
        }

        $pdo = $this->pdo();
        $pdo->beginTransaction();
        $pdo->prepare('INSERT IGNORE INTO memberships (project_id, user_id, role) VALUES (:p, :u, :r)')
            ->execute(['p' => $invite['project_id'], 'u' => $user['id'], 'r' => $invite['role']]);
        $pdo->prepare("UPDATE invitations SET status = 'accepted' WHERE id = :id")
            ->execute(['id' => $invite['id']]);
        $pdo->commit();

        Auth::login((int) $user['id'], (int) $invite['project_id']);
        $this->rememberLocale($user['locale']);
        flash_set('flash.joined', ['project' => $invite['project_name']]);
        $this->redirect('/profile');
    }

    public function logout(): void
    {
        if (csrf_valid()) {
            Auth::logout();
        }
        $this->redirect('/login');
    }

    private function loadInvite(string $code): array
    {
        $st = $this->pdo()->prepare(
            'SELECT i.*, p.name AS project_name
               FROM invitations i
               JOIN projects p ON p.id = i.project_id
              WHERE i.invite_code = :code'
        );
        $st->execute(['code' => $code]);
        $invite = $st->fetch();
        if (!$invite) {
            $this->notFound();
        }
        return $invite;
    }

    private function inviteState(array $invite): string
    {
        if ($invite['status'] === 'accepted') {
            return 'accepted';
        }
        $expires = new \DateTimeImmutable($invite['expires_at'], new \DateTimeZone('UTC'));
        if ($invite['status'] !== 'pending' || $expires < new \DateTimeImmutable('now', new \DateTimeZone('UTC'))) {
            return 'expired';
        }
        return 'open';
    }

    private function rememberLocale(string $locale): void
    {
        if (!isset($_COOKIE['lang']) && in_array($locale, Translator::SUPPORTED, true)) {
            setcookie('lang', $locale, ['expires' => time() + 60 * 60 * 24 * 365, 'path' => '/', 'samesite' => 'Lax']);
        }
    }

    private function showForm(?array $invite, string $email, array $errors, int $status = 200): void
    {
        $inviteState = $invite !== null ? $this->inviteState($invite) : null;
        $signedIn    = Auth::user();

        $this->render(
            'join',
            compact('invite', 'inviteState', 'email', 'errors', 'signedIn'),
            tab: '',
            titleKey: $invite !== null ? 'join.title' : 'join.loginTitle',
            status: $status,
        );
    }
}
