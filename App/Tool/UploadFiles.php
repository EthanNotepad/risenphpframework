<?php

namespace app\Tool;

class UploadFiles
{
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
            $pathDir = PROJECT_ROOT_PATH . $path . date('Ymd', time()) . "/";
            createFolder($pathDir);
            $ranNumber = generateNonUniqueNumber(3);
            $time = time();
            $ranKey = $ranNumber . substr($time, 7);
            $new_file = $pathDir . $ranKey . '.' . $type;
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $fileData)))) {
                return $new_file;
            } else {
                return false;
            }
        }
    }

    /**
     * read the list of files in the specified directory
     * supports filtering according to the file format
     */
    public function filesList($path, $format = '')
    {
        $files = scandir($path);
        $filteredFiles = array();

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $path . '/' . $file;
                if (is_file($filePath)) {
                    if ($format !== '' && pathinfo($filePath, PATHINFO_EXTENSION) !== $format) {
                        continue;
                    }
                    $filteredFiles[] = $filePath;
                }
            }
        }

        return $filteredFiles;
    }

    /**
     * Receive the file and store it in the specified directory
     */
    public function saveFile($file, $path, $fileName = '')
    {
        if (empty($fileName)) {
            $fileName = basename($file['name']);
        } else {
            $fileName = $fileName . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        }
        $targetFilePath = $path . '/' . $fileName;
        if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
            return $targetFilePath;
        } else {
            return false;
        }
    }

    /**
     * Copy the file to the specified directory
     */
    public function copyFile($localFilePath, $targetFilePath, $isDelLocaFile = false, $isOverWrite = true)
    {
        if (!file_exists($localFilePath)) {
            return false;
        }

        if (file_exists($targetFilePath) && !$isOverWrite) {
            $extension = pathinfo($targetFilePath, PATHINFO_EXTENSION);
            $newTargetFilePath = pathinfo($targetFilePath, PATHINFO_DIRNAME) . '/' . pathinfo($targetFilePath, PATHINFO_FILENAME) . '_' . uniqid() . '.' . $extension;

            if (!rename($targetFilePath, $newTargetFilePath)) {
                return false;
            }
        }

        if (!copy($localFilePath, $targetFilePath)) {
            return false;
        }

        if ($isDelLocaFile) {
            if (!unlink($localFilePath)) {
                return false;
            }
        }

        return true;
    }


    /**
     * Delete the file
     */
    public function deleteFile($filePath)
    {
        if (file_exists($filePath)) {
            if (unlink($filePath)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
