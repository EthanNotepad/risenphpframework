<?php

if (!function_exists('displayErrors')) {
    $display = config('app.displayErrors', true);
    function displayErrors($display)
    {
        if ($display) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        } else {
            error_reporting(0);
            ini_set('display_errors', 0);
        }
    }
    displayErrors($display);
}
