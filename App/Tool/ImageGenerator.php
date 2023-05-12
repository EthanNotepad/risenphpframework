<?php

namespace app\Tool;

class ImageGenerator
{
    /**
     * @Description generate a random profile photo
     * @DateTime 2023-04-21
     * @param string $path save path, eg: /public/uploads/profile
     * @param string $text text, eg: GD
     * @return string
     */
    public function createRandProfilePhoto(string $path, string $text = 'GD'): string
    {
        if (!extension_loaded('gd')) {
            throw new \Exception('GD library is not loaded');
        }

        $path = rtrim($path, '/');
        createFolder($path);
        $text = strtoupper(substr($text, 0, 2));
        $im = imagecreate(200, 200);
        $bg = imagecolorallocate($im, max(rand(1, 254) - 50, 1), max(rand(1, 254) - 50, 1), max(rand(1, 254) - 50, 1));
        imagefill($im, 0, 0, $bg);
        $text_color = imagecolorallocate($im, min(rand(1, 254) + 50, 255), min(rand(1, 254) + 50, 255), min(rand(1, 254) + 50, 255));
        imagefttext($im, 88, 0, 12, 140, $text_color, PROJECT_ROOT_PATH . '/public/font/arial.ttf', $text);
        $pathNew = $path . '/' . (new \libs\Helper\Generate)->generateNonUniqueNumber(7) . substr(time(), 7) . '.png';
        imagepng($im, $pathNew);
        imagedestroy($im);

        return $pathNew;
    }
}
