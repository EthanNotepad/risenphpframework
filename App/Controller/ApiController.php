<?php

namespace app\Controller;

use app\Foundations\CoreController;

class ApiController extends CoreController
{
    public function index()
    {
        \libs\Core\Message::send(200, ['name' => 'Api index'], '');
    }
}
