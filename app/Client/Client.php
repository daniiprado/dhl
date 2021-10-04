<?php

namespace App\Client;

use App\DHL\DHL;
use App\Helpers\HttpClient;


class Client
{

    /**
     * @var 
     */
    private $dhl;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->dhl = new DHL();
    }

    public function getDHL()
    {
        return $this->dhl;
    }
}