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
    function config($key, $default = null)
    {
        $pathAndValue = explode('.', $key);
        if (count($pathAndValue) == 1) {
            // If the configuration file is not specified, the default is app.php
            $config_file = 'app';
            $params = $pathAndValue;
        } else {
            $config_file = $pathAndValue[0];
            $params  = array_slice($pathAndValue, 1);
        }
        global $_CONFIG;
        if (is_null($_CONFIG)) {
            $config_path = PROJECT_ROOT_PATH . '/config/' . $config_file . '.php';
            if (!file_exists($config_path)) {
                return $default;
            }
            $config = include($config_path);
        } else {
            if (!isset($_CONFIG[$config_file])) {
                return $default;
            }
            $config = $_CONFIG[$config_file];
        }
        foreach ($params as $param) {
            if (isset($config[$param])) {
                $config = $config[$param];
            } else {
                return $default;
            }
        }
        return $config;
    }
}

/**
 * --------------------------------------------------------------------------------
 * Read the defined array data in the config folder
 * --------------------------------------------------------------------------------
 */
if (!function_exists('env')) {
    function env($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return value($default);
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        return $value;
    }
}

// Return the default value of the given value.
if (!function_exists('value')) {
    function value($value, ...$args)
    {
        return $value instanceof Closure ? $value(...$args) : $value;
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
