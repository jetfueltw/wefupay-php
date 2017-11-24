<?php

namespace Jetfuel\Wefupay;

use Jetfuel\Wefupay\Traits\ResultParser;

class DigitalPayment extends Payment
{
    use ResultParser;

    const BASE_API_URL = 'https://api.wefupay.com/';
    const API_VERSION  = 'V3.1';
    const PRODUCT_NAME = 'PRODUCT_NAME';

    /**
     * DigitalPayment constructor.
     *
     * @param string $merchantId
     * @param string $privateKey
     * @param null|string $baseApiUrl
     */
    public function __construct($merchantId, $privateKey, $baseApiUrl = null)
    {
        $baseApiUrl = $baseApiUrl === null ? self::BASE_API_URL : $baseApiUrl;

        parent::__construct($merchantId, $privateKey, $baseApiUrl);
    }

    /**
     * Create digital payment order.
     *
     * @param string $tradeNo
     * @param string $channel
     * @param float $amount
     * @param string $clientIp
     * @param string $notifyUrl
     * @return array
     */
    public function order($tradeNo, $channel, $amount, $clientIp, $notifyUrl)
    {
        $payload = $this->signPayload([
            'order_no'          => $tradeNo,
            'service_type'      => $channel,
            'order_amount'      => $amount,
            'order_time'        => $this->getCurrentTime(),
            'client_ip'         => $clientIp,
            'notify_url'        => $notifyUrl,
            'interface_version' => self::API_VERSION,
            'product_name'      => self::PRODUCT_NAME,
        ]);

        return $this->parseResponse($this->httpClient->post('gateway/api/scanpay', $payload));
    }
}
