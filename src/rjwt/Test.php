<?php

/**
 * ------------------------------------------------------------
 * How to use, you can refer to the following test
 * ------------------------------------------------------------
 * 1.Add route:
 *  Router::any('/src/rjwt/test/encode', 'src\rjwt\Test@encode');
 *  Router::any('/src/rjwt/test/decode', 'src\rjwt\Test@decode');
 * 2. Access URI: 
 *  /src/rjwt/test/encode
 *  /src/rjwt/test/decode
 */

namespace src\rjwt;

use src\rjwt\Core\RJWT;

class Test
{
    private $secret_key = 'my_secret_key';
    public function encode()
    {
        $expiredTime = 1; // expired time
        $exp = 3600; // Expires in one hour
        $data = [
            'user_id' => 1234,
            'username' => 'Ethan.Vida',
            'age' => '18'
        ];
        RJWT::setAlg('HS384');
        $jwt = RJWT::encode($data, $this->secret_key, $exp);
        dd($jwt);
    }

    public function decode()
    {
        // RJWT::setBlacklist(['6443fa3fcf422']);
        $jwt = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzM4NCJ9.eyJqdGkiOiI2NDQ0ZTZiY2JjMjRlIiwiaWF0IjoxNjgyMjM3MTE2LCJleHAiOjE2ODIyNDA3MTYsImRhdGEiOnsidXNlcl9pZCI6MTIzNCwidXNlcm5hbWUiOiJFdGhhbi5WaWRhIiwiYWdlIjoiMTgifX0.QrBgkwFQZz0G__2_JrFgP7Y_r0fcY8QXLDxXTQOkvu1At3UFsEb6_j6d8Vqz_U02';
        // $data = RJWT::decode($jwt, $this->secret_key, 'HS384');
        $data = RJWT::verifyToken($jwt, $this->secret_key, 'HS384');
        dd($data);
    }
}
