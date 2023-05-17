<?php

namespace libs\Core;

use Exception;
use libs\Core\Middleware\MiddlewareInterface;

class Router
{
    public static $halts = false;
    public static $softroute = true;
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
     * Whether to enable soft routing, default is true,
     * if set to ture, app can find the corresponding class file according to the url
     */
    public static function setSoftRoute($flag = false)
    {
        self::$softroute = $flag;
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

    public static function processUri()
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

        return $uri;
    }

    public static function processConfig()
    {
        if (config('isCacheConfig')) {
            global $_CONFIG_ROUTE;
            $routesConfig =  $_CONFIG_ROUTE;
        } else {
            $routesConfig['routes'] = self::$routes;
            $routesConfig['halts'] = self::$halts;
            $routesConfig['methods'] = self::$methods;
            $routesConfig['callbacks'] = self::$callbacks;
            $routesConfig['middleware'] = self::$middleware;
            $routesConfig['maps'] = self::$maps;
            $routesConfig['error_callback'] = self::$error_callback;
        }

        return $routesConfig;
    }

    public static function dispatch()
    {
        $uri = self::processUri();

        $routesConfig = self::processConfig();

        $method = $_SERVER['REQUEST_METHOD'];

        $searches = array_keys(static::$patterns);
        $replaces = array_values(static::$patterns);

        $found_route = false;

        $routesConfig['routes'] = preg_replace('/\/+/', '/', $routesConfig['routes']);

        // Check if route is defined without regex
        if (in_array($uri, $routesConfig['routes'])) {
            $route_pos = array_keys($routesConfig['routes'], $uri);

            // The same url can match multiple pieces of data
            foreach ($route_pos as $route) {

                // Using an ANY option to match both GET and POST requests
                if ($routesConfig['methods'][$route] == $method || $routesConfig['methods'][$route] == 'ANY' || (!empty($routesConfig['maps'][$route]) && in_array($method, $routesConfig['maps'][$route]))) {
                    $found_route = true;

                    // If there is a middleware passed, 
                    // execute the middleware first
                    if (!is_null($routesConfig['middleware'][$route])) {
                        $middleware = $routesConfig['middleware'][$route];
                        if (is_subclass_of($middleware, MiddlewareInterface::class)) {
                            $middlewareObj = new $middleware;
                            $middlewareObj->handle();
                        } else {
                            throw new Exception("Invalid middleware class: $middleware");
                        }
                    }

                    // If route is not an object
                    if (!is_object($routesConfig['callbacks'][$route])) {

                        // Grab all parts based on a / separator
                        $parts = explode('/', $routesConfig['callbacks'][$route]);
                        // dd($route);

                        // Collect the last index of the array
                        $last = end($parts);

                        // Grab the controller name and method call
                        $segments = explode('@', $last);

                        // Instanitate controller
                        $controller = new $segments[0]();

                        // Call method
                        $controller->{$segments[1]}();

                        if ($routesConfig['halts']) return;
                    } else {
                        // Call closure
                        call_user_func($routesConfig['callbacks'][$route]);

                        if ($routesConfig['halts']) return;
                    }
                }
            }
        } else {
            // check  if the uri is defined with soft routing
            if (self::$softroute) {

                // Split the URI into an array of segments
                $segments = explode('/', $uri);

                // Remove the first segment (the empty string)
                array_shift($segments);

                // Build the fully-qualified controller class name and method name from the segments
                $controller = 'app\\Controller\\' . implode('\\', array_map('ucfirst', $segments)) . 'Controller';
                $functionName = 'index';

                // Check if the fully-qualified controller class exists and the method is callable
                if (class_exists($controller) && method_exists($controller, $functionName)) {
                    $found_route = true;
                    // Create a new instance of the controller and call the method
                    $instance = new $controller();
                    $instance->$functionName();

                    if ($routesConfig['halts']) return;
                }
            }

            // Check if defined with regex
            $pos = 0;
            foreach ($routesConfig['routes'] as $route) {
                if (strpos($route, ':') !== false) {
                    $route = str_replace($searches, $replaces, $route);
                }

                if (preg_match('#^' . $route . '$#', $uri, $matched)) {
                    if ($routesConfig['methods'][$pos] == $method || $routesConfig['methods'][$pos] == 'ANY' || (!empty($routesConfig['maps'][$pos]) && in_array($method, $routesConfig['maps'][$pos]))) {
                        $found_route = true;

                        // Remove $matched[0] as [1] is the first parameter.
                        array_shift($matched);

                        // If there is a middleware passed, 
                        // execute the middleware first
                        if (!is_null($routesConfig['middleware'][$pos])) {
                            $middleware = $routesConfig['middleware'][$pos];
                            if (is_subclass_of($middleware, MiddlewareInterface::class)) {
                                $middlewareObj = new $middleware;
                                $middlewareObj->handle();
                            } else {
                                throw new Exception("Invalid middleware class: $middleware");
                            }
                        }

                        if (!is_object($routesConfig['callbacks'][$pos])) {
                            // Grab all parts based on a / separator
                            $parts = explode('/', $routesConfig['callbacks'][$pos]);

                            // Collect the last index of the array
                            $last = end($parts);

                            // Grab the controller name and method call
                            $segments = explode('@', $last);

                            // Instanitate controller
                            $controller = new $segments[0]();

                            // Fix multi parameters
                            if (!method_exists($controller, $segments[1])) {
                                throw new Exception('controller and action not found.');
                            } else {
                                call_user_func_array(array($controller, $segments[1]), $matched);
                            }

                            if ($routesConfig['halts']) return;
                        } else {
                            call_user_func_array($routesConfig['callbacks'][$pos], $matched);

                            if ($routesConfig['halts']) return;
                        }
                    }
                }
                $pos++;
            }
        }

        FileLogger::info('User sends API request: [' . $method . '] ' . $uri);

        // Run the error callback if the route was not found
        if ($found_route == false) {
            FileLogger::warning('invalid request url: [' . $method . '] ' . $uri);
            if (!$routesConfig['error_callback']) {
                $routesConfig['error_callback'] = function () {
                    header($_SERVER['SERVER_PROTOCOL'] . " 404 Not Found");
                    throw new Exception('The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found on this server.');
                };
            } else {
                if (is_string($routesConfig['error_callback'])) {
                    self::get($_SERVER['REQUEST_URI'], $routesConfig['error_callback']);
                    $routesConfig['error_callback'] = null;
                    self::dispatch();
                    return;
                }
            }
            call_user_func($routesConfig['error_callback']);
        }
    }
}
