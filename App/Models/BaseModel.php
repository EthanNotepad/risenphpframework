<?php

namespace app\Models;

use libs\Db\DB;
use libs\Core\FileLogger;
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
