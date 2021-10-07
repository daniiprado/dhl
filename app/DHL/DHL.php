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

    public function getShipmentAsPdf($shipmentTrackingNumber, array $queryParams = [])
    {
        $response = $this->client->request([
            'Accept: application/json',
        ], true)->get("shipments/$shipmentTrackingNumber/proof-of-delivery", $queryParams);

        $data = isset($response->documents[0]->content)
            ? base64_decode($response->documents[0]->content)
            : base64_decode("JVBERi0xLjYNCiXi48/TDQo0IDAgb2JqDQo8PA0KL0xpbmVhcml6ZWQgMQ0KL0wgNTExNw0KL0ggWyA4NTAgMTM4IF0NCi9PIDYNCi9FIDQ1NTQNCi9OIDENCi9UIDQ5MTENCj4+DQplbmRvYmoNCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICANCnhyZWYNCjQgMTINCjAwMDAwMDAwMTcgMDAwMDAgbg0KMDAwMDAwMDcwNyAwMDAwMCBuDQowMDAwMDAwOTg4IDAwMDAwIG4NCjAwMDAwMDEzNjMgMDAwMDAgbg0KMDAwMDAwMTQ0NSAwMDAwMCBuDQowMDAwMDAxNDgxIDAwMDAwIG4NCjAwMDAwMDE1NzggMDAwMDAgbg0KMDAwMDAwMTcxNSAwMDAwMCBuDQowMDAwMDAyMDM2IDAwMDAwIG4NCjAwMDAwMDIzNDUgMDAwMDAgbg0KMDAwMDAwMzc0NiAwMDAwMCBuDQowMDAwMDAwODUwIDAwMDAwIG4NCnRyYWlsZXINCjw8DQovU2l6ZSAxNg0KL1ByZXYgNDkwMQ0KL0luZm8gMiAwIFINCi9Sb290IDUgMCBSDQovSUQgWzw3MjQyZjFmOWMzYjQyMTE2ZGIyYTQ5NzgxZGU5NGRiZT48NzI0MmYxZjljM2I0MjExNmRiMmE0OTc4MWRlOTRkYmU+XQ0KPj4NCnN0YXJ0eHJlZg0KMA0KJSVFT0YNCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgDQo1IDAgb2JqDQo8PA0KL1R5cGUgL0NhdGFsb2cNCi9QYWdlcyAxIDAgUg0KL09DUHJvcGVydGllcyA8PCAvRCA8PCAvT04gWyA3IDAgUiBdIC9PcmRlciAzIDAgUiAvUkJHcm91cHMgWyBdID4+IC9PQ0dzIFsNCjcgMCBSIF0gPj4NCj4+DQplbmRvYmoNCjE1IDAgb2JqDQo8PA0KL1MgMzYNCi9GaWx0ZXIgL0ZsYXRlRGVjb2RlDQovTGVuZ3Ro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vz3sh/dv7g7XX2h413oMH6dMw93/uO8Gs7vfmr9JS3tBDQplbmRzdHJlYW0NCmVuZG9iag0KMSAwIG9iag0KPDwNCi9UeXBlIC9QYWdlcw0KL0tpZHMgWyA2IDAgUiBdDQovQ291bnQgMQ0KPj4NCmVuZG9iag0KMiAwIG9iag0KPDwNCi9DcmVhdGlvbkRhdGUgKEQ6MjAyMTA1MTExNjMwMzMtMDUnMDAnKQ0KL0NyZWF0b3IgKEFkb2JlIElsbHVzdHJhdG9yIDI1LjAgXChNYWNpbnRvc2hcKSkNCi9UaXRsZSAoTk9UX0ZPVU5EKQ0KL01vZERhdGUgKEQ6MjAyMTA1MTEyMTM0MjVaKQ0KL1Byb2R1Y2VyDQooMy1IZWlnaHRzXDIyMiBQREYgT3B0aW1pemF0aW9uIFNoZWxsIDYuMy4xLjUgXChodHRwOi8vd3d3LnBkZi10b29scy5jb21cKSkNCj4+DQplbmRvYmoNCjMgMCBvYmoNClsgNyAwIFIgXQ0KZW5kb2JqDQp4cmVmDQowIDQNCjAwMDAwMDAwMDAgNjU1MzUgZg0KMDAwMDAwNDU1NCAwMDAwMCBuDQowMDAwMDA0NjIwIDAwMDAwIG4NCjAwMDAwMDQ4NzMgMDAwMDAgbg0KdHJhaWxlcg0KPDwNCi9TaXplIDQNCi9JRCBbPDcyNDJmMWY5YzNiNDIxMTZkYjJhNDk3ODFkZTk0ZGJlPjw3MjQyZjFmOWMzYjQyMTE2ZGIyYTQ5NzgxZGU5NGRiZT5dDQo+Pg0Kc3RhcnR4cmVmDQoxNzgNCiUlRU9GDQo=");
        header('Content-Type: application/pdf');
        return $data;
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

    public function getTracking($shipmentTrackingNumber, array $queryParams = [])
    {
        return response()->json(
            $this->client->request([
                'Accept: application/json',
            ], true)->get("shipments/$shipmentTrackingNumber/tracking", $queryParams),
            $this->getAdditionalData(),
            $this->client->request()->getHttpStatusCode()
        );
    }
}