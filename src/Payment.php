<?php

namespace Jetfuel\Wefupay;

use Jetfuel\Wefupay\HttpClient\GuzzleHttpClient;

class Payment
{
    const BASE_API_URL = 'https://api.wefupay.com/';
    const TIME_ZONE    = 'Asia/Shanghai';
    const TIME_FORMAT  = 'Y-m-d H:i:s';
    const SIGN_TYPE    = 'RSA-S';

    /**
     * @var string
     */
    protected $merchantId;

    /**
     * @var string
     */
    protected $privateKey;

    /**
     * @var string
     */
    protected $baseApiUrl;

    /**
     * @var \Jetfuel\Wefupay\HttpClient\HttpClientInterface
     */
    protected $httpClient;

    /**
     * Payment constructor.
     *
     * @param string $merchantId
     * @param string $privateKey
     * @param string $baseApiUrl
     */
    protected function __construct($merchantId, $privateKey, $baseApiUrl = null)
    {
        $this->merchantId = $merchantId;
        $this->privateKey = $privateKey;
        $this->baseApiUrl = $baseApiUrl === null ? self::BASE_API_URL : $baseApiUrl;

        $this->httpClient = new GuzzleHttpClient($this->baseApiUrl);
    }

    /**
     * Sign request payload.
     *
     * @param array $payload
     * @return array
     */
    protected function signPayload(array $payload)
    {
        $payload['merchant_code'] = $this->merchantId;
        $payload['sign'] = Signature::generate($payload, $this->privateKey);
        $payload['sign_type'] = self::SIGN_TYPE;

        return $payload;
    }

    /**
     * Get current time.
     *
     * @return string
     */
    protected function getCurrentTime()
    {
        return (new \DateTime('now', new \DateTimeZone(self::TIME_ZONE)))->format(self::TIME_FORMAT);
    }
}
