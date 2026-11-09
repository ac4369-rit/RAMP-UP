<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Translator;

/** Base de todos los controladores: resuelve el idioma y pinta la vista dentro del layout. */
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

    /**
     * @param string $page     plantilla en templates/pages (sin .php)
     * @param array  $data     variables para la vista
     * @param string $tab      pestaña activa: pinned | understand | search | todo | '' (ninguna)
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

