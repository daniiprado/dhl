<?php

namespace App\Books;

use App\Request\Response;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Exception\IdsException;
use QuickBooksOnline\API\Facades\Invoice as AWB;

class Invoice extends QuickBookService
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return false|string
     * @throws \Exception
     */
    public function getInvoices()
    {
        return $this->getItems('invoice');
    }

    /**
     * @param $id
     * @return false|string
     */
    public function getInvoice($id)
    {
        return $this->getItemById($id,'invoice');
    }

    /**
     * @param $invoice
     * @return false|string
     * @throws \Exception
     */
    public function createInvoice($invoice)
    {
        try {
            $theResourceObj = AWB::create($invoice);
            return $this->createResponse($this->dataService->Add($theResourceObj));
        } catch (IdsException $exception) {
            return $this->createResponse(
                $exception->getMessage(),
                [
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'code' => $exception->getCode(),
                    'debug' => $exception->getDebug(),
                ],
                422
            );
        }
    }
}