<?php

namespace app\Controller;

use libs\Core\Config;

class IndexController
{
    public function index()
    {
        $appName = Config::get('app.appName');
        echo "<h1 style='margin:20px;color:#535353;font:24px/1.2 Helvetica, Arial'>
            <span style='font-size:150px;'>:)</span><br/>Hello, $appName!</h1>";
    }

    public function hello($name = '')
    {
        if (empty($name)) {
            $name = 'world';
        }
        echo 'hello, ' . $name;
    }
}
