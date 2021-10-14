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
        return [
            'plannedShippingDateAndTime' => 'required|date|date_format:Y-m-d\TH:i:s\G\M\TP',
            'pickup' => 'required|array',
            'pickup.isRequested' => 'required|boolean',
            'pickup.pickupRequestorDetails' => 'required|array',
            'pickup.pickupRequestorDetails.typeCode' => 'required|in:'.implode(',', $pickupRequestorDetailsTypeCodes),
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