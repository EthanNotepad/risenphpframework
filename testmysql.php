<?php

if (file_exists("./config/database.php")) {
    require_once './config/database.php';
} else {
    die("<h1 style='margin:20px;color:#535353;font:24px/1.2 Helvetica, Arial'>
    <span style='font-size:150px;'>:(</span><br/>Database configuration file not found</h1>");
}

$config = sprintf("mysql:host=%s;dbname=%s;chartset=%s;port=%s", DB_HOST, DB_NAME, DB_CHARSET, DB_PORT);
try {
    $pdo = new PDO($config, DB_USER, DB_PASS);
    echo "Connection successful!";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
