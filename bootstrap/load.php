<?php

namespace bootstrap;

use libs\Core\Router;

class load
{
    public static function loadConfigCache()
    {
        createFolder(PROJECT_ROOT_PATH . '/storage/framework/cache');
        $cacheFileConfig = PROJECT_ROOT_PATH . '/storage/framework/cache/config';
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

    public static function loadRoutesCache()
    {
        createFolder(PROJECT_ROOT_PATH . '/storage/framework/cache');
        $cacheRoutesConfig = PROJECT_ROOT_PATH . '/storage/framework/cache/routes';
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
            // 请修复: 路由缓存后，回调函数变成了对象，目前临时解决方案是将对象转换成预设的字符串
            // foreach ($staticVars['callbacks'] as $key => $value) {
            //     if (is_object($value)) {
            //         $staticVars['callbacks'][$key] = 'app\Controller\Index@index';
            //     }
            // }
            file_put_contents($cacheRoutesConfig, json_encode($staticVars));
            return $staticVars;
        }
    }

    public static function loadConfigFiles()
    {
        $config = [];
        foreach (glob(PROJECT_ROOT_PATH . '/config/*.php') as $file) {
            $key = str_replace('.php', '', basename($file));
            $config[$key] = require $file;
        }
        return $config;
    }

    public static function loadRoutesFiles()
    {
        foreach (glob(PROJECT_ROOT_PATH . '/routes/*.php') as $file) {
            require_once($file);
        }
    }
}
