<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Auth;
use App\Database;
use App\Translator;
use PDO;

/** Base de todos los controladores: idioma, usuario, redirecciones y render dentro del layout. */
abstract class Controller
{
    protected string $locale;

    public function __construct()
    {
        $this->locale = $this->resolveLocale();
        Translator::boot($this->locale);
    }

    private function resolveLocale(): string
    {
        $requested = $_GET['lang'] ?? null;
        if (is_string($requested) && in_array($requested, Translator::SUPPORTED, true)) {
            setcookie('lang', $requested, [
                'expires'  => time() + 60 * 60 * 24 * 365,
                'path'     => '/',
                'samesite' => 'Lax',
            ]);
            return $requested;
        }
        $saved = $_COOKIE['lang'] ?? null;
        if (is_string($saved) && in_array($saved, Translator::SUPPORTED, true)) {
            return $saved;
        }
        return Translator::DEFAULT;
    }

    protected function pdo(): PDO
    {
        return Database::getConnection();
    }

    /** Usuario autenticado o redirección al login. */
    protected function requireUser(): array
    {
        $user = Auth::user();
        if ($user === null) {
            $this->redirect('/login');
        }
        return $user;
    }

    protected function redirect(string $url): never
    {
        header('Location: ' . $url, true, 303);
        exit;
    }

    protected function notFound(): never
    {
        (new ErrorController())->notFound();
        exit;
    }

    /**
     * @param string $page     plantilla en templates/pages (sin .php)
     * @param array  $data     variables para la vista
     * @param string $tab      pestaña activa: pinned | understand | search | todo | ''
     * @param string $titleKey clave de traducción del título
     */
    protected function render(string $page, array $data, string $tab, string $titleKey, int $status = 200): void
    {
        http_response_code($status);

        $locale = $this->locale;
        $view   = __DIR__ . '/../../templates/pages/' . $page . '.php';
        extract($data, EXTR_SKIP);

        require __DIR__ . '/../../templates/layout.php';
    }
}
