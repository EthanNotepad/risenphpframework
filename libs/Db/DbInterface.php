<?php

namespace libs\Db;

interface DbInterface
{
    public static function link($index);
    public static function getInstance();
}
