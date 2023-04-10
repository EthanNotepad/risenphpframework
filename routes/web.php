<?php

use libs\Core\Router;

// Support for custom errors
// Router::error(function () {
//     echo '404';
// });

// If set to false, 
// it will be executed when multiple urls are matched
// Router::haltOnMatch(true);

// homepage
Router::get('/', function () {
    echo 'Hello Risen!';
});

Router::map(array('get', 'post', 'put'), '/', function () {
    echo 'This is a post/update request';
}, \app\Middleware\ExampleMiddleware::class);

/**
 * --------------------------------------------------------------------------------
 * Please add more custom route files here
 * --------------------------------------------------------------------------------
 */
include_once PROJECT_ROOT_PATH . "/routes/api.php";

// Api Homepage
Router::any('/api', 'app\Controller\ApiController@index', \app\Middleware\ExampleMiddleware::class);

// For Testing Functions
Router::any('/tests', 'app\Tests\Test@index');

Router::dispatch();
