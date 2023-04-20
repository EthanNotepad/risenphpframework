<?php

namespace app\Controller\Api;

use app\Foundations\CoreController;

class ApiIndexController extends CoreController
{
    public function index()
    {
        \libs\Core\Message::send(200, [], 'Api Index');
    }
}
