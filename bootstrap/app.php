<?php

defined('PROJECT_ROOT_PATH') || define("PROJECT_ROOT_PATH", __DIR__ . "/../");

// session_start();

/**
 * --------------------------------------------------------------------------------
 * Requirements
 * --------------------------------------------------------------------------------
 */
include_once PROJECT_ROOT_PATH . '/bootstrap/install.php';

/**
 * --------------------------------------------------------------------------------
 * Register The Auto Loader
 * --------------------------------------------------------------------------------
 */
// require __DIR__ . '/vendor/autoload.php';
include_once PROJECT_ROOT_PATH . '/src/autoload.php';

/**
 * --------------------------------------------------------------------------------
 * Configuration parameter module
 * --------------------------------------------------------------------------------
 */
include_once PROJECT_ROOT_PATH . "/config/database.php";

/**
 * --------------------------------------------------------------------------------
 * Configuration routes module
 * --------------------------------------------------------------------------------
 */
include_once PROJECT_ROOT_PATH . "/routes/web.php";
