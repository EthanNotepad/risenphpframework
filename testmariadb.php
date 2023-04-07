<?php

if (file_exists("./config/database.php")) {
    $configMysql = require_once './config/database.php';
} else {
    die("<h1 style='margin:20px;color:#535353;font:24px/1.2 Helvetica, Arial'>
    <span style='font-size:150px;'>:(</span><br/>Database configuration file not found</h1>");
}

define('DB_HOST', $configMysql['mariadb']['host']);
define('DB_NAME', $configMysql['mariadb']['dbname']);
define('DB_USER', $configMysql['mariadb']['username']);
define('DB_PASS', $configMysql['mariadb']['password']);

class Database
{
    private $host = DB_HOST;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $dbname = DB_NAME;
    private $pdo;

    public function __construct()
    {
        try {
            $dsn = "mysql:host=$this->host;dbname=$this->dbname";
            $this->pdo = new PDO($dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo 'Connection successful!';
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            exit;
        }
    }

    public function getPdo()
    {
        return $this->pdo;
    }
}

$database = new Database();
$pdo = $database->getPdo();
