<?php

namespace libs\Core;

use libs\Db\DB;

abstract class Model
{
    // Define the table name, default table name is the same as the class name
    protected static $tablename = '';

    // Define the database connection name, default is the default connection
    protected static $connection = '';

    // Define the primary key, default is 'id'
    // only used in the order method as the default order field now
    protected static $primaryKey = 'id';

    // Define the soft delete field, if it is empty, it means that the soft delete is not enabled, 
    // or delete the field name of the soft delete
    protected static $softdelete = '';

    // not used yet

    // Define the field name of the create time, if it is empty, it means that the create time is not enabled
    protected static $createTime = '';

    // Define the field name of the update time, if it is empty, it means that the update time is not enabled
    protected static $updateTime = '';

    // Define the field name of the delete time, if it is empty, it means that the delete time is not enabled
    protected static $deleteTime = '';

    public static function get(array $where = [], array $field = [])
    {
        try {
            $result = DB::link(static::$connection)->table(static::tablename())->field($field)->where(static::handleWhere($where))->get();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function getAll(array $where = [], array $field = [], $limit = 0, $offset = 10000, $order = '', $orderType = '')
    {
        try {
            $order = static::handleOrder($order, $orderType);
            $result = DB::link(static::$connection)->table(static::tablename())->field($field)->where(static::handleWhere($where))->limit($limit, $offset)->order($order)->select();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function all($where = [], array $field = [])
    {
        return static::getAll($where, $field);
    }

    public static function insert(array $data)
    {
        try {
            $result = DB::link(static::$connection)->table(static::tablename())->insert($data);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function insertAll(array $data)
    {
        try {
            $result = DB::link(static::$connection)->table(static::tablename())->insertAll($data);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function update(array $where, array $data)
    {
        try {
            $result = DB::link(static::$connection)->table(static::tablename())->where($where)->update($data);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function updateAll(array $data)
    {
        try {
            $result = DB::link(static::$connection)->table(static::tablename())->updateAll($data);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function updateOrInsert(array $where, array $data)
    {
        try {
            $isExist = DB::link(static::$connection)->table(static::tablename())->where(static::handleWhere($where))->exists();
            if ($isExist) {
                $result = DB::link(static::$connection)->table(static::tablename())->where(static::handleWhere($where))->update($data);
            } else {
                $result = DB::link(static::$connection)->table(static::tablename())->insert($data);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function save(array $where, array $data)
    {
        return static::updateOrInsert($where, $data);
    }

    public static function delete(array $where = [])
    {
        try {
            if (empty(static::$softdelete)) {
                $result = DB::link(static::$connection)->table(static::tablename())->where($where)->delete();
            } else {
                $result = DB::link(static::$connection)->table(static::tablename())->where($where)->update([static::$softdelete => 1]);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function deleteAll()
    {
        try {
            if (empty(static::$softdelete)) {
                $result = DB::link(static::$connection)->table(static::tablename())->deleteAll();
            } else {
                $result = DB::link(static::$connection)->table(static::tablename())->updateAll([static::$softdelete => 1]);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function truncate()
    {
        try {
            $result = DB::link(static::$connection)->table(static::tablename())->truncate();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function count(array $where = [])
    {
        try {
            $result = DB::link(static::$connection)->table(static::tablename())->where(static::handleWhere($where))->count();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function sum($field, array $where = [])
    {
        try {
            $result = DB::link(static::$connection)->table(static::tablename())->where(static::handleWhere($where))->sum($field);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function exists(array $where)
    {
        try {
            $result = DB::link(static::$connection)->table(static::tablename())->where(static::handleWhere($where))->exists();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function first($where = [], array $field = [])
    {
        try {
            $result = DB::link(static::$connection)->table(static::tablename())->field($field)->where(static::handleWhere($where))->first();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function last(array $where = [], array $field = [])
    {
        try {
            $result = DB::link(static::$connection)->table(static::tablename())->field($field)->where(static::handleWhere($where))->last();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function paginate(int $page, int $perPage, array $where = [], array $field = [], $order = '', $orderType = '')
    {
        try {
            $order = static::handleOrder($order, $orderType);
            $result = DB::link(static::$connection)->table(static::tablename())->field($field)->where(static::handleWhere($where))->order($order)->paginate($page, $perPage);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function lastInsertId()
    {
        try {
            $result = DB::link(static::$connection)->lastId();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function sql(array $where)
    {
        try {
            $result = DB::link(static::$connection)->table(static::tablename())->where(static::handleWhere($where))->getSql();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function query(string $sql)
    {
        try {
            $result = DB::link(static::$connection)->query($sql);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function execute(string $sql)
    {
        try {
            $result = DB::link(static::$connection)->execute($sql);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function beginTransaction()
    {
        try {
            $result = DB::link(static::$connection)->beginTransaction();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function commit()
    {
        try {
            $result = DB::link(static::$connection)->commit();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function rollback()
    {
        try {
            $result = DB::link(static::$connection)->rollback();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function getFields()
    {
        try {
            $result = DB::link(static::$connection)->getFields(static::tablename());
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function getPrimary()
    {
        try {
            $result = DB::link(static::$connection)->getPrimaryKey(static::tablename());
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function getTable()
    {
        try {
            $result = DB::link(static::$connection)->getTable(static::tablename());
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function getTableInfo()
    {
        try {
            $result = DB::link(static::$connection)->getTableInfo(static::tablename());
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function getTableStatus()
    {
        try {
            $result = DB::link(static::$connection)->getTableStatus(static::tablename());
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function getTables()
    {
        try {
            $result = DB::link(static::$connection)->getTables();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function getDatabases()
    {
        try {
            $result = DB::link(static::$connection)->getDatabases();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function tablename()
    {
        if (static::$tablename == '') {
            $class = explode('\\', get_called_class());
            $class = end($class);
            $class = str_replace('Model', '', $class);
            static::$tablename = strtolower($class);
        }
        return static::$tablename;
    }

    public static function handleOrder($order, $orderType)
    {
        if (!empty($order) && !empty($orderType)) {
            $order = $order . ' ' . $orderType;
        } else {
            if (!empty($orderType) && empty($order)) {
                $order = static::$primaryKey . ' ' . $orderType;
            }
            if (empty($orderType) && !empty($order)) {
                $order = $order . ' ASC';
            }
        }
        return $order;
    }

    public static function handleWhere(array $where, bool $canEmpty = true)
    {
        // handle empty value in where array
        if ($canEmpty) {
            $where = array_filter($where, function ($value) {
                return $value !== null;
            });
        } else {
            $where = array_filter($where, function ($value) {
                return $value !== null && $value !== '';
            });
        }

        // handle softdelete
        if (!empty(static::$softdelete) && !isset($where[static::$softdelete])) {
            $where = array_merge($where, [[static::$softdelete => 0]]);
        }

        return $where;
    }
}
