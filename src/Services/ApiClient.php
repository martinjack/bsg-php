<?php

namespace BSG\Services;

use Exception;

class ApiClient
{
    /**
     *
     */
    const API_URL = 'https://api.bsg.hk/v1.0/';
    /**
     *
     * API URL
     *
     * @var STRING
     *
     */
    protected $api_url;
    /**
     *
     * API KEY
     *
     * @var STRING
     *
     */
    protected $api_key;
    /**
     *
     * LOGGER
     *
     * @var
     *
     */
    protected $logger;
    /**
     *
     * INIT
     *
     * @param STRING $api_key
     *
     * @param STRING $api_source
     *
     */
    public function __construct($api_key, $api_source = null)
    {

        $this->api_key = $api_key;

        if (!$api_source) {

            $this->api_source = 'BSG PHP Library';

        } else {

            $this->api_source = $api_source;

        }

    }
    /**
     *
     * SEND REQUEST
     *
     * @param STRING $resource_url
     *
     * @param NULL|STRING|ARRAY $post_data
     *
     * @param NULL $custom_request
     *
     * @return mixed
     *
     * @throws \Exception
     *
     */
    public function sendRequest($resource_url, $post_data = null, $custom_request = null)
    {

        $client = curl_init();

        if ($post_data === null || !is_array($post_data)) {

            curl_setopt($client, CURLOPT_HTTPHEADER, array('X-API-KEY: ' . $this->api_key, 'X-API-SOURCE: ' . $this->api_source, 'Content-type: text/json; charset=utf-8'));

        } else {

            curl_setopt($client, CURLOPT_HTTPHEADER, array('X-API-KEY: ' . $this->api_key, 'X-API-SOURCE: ' . $this->api_source));
        }

        curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($client, CURLOPT_FOLLOWLOCATION, false);

        if ($custom_request !== null) {

            curl_setopt($client, CURLOPT_CUSTOMREQUEST, $custom_request);

        }

        curl_setopt($client, CURLOPT_URL, self::API_URL . $resource_url);

        if ($post_data !== null and $custom_request === null) {

            curl_setopt($client, CURLOPT_POST, true);

        }

        if ($post_data !== null) {

            curl_setopt($client, CURLOPT_POSTFIELDS, $post_data);

        }

        $result = curl_exec($client);

        if (!$result) {

            throw new Exception(curl_error($client), curl_errno($client));

        } else {

            return $result;

        }

    }
    /**
     *
     * ADD LOG
     *
     * @param STRING $message
     *
     */
    public function addLog($message)
    {}
    /**
     *
     * GET BALANCE
     *
     */
    public function getBalance()
    {

        try {

            $resp = $this->sendRequest('common/balance');

        } catch (Exception $e) {

            $error = 'Request failed (code: ' . $e->getCode() . '): ' . $e->getMessage();

            $this->addLog($error);

            throw new Exception($error, -1);

        }

        $result = json_decode($resp, true);

        return $result;

    }
}
