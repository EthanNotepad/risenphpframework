<?php

namespace libs\Core;

class Config
{
    private static $instance = null;

    private function __construct()
    {
        // avoid new
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

    public static function get($key, $default = null)
    {
        global $_CONFIG;
        $value = $_CONFIG;

        foreach (explode('.', $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }

    public static function set($key, $value)
    {
        global $_CONFIG;

        $segments = explode('.', $key);
        $config = &$_CONFIG;

        foreach ($segments as $segment) {
            if (!isset($config[$segment]) || !is_array($config[$segment])) {
                $config[$segment] = [];
            }

            $config = &$config[$segment];
        }

        $config = $value;

        return true;
    }

    public static function has($key)
    {
        global $_CONFIG;
        $value = $_CONFIG;

        foreach (explode('.', $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return false;
            }

            $value = $value[$segment];
        }

        return true;
    }

    public static function all()
    {
        global $_CONFIG;
        return $_CONFIG;
    }
}
