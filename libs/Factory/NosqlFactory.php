<?php

namespace libs\Factory;

class NosqlFactory
{
    public static function factory($className, $params = '')
    {
        $className = strtolower($className);
        switch ($className) {
            case 'redis':
                $className = 'RedisDB';
                break;
            default:
                // do nothing
        }
        $className = '\\libs\\Db\\' . $className;
        return $className::link($params);
    }
}
