<?php

use libs\Core\Router;

// When multiple routes are matched, only the first execution will be executed
Router::haltOnMatch();

// homepage
Router::get('/', 'app\Controller\IndexController@index');
Router::get('/hello', 'app\Controller\IndexController@hello');
Router::get('/hello/:all', 'app\Controller\IndexController@hello');

// For Testing Functions
Router::any('/tests', 'tests\Tests@index');
Router::any('/tests/db', 'tests\Tests@db');
