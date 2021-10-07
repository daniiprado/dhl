<?php

namespace App\Books;

use App\Request\Response;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Exception\IdsException;
use QuickBooksOnline\API\Facades\Invoice as AWB;

class Items extends QuickBookService
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return false|string
     * @throws \Exception
     */
    public function getAllItems()
    {
        return $this->getItems('item');
    }

    /**
     * @param $id
     * @return false|string
     */
    public function getOneItem($id)
    {
        return $this->getItemById($id, 'item');
    }
}