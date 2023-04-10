<?php

namespace app\Middleware;

use Closure;

class ExampleMiddleware
{
    public function handle()
    {
        echo 'ExampleMiddleware';
        // // Perform action before routing
        // $response = $next($request);
        // // Perform action after routing
        // return $response;
    }
}
