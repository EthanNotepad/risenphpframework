<?php

/**
 * --------------------------------------------------------------------------------
 * Database connection configuration
 * --------------------------------------------------------------------------------
 */

return [
    'default' => env('TYPE', 'mariadb'),
    'connections' => [
        'mysql' => [
            'host' => env('HOSTNAME', '127.0.0.1'),
            'port' => env('HOSTPORT', '3306'),
            'username' => env('USERNAME', 'root'),
            'password' => env('PASSWORD', '123456'),
            'dbname' => env('DATABASE', 'mydb'),
            'dbcharset' => env('CHARSET', 'utf8'),
        ],
        'mariadb' => [
            'host' => env('HOSTNAME', '127.0.0.1'),
            'port' => env('HOSTPORT', '3306'),
            'username' => env('USERNAME', 'root'),
            'password' => env('PASSWORD', '123456'),
            'dbname' => env('DATABASE', 'mydb'),
            'dbcharset' => env('CHARSET', 'utf8'),
        ],
    ],
    'redis' => [
        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'port' => env('REDIS_PORT', '6379'),
            'password' => env('REDIS_PASSWORD', '123456'),
            'database' => env('REDIS_DB', '0')
        ],
        'cache' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'port' => env('REDIS_PORT', '6379'),
            'password' => env('REDIS_PASSWORD', '1234562'),
            'database' => env('REDIS_DB_CACHE', '1')
        ],
    ]
];
