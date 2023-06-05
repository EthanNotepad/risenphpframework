<?php

namespace app\Tool;

use libs\Core\Config;
use libs\Helper\UploadFiles;

class Uploader
{
    /**
     * @Description upload single image
     * @DateTime 2023-06-05
     * $params[0] image file
     * $params[1] upload path
     * $params[2] is rename, default true(rename file name)
     * @return void
     */
    public function image()
    {
        $params = func_get_args();
        $files = $params[0];
        $path = isset($params[1]) ? $params[1] : '/upload/images';
        $isRename = isset($params[2]) ? $params[2] : true;
        $filesPath = $this->uploadFiles($files, 'image', $path, $isRename);
        if ($filesPath) {
            return $filesPath;
        } else {
            return false;
        }
    }

    public function files()
    {
        $params = func_get_args();
        $files = $params[0];
        $type = isset($params[1]) ? $params[1] : 'all';
        $path = isset($params[2]) ? $params[2] : '/upload/files';
        $isRename = isset($params[3]) ? $params[3] : true;
        $filesPath = false;
        if (!empty($files)) {
            $filesPath = $this->uploadFiles($files, $type, $path, $isRename);
        }
        if ($filesPath) {
            return $filesPath;
        } else {
            return false;
        }
    }

    public function uploadFiles()
    {
        $params = func_get_args();
        $files = $params[0];
        $type = $params[1];
        $uploadPath = $params[2];
        $isRename = $params[3];

        $uploader = new UploadFiles($uploadPath, '', false, $isRename);
        $diskDefault = Config::get('filesystems.default');
        $disk = Config::get('filesystems.disks.' . $diskDefault);
        $root = $disk['root'];
        $url = $disk['url'];

        $filesPath = [];
        foreach ($files as $key => $value) {
            $uploadedImageUrl = $uploader->upload($value, $type);
            if ($uploadedImageUrl) {
                $targetImageUrl =  str_replace($root, '', $uploadedImageUrl);
                $filesPath[$key] = $url . $targetImageUrl;
            } else {
                return false;
            }
        }
        return $filesPath;
    }
}
