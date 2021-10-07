<?php

namespace App\Books;

use App\Request\Response;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Exception\IdsException;
use QuickBooksOnline\API\Facades\Invoice as AWB;

class Customers extends QuickBookService
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return false|string
     * @throws \Exception
     */
    public function getCustomers()
    {
        return $this->getItems('customer');
    }

    /**
     * @param $id
     * @return false|string
     */
    public function getCustomer($id)
    {
        return $this->getItemById($id,'customer');
    }
}