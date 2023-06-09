<?php

namespace src\rjwt\Core;

use libs\Core\Config;

class RJWT
{
    const ALG_HS256 = 'HS256';
    const ALG_HS384 = 'HS384';
    const ALG_HS512 = 'HS512';

    // TODO Add more supported algorithms, such as RSA or ECDSA.
    // const ALG_RS256 = 'RS256';
    // const ALG_RS384 = 'RS384';
    // const ALG_RS512 = 'RS512';
    // const ALG_ES256 = 'ES256';
    // const ALG_ES384 = 'ES384';
    // const ALG_ES512 = 'ES512';
    // private static $allowed_alg = [self::ALG_HS256, self::ALG_HS384, self::ALG_HS512, self::ALG_RS256, self::ALG_RS384, self::ALG_RS512, self::ALG_ES256, self::ALG_ES384, self::ALG_ES512];

    private static $allowed_alg = [self::ALG_HS256, self::ALG_HS384, self::ALG_HS512];
    private static $blacklist = [];  // check if the given JWT ID has been blacklisted (revoked

    /**
     * @Description set blacklist
     * @DateTime 2023-04-23
     * @param array $blacklisted_jtis
     * @return void
     */
    public static function setBlacklist(array $blacklisted_jtis): void
    {
        self::$blacklist = $blacklisted_jtis;
    }

    /**
     * @Description Generate jwt, an array will be generated by default, including access_token, refresh_token, expire_time
     * @zh-cn 生成jwt，默认生成一个数组，包含access_token、refresh_token、expire_time
     * @DateTime 2023-04-23
     * @param array $data
     * @param string $secret_key
     * @param int $exp
     * @param bool $refresh_switch
     * @param string $refresh_key
     * @param int $refreshExpireTime
     * @return void
     */
    public static function encode(array $data, string $secret_key = '', $exp = 0, $alg = '', $refresh_switch = false, string $refresh_key = '', $refresh_expire_time = '')
    {
        $rjwtConfig = Config::get('src.rjwt');

        $default_alg = empty($alg) ? $rjwtConfig['default_alg'] : $alg;
        $secret_key = empty($secret_key) ? $rjwtConfig['secret_key'] : $secret_key;
        $expire_time = $exp > 0 ? time() + $exp : time() + $rjwtConfig['expire_time']; // Default to 1 hour expiry

        $header = ['typ' => 'JWT', 'alg' => $default_alg];
        $payload = [
            "jti" => uniqid(),
            "iat" => time(),
            "exp" => $expire_time,
            "data" => $data
        ];
        $jwt = self::sign($default_alg, $header, $payload, $secret_key);

        $refresh_switch = $refresh_switch === false ? $rjwtConfig['refresh_switch'] : $refresh_switch;
        if ($refresh_switch) {
            $refresh_expire_time = empty($refresh_expire_time) ? $rjwtConfig['refresh_expire_time'] : $refresh_expire_time;
            $refresh_key = empty($refresh_key) ? $rjwtConfig['refresh_key'] : $refresh_key;

            $refreshToken = self::generateRefreshToken($default_alg, $header, $payload, $refresh_key, $refresh_expire_time);
            $returnData = [
                "access_token" => $jwt,
                "refresh_token" => $refreshToken,
                "expire_time" => $expire_time
            ];
            return $returnData;
        } else {
            return $jwt;
        }
    }

    /**
     * @Description Decode the token generated by jwt, Not recommended, please use the verifyToken method
     * @zh-cn: 解码jwt生成的token，不推荐，请使用verifyToken方法
     * @DateTime 2023-04-23
     * @param string $token
     * @param string $key
     * @param string $alg
     * @return array
     */
    public static function decode(string $token, string $key = '', string $alg = '')
    {
        $rjwtConfig = Config::get('src.rjwt');

        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new \Exception('Invalid token format');
        }

        $decoded_header = self::base64UrlDecode($parts[0]);
        if (!$decoded_header) {
            throw new \Exception('Invalid token header');
        }

        $decoded_payload = self::base64UrlDecode($parts[1]);
        if (!$decoded_payload) {
            throw new \Exception('Invalid token payload');
        }

