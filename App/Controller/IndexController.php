<?php

namespace app\Controller;

use libs\Core\Config;
use libs\Core\Foundations;

class IndexController
{
    public function index()
    {
        $appName = Config::get('app.appName');
        $version = Foundations::version();
        echo "<h1 style='margin:20px;color:#535353;font:24px/1.2 Helvetica, Arial'>
            <span style='font-size:150px;'>:)</span><br/>
            Hello, $appName!<br/>
            <span style='font-size:12px;color:darkgray;'>Version: $version</span></h1>";
    }

    public function hello($name = 'world')
    {
        echo 'hello, ' . $name;
    }
}
