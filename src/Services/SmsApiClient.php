<?php

namespace BSG\Services;

use BSG\Services\ApiClient;
use Exception;

class SmsApiClient extends ApiClient
{
    /**
     *
     * SENDER
     *
     * @var STRING
     *
     */
    private $sender;
    /**
     *
     * TARIFF
     *
     * @var INTEGER
     *
     */
    private $tariff;
    /**
     *
     * INIT
     *
     * @param STRING $api_key
     *
     * @param STRING $sender
     *
     * @param INTEGER $tariff
     *
     * @param STRING $source
     *
     */
    public function __construct($api_key, $sender, $tariff = null, $source = null)
    {

        $this->sender = $sender;

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

        $result = json_decode($resp, true);

        return $result;

    }
    /**
     *
     * GET STATUS BY REFERENCE
     *
     * @param $reference
     *
     */
    public function getStatusByReference($reference)
    {

        return $this->getStatus('sms/reference/' . $reference);

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

        return $this->getStatus('sms/' . $message_id);

    }
    /**
     *
     * GET TASK STATUS
     *
     * @param INTEGER $task_id
     *
     */
    public function getTaskStatus($task_id)
    {

        try {

            $resp = $this->sendRequest('sms/task/' . $task_id);

        } catch (Exception $e) {

            $error = 'Request failed (code: ' . $e->getCode() . '): ' . $e->getMessage();

            return ['error' => $error];

        }

        $result = json_decode($resp, true);

        return $result;

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

            $resp = $this->sendRequest('sms/prices' . ($tariff !== null ? ('/' . $tariff) : ''));

        } catch (Exception $e) {

            $error = 'Request failed (code: ' . $e->getCode() . '): ' . $e->getMessage();

            return ['error' => $error];

        }

        $result = json_decode($resp, true);

        return $result;

    }
    /**
     *
     * GET PRICE
     *
     * @param $msisdn
     *
     * @param $originator
     *
     * @param $body
     *
     * @param $reference
     *
     * @param INTEGER $validity
     *
     * @param INTEGER $tariff
     *
     */
    public function getPrice($msisdn, $originator, $body, $reference, $validity = 72, $tariff = null)
    {

        $originator = $originator ?: $this->sender;

        $tariff = $tariff ?: $this->tariff;

        return $this->sendSms($msisdn, $body, $reference, $validity, $tariff, $originator, true);

    }
    /**
     *
     * SEND SMS
     *
     * @param $msisdn
     *
     * @param $body
     *
     * @param $reference
     *
     * @param INTEGER $validity
     *
     * @param INTEGER $tariff
     *
     * @param NULL $originator
     *
     * @param BOOLEAN $only_price
     *
     */
    public function sendSms($msisdn, $body, $reference, $validity = 72, $tariff = null, $originator = null, $only_price = false)
    {

        $originator             = $originator ?: $this->sender;
        $tariff                 = $tariff ?: $this->tariff;
        $message                = [];
        $message['destination'] = 'phone';
        $message['msisdn']      = $msisdn;
        $message['originator']  = $originator;
        $message['body']        = $body;
        $message['reference']   = $reference;
        $message['validity']    = $validity;

        if ($tariff !== null) {

            $message['tariff'] = $tariff;

        }

        $endpoint = $only_price ? 'sms/price' : 'sms/create';

        try {

            $resp = $this->sendRequest($endpoint, json_encode($message), 'PUT');

        } catch (Exception $e) {

            $error = 'Request failed (code: ' . $e->getCode() . '): ' . $e->getMessage();

            return ['error' => $error];

        }

        $result = json_decode($resp, true);

        return $result;

    }
    /**
     *
     * GET TASK PRICE
     *
     * @param $msisdns
     *
     * @param $body
     *
     * @param INTEGER $validity
     *
     * @param INTEGER $tariff
     *
     * @param NULL $originator
     *
     */
    public function getTaskPrice($msisdns, $body, $validity = 72, $tariff = null, $originator = null)
    {

        return $this->sendTask($msisdns, $body, $validity, $tariff, $originator, true);

    }
    /**
     *
     * SEND TASK
     *
     * @param $msisdns
     *
     * @param $originator
     *
     * @param $body
     *
     * @param INTEGER $validity
     *
     * @param NULL $tariff
     *
     * @param $only_price
     *
     * @return ARRAY
     *
     */
    public function sendTask($msisdns, $body, $validity = 72, $tariff = null, $originator = null, $only_price = false)
    {

        $originator             = $originator ?: $this->sender;
        $message                = [];
        $message['destination'] = 'phones';
        $message['phones']      = $msisdns;
        $message['originator']  = $originator;
        $message['body']        = $body;
        $message['validity']    = $validity;

        if ($tariff !== null) {

            $message['tariff'] = $tariff;

        }

        $endpoint = $only_price ? 'sms/price' : 'sms/create';

        try {

            $resp = $this->sendRequest($endpoint, json_encode($message), 'PUT');

        } catch (Exception $e) {

            $error = 'Request failed (code: ' . $e->getCode() . '): ' . $e->getMessage();

            return ['error' => $error];

        }

        $result = json_decode($resp, true);

        return $result;

    }
    /**
     *
     * GET MULTI PRICE
     *
     * @param ARRAY $messages
     *
     * @param INTEGER $validity
     *
     * @param INTEGER $tariff
     *
     */
    public function getMultiPrice($messages, $validity = 72, $tariff = null)
    {

        return $this->sendSmsMulti($messages, $validity, $tariff, true);

    }
    /**
     *
     * SEND SMS MULTI
     *
     * @param ARRAY $messages
     *
     * @param INTEGER $validity
     *
     * @param NULL $tariff
     *
     * @param BOOLEAN $only_price
     *
     * @return ARRAY
     *
     */
    public function sendSmsMulti($messages, $validity = 72, $tariff = null, $only_price = false)
    {

        foreach ($messages as &$msg) {

            if (!isset($msg['originator']) && $this->sender) {

                $msg['originator'] = $this->sender;

            }

        }

        $message                = [];
        $message['destination'] = 'individual';
        $message['phones']      = $messages;
        $message['validity']    = $validity;

        if ($tariff !== null) {

            $message['tariff'] = $tariff;

        }

        $endpoint = $only_price ? 'sms/price' : 'sms/create';

        try {

            $resp = $this->sendRequest($endpoint, json_encode($message), 'PUT');

        } catch (Exception $e) {

            $error = 'Request failed (code: ' . $e->getCode() . '): ' . $e->getMessage();

            return ['error' => $error];

        }

        $result = json_decode($resp, true);

        return $result;

    }
}
