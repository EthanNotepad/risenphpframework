<?php

use bootstrap\App;

define('PROJECT_ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);

require PROJECT_ROOT_PATH . '/vendor/autoload.php';

// can use this, it's the same as above but can independently use without composer
// require PROJECT_ROOT_PATH . '/bootstrap/autoload.php';

App::run();
