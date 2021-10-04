<?php

namespace App\DHL;

use App\Curl\Curl;
use App\Helpers\HttpClient;

class DHL
{
    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $endpoint = env('APP_ENV') == 'local'
            ? env('DHL_ENDPOINT_TEST')
            : env('DHL_ENDPOINT_PROD');
        $this->client = new HttpClient($endpoint);
    }

    /**
     * @param $shipmentTrackingNumber
     * @param array $queryParams
     * @return Curl
     */
    public function getShipment($shipmentTrackingNumber, array $queryParams = [])
    {
        return $this->client->request([
            'Accept: application/json',
        ])->get("shipments/$shipmentTrackingNumber/proof-of-delivery", $queryParams);
    }

    /**
     * @param array $bodyRequest
     * @return Curl
     */
    public function createShipment(array $bodyRequest = [])
    {
        return $this->client->request([
           'Accept: application/json',
        ])->post('shipments', $bodyRequest);
    }
}