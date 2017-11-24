<?php

namespace Jetfuel\Wefupay\Traits;

trait ResultParser
{
    /**
     * Parse XML format response to array.
     *
     * @param string $response
     * @return array
     */
    public function parseResponse($response)
    {
        $dinpay = new \SimpleXMLElement($response);

        return json_decode(json_encode($dinpay->response[0]), true);
    }
}
