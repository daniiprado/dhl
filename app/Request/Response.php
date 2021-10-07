<?php

namespace App\Request;

use DateTime;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;

class Response
{
    /**
     * @param array $data
     * @param array $details
     * @param int $code
     * @return false|string
     */
    public function json($data = [], $details = [], int $code = 200)
    {
        http_response_code($code != 0 ? $code : 200);
        header('Content-Type: application/json; charset=utf-8');
        return json_encode([
            'code' => $code != 0 ? $code : 200,
            'data' => $data,
            'details' => $details,
            'requested_at' => (new DateTime())->format('c')
        ], JSON_PRETTY_PRINT);
    }

    public function xml($data, $code = 200)
    {
        http_response_code($code != 0 ? $code : 200);
        header('Content-Type: application/xml; charset=utf-8');
        return XmlObjectSerializer::getPostXmlFromArbitraryEntity($data, $somevalue);
    }
}