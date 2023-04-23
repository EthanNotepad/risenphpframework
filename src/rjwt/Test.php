<?php

/**
 * ------------------------------------------------------------
 * How to use, you can refer to the following test
 * ------------------------------------------------------------
 * 1.Configure extended information.
 *  You can copy the contents of the src.example.php file in this directory to config/src.php
 *  Then modify the information in the configuration file to the actual information.
 * 2.Add route:
 *  Router::any('/src/rjwt/test/encode', 'src\rjwt\Test@encode');
 *  Router::any('/src/rjwt/test/decode', 'src\rjwt\Test@decode');
 * 3. Access URI: 
 *  /src/rjwt/test/encode
 *  /src/rjwt/test/decode
 */

namespace src\rjwt;

use src\rjwt\Core\RJWT;

class Test
{
    public function encode()
    {
        $data = [
            'user_id' => 1234,
            'username' => 'Ethan.Vida',
            'age' => '18'
        ];
        $jwt = RJWT::encode($data);
        dd($jwt);
    }

    public function decode()
    {
        // RJWT::setBlacklist(['6444fbafeb769']);
        $jwt = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI2NDQ0ZmM2N2FhZDdlIiwiaWF0IjoxNjgyMjQyNjYzLCJleHAiOjE2ODIyNDI2NjQsImRhdGEiOnsidXNlcl9pZCI6MTIzNCwidXNlcm5hbWUiOiJFdGhhbi5WaWRhIiwiYWdlIjoiMTgifX0.FvTugVvamLudDg9bOPxXeuXp0O6b4RLPHjb4soiCFMo';
        $data = RJWT::verifyToken($jwt);
        dd($data);
    }
}
