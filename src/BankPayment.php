<?php

namespace Jetfuel\Wefupay;

class BankPayment extends Payment
{
    const BASE_API_URL = 'https://pay.wefupay.com/';
    const API_VERSION  = 'V3.0';
    const SERVICE_TYPE = 'direct_pay';
    const PRODUCT_NAME = 'PRODUCT_NAME';
    const CHARSET      = 'UTF-8';

    /**
     * BankPayment constructor.
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
     * Create bank payment order.
     *
     * @param string $tradeNo
     * @param string $bank
     * @param float $amount
     * @param string $notifyUrl
     * @param null|string $returnUrl
     * @return string
     */
    public function order($tradeNo, $bank, $amount, $notifyUrl, $returnUrl = null)
    {
        $payload = $this->signPayload([
            'order_no'          => $tradeNo,
            'service_type'      => self::SERVICE_TYPE,
            'order_amount'      => $amount,
            'order_time'        => $this->getCurrentTime(),
            'notify_url'        => $notifyUrl,
            'interface_version' => self::API_VERSION,
            'product_name'      => self::PRODUCT_NAME,
            'input_charset'     => self::CHARSET,
            'bank_code'         => $bank,
        ]);

        if ($returnUrl !== null) {
            $payload['returnUrl'] = $returnUrl;
        }

        return $this->httpClient->post('gateway', $payload);
    }
}
