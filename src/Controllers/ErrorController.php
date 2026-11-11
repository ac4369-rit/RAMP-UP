<?php
declare(strict_types=1);

namespace App\Controllers;

class ErrorController extends Controller
{
    public function notFound(): void
    {
        $this->render('error', ['key' => 'errors.notFound'], tab: '', titleKey: 'errors.notFound.title', status: 404);
    }

    public function serverError(): void
    {
        $this->render('error', ['key' => 'errors.serverError'], tab: '', titleKey: 'errors.serverError.title', status: 500);
    }

    /** Página que el service worker sirve cuando no hay red ni copia guardada. */
    public function offline(): void
    {
        $this->render('error', ['key' => 'errors.offline'], tab: '', titleKey: 'errors.offline.title');
    }
}
