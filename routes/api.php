<?php

use libs\Core\Router;

// Api Homepage
Router::any('/api', 'app\Controller\Api\IndexController@index', \app\Middleware\ExampleMiddleware::class);
