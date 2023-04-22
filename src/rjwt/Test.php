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

use src\rjwt\Core\JWT;
use src\rjwt\Core\JWTV2;

class Test
{
    private $secret_key = 'my_secret_key';
    // public function encode()
    // {
    //     $expiredTime = time() - 1; // expired time
    //     $exp = time() + 3600; // Expires in one hour
    //     $data = [
    //         'user_id' => 123,
    //         'username' => 'Ethan.Vida',
    //     ];
    //     // JWT::setAlg('sha256');
    //     $jwt = JWT::encode($data, $this->secret_key, $exp);
    //     dd($jwt);
    // }

    // public function decode()
    // {
    //     // JWT::setBlacklist(['6443fa3fcf422']);
    //     $jwt = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJzaGEyNTYifQ==.eyJ1c2VyX2lkIjoxMjMsInVzZXJuYW1lIjoiRXRoYW4uVmlkYSIsImp0aSI6IjY0NDNmYTNmY2Y0MjIiLCJleHAiOiIxNjgyMTc2NTIwIn0=.8mzbvE6BgO+diQKLrM2zU8/vEIEBmCl6X0D5ePHEeLk=';
    //     $data = JWT::decode($jwt, $this->secret_key);
    //     dd($data);
    // }

    public function encode()
    {
        $data = ["id" => 123, "username" => "johndoe"];
        $tokens = JWTV2::encode($data);
        dd($tokens);
    }

    public function decode()
    {
        // JWT::setBlacklist(['6443fa3fcf422']);
        $accessToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE2ODIxODI4NzYsImV4cCI6MTY4MjE4NjQ3NiwiZGF0YSI6eyJpZCI6MTIzLCJ1c2VybmFtZSI6ImpvaG5kb2UifX0.LD-6O_DVUirVhFES4bNt4eWqOt-AhsBGkN1nDfbCkD8";
        // $data = JWTV2::decode($accessToken, "example_key", ["HS256"]);
        // dd($data);

        // $isValid = JWTV2::verify($accessToken, "example_key");
        $isValid = JWTV2::verifyAccessToken($accessToken);

        if ($isValid) {
            dd('The token is valid');
        } else {
            dd('The token is invalid');
        }
    }
}
