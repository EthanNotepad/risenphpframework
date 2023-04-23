<?php

return [
    'rjwt' => [
        'default_alg' => 'HS256',
        'blacklist' => [
            'example_blacklist',
        ],
        'secret_key' => 'example_key',
        'expire_time' => 3600,
        'refresh_switch' => false,
        'refresh_key' => 'example_refresh_key',
        'refresh_expire_time' => 86400,
    ],
];
