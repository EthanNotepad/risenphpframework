<?php

use libs\Core\Router;

// Api Homepage
Router::any('/api', 'app\Controller\ApiController@index');
// ->middleware(YourMiddleware::class);

// For Testing Functions
Router::any('/tests', 'app\Tests\Test@index');
