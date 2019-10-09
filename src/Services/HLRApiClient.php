<?php

namespace BSG\Services;

use BSG\Services\ApiClient;
use Exception;

class HLRApiClient extends ApiClient
{
    /**
     *
     * TARIFF
     *
     * @var INTEGER
     *
     */
    protected $tariff;
    /**
     *
     * INIT
     *
     * @param STRING $api_key
     *
     * @param INTEGER $tariff
     *
     * @param STRING $source
     *
     */
    public function __construct($api_key, $tariff = null, $source = null)
    {

        $this->tariff = $tariff;

        parent::__construct($api_key, $source);

    }
    /**
     *
     * GET STATUS
     *
     * @param $endpoint
     *
     */
    private function getStatus($endpoint)
    {

        try {

            $resp = $this->sendRequest($endpoint);

        } catch (Exception $e) {

            $error = 'Request failed (code: ' . $e->getCode() . '): ' . $e->getMessage();

            return ['error' => $error];

        }

        return json_decode($resp, true);

    }
    /**
     *
     * GET STATUS REFERENCE
     *
     * @param $reference
     *
     */
    public function getStatusByReference($reference)
    {

        return $this->getStatus('hlr/reference/' . $reference);

    }
    /**
     *
     * GET STATUS BY ID
     *
     * @param INTEGER $message_id
     *
     */
    public function getStatusById($message_id)
    {

        return $this->getStatus('hlr/' . $message_id);

    }
    /**
     *
     * GET PRICES
     *
     * @param INTEGER $tariff
     *
     */
    public function getPrices($tariff = null)
    {

        try {

            $resp = $this->sendRequest('hlr/prices' . ($tariff !== null ? ('/' . $tariff) : ''));

        } catch (Exception $e) {

            $error = 'Request failed (code: ' . $e->getCode() . '): ' . $e->getMessage();

            return ['error' => $error];

        }

        $result = json_decode($resp, true);

        return $result;

    }
    /**
     *
     * SEND HLR
     *
     * @param $msisdn
     *
     * @param $reference
     *
     * @param INTEGER $tariff
     *
     */
    public function sendHLR($msisdn, $reference, $tariff = null)
    {

        $tariff                 = $tariff ?: $this->tariff;
        $message                = [];
        $message['destination'] = 'phone';
        $message['msisdn']      = $msisdn;
        $message['reference']   = $reference;

        if ($tariff !== null) {

            $message['tariff'] = $tariff;

        }

        try {

            $resp = $this->sendRequest('hlr/create', $message);

        } catch (Exception $e) {

            $error = 'Request failed (code: ' . $e->getCode() . '): ' . $e->getMessage();

            return ['error' => $error];

        }

        $result = json_decode($resp, true);

        return $result;
    }
    /**
     *
     * SEND HLRS
     *
     * @param ARRAY $payload
     *
     * @return ARRAY
     *
     */
    public function sendHLRs($payload)
    {

        try {

            $resp = $this->sendRequest('hlr/create', json_encode($payload), 'PUT');

        } catch (Exception $e) {

            $error = 'Request failed (code: ' . $e->getCode() . '): ' . $e->getMessage();

            return ['error' => $error];

        }

        $result = json_decode($resp, true);

        return $result;

    }
}
