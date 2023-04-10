<?php

namespace app\Http\Middleware;

use Closure;

class ExampleMiddleware
{
    public function handle($request, Closure $next)
    {
        // Perform action before routing
        $response = $next($request);
        // Perform action after routing
        return $response;
    }
}
