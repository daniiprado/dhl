<?php
ob_start();
session_start();

require 'vendor/autoload.php';


require_once 'bootstrap/app.php';

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

$dhl = (new App\Client\Client())->getDHL();
$books_auth = new \App\Books\QuickBookAuth();
$quickbooks_invoices = new \App\Books\Invoice();
$quickbooks_customers = new \App\Books\Customers();
$quickbooks_items = new \App\Books\Items();

$cases = [
    [
        'name'  => 'quickbooks-oauth',
        'description'   => 'Conectarse a la API de Quickbooks, es el primer paso antes de realizar cualquier petición.',
    ],
    [
        'name'  => 'quickbooks-credentials',
        'description'   => 'Una vez se conecta a Quickbooks se redirige a esta URL para almacenar en sesión los respectivos tokens',
    ],
    [
        'name'  => 'quickbooks-customers',
        'description'   => 'Lista los Customers disponibles paginados y recibe parámetros de page (página actual) y per_page (indica la cantidad de datos por página)',
    ],
    [
        'name'  => 'quickbooks-customer-by-id',
        'description'   => 'Consulta un customer por ID, recibe el parámetro customer con el dato númerico',
    ],
    [
        'name'  => 'quickbooks-items',
        'description'   => 'Lista los Items disponibles paginados y recibe parámetros de page (página actual) y per_page (indica la cantidad de datos por página)',
    ],
    [
        'name'  => 'quickbooks-item-by-id',
        'description'   => 'Consulta un item por ID, recibe el parámetro item con el dato númerico',
    ],
    [
        'name'  => 'quickbooks-invoices',
        'description'   => 'Lista los Invoices disponibles paginados y recibe parámetros de page (página actual) y per_page (indica la cantidad de datos por página)',
    ],
    [
        'name'  => 'quickbooks-invoice-by-id',
        'description'   => 'Consulta un invoice por ID, recibe el parámetro invoice con el dato númerico',
    ],
    [
        'name'  => 'quickbooks-create-invoice',
        'description'   => 'Crea un invoice y retorna los datos del invoice creado. (Aún pendiente por validar)',
    ],
    [
        'name'  => 'dhl-get-shipment-by-id',
        'description'   => 'Obtiene un shipment de DHL. Y devuelve un PDF codificado en BASE64. Recibe el parámetro shipment obligatoriamente',
    ],
    [
        'name'  => 'dhl-get-shipment-by-id-as-pdf',
        'description'   => 'Obtiene un shipment de DHL. Y devuelve un archivo PDF. Recibe el parámetro shipment obligatoriamente',
    ],
    [
        'name'  => 'dhl-get-shipment-tracking-by-id',
        'description'   => 'Obtiene el tracking de un shipment de DHL. Recibe el parámetro shipment obligatoriamente',
    ],
    [
        'name'  => 'dhl-create-shipment',
        'description'   => 'Crea un shipment de DHL. (Aún pendiente por validar)',
    ],
];

