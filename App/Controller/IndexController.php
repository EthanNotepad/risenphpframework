<?php

namespace app\Controller;

use app\Foundations\CoreController;

class IndexController extends CoreController
{
    public function index()
    {
        $appName = env('APP_NAME', 'Risen');
        echo "<h1 style='margin:20px;color:#535353;font:24px/1.2 Helvetica, Arial'>
            <span style='font-size:150px;'>:)</span><br/>Hello, $appName!</h1>";
    }

    public function hello()
    {
        $appName = env('APP_NAME', 'Risen');
        echo 'hello, ' . $appName;
    }
}
