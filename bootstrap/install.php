<?php

$msg = '';
// if (version_compare(phpversion(), '7.1.0', '<')) {
//     $msg .= 'Please upgrade to PHP7.1 above<br/>';
// }
// if (!function_exists('openssl_encrypt')) {
//     $msg .= 'Extension php_openssl is not open<br/>';
// }
if (!file_exists(PROJECT_ROOT_PATH . "/src/autoload.php")) {
    $msg .= 'The /src/autoload.php file does not exist.<br>';
}
if (!file_exists(PROJECT_ROOT_PATH . "/routes/web.php")) {
    $msg .= 'The /routes/web.php file does not exist.<br>';
}
if ($msg) {
    die("<h1 style='margin:20px;color:#535353;font:24px/1.2 Helvetica, Arial'>
        <span style='font-size:150px;'>:(</span><br/>{$msg}</h1>");
}

if (!file_exists(PROJECT_ROOT_PATH . "/config/database.php")) {
    $configDatabase = sprintf(
        "<?php
/**
 * --------------------------------------------------------------------------------
 * MySQL database connection configuration
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
    ]
];",
        PHP_EOL // Line break
    );

    file_put_contents(PROJECT_ROOT_PATH . "/config/database.php", $configDatabase, FILE_APPEND);
}
