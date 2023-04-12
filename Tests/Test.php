<?php

namespace Tests;

class Test
{
    /**
     * It can be used for testing,
     * Url: /tests
     */
    public function index()
    {
        /**
         * Test the request function
         */
        if (0) {
            $data = (new \libs\Core\Request)->getPath();
            dd($data);
        }

        /**
         * Test the Response function
         */
        if (0) {
            $response = new \libs\Core\Response('Hello, world!', 404, [
                'Content-Type: text/plain'
            ]);
            $response->send();
        }

        /**
         * Test the config function
         */
        if (0) {
            global $_CONFIG;
            $displayErrors = config('app.displayErrors');
            $displayErrorsFromGlobalConfig = $_CONFIG['app'];
            dump($displayErrors, $displayErrorsFromGlobalConfig);
        }

        /**
         * Test the data validator
         */
        if (0) {
            $data = [
                'name' => 'hahh',
                'age' => '12',
                'email' => 'svip2011@qq.com',
            ];

            $rules = [
                'name' => ['required'],
                'age' => ['required', 'numeric', 'min:18'],
                'email' => ['required', 'email'],
            ];

            $messages = [
                'name.required' => 'The name field is required.',
                'age.required' => 'The age field is required.',
                'age.numeric' => 'The age field must be a number.',
                'age.min' => 'The age field must be at least 18.',
                'email.required' => 'The email field is required.',
                'email.email' => 'The email field must be a valid email address.',
            ];
            $validator = new \libs\Core\Validator($data, $rules, $messages);
            $validator->setFields(['name', 'age']);
            $resultValidated = $validator->validate();
            if ($resultValidated !== true) {
                \libs\Core\Message::send(412, $resultValidated, 'Validation failed');
            }
            echo 'moving on';
        }

        /**
         * Test bash64 file upload function
         */
        if (0) {
            $filePath = __DIR__ . '/imageData.base64';
            $imgData = file_get_contents($filePath);
            $uploader = new \app\Tool\UploadFiles();
            $imagePath = $uploader->uploadFilesBase64($imgData, 'public/upload/images/');
            echo $imagePath;
        }

        /**
         * Test log database table functionality
         */
        if (0) {
            $dbname = config('database.mysql.dbname');
            $ishavelogssql = "SHOW TABLES IN $dbname WHERE Tables_in_$dbname = 'logs'";
            $result = \libs\Db\DB::link()->query($ishavelogssql);
            if (count($result)) {
                \libs\Core\Logger::sensitive(1);
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
                \libs\Core\Logger::sensitive(1);
            }
            echo 'moving on';
        }

        /**
         * Test the database query function
         */
        if (0) {
            $id = $_GET['id'] = "1 OR 1=1";     // test sql injection
            $whereSql[] = array('logs.id', '=', $id);
            $getOne = \libs\Db\DB::link()->table('logs')->where('id', '=', 59)->field('id', 'user_id')->getOne();
            // dd($getOne);
            $getAll = \libs\Db\DB::link()->table('logs')->where('id', '=', 1)->field('id', 'user_id')->limit(1, 100)->order("id ASC")->get();
            $getCount = \libs\Db\DB::link()->table('logs')->where('id', '=', 3)->field('id')->count();
            $getLeftJoin = \libs\Db\DB::link()->table('logs')->where($whereSql)->join('logs AS logs2', 'logs2.user_id = logs.id')->get();
            $getFieldNotSafe = \libs\Db\DB::link()->table('logs')->where($whereSql)->join('logs AS logs2', 'logs2.user_id = logs.id')->fieldString('logs.id')->get();
            // $getSqlDd = \libs\Db\DB::link()->table('logs')->where('id', '=', 1)->field('id', 'user_id')->limit(1, 100)->order("id ASC")->dd('get');

            // Data insertion and getting the inserted id
            $insertData = [
                'user_id' => '3',
                'action' => 'login'
            ];
            // \libs\Db\DB::link()->table('logs')->insert($insertData);
            $insertId = \libs\Db\DB::link()->lastId();
            dd($insertId);
        }

        /**
         * Test the http request function
         */
        if (0) {
            $httpRequest =  new \libs\Core\HttpRequest('http://localhost:8888/risen/public/api');
            dd($httpRequest->send());
        }

        /**
         * Test the Redis function
         */
        if (0) {
            $redis = \libs\Db\RedisDB::link();
            $redis->set('mykey', 'shejibiji.com');
            echo $redis->get('mykey');
        }

        /**
         * Test the NoSql factory function
         */
        if (0) {
            $redis = \libs\Factory\NosqlFactory::factory('redis');
            $redis->set('mykey', 'shejibiji.com');
            echo $redis->get('mykey');
        }

        /**
         * Test the LDAP function
         */
        if (0) {
            $ldapconn = ldap_connect("ldap://ldap.wqzbxh.site");
            // 绑定到LDAP服务器
            // ldap_bind($ldapconn, "cn=wqzbxh,dc=ldap.wqzbxh,dc=site", "admin123");
            // 搜索LDAP目录
            $search_results = ldap_search($ldapconn, "dc=ldap.wqzbxh,dc=site", "(&(objectClass=person)(uid=14))");
            // 获取搜索结果
            $entries = ldap_get_entries($ldapconn, $search_results);
            // 输出搜索结果
            var_dump($entries);
            // 断开LDAP连接
            ldap_unbind($ldapconn);
        }

        echo '<br>';
        echo '<br>';
        echo '<hr>';
        echo 'Testing End';
    }
}
