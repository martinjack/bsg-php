<?php

namespace BSG\Services;

use BSG\Services\ApiClient;
use Exception;

class ViberApiClient extends ApiClient
{

    /**
     *
     * MESSAGES
     *
     * @var ARRAY
     *
     */
    protected $messages = [];
    /**
     *
     * SENDER
     *
     * @var STRING
     *
     */
    protected $sender;
    /**
     *
     * INIT
     *
     * @param STRING $api_key
     *
     * @param STRING $sender
     *
     * @param STRING $source
     *
     */
    public function __construct($api_key, $sender, $source = null)
    {

        $this->sender = $sender;

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

        } catch (\Exception $e) {

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

        return $this->getStatus('viber/reference/' . $reference);

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

        return $this->getStatus('viber/' . $message_id);

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

            $resp = $this->sendRequest('viber/prices' . ($tariff !== null ? ('/' . $tariff) : ''));

        } catch (\Exception $e) {

            $error = 'Request failed (code: ' . $e->getCode() . '): ' . $e->getMessage();

            return ['error' => $error];

        }

        $result = json_decode($resp, true);

        return $result;

    }
    /**
     *
     * CLEAN MESSAGES
     *
     * @return VOID
     *
     */
    public function clearMessages(): void
    {
        $this->messages = [];
    }
    /**
     * ADD MESSAGE
     *
     * @param $to
     *
     * @param $text
     *
     * @param $alpha_name
     *
     * @param ARRAY $viber_options
     *
     * @param BOOLEAN $is_promotional
     *
     * @param STRING $callback_url
     *
     */
    public function addMessage($to, $text, $viber_options = [], $alpha_name = null, $is_promotional = true, $callback_url = '')
    {
        $alpha_name            = $alpha_name ?: $this->sender;
        $message               = [];
        $message['to']         = $to;
        $message['text']       = $text;
        $message['alpha_name'] = $alpha_name;

        if (!$is_promotional) {

            $message['is_promotional'] = $is_promotional;

        }

        if ($callback_url != '') {

            $message['callback_url'] = $callback_url;

        }

        if (count($viber_options) > 0) {

            $message['options']['viber'] = $viber_options;

        }

        $this->messages[] = $message;

    }
    /**
     *
     * GET MESSAGES PRICE
     *
     * @param INTEGER $validity
     *
     * @param INTEGER $tariff
     *
     */
    public function getMessagesPrice($validity = 86400, $tariff = null)
    {

        return $this->sendMessages($validity, $tariff, true);

    }
    /**
     *
     * SEND MESSAGES
     *
     * @param INTEGER $validity
     *
     * @param NULL $tariff
     *
     * @param BOOLEAN $only_price
     *
     * @return mixed
     *
     */
    public function sendMessages($validity = 86400, $tariff = null, $only_price = false)
    {
        if (count($this->messages) == 0) {

            return ['error' => 'No messages to send'];

        }

        $message = [];

        $message['validity'] = $validity;

        if ($tariff !== null) {

            $message['tariff'] = $tariff;

        }

        $message['messages'] = $this->messages;

        $endpoint = $only_price ? 'viber/price' : 'viber/create';

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
