<?php

namespace libs\Db;

use Redis;
use RedisException;

class RedisDB implements CoreDB
{
    protected static $dbConfig;

    private static $redis;

    private function __construct()
    {
        global $_CONFIG;
        self::$dbConfig = $_CONFIG['database']['redis']['default'];
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
}
