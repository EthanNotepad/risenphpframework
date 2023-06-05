<?php

namespace libs\Helper;

use libs\Core\Config;
use libs\Helper\Generate;

class UploadFiles
{
    private $uploadDir;
    private $maxFileSize;
    private $throw;
    private $isRename;

    public function __construct($fileDir = '', $maxFileSize = '', $throw = false, $isRename = true)
    {
        $diskDefault = Config::get('filesystems.default');
        $disk = Config::get('filesystems.disks.' . $diskDefault);
        $root = $disk['root'];

        $this->throw = isset($disk['throw']) ? $disk['throw'] : $throw;
        $this->isRename = $isRename;
        $this->uploadDir = $root . '/upload';
        $this->maxFileSize = 5 * 1024 * 1024; // 5 MB

        if (!empty($fileDir)) {
            $this->uploadDir = $root . $fileDir;
        }
        if (!empty($maxFileSize)) {
            $this->maxFileSize = $maxFileSize;
        }
    }

    /**
     * $path like 'public/files/'
     * A folder will be created in public/files/ in the root directory
     */
    public function uploadFilesBase64($fileData)
    {
        if (preg_match('/^(data:\s*image\/(jpeg|jpg|png|gif|pdf|x-icon);base64,)/', $fileData, $result)) {
            $type = $result[2];
            if ($type == 'x-icon') {
                $type = 'ico';
            }
            $path = rtrim($this->uploadDir, '/') . '/';
            $pathDir = $path . date('Ymd', time()) . "/";
            createFolder($pathDir);
            $filename = (new Generate)->generateNonUniqueNumber(5, 5);
            $new_file = $pathDir . $filename . '.' . $type;

            $fileData = str_replace($result[1], '', $fileData);
            if (file_put_contents($new_file, base64_decode($fileData))) {
                return $new_file;
            } else {
                if ($this->throw) {
                    throw new \Exception('File upload failed');
                }
                return false;
            }
        }
    }

    public function upload($file, $type = 'all')
    {
        $targetDir = rtrim($this->uploadDir, '/') . '/';
        createFolder($targetDir);
        if ($this->isRename) {
            $filename = (new Generate)->generateNonUniqueNumber(5, 5) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        } else {
            $filename = $file['name'];
        }
        $targetFile = $targetDir . $filename;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if the file already exists
        if (file_exists($targetFile)) {
            if ($this->throw) {
                throw new \Exception('File already exists');
            }
            return false;
        }

        // Adjust the maximum file size as per your requirements
        if ($file['size'] > $this->maxFileSize) {
            if ($this->throw) {
                throw new \Exception('File size exceeds the limit');
            }
            return false;
        }

        // Allow only specific file formats
        if ($type == 'image') {
            $allowedFormats = array('jpg', 'jpeg', 'png', 'gif');
        } elseif ($type == 'video') {
            $allowedFormats = array('mp4', 'avi', 'mov', 'wmv', 'flv', '3gp', 'mkv');
        } elseif ($type == 'audio') {
            $allowedFormats = array('mp3', 'wav', 'wma', 'aac', 'ogg', 'flac', 'alac');
        } else {
            $allowedFormats = array('jpg', 'jpeg', 'png', 'gif', 'mp4', 'avi', 'mov', 'wmv', 'flv', '3gp', 'mkv', 'mp3', 'wav', 'wma', 'aac', 'ogg', 'flac', 'alac', 'sql', 'php', 'txt', 'pdf', 'md', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'rar', 'tar', 'gz', '7z');
        }
        if (!in_array($imageFileType, $allowedFormats)) {
            if ($this->throw) {
                throw new \Exception('Not allowed file format');
            }
            return false;
        }

        // Move the uploaded file to the target directory
        if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
            if ($this->throw) {
                throw new \Exception('Failed to move the file');
            }
            return false;
        }

        return $targetFile; // Return the uploaded file path
    }
}
