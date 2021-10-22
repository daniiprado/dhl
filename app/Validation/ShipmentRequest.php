<?php

namespace App\Validation;

use App\Request\Request;

class ShipmentRequest
{
    use FormValidator;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var array
     */
    private $errors = [];

    public function __construct(array $request)
    {
        $this->request = request($request);
    }

    public function rules()
    {
        $pickupRequestorDetailsTypeCodes = ['business', 'direct_consumer', 'government', 'other', 'private', 'reseller'];
        $productCode = [
            'K', // Express 9.00
            'T', // Express 12.00
            'Y', // Express 12.00
            'E', // Express 9.00
            'P', // Express Wordwilde
            'U', // Express Wordwilde (EU)
            'D', // Wordwilde
            'N', // Domestic Express
            'H', // Economy select
            'W', // Economy select (EU)
        ];
        $customerReference = [
            'AAO',
            'CU',
            'FF',
            'FN',
            'IBC',
            'LLR',
            'OBC',
            'PRN',
            'ACP',
            'ACS',
            'ACR',
            'CDN',
            'STD',
            'CO'
        ];
        return [
            'plannedShippingDateAndTime' => 'required|date|date_format:Y-m-d\TH:i:s\G\M\TP',
            'product_code' => 'required|in:'.implode(',', $productCode),
            'company'  => 'required|string',
            'contry'  => 'required|size:2',
            'postalcode'  => 'required|string',
            'city'  => 'required|string',
            'street'  => 'required|string',
            'state'  => 'required|string',
            'firstname'  => 'required|string',
            'lastname'  => 'required|string',
            'product_name'  => 'required|string',
            'product_weight'  => 'required|float',
            'id_order' => 'required|numeric',
            'mg_order' => 'required|numeric',
        ];
    }

    /**
     * @return bool
     */
    public function fails()
    {
        return count($this->errors) > 0;
    }

    /**
     * @return bool
     */
    public function doesntFails()
    {
        return ! $this->fails();
    }

    /**
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }
}