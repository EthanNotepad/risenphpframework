<?php

use App\Lib\Router;

// Api Homepage
// For Testing Functions
Router::any('/api', 'App\Controller\ApiController@index');

Router::any('/tests', 'App\Tests\Test@index');
