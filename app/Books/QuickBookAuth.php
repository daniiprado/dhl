<?php

namespace App\Books;

use App\Request\Response;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Exception\SdkException;

class QuickBookAuth
{
    /**
     * @var DataService
     */
    private $dataService;
    /**
     * @var Response
     */
    private $response;

    /**
     * @throws SdkException
     */
    public function __construct()
    {
        $this->dataService = DataService::Configure([
            'auth_mode' => 'oauth2',
            'ClientID' => env('BOOKS_CLIENT_ID'),
            'ClientSecret' => env('BOOKS_CLIENT_SECRET'),
            'RedirectURI' => env('BOOKS_REDIRECT'),
            'scope' => "com.intuit.quickbooks.accounting com.intuit.quickbooks.payment openid profile email phone address",
            'baseUrl' => env('BOOKS_BASE_URL')
        ]);
        $this->dataService->setLogLocation(dirname('').'logs');
        $this->dataService->throwExceptionOnError(env('APP_DEBUG', true));
        $this->response = response();
    }

    public function login()
    {
        $OAuth2LoginHelper = $this->dataService->getOAuth2LoginHelper();
        session()->setsSession('authUrl', $OAuth2LoginHelper->getAuthorizationCodeURL());
        return $OAuth2LoginHelper->getAuthorizationCodeURL();
    }

    public function setCredentials()
    {
        $OAuth2LoginHelper = $this->dataService->getOAuth2LoginHelper();
        $accessToken = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken(session()->get('code'), session()->get('realmId'));
        $refreshedAccessToken = $OAuth2LoginHelper->refreshToken();
        $this->dataService->updateOAuth2Token($accessToken);
        session()->setsSession('access_token', $refreshedAccessToken->getAccessToken());
        session()->setsSession('refresh_token', $refreshedAccessToken->getRefreshToken());
        $this->dataService->throwExceptionOnError(true);
        $CompanyInfo = $this->dataService->getCompanyInfo();
        $nameOfCompany = $CompanyInfo->CompanyName;
        $company = XmlObjectSerializer::getPostXmlFromArbitraryEntity($CompanyInfo, $somevalue);
        session()->setsSession('company', $company);
        return $this->createResponse($CompanyInfo, "Test for OAuth Complete. Company Name is {$nameOfCompany}");
    }

    private function createResponse($data = null, $details = null, $code = 200) {
        $error = $this->dataService->getLastError();
        if ($error) {
            return $this->response->json(
                $error->getOAuthHelperError(),
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