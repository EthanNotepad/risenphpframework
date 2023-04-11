<?php

namespace bootstrap;

use libs\Core\Router;

class App
{
    public static function run()
    {
        Install::checkInstall();
        self::isCacheConfig();
        self::loadConfig();
        self::init();
        include_once PROJECT_ROOT_PATH . "/routes/web.php";
        self::runAction();
    }

    public static function init()
    {
        global $_CONFIG;
        self::appTimeZone($_CONFIG['app']);
        self::appDisplayErrors($_CONFIG['app']);
    }

    public static function appTimeZone($config)
    {
        if ($config['isConfigTimeZone']) {
            $defaultTimeZone = $config['defaultTimeZone'];
            if (!empty($defaultTimeZone)) {
                // date_default_timezone_set("PRC");
                ini_set('date.timezone', $defaultTimeZone);
            }
        }
    }

    public static function appDisplayErrors($config)
    {
        if ($config['displayErrors']) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        } else {
            error_reporting(0);
            ini_set('display_errors', 0);
        }
    }

    public static function isCacheConfig()
    {
        $isCacheConfig = config('app.isCacheConfig');
        if (!$isCacheConfig) {
            // if the config is not cached, delete the cache file every time
            $cacheDir = PROJECT_ROOT_PATH . '/runtime/cache';
            $files = glob($cacheDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }

    public static function loadConfig()
    {
        global $_CONFIG;
        global $_CONFIG_ROUTE;
        $_CONFIG = self::loadConfigFiles();
        $isCacheConfig = config('app.isCacheConfig');
        if ($isCacheConfig) {
            $_CONFIG_ROUTE = self::loadRoutesConfig();
        }
    }

    public static function loadConfigFiles()
    {
        $cacheFileConfig = PROJECT_ROOT_PATH . '/runtime/cache/config';
        if (file_exists($cacheFileConfig) && !filesize($cacheFileConfig)) {
            // If the cache file exists but is empty, remove it to force a rebuild
            unlink($cacheFileConfig);
        }
        if (file_exists($cacheFileConfig)) {
            // If the cache file exists and is not empty, read and return its contents
            return json_decode(file_get_contents($cacheFileConfig), true);
        } else {
            // Otherwise, read all PHP files from the config directory and write them to the cache
            $config = [];
            foreach (glob(PROJECT_ROOT_PATH . '/config/*.php') as $file) {
                $key = str_replace('.php', '', basename($file));
                $config[$key] = require $file;
            }
            file_put_contents($cacheFileConfig, json_encode($config));
            return $config;
        }
    }

    public static function loadRoutesConfig()
    {
        $cacheRoutesConfig = PROJECT_ROOT_PATH . '/runtime/cache/routes';
        if (file_exists($cacheRoutesConfig) && !filesize($cacheRoutesConfig)) {
            // If the cache file exists but is empty, remove it to force a rebuild
            unlink($cacheRoutesConfig);
        }
        if (file_exists($cacheRoutesConfig)) {
            // If the cache file exists and is not empty, read and return its contents
            return json_decode(file_get_contents($cacheRoutesConfig), true);
        } else {
            // Otherwise, read all PHP files from the routes directory and write them to the cache
            foreach (glob(PROJECT_ROOT_PATH . '/routes/*.php') as $file) {
                require_once($file);
            }
            $reflectionClass = new \ReflectionClass(Router::class);
            $staticVars = array_filter($reflectionClass->getStaticProperties(), function ($key) use ($reflectionClass) {
                $reflectionProperty = $reflectionClass->getProperty($key);
                return $reflectionProperty->isPublic() && $reflectionProperty->isStatic();
            }, ARRAY_FILTER_USE_KEY);
            // FIXME After the route is cached, the callback function becomes an object
            // 修复: 路由缓存后，回调函数变成了对象
            foreach ($staticVars['callbacks'] as $key => $value) {
                if (is_object($value)) {
                    $staticVars['callbacks'][$key] = 'app\Controller\ApiController@index';
                }
            }
            file_put_contents($cacheRoutesConfig, json_encode($staticVars));
            return $staticVars;
        }
    }

    public static function runAction()
    {
        $isCacheConfig = config('app.isCacheConfig');
        if ($isCacheConfig) {
            Router::dispatch4cache();
        } else {
            Router::dispatch();
        }
    }
}
