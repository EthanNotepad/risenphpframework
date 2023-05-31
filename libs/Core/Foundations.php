<?php

namespace libs\Core;

class Foundations
{
    const VERSION = '1.0.34';

    /**
     * Get the version number of the application.
     *
     * @return string
     */
    public static function version()
    {
        return static::VERSION;
    }
}
