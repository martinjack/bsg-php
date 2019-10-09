<?php

namespace BSG;

use BSG\Services\HLRApiClient;
use BSG\Services\SmsApiClient;
use BSG\Services\ViberApiClient;

class BSG
{
    /**
     *
     * API KEY
     *
     * @var STRING
     *
     */
    private $apiKey;
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
     * @var STRING
     *
     */
    private $tariff;
    /**
     *
     * VIBER SENDER
     *
     * @var STIRNG
     *
     */
    private $viberSender;
    /**
     *
     * API SOURCE
     *
     * @var STRING
     *
     */
    private $apiSource;
    /**
     *
     * INIT
     *
     * @param STRING $apiKey
     *
     * @param STRING $sender
     *
     * @param STRING $viberSender
     *
     * @param INTEGER $tariff
     *
     * @param STRING $apiSource
     *
     */
    public function __construct($apiKey, $sender = null, $viberSender = null, $tariff = null, $apiSource = null)
    {

        $this->apiKey      = $apiKey;
        $this->sender      = $sender;
        $this->tariff      = $tariff;
        $this->viberSender = $viberSender;
        $this->apiSource   = $apiSource;

    }
    /**
     *
     * GET SMS CLIENT
     *
     * @return SmsApiClient
     *
     */
    public function getSmsClient()
    {

        return new SmsApiClient($this->apiKey, $this->sender, $this->tariff, $this->apiSource);

    }
    /**
     *
     * GET HLRC CLIENT
     *
     * @return HLRApiClient
     *
     */
    public function getHLRClient()
    {

        return new HLRApiClient($this->apiKey, $this->tariff, $this->apiSource);

    }
    /**
     *
     * GET VIBER CLIENT
     *
     * @return ViberApiClient
     *
     */
    public function getViberClient()
    {

        return new ViberApiClient($this->apiKey, $this->viberSender, $this->apiSource);

    }

}
