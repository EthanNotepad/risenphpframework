<?php

namespace libs\Core;

use Exception;
use libs\Core\Middleware\MiddlewareInterface;

class Router
{
    public static $halts = false;
    public static $routes = array();
    public static $methods = array();
    public static $callbacks = array();
    public static $middleware = array();
    public static $maps = array();
    private static $patterns = array(
        ':any' => '[^/]+',
        ':num' => '[0-9]+',
        ':all' => '.*'
    );
    public static $error_callback;
    private static $root;

    /**
     * 1. Register the route first
     * 2. Match the route according to the requested url and call the method
     * 3. If there is middleware, execute the middleware first
     */

    /**
     * Defines a route w/ callback and method
     */
    public static function __callstatic($method, $params)
    {
        // You can use the map method to put multiple request methods in the form of an array
        if ($method == 'map') {
            $maps = array_map('strtoupper', $params[0]);
            $uri = strpos($params[1], '/') === 0 ? $params[1] : '/' . $params[1];
            $callback = $params[2];
            $middleware = $params[3] ?? null;
        } else {
            $maps = null;
            $uri = strpos($params[0], '/') === 0 ? $params[0] : '/' . $params[0];
            $callback = $params[1];
            $middleware = $params[2] ?? null;
        }
        array_push(self::$maps, $maps);
        array_push(self::$routes, $uri);
        array_push(self::$methods, strtoupper($method));
        array_push(self::$callbacks, $callback);
        array_push(self::$middleware, $middleware);
    }

    /**
     * Defines callback if route is not found
     */
    public static function error($callback)
    {
        self::$error_callback = $callback;
    }

    /**
     * if the url matches multiple routing configurations
     * whether to execute one or multiple routing configurations
     */
    public static function haltOnMatch($flag = true)
    {
        self::$halts = $flag;
    }

    /**
     * Get the root path of the current site and remove index.php
     */
    public static function getRoot()
    {
        if (self::$root === null) {
            self::$root = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
        }
        return self::$root;
    }