switch (request()->get('action')) {
    // Quickbooks
    case 'quickbooks-oauth':
        session()->forgetAll();
        $url = $books_auth->login();
        echo "<form action='$url' method='post'><button type='submit'>Conectar con Quickbooks</button></form>";
        break;
    case 'quickbooks-credentials':
        if (request()->has('code', 'realmId')) {
            session()->setsSession('code', request()->get('code'));
            session()->setsSession('realmId', request()->get('realmId'));
            echo $books_auth->setCredentials();
        } else {
            echo response()->json(
                request()->all(),
                [],
                422
            );
        }
        break;
    case 'quickbooks-customers':
        echo $quickbooks_customers->getCustomers();
        break;
    case 'quickbooks-customer-by-id':
        if (request()->has('customer')) {
            echo $quickbooks_customers->getCustomer(request()->get('customer'));
        } else {
            echo "El parámetro customer es obligatorio";
        }
        break;
    case 'quickbooks-items':
        echo $quickbooks_items->getAllItems();
        break;
    case 'quickbooks-item-by-id':
        if (request()->has('item')) {
            echo $quickbooks_items->getOneItem(request()->get('item'));
        } else {
            echo "El parámetro customer es obligatorio";
        }
        break;
    case 'quickbooks-invoices':
        echo $quickbooks_invoices->getInvoices();
        break;
    case 'quickbooks-invoice-by-id':
        if (request()->has('invoice')) {
            echo $quickbooks_invoices->getInvoice(request()->get('invoice'));
        } else {
            echo "El parámetro invoice es obligatorio";
        }
        break;
    case 'quickbooks-create-invoice':
        $invoice = [
            "Line" => [
                [
                    "DetailType" => "SalesItemLineDetail",
                    "Amount" => 100.00,
                    "SalesItemLineDetail" => [
                        "ItemRef" => [
                            "name" => "Services",
                            "value" => 20,
                        ]
                    ]
                ]
            ],
            "CustomerRef"=> [
                "value"=> 1
            ],
        ];
        echo $quickbooks_invoices->createInvoice($invoice);
        break;
    // DHL
    case 'dhl-get-shipment-by-id':
        if (request()->methodIsGet() && request()->has('shipment')) {
            echo $dhl->getShipment(
                request()->get('shipment'),
                request()->except('shipment')
            );
        } else {
            echo "El parámetro shipment es obligatorio. Ejemplo: 1234567890";
        }
        break;
    case 'dhl-get-shipment-by-id-as-pdf':
        if (request()->methodIsGet() && request()->has('shipment')) {
            echo $dhl->getShipmentAsPdf(
                request()->get('shipment'),
                request()->except('shipment')
            );
        } else {
            echo "El parámetro shipment es obligatorio. Ejemplo: 1234567890";
        }
        break;
    case 'dhl-get-shipment-tracking-by-id':
        if (request()->methodIsGet() && request()->has('shipment')) {
            echo $dhl->getTracking(
                request()->get('shipment'),
                request()->except('shipment')
            );
        } else {
            echo "El parámetro shipment es obligatorio. Ejemplo: 1234567890";
        }
        break;
    case 'dhl-create-shipment':
        $request = request()->except('action');
        $validation = new \App\Validation\ShipmentRequest($request);
        $validation->validate();
        if ($validation->doesntFails()) {
            $shipment = [
                "plannedShippingDateAndTime" => request()->get('plannedShippingDateAndTime'),
                "pickup" => [
                    "isRequested" => true,
                    "closeTime" => env('BUSINESS_CLOSE_TIME'),
                    "pickupDetails" => [
                        "postalAddress" => [
                            "postalCode" => env('BUSINESS_POSTAL_CODE'),
                            "cityName" => env('BUSINESS_CITY'),
                            "countryCode" => env('BUSINESS_COUNTRY_CODE'),
                            "provinceCode" => env('BUSINESS_PROVINCE_CODE'),
                            "addressLine1" => env('BUSINESS_ADDRESS'),
                            "addressLine2" => env('BUSINESS_NAME'),
                            "countyName" => env('BUSINESS_COUNTY_NAME')
                        ],
                        "contactInformation" => [
                            "email" => env('BUSINESS_EMAIL'),
                            "phone" => env('BUSINESS_PHONE'),
                            "mobilePhone" => env('BUSINESS_MOBILE'),
                            "companyName" => env('BUSINESS_NAME'),
                            "fullName" => env('BUSINESS_CONTACT_NAME')
                        ],
                        "typeCode" => "business"
                    ],
                ],
                "productCode" => request()->get('product_code', 'Y'),
                "getRateEstimates" => true,
                "accounts" => [
                    [
                        "typeCode" => "shipper",
                        "number" => env('DHL_SHIPPER_ACCOUNT')
                    ]
                ],
                "outputImageProperties" => [
                    "printerDPI" => 300,
                    "customerBarcodes" => [
                        [
                            "content" => request()->get('id_order').' - '.request()->get('mg_order'),
                            "textBelowBarcode" => request()->get('id_order').' - '.request()->get('mg_order'),
                            "symbologyCode" => "128"
                        ]
                    ],
                    "customerLogos" => [
                        [
                            "fileFormat" => "PNG",
                            "content" => env('BUSINESS_LOGO')
                        ]
                    ],
                    "encodingFormat" => "pdf",
                    "imageOptions" => [
                        [
                            "typeCode" => "label"
                        ]
                    ],
                    "splitTransportAndWaybillDocLabels" => true,
                    "allDocumentsInOneImage" => true,
                    "splitDocumentsByPages" => true,
                    "splitInvoiceAndReceipt" => true
                ],
                "customerDetails" => [
                    "shipperDetails" => [
                        "postalAddress" => [
                            "postalCode" => env('BUSINESS_POSTAL_CODE'),
                            "cityName" => env('BUSINESS_CITY'),
                            "countryCode" => env('BUSINESS_COUNTRY_CODE'),
                            "provinceCode" => env('BUSINESS_PROVINCE_CODE'),
                            "addressLine1" => env('BUSINESS_ADDRESS'),
                            "addressLine2" => env('BUSINESS_NAME'),
                            "countyName" => env('BUSINESS_COUNTY_NAME')
                        ],
                        "contactInformation" => [
                            "email" => env('BUSINESS_EMAIL'),
                            "phone" => env('BUSINESS_PHONE'),
                            "mobilePhone" => env('BUSINESS_MOBILE'),
                            "companyName" => env('BUSINESS_NAME'),
                            "fullName" => env('BUSINESS_CONTACT_NAME')
                        ],
                        "typeCode" => "business"
                    ],
                    "receiverDetails" => [
                        "postalAddress" => [
                            "postalCode" => request()->get('postalcode'),
                            "cityName" => request()->get('city'),
                            "countryCode" => request()->get('contry'),
                            "provinceCode" => request()->get('state'),
                            "addressLine1" => request()->get('street'),
                        ],
                        "contactInformation" => [
                            "phone" => env('BUSINESS_PHONE'),
                            "companyName" => request()->get('company'),
                            "fullName" => request()->get('firstname').' '.request()->get('lastname')
                        ],
                        "typeCode" => "business"
                    ]
                ],
                "content" => [
                    "packages" => [
                        [
                            "weight" => (float) request()->get('product_weight'),
                            "dimensions" => [
                                "length" => 15,
                                "width" => 15,
                                "height" => 40
                            ],
                            "description" => request()->get('product_name'),
                        ]
                    ],
                    "isCustomsDeclarable" => false,
                    "description" => request()->get('product_name'),
                    "incoterm" => "DAP",
                    "unitOfMeasurement" => "metric"
                ],
                "requestOndemandDeliveryURL" => false,
                "getOptionalInformation" => false,
            ];
            echo $dhl->createShipment($shipment);
        } else {
            echo response()->json(
                $validation->errors(),
                'Para visualizar más información sobre la creación de un shipment y sus parámetros visita https://developer.dhl.com/api-reference/dhl-express-mydhl-api#reference-docs-section%create-shipment',
                422
            );
        }
        break;
    default:
        $url = env('APP_URL', 'http://localhost');
        echo "<p>Debe indicar el parámetro action con los siguientes posibles valores:</p>";
        echo "<ul>";
        foreach ($cases as $case) {
            echo "<li><a href='{$url}?action={$case['name']}'>{$case['description']}</a></li>";
        }
        echo "</ul>";
        break;
}

ob_end_flush();