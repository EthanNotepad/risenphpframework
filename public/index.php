<?php

use bootstrap\App;

defined('PROJECT_ROOT_PATH') || define('PROJECT_ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

require PROJECT_ROOT_PATH . '/vendor/autoload.php';
// require PROJECT_ROOT_PATH . '/bootstrap/autoload.php';

App::run();