    public static function dispatch()
    {
        // Get the root directory of the website
        $rootUri = self::getRoot();

        // To avoid entering '/' at the end of the uri
        $uri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        // Remove the root directory from uri
        if (!empty($rootUri) && $rootUri !== '/') {
            $rootUri = rtrim($rootUri, '/');
            if ($rootUri === $uri) {
                $uri = '/';
            } else {
                $uri = substr_replace($uri, '', strpos($uri, $rootUri), strlen($rootUri));
            }
        }

        // If the uri is empty, set it to '/'
        if ($uri == '') {
            $uri = '/';
        }

        $method = $_SERVER['REQUEST_METHOD'];

        $searches = array_keys(static::$patterns);
        $replaces = array_values(static::$patterns);

        $found_route = false;

        self::$routes = preg_replace('/\/+/', '/', self::$routes);

        FileLogger::info('User sends API request: [' . $method . '] ' . $uri);

        // Check if route is defined without regex
        if (in_array($uri, self::$routes)) {
            $route_pos = array_keys(self::$routes, $uri);

            // The same url can match multiple pieces of data
            foreach ($route_pos as $route) {

                // Using an ANY option to match both GET and POST requests
                if (self::$methods[$route] == $method || self::$methods[$route] == 'ANY' || (!empty(self::$maps[$route]) && in_array($method, self::$maps[$route]))) {
                    $found_route = true;

                    // If there is a middleware passed, 
                    // execute the middleware first
                    if (!is_null(self::$middleware[$route])) {
                        $middleware = self::$middleware[$route];
                        if (is_subclass_of($middleware, MiddlewareInterface::class)) {
                            $middlewareObj = new $middleware;
                            $middlewareObj->handle();
                        } else {
                            throw new Exception("Invalid middleware class: $middleware");
                        }
                    }

                    // If route is not an object
                    if (!is_object(self::$callbacks[$route])) {

                        // Grab all parts based on a / separator
                        $parts = explode('/', self::$callbacks[$route]);

                        // Collect the last index of the array
                        $last = end($parts);

                        // Grab the controller name and method call
                        $segments = explode('@', $last);

                        // Instanitate controller
                        $controller = new $segments[0]();

                        // Call method
                        $controller->{$segments[1]}();

                        if (self::$halts) return;
                    } else {
                        // Call closure
                        call_user_func(self::$callbacks[$route]);

                        if (self::$halts) return;
                    }
                }
            }
        } else {
            // Check if defined with regex
            $pos = 0;
            foreach (self::$routes as $route) {
                if (strpos($route, ':') !== false) {
                    $route = str_replace($searches, $replaces, $route);
                }

                if (preg_match('#^' . $route . '$#', $uri, $matched)) {
                    if (self::$methods[$pos] == $method || self::$methods[$pos] == 'ANY' || (!empty(self::$maps[$pos]) && in_array($method, self::$maps[$pos]))) {
                        $found_route = true;

                        // Remove $matched[0] as [1] is the first parameter.
                        array_shift($matched);

                        // If there is a middleware passed, 
                        // execute the middleware first
                        if (!is_null(self::$middleware[$pos])) {
                            $middleware = self::$middleware[$pos];
                            if (is_subclass_of($middleware, MiddlewareInterface::class)) {
                                $middlewareObj = new $middleware;
                                $middlewareObj->handle();
                            } else {
                                throw new Exception("Invalid middleware class: $middleware");
                            }
                        }

                        if (!is_object(self::$callbacks[$pos])) {

                            // Grab all parts based on a / separator
                            $parts = explode('/', self::$callbacks[$pos]);

                            // Collect the last index of the array
                            $last = end($parts);

                            // Grab the controller name and method call
                            $segments = explode('@', $last);

                            // Instanitate controller
                            $controller = new $segments[0]();

                            // Fix multi parameters
                            if (!method_exists($controller, $segments[1])) {
                                echo "controller and action not found";
                            } else {
                                call_user_func_array(array($controller, $segments[1]), $matched);
                            }

                            if (self::$halts) return;
                        } else {
                            call_user_func_array(self::$callbacks[$pos], $matched);

                            if (self::$halts) return;
                        }
                    }
                }
                $pos++;
            }
        }

        // Run the error callback if the route was not found
        if ($found_route == false) {
            FileLogger::warning('invalid request url: [' . $method . '] ' . $uri);
            if (!self::$error_callback) {
                self::$error_callback = function () {
                    header($_SERVER['SERVER_PROTOCOL'] . " 404 Not Found");
                    Message::send(404, [], 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found on this server.');
                };
            } else {
                if (is_string(self::$error_callback)) {
                    self::get($_SERVER['REQUEST_URI'], self::$error_callback);
                    self::$error_callback = null;
                    self::dispatch();
                    return;
                }
            }
            call_user_func(self::$error_callback);
        }
    }

    public static function dispatch4cache()
    {
        global $_CONFIG_ROUTE;

        // Get the root directory of the website
        $rootUri = self::getRoot();

        // To avoid entering '/' at the end of the uri
        $uri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        // Remove the root directory from uri
        if (!empty($rootUri) && $rootUri !== '/') {
            $rootUri = rtrim($rootUri, '/');
            if ($rootUri === $uri) {
                $uri = '/';
            } else {
                $uri = substr_replace($uri, '', strpos($uri, $rootUri), strlen($rootUri));
            }
        }

        // If the uri is empty, set it to '/'
        if ($uri == '') {
            $uri = '/';
        }

        $method = $_SERVER['REQUEST_METHOD'];

        $searches = array_keys(static::$patterns);
        $replaces = array_values(static::$patterns);

        $found_route = false;

        $_CONFIG_ROUTE['routes'] = preg_replace('/\/+/', '/', $_CONFIG_ROUTE['routes']);

        FileLogger::info('User sends API request: [' . $method . '] ' . $uri);

        // Check if route is defined without regex
        if (in_array($uri, $_CONFIG_ROUTE['routes'])) {
            $route_pos = array_keys($_CONFIG_ROUTE['routes'], $uri);

            // The same url can match multiple pieces of data
            foreach ($route_pos as $route) {

                // Using an ANY option to match both GET and POST requests
                if ($_CONFIG_ROUTE['methods'][$route] == $method || $_CONFIG_ROUTE['methods'][$route] == 'ANY' || (!empty($_CONFIG_ROUTE['maps'][$route]) && in_array($method, $_CONFIG_ROUTE['maps'][$route]))) {
                    $found_route = true;

                    // If there is a middleware passed, 
                    // execute the middleware first
                    if (!is_null($_CONFIG_ROUTE['middleware'][$route])) {
                        $middleware = $_CONFIG_ROUTE['middleware'][$route];
                        if (is_subclass_of($middleware, MiddlewareInterface::class)) {
                            $middlewareObj = new $middleware;
                            $middlewareObj->handle();
                        } else {
                            throw new Exception("Invalid middleware class: $middleware");
                        }
                    }

                    // If route is not an object
                    if (!is_object($_CONFIG_ROUTE['callbacks'][$route])) {


                        // Grab all parts based on a / separator
                        $parts = explode('/', $_CONFIG_ROUTE['callbacks'][$route]);
                        // dd($route);

                        // Collect the last index of the array
                        $last = end($parts);

                        // Grab the controller name and method call
                        $segments = explode('@', $last);

                        // Instanitate controller
                        $controller = new $segments[0]();

                        // Call method
                        $controller->{$segments[1]}();

                        if ($_CONFIG_ROUTE['halts']) return;
                    } else {
                        // Call closure
                        call_user_func($_CONFIG_ROUTE['callbacks'][$route]);

                        if ($_CONFIG_ROUTE['halts']) return;
                    }
                }
            }
        } else {
            // Check if defined with regex
            $pos = 0;
            foreach ($_CONFIG_ROUTE['routes'] as $route) {
                if (strpos($route, ':') !== false) {
                    $route = str_replace($searches, $replaces, $route);
                }

                if (preg_match('#^' . $route . '$#', $uri, $matched)) {
                    if ($_CONFIG_ROUTE['methods'][$pos] == $method || $_CONFIG_ROUTE['methods'][$pos] == 'ANY' || (!empty($_CONFIG_ROUTE['maps'][$pos]) && in_array($method, $_CONFIG_ROUTE['maps'][$pos]))) {
                        $found_route = true;

                        // Remove $matched[0] as [1] is the first parameter.
                        array_shift($matched);

                        // If there is a middleware passed, 
                        // execute the middleware first
                        if (!is_null($_CONFIG_ROUTE['middleware'][$pos])) {
                            $middleware = $_CONFIG_ROUTE['middleware'][$pos];
                            if (is_subclass_of($middleware, MiddlewareInterface::class)) {
                                $middlewareObj = new $middleware;
                                $middlewareObj->handle();
                            } else {
                                throw new Exception("Invalid middleware class: $middleware");
                            }
                        }

                        if (!is_object($_CONFIG_ROUTE['callbacks'][$pos])) {

                            // Grab all parts based on a / separator
                            $parts = explode('/', $_CONFIG_ROUTE['callbacks'][$pos]);

                            // Collect the last index of the array
                            $last = end($parts);

                            // Grab the controller name and method call
                            $segments = explode('@', $last);

                            // Instanitate controller
                            $controller = new $segments[0]();

                            // Fix multi parameters
                            if (!method_exists($controller, $segments[1])) {
                                echo "controller and action not found";
                            } else {
                                call_user_func_array(array($controller, $segments[1]), $matched);
                            }

                            if ($_CONFIG_ROUTE['halts']) return;
                        } else {
                            call_user_func_array($_CONFIG_ROUTE['callbacks'][$pos], $matched);

                            if ($_CONFIG_ROUTE['halts']) return;
                        }
                    }
                }
                $pos++;
            }
        }

        // Run the error callback if the route was not found
        if ($found_route == false) {
            FileLogger::warning('invalid request url: [' . $method . '] ' . $uri);
            if (!$_CONFIG_ROUTE['error_callback']) {
                $_CONFIG_ROUTE['error_callback'] = function () {
                    header($_SERVER['SERVER_PROTOCOL'] . " 404 Not Found");
                    Message::send(404, [], 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found on this server.');
                };
            } else {
                if (is_string($_CONFIG_ROUTE['error_callback'])) {
                    self::get($_SERVER['REQUEST_URI'], $_CONFIG_ROUTE['error_callback']);
                    $_CONFIG_ROUTE['error_callback'] = null;
                    self::dispatch();
                    return;
                }
            }
            call_user_func($_CONFIG_ROUTE['error_callback']);
        }
    }
}
