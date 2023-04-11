<?php

namespace bootstrap;

class Install
{
    public static function checkInstall()
    {
        $msg = '';
        if (!file_exists(PROJECT_ROOT_PATH . "/src/autoload.php")) {
            $msg .= 'The /src/autoload.php file does not exist.<br>';
        }
        if (!file_exists(PROJECT_ROOT_PATH . "/routes/web.php")) {
            $msg .= 'The /routes/web.php file does not exist.<br>';
        }
        if ($msg) {
            die("<h1 style='margin:20px;color:#535353;font:24px/1.2 Helvetica, Arial'>
                <span style='font-size:150px;'>:(</span><br/>{$msg}</h1>");
        }
    }
}
