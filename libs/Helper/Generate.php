<?php

namespace libs\Helper;

class Generate
{
    /**
     * @Description generate random string
     * @cn-zh 生成随机字符串
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
     * @cn-zh 生成随机数字
     * @DateTime 2023-05-12
     * @param int $digits
     * @return string
     */
    function generateNonUniqueNumber(int $digits): string
    {
        $number = '';
        for ($i = 0; $i < $digits; $i++) {
            $number .= mt_rand(0, 9); // append a random digit to the number
        }
        return $number;
    }

    /**
     * @Description generate token
     * @cn-zh 生成token
     * @DateTime 2023-05-12
     * @param int $length
     * @return string
     */
    function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }
}
