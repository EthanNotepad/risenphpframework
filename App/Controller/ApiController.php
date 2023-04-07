<?php

namespace app\Controller;

use app\Foundations\CoreController;

class ApiController extends CoreController
{
    public function index()
    {
        // $data = $this->request->getData();
        // dd($data);
        \libs\Core\Message::send(200, ['name' => 'Api index'], '');
        // \libs\Core\Message::send(301, [], '');
    }
}
