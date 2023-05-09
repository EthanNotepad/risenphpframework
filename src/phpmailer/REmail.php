<?php

namespace src\phpmailer;

class REmail
{
    protected $attachment = array();  // attachment @zh-cn: 附件

    function send($to, $title, $content)
    {
        try {
            // read config
            global $_CONFIG;
            $defaultConfig = $_CONFIG['src']['email']['driver'];
            $conConfig = $_CONFIG['src']['email'][$defaultConfig];

            // new phpmailer
            $mail = new \src\phpmailer\Core\PHPMailer(true);

            // set stmp 
            // @zh: 使用smtp鉴权方式发送邮件
            $mail->IsSMTP();

            // set char 
            // @zh-c n: 设置邮件的字符编码，这很重要，不然中文乱码 
            $mail->CharSet = 'UTF-8';

            // open smtp auth 
            // @zh-cn: 开启认证
            $mail->SMTPAuth = true;

            // Set login authentication using ssl encryption
            // @zh-cn: 设置使用ssl加密方式登录鉴权
            $mail->SMTPSecure = 'ssl';

            // set smtp port
            // @zh-cn: 设置ssl连接smtp服务器的远程服务器端口号，以前的默认是25，但是现在新的好像已经不可用了 可选465或587'];
            $mail->Port = $conConfig['port'];

            // set smtp host
            // @zh-cn: 链接qq域名邮箱的服务器地址，如smtp.sina.com.cn
            $mail->Host = $conConfig['host'];

            // set sender info
            // set host domain @zh-cn: 设置发件人的主机域 可有可无 默认为localhost 内容任意，建议使用你的域名
            $mail->Hostname = $conConfig['hostname'];
            // @zh-cn: 邮箱账号
            $mail->Username = $conConfig['username'];
            // @zh-cn: STMP授权码，上面提到需要保存使用的
            $mail->Password = $conConfig['password'];
            // @zh-cn: 设置发件人邮箱地址 这里填入上述提到的“发件人邮箱”
            $mail->From = $conConfig['username'];
            // @zh-cn: 设置发件人邮箱的昵称
            $mail->FromName = $conConfig['fromemailname'];

            if (is_array($to)) {
                // send to multiple people @zh-cn: 多人接收
                foreach ($to as $value) {
                    $mail->AddAddress($value);
                }
            } else {
                // send to single people @zh-cn: 单人接收
                $mail->AddAddress($to);
            }

            // set mail content @zh-cn: 设置邮件内容
            $mail->Subject = $title;
            // @zh-cn: 添加邮件正文 上方将isHTML设置成了true，则可以是完整的html字符串 如：使用file_get_contents函数读取本地的html文件
            $mail->Body = $content;
            // @zh-cn: 为该邮件添加附件 该方法也有两个参数 第一个参数为附件存放的目录（相对目录、或绝对目录均可） 第二参数为在邮件附件中该附件的名称
            if (!empty($this->attachment)) {
                foreach ($this->attachment as $value) {
                    $mail->addAttachment($value['path'], $value['name']);
                }
            }

            // send mail
            // @zh-cn: 设置每行字符串的长度 
            $mail->WordWrap = 80;
            $mail->IsHTML(true);
            $ret = $mail->Send();
        } catch (\Exception $e) {
            $ret = $e->getMessage();
        }
        return $ret;
    }

    public function setAttachment($attachmentPath, $attachmentName)
    {
        $newAttachment = array(
            'path' => $attachmentPath,
            'name' => $attachmentName,
        );
        $this->attachment[] = $newAttachment;
    }
}
