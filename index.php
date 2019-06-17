<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'vendor/autoload.php';

$cache = new CachePrintFile(dirname(__FILE__).'/cache'); 
$connectPSR = new ConnectPSR($cache, 300);

$connectPSR->connect('https://api.printful.com/', '77qn9aax-qrrm-idki:lnh0-fm2nhmp0yca7');
$request_data = [
        "recipient"=> [
                "address1" => "​11025 Westlake Dr, Charlotte, North Carolina, 28273",
                "country_code" => "US",
        ],
        "items"=> [
                [
                    "quantity" => 2,
                    "variant_id" => 7679
                ]
        ],
    ];
$connectPSR->requestPost('/shipping/rates', $request_data);

$connectPSR->statusRequest();