<?php

namespace libs\Db;

use libs\Core\Config;
use Redis;
use RedisException;

class RedisDB implements DbInterface
{
    protected static $dbConfig;
    private static $instance;
    private static $redis_db;

    private function __construct()
    {
        self::$instance = new Redis();
        try {
            self::$instance->connect(self::$dbConfig['host'], self::$dbConfig['port']);
            self::$instance->auth(self::$dbConfig['password']);
            self::$instance->select(self::$dbConfig['database']);
        } catch (RedisException $e) {
            echo 'Redis Error: ' . $e->getMessage();
            exit();
        }
    }

    public static function link($redis_db = 'default')
    {
        self::$redis_db = $redis_db;
        return self::getInstance();
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$dbConfig = Config::get('database.redis.' .  self::$redis_db);
            new self;
        }
        return self::$instance;
    }
}
