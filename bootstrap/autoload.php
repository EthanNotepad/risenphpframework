<?php

/**
 * --------------------------------------------------------------------------------
 * Important!! automatic loading of classes
 * --------------------------------------------------------------------------------
 */
spl_autoload_register(function ($class) {
    $file = PROJECT_ROOT_PATH . str_replace('\\', '/', $class) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});
