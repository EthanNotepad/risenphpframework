<?php

namespace app\Controller\Api;

use app\Foundations\CoreController;

class ApiIndexController extends CoreController
{
    public function index()
    {
        \app\Controller\Message::send(200, [], 'Api Index');
    }
}
