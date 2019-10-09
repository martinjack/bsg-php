<?php

require 'vendor/autoload.php';

use BSG\BSG;

$api = new BSG('API_KEY', 'NAME_SENDER');

$client = $api->getSmsClient();

print_r(

    $client->sendSms(

        'phone',

        'text',

        'successSend' . (string) time()

    )

);
