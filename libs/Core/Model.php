<?php

namespace libs\Core;

use libs\Db\DB;

abstract class Model
{
    protected static $tablename = '';
    protected static $connection = '';

    public static function get(array $where, array $field = [])
    {
        try {
            $result = DB::link(static::$connection)->table(static::tablename())->field($field)->where($where)->get();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function getAll(array $where, array $field = [])
    {
        try {
            $result = DB::link(static::$connection)->table(static::tablename())->field($field)->where($where)->getAll();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
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

    public static function update(array $data, array $where)
    {
        try {
            $result = DB::link(static::$connection)->table(static::tablename())->where($where)->update($data);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function delete(array $where)
    {
        try {
            $result = DB::link(static::$connection)->table(static::tablename())->where($where)->delete();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function count(array $where = [])
    {
        try {
            $result = DB::link(static::$connection)->table(static::tablename())->where($where)->count();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function exists(array $where)
    {
        try {
            $result = DB::link(static::$connection)->table(static::tablename())->where($where)->exists();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function first(array $where, array $field = [])
    {
        try {
            $result = DB::link(static::$connection)->table(static::tablename())->field($field)->where($where)->first();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function last(array $where, array $field = [])
    {
        try {
            $result = DB::link(static::$connection)->table(static::tablename())->field($field)->where($where)->last();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    public static function paginate(int $page, int $perPage, array $where = [], array $field = [])
    {
        try {
            $result = DB::link(static::$connection)->table(static::tablename())->field($field)->where($where)->paginate($page, $perPage);
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
}
