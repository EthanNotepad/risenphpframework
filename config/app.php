<?php

/**
 * --------------------------------------------------------------------------------
 * This configuration mainly stores parameters related to environment configuration
 * isUseEnv:
 *     Whether to use the environment variable
 *     if true, the environment variable will be used to configure the database
 *     note: If no .env file, the config/database.php file will be used
 * isCacheConfig: 
 *     Whether to cache the configuration file
 *     if true, the configuration file will be cached in the runtime/cache/config file
 *     note: After the cache is enabled, the closure function cannot be used in the route, 
 *         and it will not be cached correctly
 * --------------------------------------------------------------------------------
 */

return [
    'displayErrors' => env('APP_DEBUG', true),
    'defaultErrorMessage' => 'Error, please contact the administrator!',
    'isUseEnv' => true,
    'isConfigTimeZone' => false,
    'defaultTimeZone' => 'PRC',
    'isCacheConfig' => false,
];
