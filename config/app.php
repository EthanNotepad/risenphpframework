<?php

/**
 * --------------------------------------------------------------------------------
 * This configuration mainly stores parameters related to environment configuration
 * --------------------------------------------------------------------------------
 */

return [
    /**
     * Whether to open php debugging error message
     * 是否打开php调试错误信息，默认开启
     */
    'displayErrors' => true,

    /**
     * whether to use the environment variable
     * 是否使用本地环境变量，默认开启
     */
    'useEnv' => true,

    /**
     * Whether to customize the time zone, it is not enabled by default
     * 是否自定义时区，默认不打开
     */
    // Whether to customize the time zone
    'isConfigTimeZone' => false,
    // custom time zone here like “PRC” or “Asia/Shanghai”
    'defaultTimeZone' => 'PRC',
];
