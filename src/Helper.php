<?php

/**
 * --------------------------------------------------------------------------------
 * Dump [and Die ].
 * --------------------------------------------------------------------------------
 */
if (!function_exists('dd')) {
    function dd()
    {
        $args = func_get_args();
        call_user_func_array('var_dump', $args);
        die(1);
    }
}

if (!function_exists('dump')) {
    function dump()
    {
        $args = func_get_args();
        call_user_func_array('var_dump', $args);
    }
}

/**
 * --------------------------------------------------------------------------------
 * Read the defined array data in the config folder
 * --------------------------------------------------------------------------------
 */
if (!function_exists('config')) {
    function config($key, $default_val = null)
    {
        $pathAndValue = explode('.', $key, 2);
        if (count($pathAndValue) == 1) {
            // If the configuration file is not specified, the default is app.php
            $config_file = 'app';
            $param = $pathAndValue[0];
        } else {
            $config_file = $pathAndValue[0];
            $param  = $pathAndValue[1];
        }
        $config_path = PROJECT_ROOT_PATH . '/config/' . $config_file . '.php';
        if (file_exists($config_path)) {
            $config = include($config_path);
            return $config[$param] ?? $default_val;
        } else {
            return $default_val;
        }
    }
}

/**
 * --------------------------------------------------------------------------------
 * Read the defined array data in the .env file
 * if the .env file does not exist, return the default value
 * --------------------------------------------------------------------------------
 */
if (!function_exists('env')) {
    function env($param, $default_val = null)
    {
        $config_path = PROJECT_ROOT_PATH . '.env';
        if (file_exists($config_path)) {
            $config = include($config_path);
            return $config[$param] ?? $default_val;
        } else {
            return $default_val;
        }
    }
}

/**
 * --------------------------------------------------------------------------------
 * generate a unique number
 * --------------------------------------------------------------------------------
 */
if (!function_exists('generateNonUniqueNumber')) {
    function generateNonUniqueNumber($digits)
    {
        $number = '';
        for ($i = 0; $i < $digits; $i++) {
            $number .= mt_rand(0, 9); // append a random digit to the number
        }
        return $number;
    }
}
