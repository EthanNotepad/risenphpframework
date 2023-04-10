<?php

namespace app\Foundations;

use libs\Core\Request;

class CoreController
{
    protected $request;
    public function __construct()
    {
        $this->request = new Request();
    }
}
