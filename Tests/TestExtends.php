<?php

namespace tests;

abstract class TestExtends
{
    protected static $params = 'TestExtends';
    public function __construct()
    {
        // do something
    }
    protected static function getParams()
    {
        return static::$params;
    }
}
