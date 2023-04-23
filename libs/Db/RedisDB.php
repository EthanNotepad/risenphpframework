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
        // if (config('isUseEnv')) {
        //     $this->initByEnv();
        // } else {
        //     $this->initByConfig();
        // }
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

    private function initByEnv()
    {
        self::$dbConfig = [
            'host' => env('REDIS_HOST', ''),
            'port' => env('REDIS_PORT'),
            'password' => env('REDIS_PASSWORD'),
            'dbindex' => env('REDIS_DBINDEX'),
        ];
    }

    public static function link()
    {
        if (self::$redis == null) {
            new self;
        }
        return self::$redis;
    }
}
