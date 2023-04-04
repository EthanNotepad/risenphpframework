<?php

/**
 * --------------------------------------------------------------------------------
 * automatic loading of classes
 * --------------------------------------------------------------------------------
 */
spl_autoload_register(function ($class) {
    $file = str_replace('\\', '/', $class) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

/**
 * --------------------------------------------------------------------------------
 * register helper
 * --------------------------------------------------------------------------------
 */
include_once 'Helper.php';

/**
 * --------------------------------------------------------------------------------
 * load timezone configuration
 * --------------------------------------------------------------------------------
 */
if (config('app.isConfigTimeZone', false) == true) {
    $defaultTimeZone = config('app.defaultTimeZone', '');
    if (!empty($defaultTimeZone)) {
        // date_default_timezone_set("PRC");
        ini_set('date.timezone', $defaultTimeZone);
    }
}

/**
 * --------------------------------------------------------------------------------
 * Load Debugger configuration
 * --------------------------------------------------------------------------------
 */
include_once 'Debugger.php';