        $alg = empty($alg) ? $rjwtConfig['default_alg'] : $alg;
        $key = empty($key) ? $rjwtConfig['secret_key'] : $key;
        if (!self::verifySignature($token, $key, $alg)) {
            throw new \Exception('Invalid token signature');
        }
        return json_decode($decoded_payload, true);
    }

    /**
     * @Description Verify the token generated by jwt and return the valid content
     * @zh-cn: 验证jwt生成的token并返回有效内容
     * @DateTime 2023-04-23
     * @param string $token
     * @param string $key
     * @param string $alg
     * @return array
     */
    public static function verifyToken(string $token, string $key = '', string $alg = '')
    {
        // Verify the access token and return the payload
        $rjwtConfig = Config::get('src.rjwt');
        $alg = empty($alg) ? $rjwtConfig['default_alg'] : $alg;
        $key = empty($key) ? $rjwtConfig['secret_key'] : $key;
        $payload = self::verify($token, $key, $alg);
        return $payload;
    }

    /**
     * @Description Verify the refresh token generated by jwt and return the valid content
     * @zh-cn: 验证jwt生成的refresh token并返回有效内容
     * @DateTime 2023-04-23
     * @param string $token
     * @param string $refresh_key
     * @param string $alg
     * @return void
     */
    public static function verifyRefreshToken(string $token, string $refresh_key = '', string $alg = '')
    {
        // Verify the access token and return the payload
        $rjwtConfig = Config::get('src.rjwt');
        $alg = empty($alg) ? $rjwtConfig['default_alg'] : $alg;
        $refresh_key = empty($refresh_key) ? $rjwtConfig['refresh_key'] : $refresh_key;
        $payload = self::verify($token, $refresh_key, $alg);
        return $payload;
    }

    private static function verify($token, $key, $alg)
    {
        $decoded = self::decode($token, $key, $alg);
        if (!$decoded) {
            throw new \Exception('Invalid token');
        }
        if ($decoded && isset($decoded['exp']) && $decoded['exp'] < time()) {
            throw new \Exception('Token has expired');
        }

        if ($decoded && isset($decoded['jti']) && self::is_blacklisted($decoded['jti'])) {
            throw new \Exception('Token has been revoked');
        }
        return $decoded;
    }

    private static function generateRefreshToken($alg, $header, $payload, $refresh_key, $refreshExpireTime)
    {
        unset($payload['exp']);
        $payload["exp"] = time() + $refreshExpireTime;

        $jwt = self::sign($alg, $header, $payload, $refresh_key);

        return $jwt;
    }

    private static function sign($alg, $header, $payload,  $key)
    {
        $header = json_encode($header);
        $payload = json_encode($payload);
        $base64UrlHeader = self::base64UrlEncode($header);
        $base64UrlPayload = self::base64UrlEncode($payload);

        switch ($alg) {
            case self::ALG_HS256:
                $hash = 'sha256';
                break;
            case self::ALG_HS384:
                $hash = 'sha384';
                break;
            case self::ALG_HS512:
                $hash = 'sha512';
                break;
                // handle other algorithms here
            default:
                $hash = self::getSupportedAlgorithm($alg);
        }
        $signature = hash_hmac($hash, $base64UrlHeader . '.' . $base64UrlPayload, $key, true);
        $base64UrlSignature = self::base64UrlEncode($signature);
        return "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";
    }

    private static function is_blacklisted($jti)
    {
        $blacklistConfig = Config::get('src.rjwt.blacklist');
        $blacklist = empty(self::$blacklist) ? $blacklistConfig : self::$blacklist;
        return in_array($jti, $blacklist);
    }

    // Token encryption
    private static function base64UrlEncode($data)
    {
        $base64 = base64_encode($data);
        if ($base64 === false) {
            return false;
        }
        $base64Url = strtr($base64, "+/", "-_");
        return rtrim($base64Url, "=");
    }

    private static function base64UrlDecode($base64Url)
    {
        $base64 = strtr($base64Url, "-_", "+/");
        $decoded = base64_decode($base64, true);
        if ($decoded === false) {
            return false;
        }
        return $decoded;
    }

    private static function verifySignature(string $jwt, string $key, string $alg)
    {
        $segments = explode('.', $jwt);
        if (count($segments) !== 3) {
            throw new \Exception('Invalid JWT');
        }
        list($headerEncoded, $payloadEncoded, $signatureEncoded) = $segments;

        $algorithm = self::getSupportedAlgorithm($alg);

        $signature = self::base64UrlDecode($signatureEncoded);
        if ($signature === false) {
            throw new \Exception('Invalid JWT signature');
        }

        $data = $headerEncoded . '.' . $payloadEncoded;
        $verified = false;

        switch ($algorithm) {
            case 'HS256':
                $verified = hash_hmac('sha256', $data, $key, true) === $signature;
                break;
            case 'HS384':
                $verified = hash_hmac('sha384', $data, $key, true) === $signature;
                break;
            case 'HS512':
                $verified = hash_hmac('sha512', $data, $key, true) === $signature;
                break;
                // case 'RS256':
                //     $publicKey = openssl_get_publickey($key);
                //     $verified = openssl_verify($data, $signature, $publicKey, OPENSSL_ALGO_SHA256);
                //     break;
                // case 'RS384':
                //     $publicKey = openssl_get_publickey($key);
                //     $verified = openssl_verify($data, $signature, $publicKey, OPENSSL_ALGO_SHA384);
                //     break;
                // case 'RS512':
                //     $publicKey = openssl_get_publickey($key);
                //     $verified = openssl_verify($data, $signature, $publicKey, OPENSSL_ALGO_SHA512);
                //     break;
                // case 'ES256':
                //     $publicKey = openssl_get_publickey($key);
                //     $verified = openssl_verify($data, $signature, $publicKey, OPENSSL_ALGO_SHA256);
                //     break;
                // case 'ES384':
                //     $publicKey = openssl_get_publickey($key);
                //     $verified = openssl_verify($data, $signature, $publicKey, OPENSSL_ALGO_SHA384);
                //     break;
                // case 'ES512':
                //     $publicKey = openssl_get_publickey($key);
                //     $verified = openssl_verify($data, $signature, $publicKey, OPENSSL_ALGO_SHA512);
                //     break;
        }

        return $verified;
    }

    private static function getSupportedAlgorithm($alg)
    {
        if (in_array($alg, self::$allowed_alg)) {
            return $alg;
        } else {
            throw new \Exception('Unsupported algorithm');
        }
    }
}
