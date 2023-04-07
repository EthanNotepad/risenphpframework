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
include_once PROJECT_ROOT_PATH . "/routes/api.php";

Router::dispatch();
