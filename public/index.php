<?php

use bootstrap\App;

defined('PROJECT_ROOT_PATH') || define("PROJECT_ROOT_PATH", __DIR__ . "/../");

// require __DIR__ . '/vendor/autoload.php';
require PROJECT_ROOT_PATH . '/src/autoload.php';

App::run();
