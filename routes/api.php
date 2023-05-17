<?php

use libs\Core\Router;

// Api Homepage (contain middleware example)
Router::any('/api', 'app\Controller\Api\ApiindexController@index', \app\Middleware\ExampleMiddleware::class);
