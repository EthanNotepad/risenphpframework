<?php

namespace libs\Core;

use libs\Core\FileLogger;

class Message
{
    /**
     * Define the returned API data code information
     */
    const CODE_INFO = [
        200 => 'success',

        411 => 'Database Connection Error',
        412 => 'Data validation failed',

        // Model Related
        10400 => 'Model error, missing table name',
        10401 => 'Model error, When updating or deleting, where is required.',
        10402 => 'Please pass in the second parameter, the data to be inserted cannot be empty.',
        10403 => 'There is associated data, delete operation is not allowed.',      // use for model
        10404 => 'When there is a where condition, the value cannot be empty.',
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
