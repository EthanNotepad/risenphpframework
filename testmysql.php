<?php

if (file_exists("./config/database.php")) {
    $configMysql = require_once './config/database.php';
} else {
    die("<h1 style='margin:20px;color:#535353;font:24px/1.2 Helvetica, Arial'>
    <span style='font-size:150px;'>:(</span><br/>Database configuration file not found</h1>");
}

define('DB_HOST', $configMysql['mysql']['host']);
define('DB_NAME', $configMysql['mysql']['dbname']);
define('DB_CHARSET', $configMysql['mysql']['dbcharset']);
define('DB_PORT', $configMysql['mysql']['port']);
define('DB_USER', $configMysql['mysql']['username']);
define('DB_PASS', $configMysql['mysql']['password']);

$config = sprintf("mysql:host=%s;dbname=%s;chartset=%s;port=%s", DB_HOST, DB_NAME, DB_CHARSET, DB_PORT);
try {
    $pdo = new PDO($config, DB_USER, DB_PASS);
    echo "Connection successful!";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
