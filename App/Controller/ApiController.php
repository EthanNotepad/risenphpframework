<?php

namespace App\Controller;

class ApiController
{
    public function index()
    {
        \App\Lib\ApiOutput::ApiOutput('Api homepage!', 200);
        // \App\Lib\ApiOutput::ApiOutput('Api 404', 404);
    }
}
