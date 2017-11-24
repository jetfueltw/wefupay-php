<?php

namespace Jetfuel\Wefupay;

use Jetfuel\Wefupay\Traits\ResultParser;

class TradeQuery extends Payment
{
    use ResultParser;

    const BASE_API_URL = 'https://query.wefupay.com/';
    const API_VERSION  = 'V3.0';
    const SERVICE_TYPE = 'single_trade_query';

    /**
     * OrderQuery constructor.
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
     * Find Order by trade number.
     *
     * @param string $tradeNo
     * @return array|null
     */
    public function find($tradeNo)
    {
        $payload = $this->signPayload([
            'order_no'          => $tradeNo,
            'interface_version' => self::API_VERSION,
            'service_type'      => self::SERVICE_TYPE,
        ]);

        $order = $this->parseResponse($this->httpClient->post('query', $payload));

        if ($order['is_success'] !== 'T') {
            return null;
        }

        return $order;
    }

    /**
     * Is order already paid.
     *
     * @param string $tradeNo
     * @return bool
     */
    public function isPaid($tradeNo)
    {
        $order = $this->find($tradeNo);

        if ($order === null || !isset($order['trade']['trade_status']) || $order['trade']['trade_status'] !== 'SUCCESS') {
            return false;
        }

        return true;
    }
}
