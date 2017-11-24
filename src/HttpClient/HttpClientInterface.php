<?php

namespace Jetfuel\Wefupay\HttpClient;

interface HttpClientInterface
{
    /**
     * HttpClientInterface constructor.
     *
     * @param string $baseUrl
     * @param null|string $httpReferer
     */
    public function __construct($baseUrl, $httpReferer);

    /**
     * POST request.
     *
     * @param string $uri
     * @param array $data
     * @return string
     */
    public function post($uri, array $data);
}
