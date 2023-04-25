<?php

namespace libs\Core;

class Config
{
    private static $instance = null;

    private function __construct()
    {
    }

    private function __clone()
    {
        // avoid clone
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public static function get($key = '')
    {
        $keyArray = explode('.', $key);
        global $_CONFIG;
        if (!is_null($_CONFIG)) {
            $config = $_CONFIG;
            foreach ($keyArray as $item) {
                $config = $config[$item] ?? '';
            }
            return $config;
        }
        return '';
    }
}
