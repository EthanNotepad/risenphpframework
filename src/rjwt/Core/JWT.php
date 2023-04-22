<?php

/**
 * Here are some of the features that are missing from this implementation:
 * Support for different signing algorithms: The JWT class only supports the HMAC SHA-256 algorithm for signing the token. A more complete library would support multiple signing algorithms, such as RSA or ECDSA.
 * Token refreshing: If a JWT is valid but has expired, the user must obtain a new token to continue using the service. A more complete JWT library might include support for refreshing tokens without requiring the user to re-authenticate.
 * Token encryption: The JWT class implementation does not encrypt the token payload. If the payload contains sensitive information, it may be vulnerable to interception or tampering. A more complete JWT library might include support for encrypting the payload.
 */

namespace src\rjwt\Core;

class JWT
{
    private static $alg = 'sha256';  // hash_hmac_algos(): array can get list
    private static $blacklist = [];  // check if the given JWT ID has been blacklisted (revoked

    public static function setAlg($algname)
    {
        self::$alg = $algname;
    }

    public static function setBlacklist(array $blacklisted_jtis)
    {
        self::$blacklist = $blacklisted_jtis;
    }

    public static function encode(array $data, $secret_key, $exp = 0)
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => self::$alg]);
        $data['jti'] = uniqid();
        $expire_time = $exp > 0 ? $exp : time() + 3600; // Default to 1 hour expiry
        $data['exp'] = $expire_time;
        $payload = json_encode($data);
        $token = base64_encode($header) . '.' . base64_encode($payload) . '.' . self::sign(self::$alg, $header, $payload, $secret_key, $expire_time);
        return $token;
    }

    public static function decode($token, $secret_key, $alg = '')
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new \Exception('Invalid token');
        }
        $header = json_decode(base64_decode($parts[0]), true);
        $payload = json_decode(base64_decode($parts[1]), true);
        $signature = $parts[2];
        $alg = empty($alg) ? self::$alg : $alg;
        if (!self::verify($alg, $header, $payload, $secret_key, $signature)) {
            throw new \Exception('Invalid signature');
        }
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            throw new \Exception('Token has expired');
        }
        if (isset($payload['jti']) && self::is_blacklisted($payload['jti'])) {
            throw new \Exception('Token has been revoked');
        }
        return $payload;
    }

    private static function sign($alg, $header, $payload,  $secret_key, $expire_time)
    {
        $signature = hash_hmac($alg, $header . '.' . $payload . $expire_time, $secret_key, true);
        return base64_encode($signature);
    }

    private static function verify($alg, $header, $payload, $secret_key, $signature)
    {
        $expected = self::sign($alg, json_encode($header), json_encode($payload), $secret_key, $payload['exp']);
        return hash_equals($expected, $signature);
    }

    private static function is_blacklisted($jti)
    {
        return in_array($jti, self::$blacklist);
    }
}
