<?php

use libs\Core\Router;

// Api Homepage
Router::any('/api', 'app\Controller\ApiController@index');

// For Testing Functions
Router::any('/tests', 'app\Tests\Test@index');
