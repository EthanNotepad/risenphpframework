<?php

namespace libs\Core\Middleware;

class YourMiddleware implements MiddlewareInterface
{
    public function handle()
    {
        echo 'middleware';
    }
}
