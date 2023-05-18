<?php

namespace libs\Core;

class Foundations
{
    const VERSION = '1.0.0';

    /**
     * Get the version number of the application.
     *
     * @return string
     */
    public function version()
    {
        return static::VERSION;
    }
}
