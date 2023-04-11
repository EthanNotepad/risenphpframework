<?php

use libs\Core\Router;

// Support for custom errors
// Router::error(function () {
//     echo '404';
// });

// If set to false, 
// it will be executed when multiple urls are matched
Router::haltOnMatch(true);

// homepage
Router::get('/', function () {
    global $_CONFIG;
    $appName = $_CONFIG['app']['appName'];
    echo "<h1 style='margin:20px;color:#535353;font:24px/1.2 Helvetica, Arial'>
    <span style='font-size:150px;'>:)</span><br/>Hello, $appName!</h1>";
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
