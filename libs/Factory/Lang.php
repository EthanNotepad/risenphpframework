<?php

namespace libs\Helper;

use libs\Core\Config;

class Lang
{
    public static function get($translation)
    {
        if (Config::get('app.isLocale')) {
            global $_CONFIG_LANG;
            $language = self::getLang();
            if (!isset($_CONFIG_LANG[$language])) {
                $_CONFIG_LANG[$language] = self::load($language);
            }
            if (isset($_CONFIG_LANG[$language][$translation])) {
                return $_CONFIG_LANG[$language][$translation];
            }
        }
        return $translation;
    }

    public static function set($translation, $value)
    {
        global $_CONFIG_LANG;
        $language = self::getLang();
        if (!isset($_CONFIG_LANG[$language])) {
            $_CONFIG_LANG[$language] = self::load($language);
        }
        $_CONFIG_LANG[$language][$translation] = $value;
    }

    public static function load($language)
    {
        global $_CONFIG_LANG;
        if (!isset($_CONFIG_LANG[$language])) {
            if (Config::get('app.isCacheConfig') == true) {
                $_CONFIG_LANG[$language] = self::loadLangCache();
            } else {
                $_CONFIG_LANG[$language] = self::loadLangFile();
            }
        }
        return $_CONFIG_LANG[$language];
    }

    public static function getLang()
    {
        $language = substr(Config::get('app.defaultLang'), 0, 2);
        return $language;
    }

    public static function loadLangCache()
    {
        $language = self::getLang();
        createFolder(PROJECT_CACHE_PATH);
        $cacheFileConfig = PROJECT_CACHE_PATH . '/lang' . '_' . $language;
        if (file_exists($cacheFileConfig) && !filesize($cacheFileConfig)) {
            // If the cache file exists but is empty, remove it to force a rebuild
            unlink($cacheFileConfig);
        }
        if (file_exists($cacheFileConfig)) {
            // If the cache file exists and is not empty, read and return its contents
            return json_decode(file_get_contents($cacheFileConfig), true);
        } else {
            // Otherwise, read all PHP files from the config directory and write them to the cache
            $config = self::loadLangFile();
            file_put_contents($cacheFileConfig, json_encode($config));
            return $config;
        }
    }

    public static function loadLangFile()
    {
        $language = self::getLang();
        $config = [];
        foreach (glob(PROJECT_ROOT_PATH . '/lang/' . $language . '/*.php') as $filename) {
            $config = array_merge($config, include $filename);
        }
        return $config;
    }
}
