<?php

namespace app\Middleware;

use libs\Core\Middleware\MiddlewareInterface;

class ExampleMiddleware implements MiddlewareInterface
{
    public function handle()
    {
        echo "passed middleware\n";
    }
}
