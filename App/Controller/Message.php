<?php

namespace app\Controller;

use libs\Core\FileLogger;
use libs\Helper\Lang;

class Message
{
    /**
     * Define the returned API data code information
     */
    const CODE_INFO = [
        200 => 'success',
        401 => 'Unauthorized',
        412 => 'Database error',

        1000 => 'Unknown error',
    ];

    public static function send(int $code, array $data = [], string $message = '')
    {
        // Define the returned API data header
        header_remove('Set-Cookie');
        header('Content-Type: application/json');


        if (in_array($code, array_keys(self::CODE_INFO))) {
            $extraMessage = Lang::get(self::CODE_INFO[$code]);
        } else {
            $extraMessage = Lang::get(self::CODE_INFO[1000]);
        }
        if (empty($message)) {
            $message = $extraMessage;
        } else {
            $message = $extraMessage . ': ' . $message;
        }

        // if ($code !== 200) {
        //     FileLogger::warning('Failed to return data: [' . $code . '] ' . $message);
        // }

        // Define the returned API data format
        $returnMessage = json_encode(array(
            'code' => $code,
            'data' => $data,
            'msg' => $message,
        ));

        echo $returnMessage;
        exit;
    }
}
