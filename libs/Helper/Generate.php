<?php

namespace libs\Helper;

class Generate
{
    /**
     * @Description generate random string
     * @zh-cn 生成随机字符串
     * @DateTime 2023-05-12
     * @param int $length
     * @param bool $includeLetters
     * @param bool $includeSymbols
     * @return string $string
     */
    function generateRandomString($length = 10, $includeLetters = true, $includeSymbols = true): string
    {
        $chars = '';

        if ($includeLetters) {
            $chars .= 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        if ($includeSymbols) {
            $chars .= '!@#$%^&*()-_=+[]{};:,.<>?';
        }

        $string = '';
        $charsLength = strlen($chars);

        for ($i = 0; $i < $length; $i++) {
            $string .= $chars[rand(0, $charsLength - 1)];
        }

        return $string;
    }

    /**
     * @Description generate random number
     * @zh-cn 生成随机数字
     * @DateTime 2023-05-12
     * @param int $digits
     * @return string
     */
    function generateNonUniqueNumber(int $digits, int $timeLength = 0): string
    {
        $number = '';
        for ($i = 0; $i < $digits; $i++) {
            $number .= mt_rand(0, 9); // append a random digit to the number
        }
        $timeNumber = '';
        if ($timeLength !== 0) {
            // Get the number at the end of the timestamp
            $timeNumber = substr(time(), max(10 - $timeLength, 0));
        }
        return $number . $timeNumber;
    }

    /**
     * @Description generate token
     * @zh-cn 生成token
     * @DateTime 2023-05-12
     * @param int $length
     * @return string
     */
    function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }
}
