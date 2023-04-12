<?php

namespace app\Foundations;

use libs\Core\Request;

abstract class CoreController
{
    protected $request;
    public function __construct()
    {
        $this->request = new Request();
        $this->initialize();
    }

    protected function initialize()
    {
        // initialization, you can perform some pre-actions here
    }
}
