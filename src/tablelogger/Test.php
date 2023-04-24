<?php

/**
 * ------------------------------------------------------------
 * How to use, you can refer to the following test
 * ------------------------------------------------------------
 * 1. Add route:
 *  Router::any('src/tablelogger/test/index', 'src\tablelogger\Test@index');
 *  Router::any('src/tablelogger/test/install', 'src\tablelogger\Test@install');
 * 2. start install:
 *  open url: /src/tablelogger/test/install, install logs table
 * 3. Access URI: 
 *  /src/tablelogger/test/index
 */

namespace src\tablelogger;

use src\tablelogger\Core\Logger;

class Test
{
    public function index()
    {
        Logger::sensitive(2);
        echo 'logger a new log to logs table success';
    }

    public function install()
    {
        $dbname = config('database.connections')[config('database.default')]['dbname'];
        $ishavelogssql = "SHOW TABLES IN $dbname WHERE Tables_in_$dbname = 'logs'";
        $result = \libs\Db\DB::link()->query($ishavelogssql);
        if (count($result)) {
            echo 'logs table already exists';
        } else {
            // If there is no logs table, a logs table will be created first
            $sql = "CREATE TABLE `logs` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `user_id` int(11) DEFAULT NULL,
                    `action` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                    `request_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                    `status` tinyint(2) DEFAULT NULL COMMENT '1 success, 2 failure',
                    `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                    `log_type` tinyint(2) DEFAULT NULL COMMENT '1 web, 2 system',
                    `ip_address` char(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                    `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
                  ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            \libs\Db\DB::link()->query($sql);
        }
        echo 'install success';
    }
}
