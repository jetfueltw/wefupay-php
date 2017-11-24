<?php

namespace Jetfuel\Wefupay;

class Signature
{
    /**
     * Generate signature.
     *
     * @param array $payload
     * @param string $privateKey
     * @return string
     */
    public static function generate(array $payload, $privateKey)
    {
        $baseString = self::buildBaseString($payload);

        return base64_encode(self::rsaSign($baseString, $privateKey));
    }

    /**
     * Validate signature.
     *
     * @param array $payload
     * @param string $publicKey
     * @param string $signature
     * @return bool
     */
    public static function validate(array $payload, $publicKey, $signature)
    {
        $baseString = self::buildBaseString($payload);

        return self::rsaVerify($baseString, $publicKey, $signature);
    }

    private static function buildBaseString(array $payload)
    {
        ksort($payload);

        $baseString = '';
        foreach ($payload as $key => $value) {
            $baseString .= $key.'='.$value.'&';
        }

        return rtrim($baseString, '&');
    }

    private static function rsaSign($baseString, $privateKey)
    {
        $privateKey = openssl_get_privatekey($privateKey);

        openssl_sign($baseString, $signature, $privateKey, OPENSSL_ALGO_MD5);

        return $signature;
    }

    private static function rsaVerify($baseString, $publicKey, $signature)
    {
        $signature = base64_decode($signature);

        $publicKey = openssl_get_publickey($publicKey);

        return openssl_verify($baseString, $signature, $publicKey, OPENSSL_ALGO_MD5) > 0;
    }
}
