<?php

namespace app\Controller;

class ApiController
{
    public function index()
    {
        \libs\Core\Message::send(200, ['name' => 'Api index'], '');
        // \libs\Core\Message::send(301, [], '');
    }
}
