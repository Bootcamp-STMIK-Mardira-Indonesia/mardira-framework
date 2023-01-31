<?php

namespace App\Core;

use App\Core\Responses;

class Router
{
    use Responses;
    private static $routes = [];

    public static function add(
        string $method,
        string $path,
        string $controller,
        string $function,
        array  $middlewares = []
    ): void {
        self::$routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'function' => $function,
            'middleware' => $middlewares
        ];
    }

    public static function run(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = $_SERVER['REQUEST_URI'];
        foreach (self::$routes as $route) {
            if ($route['method'] === $requestMethod) {
                $path = $route['path'];
                $path = str_replace('/', '\/', $path);
                $path = preg_replace('/\{[a-zA-Z0-9]+\}/', '([a-zA-Z0-9]+)', $path);

                $path = '/^' . $path . '$/';
                if (preg_match($path, $requestUri, $matches)) {
                    $controller = $route['controller'];
                    $function = $route['function'];
                    $controller = new $controller;
                    $controller->$function(...array_slice($matches, 1));
                    return;
                }
            }
        }
        self::response(404, ['message' => 'Not found']);
    }
}
