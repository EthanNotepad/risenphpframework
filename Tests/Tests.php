<?php

namespace tests;

use Exception;

class Tests
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
            $data = (new \libs\Core\Request)->path();
            dd($data);
        }

        /**
         * Test the response function
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
            dump(\libs\Core\Config::all());
            // $displayErrors = config('database.connections');
            // global $_CONFIG;
            // $displayErrorsFromGlobalConfig = $_CONFIG['database']['connections'];
            // $displayErrorsFromConfigClass = \libs\Core\Config::get('database.connections');
            // \libs\Core\Config::set('database.connections.mariadb', 'test');
            // $displayErrorsFromConfigClassNew = \libs\Core\Config::get('database.connections');
            // dump($displayErrors, $displayErrorsFromGlobalConfig, $displayErrorsFromConfigClass, $displayErrorsFromConfigClassNew);
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
                throw new Exception("Validation failed: " . $resultValidated);
            }
            echo 'moving on';
        }

        /**
         * Test bash64 file upload function
         */
        if (0) {
            $filePath = PROJECT_ROOT_PATH . '/tests/imageData.base64';
            $imgData = file_get_contents($filePath);
            $uploader = new \app\Tool\UploadFiles();
            $imagePath = $uploader->uploadFilesBase64($imgData, 'public/upload/images/');
            echo $imagePath;
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
            $redis = \libs\Factory\NosqlFactory::factory('redis'); // NoSql factory function
            // $redis = \libs\Db\RedisDB::link('cache');
            $redis->set('mykey', 'shejibiji.com');
            echo $redis->get('mykey');
        }

        // Test the error handler function
        if (0) {
            echo $nothisvar; // error
            throw new \Exception('test Exception'); // exception
        }

        // Test the image generator function
        if (0) {
            $test = new \app\Tool\ImageGenerator;
            $path = $test->createRandProfilePhoto(PROJECT_ROOT_PATH . '/public/uploads/profile_photo/');
            echo 'generate image success, path is: ' . $path;
        }

        // Test the file handler function
        if (0) {
            $test = new \app\Tool\FileHandler;
            $result = $test->copyFile(PROJECT_ROOT_PATH . '/public/uploads/new.png', PROJECT_ROOT_PATH . '/public/uploads/profile_photo/back1.png', true);
            if ($result) {
                echo 'copy file success';
            }

            $result = $test->deleteFile(PROJECT_ROOT_PATH . '/public/uploads/profile_photo/4379686249.png');
            if ($result) {
                echo 'delete file success';
            }
        }

        // Test the helper function
        if (0) {
            // dd(getenv(), env('APP_NAME', 'Risen')); // env function
            // $value = value(function () {
            //     return 'test';
            // }, 'test');
            // // $value = value('value', 'test');
            // dd($value);
        }

        echo '<br>';
        echo '<br>';
        echo '<hr>';
        echo 'Testing End';
        die;
    }

    public function db()
    {
        /**
         * Test the database query function, support chain operation
         */
        $id = $_GET['id'] = "1 OR 1=1";     // test sql injection
        // different where can handle same
        $whereSql = [
            0 => ['logs.id', $id],
            1 => ['logs.user_id', '=', 1],
        ];
        $whereSql = [
            'logs.id' => $id,
            'logs.user_id' => 1,
        ];
        $whereSql = "logs.id = $id AND logs.user_id = 1";
        // dd(\libs\Db\DB::link()->getConfig());
        // $where_test = \libs\Db\DB::link()->table('logs')->where($id)->dd(); // where statement have one parameters, note this is a dangerous action because easy to be injected
        // $where_test = \libs\Db\DB::link()->table('logs')->where('id', $id)->dd(); // where statement have two parameters
        // $where_test = \libs\Db\DB::link()->table('logs')->where('id', '=', $id)->dd(); // where statement have three parameters
        // $where_test = \libs\Db\DB::link()->table('logs')->where($whereSql)->dd(); // where statement is an array
        // $set_connect_and_dbname = \libs\Db\DB::link('mysql.timetracker')->table('logs')->dd();  // support set dbname
        // $set_dbname = \libs\Db\DB::link('mysql')->table('logs')->dd();  // support set dbname
        // $getone_last = \libs\Db\DB::link()->table('logs')->order('id ASC')->limit(1, 10)->last();  // last function
        // $getCount = \libs\Db\DB::link()->table('logs')->where('id', '=', 3)->field('id')->count();
        // $getAll = \libs\Db\DB::link()->table('logs')->where('id', '=', 1)->field('id', 'user_id')->limit(1, 100)->order("id ASC")->getAll();
        // $getLeftJoin = \libs\Db\DB::link()->table('logs')->where('id', '<>', 1)->field('id', 'logs')->join('logs AS logs2', 'logs2.user_id = logs.id')->limit(1, 10)->order('id ASC')->paginate(1, 10);
        // $getFieldNotSafe = \libs\Db\DB::link()->table('logs')->where($whereSql)->join('logs AS logs2', 'logs2.user_id = logs.id')->fieldString('count(*) AS total')->get();
        // dd($set_connect_and_dbname);

        // Data update and insert
        // Data insertion and getting the inserted id
        // $insertData = [
        //     'user_id' => '3',
        //     'action' => 'login'
        // ];
        // \libs\Db\DB::link()->table('logs')->insert($insertData);
        // $insertId = \libs\Db\DB::link()->lastId();
        // dd($insertId);

        echo '<br>';
        echo '<br>';
        echo '<hr>';
        echo 'DB Testing End';
        die;
    }
}
