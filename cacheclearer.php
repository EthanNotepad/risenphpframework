<?php

class CacheClearer
{
    public function clearCache()
    {
        $cacheDir = 'runtime/cache';
        $files = glob($cacheDir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo 'Cache cleared!';
    }
}

$CacheClearer = new CacheClearer();
$CacheClearer->clearCache();
