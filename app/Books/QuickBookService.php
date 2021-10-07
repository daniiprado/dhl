<?php

namespace App\Books;

use App\Request\Response;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Exception\IdsException;

class QuickBookService
{
    /**
     * @var DataService
     */
    protected $dataService;
    /**
     * @var Response
     */
    protected $response;

    public function __construct()
    {
        $this->dataService = DataService::Configure(array(
            'auth_mode'       => 'oauth2',
            'ClientID'        => env('BOOKS_CLIENT_ID'),
            'ClientSecret'    => env('BOOKS_CLIENT_SECRET'),
            'accessTokenKey'  => session()->get('access_token'),
            'refreshTokenKey' => session()->get('refresh_token'),
            'QBORealmID'      => session()->get('realmId'),
            'baseUrl'         => env('BOOKS_BASE_URL')
        ));
        $this->dataService->setLogLocation(dirname('').'logs');
        $this->dataService->throwExceptionOnError(env('APP_DEBUG', true));
        $this->response = response();
    }

    /**
     * @return false|string
     * @throws \Exception
     */
    public function getItems($entity)
    {
        try {
            $data = [];
            $items = $this->dataService->FindAll($entity, request()->get('page', 1), request()->get('per_page', 10));
            foreach ($items as $item) {
                $data[] = $item;
            }
            $table = ucfirst($entity);
            return $this->createResponse(
                $data,
                [
                    'page' => request()->get('page', 1),
                    'per_page' => request()->get('per_page', 10),
                    'total' => $this->dataService->Query("SELECT COUNT(*) FROM {$table}")
                ]
            );
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

    public function getItemById($id, $entity)
    {
        try {
            return $this->createResponse($this->dataService->FindById($entity, $id));
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

    /**
     * @param null $data
     * @param null $details
     * @param int $code
     * @return false|string
     */
    public function createResponse($data = null, $details = null, int $code = 200) {
        $error = $this->dataService->getLastError();
        if ($error) {
            return $this->response->json(
                $error->getResponseBody(),
                $error->getOAuthHelperError(),
                $error->getHttpStatusCode()
            );
        }
        return request()->get('format', 'json') == 'json'
            ? $this->response->json(
                $data,
                $details,
                $code
            )
            : $this->response->xml($data);
    }
}