<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

//use App\Controllers\DecisionController;
use App\Router;
use Dotenv\Dotenv;

Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

if (env('APP_DEBUG') === 'true') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => !empty($_SERVER['HTTPS']),
]);
session_start();   

$router = new Router();


$router->get('/', fn () => header('Location: /decisions', true, 302));

//Understanding the project (Alba's part)
//$router->get('/decisions', [DecisionController::class, 'index']);
// $router->get('/missed',            [MissedController::class,   'index']);   // 01
// $router->get('/status',            [StatusController::class,   'index']);   // 02
// $router->get('/decisions/{id}',    [DecisionController::class, 'show']);    // 04
// $router->get('/glossary',          [GlossaryController::class, 'index']);   // 05
// $router->get('/resources',         [ResourceController::class, 'index']);   // 06
// $router->get('/search',            [SearchController::class,   'index']);   // 07
// $router->get('/ask',               [QuestionController::class, 'create']);  // 08
// $router->post('/ask',              [QuestionController::class, 'store']);
// $router->get('/questions/{id}',    [QuestionController::class, 'show']);    // 09
// $router->get('/check',             [CheckController::class,    'index']);   // 10

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);