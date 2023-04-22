<?php

namespace src\rjwt\Core;

class JWTV2
{
    private static $key = "example_key";
    private static $refreshKey = "example_refresh_key";
    private static $expireTime = 3600;
    private static $refreshExpireTime = 86400;

    public static function encode($data)
    {
        $payload = [
            "iat" => time(),
            "exp" => time() + self::$expireTime,
            "data" => $data
        ];

        $jwt = JWTV2::sign($payload, self::$key);

        $refreshToken = JWTV2::generateRefreshToken($payload);

        return [
            "access_token" => $jwt,
            "refresh_token" => $refreshToken
        ];
    }

    private static function sign($payload, $key)
    {
        $header = json_encode(["typ" => "JWT", "alg" => "HS256"]);
        $base64UrlHeader = JWTV2::base64UrlEncode($header);
        $base64UrlPayload = JWTV2::base64UrlEncode(json_encode($payload));
        $signature = hash_hmac("sha256", "$base64UrlHeader.$base64UrlPayload", $key, true);
        $base64UrlSignature = JWTV2::base64UrlEncode($signature);
        return "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";
    }

    private static function generateRefreshToken($payload)
    {
        unset($payload['exp']);
        $payload["exp"] = time() + self::$refreshExpireTime;

        $jwt = JWTV2::sign($payload, self::$refreshKey);

        return $jwt;
    }

    public static function decode($token, $key, $allowed_algs = array())
    {
        $parts = explode('.', $token);
        if (count($parts) != 3) {
            throw new \Exception('Invalid token format');
        }
        list($header, $payload, $signature) = $parts;

        $decoded_header = JWTV2::base64UrlDecode($header);
        if (!$decoded_header) {
            throw new \Exception('Invalid token header');
        }

        $algorithm = json_decode($decoded_header, true)['alg'];
        if (!in_array($algorithm, $allowed_algs)) {
            throw new \Exception('Algorithm not allowed');
        }

        $decoded_payload = JWTV2::base64UrlDecode($payload);
        if (!$decoded_payload) {
            throw new \Exception('Invalid token payload');
        }

        if (!JWTV2::verifySignature($token, $key, $algorithm)) {
            throw new \Exception('Invalid token signature');
        }

        return json_decode($decoded_payload);
    }


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

    public static function verify($token, $key)
    {
        try {
            $decoded = JWTV2::decode($token, $key, array('HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function verifyAccessToken($token)
    {
        // Verify the access token and return the payload
        $payload = self::verify($token, self::$key);
        if ($payload && isset($payload['exp']) && time() < $payload['exp']) {
            return $payload;
        }
        return false;
    }

    public static function verifyRefreshToken($token)
    {
        // Verify the refresh token and return the payload
        $payload = self::verify($token, self::$refreshKey);
        if ($payload && isset($payload['exp']) && time() < $payload['exp']) {
            return $payload;
        }
        return false;
    }

    public static function verifySignature(string $jwt, string $key, string $alg): bool
    {
        $segments = explode('.', $jwt);
        if (count($segments) !== 3) {
            throw new \Exception('Invalid JWT');
        }
        list($headerEncoded, $payloadEncoded, $signatureEncoded) = $segments;

        $algorithm = self::getSupportedAlgorithm($alg);

        $signature = self::base64UrlDecode($signatureEncoded);

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
            case 'RS256':
                $publicKey = openssl_get_publickey($key);
                $verified = openssl_verify($data, $signature, $publicKey, OPENSSL_ALGO_SHA256);
                break;
            case 'RS384':
                $publicKey = openssl_get_publickey($key);
                $verified = openssl_verify($data, $signature, $publicKey, OPENSSL_ALGO_SHA384);
                break;
            case 'RS512':
                $publicKey = openssl_get_publickey($key);
                $verified = openssl_verify($data, $signature, $publicKey, OPENSSL_ALGO_SHA512);
                break;
            case 'ES256':
                $publicKey = openssl_get_publickey($key);
                $verified = openssl_verify($data, $signature, $publicKey, OPENSSL_ALGO_SHA256);
                break;
            case 'ES384':
                $publicKey = openssl_get_publickey($key);
                $verified = openssl_verify($data, $signature, $publicKey, OPENSSL_ALGO_SHA384);
                break;
            case 'ES512':
                $publicKey = openssl_get_publickey($key);
                $verified = openssl_verify($data, $signature, $publicKey, OPENSSL_ALGO_SHA512);
                break;
        }

        return $verified;
    }

    private static function getSupportedAlgorithm($alg)
    {
        switch ($alg) {
            case 'HS256':
            case 'HS384':
            case 'HS512':
            case 'RS256':
            case 'RS384':
            case 'RS512':
                return $alg;
            default:
                throw new \Exception('Unsupported algorithm');
        }
    }
}
