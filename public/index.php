<?php

use bootstrap\App;

define('PROJECT_ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('PROJECT_CACHE_PATH', PROJECT_ROOT_PATH . '/storage/framework/cache');

require PROJECT_ROOT_PATH . '/vendor/autoload.php';

// can use this, it's the same as above but can independently use without composer
// require PROJECT_ROOT_PATH . '/bootstrap/autoload.php';

App::run();
