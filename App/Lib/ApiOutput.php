<?php

namespace App\Lib;

use App\Lib\FileLogger;

class ApiOutput
{
    /**
     * @Description Data is returned only when the return status code is 200,
     *              otherwise the corresponding string will be displayed
     */
    public static $statusTexts = [
        200 => 'OK',

        411 => 'Database Connection Error',
        412 => 'Data validation failed',

        // Model Related
        10400 => 'Model error, missing table name',
        10401 => 'Model error, When updating or deleting, where is required.',
        10402 => 'Please pass in the second parameter, the data to be inserted cannot be empty.',
        10403 => 'There is associated data, delete operation is not allowed.',      // use for model
        10404 => 'When there is a where condition, the value cannot be empty.',
    ];

    public static function ApiOutput($data, $code, $header = '')
    {
        // Define the returned API data header
        header_remove('Set-Cookie');
        $httpHeaders = array('Content-Type: application/json', $header);
        if (is_array($httpHeaders) && count($httpHeaders)) {
            foreach ($httpHeaders as $httpHeader) {
                header($httpHeader);
            }
        }

        $success = true;
        if ($code != 200) {
            if (empty($data)) {
                $data = self::$statusTexts[$code];
                FileLogger::warning('Failed to return data: [' . $code . '] ' . $data);
            }
            $success = false;
        }
        // Define the returned API data format
        $returnData = json_encode(array(
            'success' => $success,
            'payload' => [
                'code' => $code,
                'data' => $data
            ]
        ));

        echo $returnData;
        exit;
    }
}
