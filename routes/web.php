<?php

use libs\Core\Router;

// When multiple routes are matched, only the first execution will be executed
Router::haltOnMatch();

// homepage
Router::get('/', 'app\Controller\Index@index');
Router::get('/hello', 'app\Controller\Index@hello');
Router::get('/hello/:all', 'app\Controller\Index@hello');

// For Testing Functions
Router::any('/tests', 'Tests\Test@index');
