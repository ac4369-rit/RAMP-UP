<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\AccessController;
use App\Controllers\BuddyController;
use App\Controllers\CheckController;
use App\Controllers\DecisionController;
use App\Controllers\ErrorController;
use App\Controllers\GlossaryController;
use App\Controllers\HelloController;
use App\Controllers\HomeController;
use App\Controllers\JoinController;
use App\Controllers\MissedController;
use App\Controllers\ProfileController;
use App\Controllers\QuestionController;
use App\Controllers\ResourceController;
use App\Controllers\SearchController;
use App\Controllers\StatusController;
use App\Controllers\TaskController;
use App\Router;
use Dotenv\Dotenv;

Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

date_default_timezone_set('UTC');

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

$router->get('/', fn () => header('Location: /home', true, 302));

$router->get('/offline',                [ErrorController::class,   'offline']);
$router->get('/login',                  [JoinController::class,    'login']);
$router->post('/login',                 [JoinController::class,    'doLogin']);
$router->post('/logout',                [JoinController::class,    'logout']);
$router->get('/join/{code}',            [JoinController::class,    'invite']);
$router->post('/join/{code}',           [JoinController::class,    'accept']);
$router->get('/home',                   [HomeController::class,    'index']);
$router->get('/project',                [HomeController::class,    'project']);
$router->get('/team',                   [HomeController::class,    'team']);
$router->get('/profile',                [ProfileController::class, 'skills']);
$router->post('/profile',               [ProfileController::class, 'saveSkills']);
$router->get('/access',                 [AccessController::class,  'index']);
$router->post('/access/{id}',           [AccessController::class,  'update']);
$router->get('/buddy',                  [BuddyController::class,   'index']);
$router->post('/buddy',                 [BuddyController::class,   'book']);
$router->get('/buddy/{id}/cancel',      [BuddyController::class,   'confirmCancel']);
$router->post('/buddy/{id}/cancel',     [BuddyController::class,   'cancel']);
$router->get('/hello',                  [HelloController::class,   'index']);
$router->post('/hello',                 [HelloController::class,   'store']);
$router->get('/hello/{id}/delete',      [HelloController::class,   'confirmDelete']);
$router->post('/hello/{id}/delete',     [HelloController::class,   'destroy']);
$router->get('/rules',                  [HomeController::class,    'rules']);
$router->get('/notifications',          [ProfileController::class, 'notifications']);
$router->post('/notifications',         [ProfileController::class, 'saveNotifications']);

$router->get('/missed',                 [MissedController::class,   'index']);
$router->get('/status',                 [StatusController::class,   'index']);
$router->get('/decisions',              [DecisionController::class, 'index']);
$router->get('/decisions/{id}',         [DecisionController::class, 'show']);
$router->get('/glossary',               [GlossaryController::class, 'index']);
$router->get('/resources',              [ResourceController::class, 'index']);
$router->get('/search',                 [SearchController::class,   'index']);
$router->get('/ask',                    [QuestionController::class, 'create']);
$router->post('/ask',                   [QuestionController::class, 'store']);
$router->get('/questions/{id}',         [QuestionController::class, 'show']);
$router->post('/questions/{id}/answers',[QuestionController::class, 'answer']);
$router->get('/questions/{id}/edit',    [QuestionController::class, 'edit']);
$router->post('/questions/{id}/edit',   [QuestionController::class, 'update']);
$router->get('/questions/{id}/delete',  [QuestionController::class, 'confirmDelete']);
$router->post('/questions/{id}/delete', [QuestionController::class, 'destroy']);
$router->get('/check',                  [CheckController::class,    'index']);
$router->post('/check',                 [CheckController::class,    'saveScore']);

$router->get('/tasks',                  [TaskController::class, 'index']);
$router->get('/tasks/starter',          [TaskController::class, 'starter']);
$router->post('/tasks/{id}/take',       [TaskController::class, 'take']);
$router->get('/tasks/{id}',             [TaskController::class, 'show']);
$router->post('/tasks/{id}/decide',     [TaskController::class, 'decide']);
$router->get('/tasks/{id}/handoff',     [TaskController::class, 'handoff']);
$router->get('/tasks/{id}/blocked',     [TaskController::class, 'blockedForm']);
$router->post('/tasks/{id}/blocked',    [TaskController::class, 'markBlocked']);
$router->get('/tasks/{id}/progress',    [TaskController::class, 'progressForm']);
$router->post('/tasks/{id}/progress',   [TaskController::class, 'saveProgress']);
$router->get('/tasks/{id}/review',      [TaskController::class, 'reviewForm']);
$router->post('/tasks/{id}/review',     [TaskController::class, 'submitReview']);
$router->get('/tasks/{id}/feedback',    [TaskController::class, 'feedback']);
$router->get('/tasks/{id}/deadline',    [TaskController::class, 'deadline']);
$router->get('/tasks/{id}/accepted',    [TaskController::class, 'accepted']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
