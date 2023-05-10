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
            'prefix' => '',
            'prefix_indexes' => true,
        ],
        'mariadb' => [
            'host' => env('HOSTNAME', '127.0.0.1'),
            'port' => env('HOSTPORT', '3306'),
            'username' => env('USERNAME', 'root'),
            'password' => env('PASSWORD', '123456'),
            'dbname' => env('DATABASE', 'mydb'),
            'dbcharset' => env('CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
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
            'password' => env('REDIS_PASSWORD', '123456'),
            'database' => env('REDIS_DB_CACHE', '1')
        ],
    ]
];
