<?php

defined('PROJECT_ROOT_PATH') || define("PROJECT_ROOT_PATH", __DIR__ . "/../");

/**
 * --------------------------------------------------------------------------------
 * Register The Auto Loader
 * --------------------------------------------------------------------------------
 */
// require __DIR__ . '/vendor/autoload.php';
include_once PROJECT_ROOT_PATH . '/src/autoload.php';

/**
 * --------------------------------------------------------------------------------
 * Ready For Use
 * --------------------------------------------------------------------------------
 */
require_once PROJECT_ROOT_PATH . '/bootstrap/app.php';
