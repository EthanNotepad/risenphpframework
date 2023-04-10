<?php

namespace libs\Core;

use Exception;
use libs\Core\Middleware\MiddlewareInterface;

class Router
{
    private static $routes = [];
    private static $prefix = '';
    private static $uri = '';
    private static $groupUri = '';
    private static $isGroup = false;
    private static $middleware = [];

    public static function any($uri, $handler, $middleware = null)
    {
        self::$routes[] = [
            'uri' => $uri,
            'method' => 'any',
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function middleware($middleware)
    {
        self::$middleware[] = $middleware;
    }

    public static function dispatch()
    {
        $uri = $_SERVER['REQUEST_URI'];
        // $method = strtolower($_SERVER['REQUEST_METHOD']);

        $uri = '/';
        $method = 'any';

        // dd(self::$routes);
        foreach (self::$routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === $method) {
                $handler = $route['handler'];

                if (is_callable($handler)) {
                    if (isset($route['middleware'])) {
                        $middleware = $route['middleware'];
                        if (is_subclass_of($middleware, MiddlewareInterface::class)) {
                            $middlewareObj = new $middleware;
                            $middlewareObj->handle();
                        } else {
                            throw new Exception("Invalid middleware class: $middleware");
                        }
                    }

                    $handler();
                    return;
                } else if (is_string($handler)) {
                    $handlerParts = explode('@', $handler);

                    if (count($handlerParts) !== 2) {
                        throw new Exception("Invalid handler string: $handler");
                    }

                    $controllerName = $handlerParts[0];
                    $methodName = $handlerParts[1];

                    $controller = new $controllerName;

                    if (isset($route['middleware'])) {
                        $middleware = $route['middleware'];
                        if (is_subclass_of($middleware, MiddlewareInterface::class)) {
                            $middlewareObj = new $middleware;
                            $middlewareObj->handle();
                        } else {
                            throw new Exception("Invalid middleware class: $middleware");
                        }
                    }

                    $controller->$methodName();
                    return;
                } else {
                    throw new Exception("Invalid handler type: " . gettype($handler));
                }
            }
        }

        throw new Exception("Route not found: $uri");
    }
}
