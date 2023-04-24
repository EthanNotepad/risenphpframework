<?php

namespace bootstrap;

use libs\Core\CoreError;
use libs\Core\Router;

class App
{
    public static function run()
    {
        // self::check();
        self::init();
        self::loadConfig();
        self::runAction();
    }

    // public static function check()
    // {
    //     $msg = '';
    //     if (!file_exists(PROJECT_ROOT_PATH . "/routes/web.php")) {
    //         $msg .= 'The /routes/web.php file does not exist.<br>';
    //     }
    //     if ($msg) {
    //         die("<h1 style='margin:20px;color:#535353;font:24px/1.2 Helvetica, Arial'>
    //             <span style='font-size:150px;'>:(</span><br/>{$msg}</h1>");
    //     }
    // }

    public static function init()
    {
        self::appFiles();
        self::appEnv();
        self::appTimeZone();
        self::appDisplayErrors();
    }

    public static function appFiles()
    {
        $baseDir = PROJECT_ROOT_PATH . '/bootstrap/App';
        $pattern = $baseDir . '/*.php';
        foreach (glob($pattern) as $file) {
            require_once $file;
        }
    }

    public static function appEnv()
    {
        if (config('isUseEnv')) {
            $envFile = PROJECT_ROOT_PATH . '.env';
            if (file_exists($envFile)) {
                $envLines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($envLines as $envLine) {
                    if (preg_match('/^\s*(\w+)\s*=\s*(.*)$/', $envLine, $matches)) {
                        list(, $envName, $envValue) = $matches;
                        putenv("$envName=$envValue");
                    }
                }
            }
        }
    }

    public static function appTimeZone()
    {
        if (config('isConfigTimeZone')) {
            $defaultTimeZone = config('defaultTimeZone');
            if (!empty($defaultTimeZone)) {
                // date_default_timezone_set("PRC");
                ini_set('date.timezone', $defaultTimeZone);
            }
        }
    }

    public static function appDisplayErrors()
    {
        (new CoreError(config('displayErrors')))->bootstrap();
    }

    public static function loadConfig()
    {
        global $_CONFIG;
        global $_CONFIG_ROUTE;
        $isCacheConfig = config('app.isCacheConfig');
        if ($isCacheConfig) {
            $_CONFIG = load::loadConfigCache();
            $_CONFIG_ROUTE = load::loadRoutesCache();
        } else {
            $_CONFIG = load::loadConfigFiles();
            $_CONFIG_ROUTE = load::loadRoutesFiles();
            self::deleteCache();
        }
    }

    public static function deleteCache()
    {
        $cacheDir = PROJECT_ROOT_PATH . '/storage/framework/cache';
        if (is_dir($cacheDir)) {
            $files = scandir($cacheDir);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    unlink($cacheDir . '/' . $file);
                }
            }
        }
    }

    public static function runAction()
    {
        Router::dispatch();
    }
}
