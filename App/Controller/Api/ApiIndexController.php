<?php

namespace app\Controller\Api;

use app\Foundations\CoreController;

class ApiindexController extends CoreController
{
    public function index()
    {
        parent::index();
        \app\Controller\Message::send(200, [], 'Api Index');
    }
}
