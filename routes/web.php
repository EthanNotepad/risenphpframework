<?php

use libs\Core\Router;

// homepage
Router::any('/', function () {
    echo 'Hello Risen!';
});

/**
 * --------------------------------------------------------------------------------
 * Please add more custom route files here
 * --------------------------------------------------------------------------------
 */
// include_once PROJECT_ROOT_PATH . "/routes/api.php";

// Api Homepage
Router::any('/api', 'app\Controller\ApiController@index')->middleware(YourMiddleware::class);

// For Testing Functions
Router::any('/tests', 'app\Tests\Test@index');

Router::dispatch();
