<?php

namespace app\Controller;

use app\Foundations\CoreController;

class Index extends CoreController
{
    public function index()
    {
        global $_CONFIG;
        $appName = $_CONFIG['app']['appName'];
        echo "<h1 style='margin:20px;color:#535353;font:24px/1.2 Helvetica, Arial'>
            <span style='font-size:150px;'>:)</span><br/>Hello, $appName!</h1>";
    }

    public function hello($name = 'Framework')
    {
        echo 'hello, ' . $name;
    }
}
