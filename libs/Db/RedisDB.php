<?php

namespace libs\Db;

use Redis;
use RedisException;

class RedisDB
{
    protected static $dbConfig;

    private static $redis;

    private function __construct()
    {
        if (config('useEnv')) {
            self::$dbConfig = env('redis');
            if (empty(self::$dbConfig)) {
                self::$dbConfig = config('database.redis');
            }
        } else {
            self::$dbConfig = config('database.redis');
        }
        self::$redis = new Redis();
        try {
            self::$redis->connect(self::$dbConfig['host'], self::$dbConfig['port']);
            self::$redis->auth(self::$dbConfig['password']);
            self::$redis->select(self::$dbConfig['dbindex']);
        } catch (RedisException $e) {
            echo 'Error: ' . $e->getMessage();
            exit();
        }
    }

    public static function link()
    {
        if (self::$redis == null) {
            new self;
        }
        return self::$redis;
    }

    public function set($key, $value)
    {
        return self::link()->set($key, $value);
    }

    public function get($key)
    {
        return self::link()->get($key);
    }

    public function delete($key)
    {
        return self::link()->delete($key);
    }

    public function keys($pattern)
    {
        return self::link()->keys($pattern);
    }

    public function flushAll()
    {
        return self::link()->flushAll();
    }
}
