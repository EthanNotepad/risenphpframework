<?php

namespace libs\Core\Middleware;

use libs\Core\Config;

class HandleCors implements MiddlewareInterface
{
    public function handle()
    {
        $corsConfig = Config::get('cors');
        if (empty($corsConfig) || !isset($corsConfig['enable']) || !$corsConfig['enable']) {
            return;
        }
        $allowedOrigins = $corsConfig['allowed_origins'];
        $allowedMethods = $corsConfig['allowed_methods'];
        $allowedHeaders = $corsConfig['allowed_headers'];
        $exposedHeaders = $corsConfig['exposed_headers'];
        $maxAge = $corsConfig['max_age'];
        $allowCredentials = $corsConfig['allow_credentials'];

        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        if (in_array('*', $allowedOrigins) || in_array($origin, $allowedOrigins)) {
            header("Access-Control-Allow-Origin: $origin");
            header("Access-Control-Allow-Methods: $allowedMethods");
            header("Access-Control-Allow-Headers: $allowedHeaders");
            header("Access-Control-Expose-Headers: $exposedHeaders");
            header("Access-Control-Max-Age: $maxAge");
            header("Access-Control-Allow-Credentials: $allowCredentials");
        }

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }
}
