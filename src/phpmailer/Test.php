<?php

/**
 * ------------------------------------------------------------
 * How to use, you can refer to the following test
 * ------------------------------------------------------------
 * 1.Release the email related configuration in the configuration file config\src.php,
 *  and configure the email server information
 * 2.Add route:
 *  Router::any('/src/email/test', 'src\phpmailer\Test@test');
 * 3. Access URI: 
 *  /src/email/test
 */

namespace src\phpmailer;

class Test
{
    public function test()
    {
        $to_user = ['svip2011@qq.com'];
        $title = "Rental Agreement Project";
        $content = "<h1>Test email</h1>
            <p>Test email</p>";
        $sendmail_model = new REmail;
        $sendmail_model->setAttachment(PROJECT_ROOT_PATH . 'Tests/871475.jpeg', '871475.jpeg');
        $return = $sendmail_model->send($to_user, $title, $content);
        if ($return === true) {
            echo "Email send success";
        } else {
            echo "Email send failed, error: " . $return;
        }
    }
}
