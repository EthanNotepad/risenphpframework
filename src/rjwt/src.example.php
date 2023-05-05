<?php

/**
 * ------------------------------------------------------------
 * Explanation of configuration items
 * ------------------------------------------------------------
 * default_alg: This key specifies the default algorithm to use for encoding and decoding JWTs. 
 *     In this case, the default algorithm is HS256, which refers to the HMAC-SHA256 algorithm for hashing the JWT signature.
 * blacklist: This key contains a list of token identifiers that should be invalidated or "blacklisted". 
 *     token identifier is typically a unique identifier that is included in the JWT payload or header. 
 *     When a token is blacklisted, it is considered invalid and cannot be used for authentication or authorization.
 * secret_key: This key specifies the secret key that is used to sign and verify JWTs. 
 *     The secret key should be kept confidential and should only be known to the server and trusted parties. 
 * expire_time: This key specifies the expiration time for JWTs. 
 *     The expiration time is expressed in seconds and is used to limit the lifetime of a token. 
 *     After the expiration time has passed, the token is considered invalid and cannot be used for authentication or authorization.
 * refresh_switch: This key specifies whether token refresh is enabled or not. 
 *     Token refresh is a mechanism for generating new JWTs that have a new expiration time and a new signature, 
 *     while preserving the original payload. This is useful for extending the lifetime of a token without requiring the user to reauthenticate.
 * refresh_key: 
 *     This key specifies the secret key that is used to sign and verify refresh tokens. 
 *     Refresh tokens are similar to regular JWTs, but they have a longer expiration time and are used exclusively for generating new JWTs.
 * refresh_expire_time: 
 *     This key specifies the expiration time for refresh tokens. Refresh tokens have a longer expiration time than regular JWTs, 
 *     since they are used to generate new JWTs. The expiration time for refresh tokens should be long enough to allow for frequent use, 
 *     but short enough to limit the potential risk of unauthorized access.
 * ------------------------------------------------------------
 */
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
