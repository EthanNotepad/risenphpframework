<?php

namespace app\Tool;

class ImageGenerator
{
    // TODO Need to test, handle the exception
    // Need GD library
    // path like 'public/Img/user' now
    public function createRandProfilePhoto($path, $text = 'GD')
    {
        createFolder($path);
        $text = strtoupper(substr($text, 0, 2));
        $im = imagecreate(200, 200);
        $bg = imagecolorallocate($im, max(rand(1, 254) - 50, 1), max(rand(1, 254) - 50, 1), max(rand(1, 254) - 50, 1));
        imagefill($im, 0, 0, $bg);
        $text_color = imagecolorallocate($im, min(rand(1, 254) + 50, 255), min(rand(1, 254) + 50, 255), min(rand(1, 254) + 50, 255));

        imagefttext($im, 90, 0, 26, 140, $text_color, PROJECT_ROOT_PATH . '/public/font/arial.ttf', $text);
        $pathNew = $path . '/' . generateNonUniqueNumber(9) . time() . '.png';
        imagepng($im, $pathNew);
        imagedestroy($im);
        return $pathNew;
    }
}
