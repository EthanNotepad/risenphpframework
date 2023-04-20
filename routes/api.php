<?php

use libs\Core\Router;

// Api Homepage
Router::any('/api', 'app\Controller\Api\ApiIndexController@index', \app\Middleware\ExampleMiddleware::class);
