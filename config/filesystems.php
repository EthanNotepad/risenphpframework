<?php

/**
 * --------------------------------------------------------------------------------
 * This configuration mainly stores parameters related to filesystem configuration
 * --------------------------------------------------------------------------------
 */

return [
    'default' => env('FILESYSTEM_DISK', 'public'),
    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => PROJECT_ROOT_PATH,
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => PROJECT_ROOT_PATH . '/public',
            'url' => env('APP_URL', 'http://localhost'),
            'visibility' => 'public',
            'throw' => false,
        ],

    ],
];
