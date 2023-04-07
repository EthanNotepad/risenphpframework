<?php

namespace libs\Db;

use Redis;
use RedisException;

class RedisDB
{
    private static $redis;

    private function __construct()
    {
        // FIXME, 连接数据还需要修改
        self::$redis = new Redis();

        try {
            self::$redis->connect('localhost', 6379);
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
