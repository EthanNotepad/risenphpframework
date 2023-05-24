<?php

namespace app\Tool;

use libs\Helper\Generate;

class UploadFiles
{
    private $uploadDir = PROJECT_ROOT_PATH . 'public/upload/images/';
    private $maxFileSize = 5 * 1024 * 1024; // 5 MB

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

    public function upload($file, $type = 'all')
    {
        $targetDir = rtrim($this->uploadDir, '/') . '/';
        createFolder($targetDir);
        $filename = (new Generate)->generateNonUniqueNumber(5, 5);
        $targetFile = $targetDir . $filename . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if the file already exists
        if (file_exists($targetFile)) {
            return false; // File already exists
        }

        // Check file size (if needed)
        // Adjust the maximum file size as per your requirements
        if ($file['size'] > $this->maxFileSize) {
            return false; // File size exceeds the limit
        }

        // Allow only specific file formats
        if ($type == 'image') {
            $allowedFormats = array('jpg', 'jpeg', 'png', 'gif');
        } elseif ($type == 'video') {
            $allowedFormats = array('mp4', 'avi', 'mov', 'wmv', 'flv', '3gp', 'mkv');
        } elseif ($type == 'audio') {
            $allowedFormats = array('mp3', 'wav', 'wma', 'aac', 'ogg', 'flac', 'alac');
        } else {
            $allowedFormats = array('jpg', 'jpeg', 'png', 'gif', 'mp4', 'avi', 'mov', 'wmv', 'flv', '3gp', 'mkv', 'mp3', 'wav', 'wma', 'aac', 'ogg', 'flac', 'alac', 'sql', 'php', 'txt', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'rar', 'tar', 'gz', '7z');
        }
        if (!in_array($imageFileType, $allowedFormats)) {
            return false; // not allowed file format
        }

        // Move the uploaded file to the target directory
        if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
            return false; // Failed to move the file
        }

        return $targetFile; // Return the uploaded file path
    }
}
