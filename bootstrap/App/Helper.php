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
    function dump(...$vars)
    {

        ob_start();
        var_dump(...$vars);

        $output = ob_get_clean();
        $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);


        if (PHP_SAPI == 'cli') {
            $output = PHP_EOL . $output . PHP_EOL;
        } else {
            if (!extension_loaded('xdebug')) {
                $output = htmlspecialchars($output, ENT_SUBSTITUTE);
            }
            $output = '<pre>' . $output . '</pre>';
        }

        echo $output;
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
 * Read the defined array data in the config folder
 * --------------------------------------------------------------------------------
 */
if (!function_exists('env')) {
    function env($key, $default_val = null)
    {
        $result = getenv($key);
        if ($result === false) {
            $result = $default_val;
        }
        return $result;
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

/**
 * --------------------------------------------------------------------------------
 * create a folder
 * --------------------------------------------------------------------------------
 */
if (!function_exists('createFolder')) {
    function createFolder($folderName)
    {
        if (!file_exists($folderName)) {
            mkdir($folderName, 0777, true);
        }
    }
}
