<?php

namespace libs\Core;

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
            if (!is_dir($pathDir)) {
                mkdir($pathDir, 0777, true);
            }
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
}
