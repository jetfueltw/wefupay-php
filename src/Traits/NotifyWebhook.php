<?php

namespace Jetfuel\Wefupay\Traits;

use Jetfuel\Wefupay\Signature;

trait NotifyWebhook
{
    /**
     * Verify notify request's signature.
     *
     * @param array $payload
     * @param string $publicKey
     * @return bool
     */
    public function verifyNotifyPayload(array $payload, $publicKey)
    {
        if (!isset($payload['sign'])) {
            return false;
        }

        $signature = $payload['sign'];

        unset($payload['sign']);
        unset($payload['sign_type']);

        return Signature::validate($payload, $publicKey, $signature);
    }

    /**
     * Verify notify request's signature and parse payload.
     *
     * @param array $payload
     * @param string $publicKey
     * @return array|null
     */
    public function parseNotifyPayload(array $payload, $publicKey)
    {
        if (!$this->verifyNotifyPayload($payload, $publicKey)) {
            return null;
        }

        return $payload;
    }

    /**
     * Response content for successful notify.
     *
     * @return string
     */
    public function successNotifyResponse()
    {
        return 'SUCCESS';
    }
}
