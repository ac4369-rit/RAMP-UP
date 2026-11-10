<?php
declare(strict_types=1);

namespace App;

use App\Controllers\ErrorController;
use Throwable;
//The class is used to parse the url so that we know what controller and what action is being implemented
final class Router
{
    /** @var list<array{string, string, callable|array}> */
    private array $routes = [];

    public function get(string $pattern, callable|array $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    public function post(string $pattern, callable|array $handler): void
    {
        $this->add('POST', $pattern, $handler);
    }

    private function add(string $method, string $pattern, callable|array $handler): void
    {
        $regex = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $pattern) . '$#';
        $this->routes[] = [$method, $regex, $handler];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = rtrim((string) parse_url($uri, PHP_URL_PATH), '/') ?: '/';

        try {
            foreach ($this->routes as [$routeMethod, $regex, $handler]) {
                if ($routeMethod !== $method || !preg_match($regex, $path, $m)) {
                    continue;
                }
                $params = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);

                if (is_array($handler)) {
                    [$class, $action] = $handler;
                    (new $class())->{$action}(...$params);
                } else {
                    $handler(...$params);
                }
                return;
            }
            (new ErrorController())->notFound();
        } catch (Throwable $e) {
            error_log((string) $e);
            if (env('APP_DEBUG') === 'true') {
                throw $e;
            }
            (new ErrorController())->serverError();
        }
    }
}