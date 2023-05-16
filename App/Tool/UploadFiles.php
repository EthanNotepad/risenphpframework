<?php

namespace app\Tool;

use libs\Helper\Generate;

class UploadFiles
{
    private $uploadDir = PROJECT_ROOT_PATH . 'public/upload/images/';

    public function __construct()
    {
        // do something
    }

    /**
     * $path like 'public/files/'
     * A folder will be created in public/files/ in the root directory
     */
    public function uploadFilesBase64($fileData, $path)
    {
        if (preg_match('/^(data:\s*image\/(jpeg|jpg|png|gif|pdf|x-icon);base64,)/', $fileData, $result)) {
            $type = $result[2];
            if ($type == 'x-icon') {
                $type = 'ico';
            }
            $path = rtrim($path, '/') . '/';
            $pathDir = PROJECT_ROOT_PATH . $path . date('Ymd', time()) . "/";
            createFolder($pathDir);
            $filename = (new Generate)->generateNonUniqueNumber(5, 5);
            $new_file = $pathDir . $filename . '.' . $type;
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $fileData)))) {
                return $new_file;
            } else {
                return false;
            }
        }
    }

    public function upload($file)
    {
        $targetDir = rtrim($this->uploadDir, '/') . '/';
        createFolder($targetDir);
        $filename = (new Generate)->generateNonUniqueNumber(5, 5);
        $targetFile = $targetDir . $filename . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if the file is an image
        $check = getimagesize($file['tmp_name']);
        if ($check === false) {
            return false; // Not an image file
        }

        // Check if the file already exists
        if (file_exists($targetFile)) {
            return false; // File already exists
        }

        // Check file size (if needed)
        // Adjust the maximum file size as per your requirements
        $maxFileSize = 5 * 1024 * 1024; // 5 MB
        if ($file['size'] > $maxFileSize) {
            return false; // File size exceeds the limit
        }

        // Allow only specific image file formats (you can customize this)
        $allowedFormats = array('jpg', 'jpeg', 'png', 'gif');
        if (!in_array($imageFileType, $allowedFormats)) {
            return false; // Invalid image format
        }

        // Move the uploaded file to the target directory
        if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
            return false; // Failed to move the file
        }

        return $targetFile; // Return the image address
    }
}
