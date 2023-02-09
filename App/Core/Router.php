<?php

namespace App\Core;

use App\Core\Responses;

class Router
{
    use Responses;
    private static $routes = [];
    private static $controller = null;
    private static $middlewares = [];

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

    public static function get(string $path, $action = null, array $middlewares = []): void
    {
        if (is_array($action)) {
            $controller = $action[0];
            $function = $action[1];
        } else {
            $controller = self::$controller;
            $function = $action;
        }

        if (count(self::$middlewares) > 0) {
            $middlewares = array_merge(self::$middlewares, $middlewares);
        }

        self::add('GET', $path, $controller, $function, $middlewares);
    }

    public static function post(string $path, $action = null, array $middlewares = []): void
    {
        if (is_array($action)) {
            $controller = $action[0];
            $function = $action[1];
        } else {
            $controller = self::$controller;
            $function = $action;
        }

        if (count(self::$middlewares) > 0) {
            $middlewares = array_merge(self::$middlewares, $middlewares);
        }

        self::add('POST', $path, $controller, $function, $middlewares);
    }

    public static function put(string $path, $action = null, array $middlewares = []): void
    {
        if (is_array($action)) {
            $controller = $action[0];
            $function = $action[1];
        } else {
            $controller = self::$controller;
            $function = $action;
        }

        if (count(self::$middlewares) > 0) {
            $middlewares = array_merge(self::$middlewares, $middlewares);
        }

        self::add('PUT', $path, $controller, $function, $middlewares);
    }


    public static function patch(string $path, $action = null, array $middlewares = []): void
    {
        if (is_array($action)) {
            $controller = $action[0];
            $function = $action[1];
        } else {
            $controller = self::$controller;
            $function = $action;
        }

        if (count(self::$middlewares) > 0) {
            $middlewares = array_merge(self::$middlewares, $middlewares);
        }

        self::add('PATCH', $path, $controller, $function, $middlewares);
    }

    public static function delete(string $path, $action = null, array $middlewares = []): void
    {
        if (is_array($action)) {
            $controller = $action[0];
            $function = $action[1];
        } else {
            $controller = self::$controller;
            $function = $action;
        }

        if (count(self::$middlewares) > 0) {
            $middlewares = array_merge(self::$middlewares, $middlewares);
        }

        self::add('DELETE', $path, $controller, $function, $middlewares);
    }



    public static function controller(string $controller, array $middlewares = []): Router
    {
        self::$controller = $controller;
        self::$middlewares = $middlewares;
        return new self;
    }

    public function group(callable $callback): void
    {
        $callback();
    }

    public static function run(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = $_SERVER['REQUEST_URI'];
        $requestUri = explode('?', $requestUri)[0];

        // if default routes response welcome message
        if ($requestUri === '/') {
            self::response(200, ['message' => 'Welcome to the Mardira Framework']);
            return;
        }

        foreach (self::$routes as $route) {

            // if request uri same with route path
            if ($route['path'] === $requestUri) {
                if ($route['method'] !== $requestMethod) {
                    self::response(405, ['message' => 'Method Not Allowed']);
                    return;
                }
            }


            if ($route['method'] === $requestMethod) {

                $path = $route['path'];
                $path = str_replace('/', '\/', $path);
                $path = preg_replace('/\{[a-zA-Z0-9]+\}/', '([a-zA-Z0-9]+)', $path);
                $path = '/^' . $path . '$/';
                try {
                    if (preg_match($path, $requestUri, $matches)) {

                        // if route has middleware
                        if (count($route['middleware']) > 0) {
                            foreach ($route['middleware'] as $middleware) {
                                $middleware = new $middleware;
                                $middleware->handle(function () {
                                    return;
                                });
                            }
                        }

                        $controller = $route['controller'];
                        $function = $route['function'];
                        $controller = new $controller;
                        $controller->$function(...array_slice($matches, 1));
                        return;
                    }
                } catch (\Throwable $th) {
                    self::response(500, ['message' => $th->getMessage()]);
                    return;
                }
            }
        }

        self::response(404, ['message' => 'Not Found']);
    }
}
