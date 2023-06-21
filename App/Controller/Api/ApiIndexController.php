<?php

namespace app\Controller\Api;

use app\Foundations\CoreController;
use libs\Core\Config;
use libs\Core\Foundations;

class ApiindexController extends CoreController
{
    public function index()
    {
        parent::index();
        return \app\Controller\Message::send(200, [
            'version' => Foundations::version(),
            'appName' => Config::get('app.appName'),
        ]);
    }
}
