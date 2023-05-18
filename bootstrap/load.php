<?php

namespace bootstrap;

use libs\Core\Router;

class Load
{
    public static function loadConfigCache()
    {
        createFolder(PROJECT_CACHE_PATH);
        $cacheFileConfig = PROJECT_CACHE_PATH . '/config';
        if (file_exists($cacheFileConfig) && !filesize($cacheFileConfig)) {
            // If the cache file exists but is empty, remove it to force a rebuild
            unlink($cacheFileConfig);
        }
        if (file_exists($cacheFileConfig)) {
            // If the cache file exists and is not empty, read and return its contents
            return json_decode(file_get_contents($cacheFileConfig), true);
        } else {
            // Otherwise, read all PHP files from the config directory and write them to the cache
            $config = self::loadConfigFiles();
            file_put_contents($cacheFileConfig, json_encode($config));
            return $config;
        }
    }

    public static function loadRoutesCache()
    {
        createFolder(PROJECT_CACHE_PATH);
        $cacheRoutesConfig = PROJECT_CACHE_PATH . '/routes';
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
            // FIXME After the route is cached, the closure function is not supported for the current framework version
            // @zh-cn: 因为闭包函数不支持序列化，因此目前不支持调用的类为闭包函数的情况
            foreach ($staticVars['callbacks'] as $key => $value) {
                if (is_object($value)) {
                    throw new \Exception('After the routing cache is enabled, the closure function is not supported for the this framework version');
                }
            }
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
