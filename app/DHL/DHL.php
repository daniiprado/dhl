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
     * @return array
     */
    public function getAdditionalData()
    {
        return [
            'request'   => request()->all(),
            'endpoint' => $this->client->request()->getUrl(),
            'raw_response' => $this->client->request()->getRawResponse(),
            'info' => $this->client->request()->getInfo(),
            'http_status_code' => $this->client->request()->getHttpStatusCode(),
            'error' => $this->client->request()->getErrorCode(),
            'curl_error_code' => $this->client->request()->getCurlErrorCode(),
            'curl_message' => $this->client->request()->getCurlErrorMessage(),
            'response_headers' => $this->client->request()->getResponseHeaders(),
            'request_headers' => $this->client->request()->getRequestHeaders(),
        ];
    }

    /**
     * @param $shipmentTrackingNumber
     * @param array $queryParams
     * @return Curl|false|string
     */
    public function getShipment($shipmentTrackingNumber, array $queryParams = [])
    {
        return response()->json(
            $this->client->request([
                'Accept: application/json',
            ], true)->get("shipments/$shipmentTrackingNumber/proof-of-delivery", $queryParams),
            $this->getAdditionalData(),
            $this->client->request()->getHttpStatusCode()
        );
    }

    /**
     * @param array $bodyRequest
     * @return Curl|false|string
     */
    public function createShipment(array $bodyRequest = [])
    {
        return response()->json(
            $this->client->request([
                'Accept: application/json',
            ], true)->post('shipments', $bodyRequest),
            $this->getAdditionalData(),
            $this->client->request()->getHttpStatusCode()
        );
    }
}