<?php

namespace app\Controller;

use libs\Core\FileLogger;

class Message
{
    /**
     * Define the returned API data code information
     */
    const CODE_INFO = [
        200 => 'success',
    ];

    public static function send(int $code, array $data = [], string $message = '')
    {
        // Define the returned API data header
        header_remove('Set-Cookie');
        header('Content-Type: application/json');

        if (empty($message)) {
            if (in_array($code, array_keys(self::CODE_INFO))) {
                $message = self::CODE_INFO[$code];
            } else {
                $message = 'Unknown error';
            }
        }

        if ($code !== 200) {
            FileLogger::warning('Failed to return data: [' . $code . '] ' . $message);
        }

        // Define the returned API data format
        $returnMessage = json_encode(array(
            'code' => $code,
            'message' => $message,
            'data' => $data
        ));

        echo $returnMessage;
        exit;
    }
}
