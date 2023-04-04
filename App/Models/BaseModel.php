<?php

namespace App\Models;

use App\Lib\DB;
use App\Lib\FileLogger;
use PDOException;

class BaseModel
{
    public function getSingle($tableName, $id, $whereField = 'id')
    {
        try {
            $whereSql = "$whereField = $id";
            $returnArray = DB::link()->table($tableName)->where($whereSql)->get();
        } catch (PDOException $e) {
            // Log the error
            FileLogger::error('Error Get data: ' . $e->getMessage());
        }
        return $returnArray;
    }
}
