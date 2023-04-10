<?php

/**
 * --------------------------------------------------------------------------------
 * Database connection configuration
 * --------------------------------------------------------------------------------
 */

return [
    'mysql' => [
        'host' => '127.0.0.1',
        'port' => '8889',
        'username' => 'root',
        'password' => 'root',
        'dbname' => 'mydb',
        'dbcharset' => 'utf8'
    ],
    'mariadb' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'username' => 'root',
        'password' => '123456',
        'dbname' => 'mydb',
        'dbcharset' => 'utf8'
    ],
    'redis' => [
        'host' => '127.0.0.1',
        'port' => '6379',
        'password' => '123456',
        'dbindex' => 0
    ]
];
