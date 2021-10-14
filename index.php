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
            /*
            $shipment = [
                "plannedShippingDateAndTime" => "2021-10-18T15:23:00GMT+01:00",
                "pickup" => [
                    "isRequested" => true,
                    "closeTime" => "18:00",
                    "pickupDetails" => [
                        "postalAddress" => [
                            "postalCode" => "N3S4C1",
                            "cityName" => "Brantford",
                            "countryCode" => "CA",
                            "provinceCode" => "ON",
                            "addressLine1" => "159 Mary Street Brantford",
                            "addressLine2" => "APD Printing",
                            "countyName" => "Ontario"
                        ],
                        "contactInformation" => [
                            "email" => "luis@apdprinting.com",
                            "phone" => "1-866-215-7831 ext 113",
                            "mobilePhone" => "+60112345678",
                            "companyName" => "APD Printing",
                            "fullName" => "LUIS PEREZ"
                        ],
                        "registrationNumbers" => [
                            [
                                "typeCode" => "VAT",
                                "number" => "CZ123456789",
                                "issuerCountryCode" => "CA"
                            ]
                        ],
                        "bankDetails" => [
                            [
                                "name" => "Russian Bank Name",
                                "settlementLocalCurrency" => "RUB",
                                "settlementForeignCurrency" => "USD"
                            ]
                        ],
                        "typeCode" => "business"
                    ],
                    "pickupRequestorDetails" => [
                        "postalAddress" => [
                            "postalCode" => "75212",
                            "cityName" => "Dallas",
                            "countryCode" => "US",
                            "provinceCode" => "TX",
                            "addressLine1" => "699 College Avenue",
                            "countyName" => "Texas"
                        ],
                        "contactInformation" => [
                            "email" => "that@before.de",
                            "phone" => "+1123456789",
                            "mobilePhone" => "+60112345678",
                            "companyName" => "Company Name",
                            "fullName" => "John Brew"
                        ],
                        "registrationNumbers" => [
                            [
                                "typeCode" => "VAT",
                                "number" => "CZ123456789",
                                "issuerCountryCode" => "CA"
                            ]
                        ],
                        "bankDetails" => [
                            [
                                "name" => "Russian Bank Name",
                                "settlementLocalCurrency" => "RUB",
                                "settlementForeignCurrency" => "USD"
                            ]
                        ],
                        "typeCode" => "business"
                    ]
                ],
                "productCode" => "Y",
                "getRateEstimates" => false,
                "accounts" => [
                    [
                        "typeCode" => "shipper",
                        "number" => "971929344"
                    ]
                ],
                "outputImageProperties" => [
                    "printerDPI" => 300,
                    "customerBarcodes" => [
                        [
                            "content" => "barcode content",
                            "textBelowBarcode" => "text below barcode",
                            "symbologyCode" => "128"
                        ]
                    ],
                    "customerLogos" => [
                        [
                            "fileFormat" => "PNG",
                            "content" => "iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAApgAAAKYB3X3/OAAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAANCSURBVEiJtZZPbBtFFMZ/M7ubXdtdb1xSFyeilBapySVU8h8OoFaooFSqiihIVIpQBKci6KEg9Q6H9kovIHoCIVQJJCKE1ENFjnAgcaSGC6rEnxBwA04Tx43t2FnvDAfjkNibxgHxnWb2e/u992bee7tCa00YFsffekFY+nUzFtjW0LrvjRXrCDIAaPLlW0nHL0SsZtVoaF98mLrx3pdhOqLtYPHChahZcYYO7KvPFxvRl5XPp1sN3adWiD1ZAqD6XYK1b/dvE5IWryTt2udLFedwc1+9kLp+vbbpoDh+6TklxBeAi9TL0taeWpdmZzQDry0AcO+jQ12RyohqqoYoo8RDwJrU+qXkjWtfi8Xxt58BdQuwQs9qC/afLwCw8tnQbqYAPsgxE1S6F3EAIXux2oQFKm0ihMsOF71dHYx+f3NND68ghCu1YIoePPQN1pGRABkJ6Bus96CutRZMydTl+TvuiRW1m3n0eDl0vRPcEysqdXn+jsQPsrHMquGeXEaY4Yk4wxWcY5V/9scqOMOVUFthatyTy8QyqwZ+kDURKoMWxNKr2EeqVKcTNOajqKoBgOE28U4tdQl5p5bwCw7BWquaZSzAPlwjlithJtp3pTImSqQRrb2Z8PHGigD4RZuNX6JYj6wj7O4TFLbCO/Mn/m8R+h6rYSUb3ekokRY6f/YukArN979jcW+V/S8g0eT/N3VN3kTqWbQ428m9/8k0P/1aIhF36PccEl6EhOcAUCrXKZXXWS3XKd2vc/TRBG9O5ELC17MmWubD2nKhUKZa26Ba2+D3P+4/MNCFwg59oWVeYhkzgN/JDR8deKBoD7Y+ljEjGZ0sosXVTvbc6RHirr2reNy1OXd6pJsQ+gqjk8VWFYmHrwBzW/n+uMPFiRwHB2I7ih8ciHFxIkd/3Omk5tCDV1t+2nNu5sxxpDFNx+huNhVT3/zMDz8usXC3ddaHBj1GHj/As08fwTS7Kt1HBTmyN29vdwAw+/wbwLVOJ3uAD1wi/dUH7Qei66PfyuRj4Ik9is+hglfbkbfR3cnZm7chlUWLdwmprtCohX4HUtlOcQjLYCu+fzGJH2QRKvP3UNz8bWk1qMxjGTOMThZ3kvgLI5AzFfo379UAAAAASUVORK5CYII="
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
                "customerReferences" => [
                    [
                        "value" => "Customer reference",
                        "typeCode" => "CU"
                    ]
                ],
                "customerDetails" => [
                    "shipperDetails" => [
                        "postalAddress" => [
                            "postalCode" => "N3S4C1",
                            "cityName" => "Brantford",
                            "countryCode" => "CA",
                            "provinceCode" => "ON",
                            "addressLine1" => "159 Mary Street Brantford",
                            "addressLine2" => "APD Printing",
                            "countyName" => "Ontario"
                        ],
                        "contactInformation" => [
                            "email" => "luis@apdprinting.com",
                            "phone" => "1-866-215-7831 ext 113",
                            "mobilePhone" => "+60112345678",
                            "companyName" => "APD Printing",
                            "fullName" => "LUIS PEREZ"
                        ],
                        "registrationNumbers" => [
                            [
                                "typeCode" => "VAT",
                                "number" => "CZ123456789",
                                "issuerCountryCode" => "CZ"
                            ]
                        ],
                        "bankDetails" => [
                            [
                                "name" => "Russian Bank Name",
                                "settlementLocalCurrency" => "RUB",
                                "settlementForeignCurrency" => "USD"
                            ]
                        ],
                        "typeCode" => "business"
                    ],
                    "receiverDetails" => [
                        "postalAddress" => [
                            "postalCode" => "14800",
                            "cityName" => "Prague",
                            "countryCode" => "CZ",
                            "provinceCode" => "CZ",
                            "addressLine1" => "V Parku 2308/10",
                            "addressLine2" => "addres2",
                            "addressLine3" => "addres3",
                            "countyName" => "Central Bohemia"
                        ],
                        "contactInformation" => [
                            "email" => "that@before.de",
                            "phone" => "+1123456789",
                            "mobilePhone" => "+60112345678",
                            "companyName" => "Company Name",
                            "fullName" => "John Brew"
                        ],
                        "registrationNumbers" => [
                            [
                                "typeCode" => "VAT",
                                "number" => "CZ123456789",
                                "issuerCountryCode" => "CZ"
                            ]
                        ],
                        "bankDetails" => [
                            [
                                "name" => "Russian Bank Name",
                                "settlementLocalCurrency" => "RUB",
                                "settlementForeignCurrency" => "USD"
                            ]
                        ],
                        "typeCode" => "business"
                    ]
                ],
                "content" => [
                    "packages" => [
                        [
                            "typeCode" => "2BP",
                            "weight" => 22.5,
                            "dimensions" => [
                                "length" => 15,
                                "width" => 15,
                                "height" => 40
                            ],
                            "customerReferences" => [
                                [
                                    "value" => "Customer reference",
                                    "typeCode" => "CU"
                                ]
                            ],
                            "identifiers" => [
                                [
                                    "typeCode" => "shipmentId",
                                    "value" => "1234567890"
                                ]
                            ],
                            "description" => "Piece content description",
                            "labelBarcodes" => [
                                [
                                    "position" => "left",
                                    "symbologyCode" => "93",
                                    "content" => "string",
                                    "textBelowBarcode" => "text below left barcode"
                                ]
                            ],
                            "labelText" => [
                                [
                                    "position" => "left",
                                    "caption" => "text caption",
                                    "value" => "text value"
                                ]
                            ],
                            "labelDescription" => "bespkoe label description"
                        ]
                    ],
                    "isCustomsDeclarable" => true,
                    "declaredValue" => 150,
                    "declaredValueCurrency" => "USD",
                    "exportDeclaration" => [
                        "lineItems" => [
                            [
                                "number" => 1,
                                "description" => "line item description",
                                "price" => 150,
                                "quantity" => [
                                    "value" => 1,
                                    "unitOfMeasurement" => "BOX"
                                ],
                                "commodityCodes" => [
                                    [
                                        "typeCode" => "outbound",
                                        "value" => "HS1234567890"
                                    ]
                                ],
                                "exportReasonType" => "permanent",
                                "manufacturerCountry" => "CA",
                                "exportControlClassificationNumber" => "US123456789",
                                "weight" => [
                                    "netValue" => 10,
                                    "grossValue" => 10
                                ],
                                "isTaxesPaid" => true,
                                "additionalInformation" => [
                                    "string"
                                ],
                                "customerReferences" => [
                                    [
                                        "typeCode" => "AFE",
                                        "value" => "string"
                                    ]
                                ],
                                "customsDocuments" => [
                                    [
                                        "typeCode" => "972",
                                        "value" => "string"
                                    ]
                                ]
                            ]
                        ],
                        "invoice" => [
                            "number" => "12345-ABC",
                            "date" => "2020-03-18",
                            "signatureName" => "Brewer",
                            "signatureTitle" => "Mr.",
                            "signatureImage" => "iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAApgAAAKYB3X3/OAAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAANCSURBVEiJtZZPbBtFFMZ/M7ubXdtdb1xSFyeilBapySVU8h8OoFaooFSqiihIVIpQBKci6KEg9Q6H9kovIHoCIVQJJCKE1ENFjnAgcaSGC6rEnxBwA04Tx43t2FnvDAfjkNibxgHxnWb2e/u992bee7tCa00YFsffekFY+nUzFtjW0LrvjRXrCDIAaPLlW0nHL0SsZtVoaF98mLrx3pdhOqLtYPHChahZcYYO7KvPFxvRl5XPp1sN3adWiD1ZAqD6XYK1b/dvE5IWryTt2udLFedwc1+9kLp+vbbpoDh+6TklxBeAi9TL0taeWpdmZzQDry0AcO+jQ12RyohqqoYoo8RDwJrU+qXkjWtfi8Xxt58BdQuwQs9qC/afLwCw8tnQbqYAPsgxE1S6F3EAIXux2oQFKm0ihMsOF71dHYx+f3NND68ghCu1YIoePPQN1pGRABkJ6Bus96CutRZMydTl+TvuiRW1m3n0eDl0vRPcEysqdXn+jsQPsrHMquGeXEaY4Yk4wxWcY5V/9scqOMOVUFthatyTy8QyqwZ+kDURKoMWxNKr2EeqVKcTNOajqKoBgOE28U4tdQl5p5bwCw7BWquaZSzAPlwjlithJtp3pTImSqQRrb2Z8PHGigD4RZuNX6JYj6wj7O4TFLbCO/Mn/m8R+h6rYSUb3ekokRY6f/YukArN979jcW+V/S8g0eT/N3VN3kTqWbQ428m9/8k0P/1aIhF36PccEl6EhOcAUCrXKZXXWS3XKd2vc/TRBG9O5ELC17MmWubD2nKhUKZa26Ba2+D3P+4/MNCFwg59oWVeYhkzgN/JDR8deKBoD7Y+ljEjGZ0sosXVTvbc6RHirr2reNy1OXd6pJsQ+gqjk8VWFYmHrwBzW/n+uMPFiRwHB2I7ih8ciHFxIkd/3Omk5tCDV1t+2nNu5sxxpDFNx+huNhVT3/zMDz8usXC3ddaHBj1GHj/As08fwTS7Kt1HBTmyN29vdwAw+/wbwLVOJ3uAD1wi/dUH7Qei66PfyuRj4Ik9is+hglfbkbfR3cnZm7chlUWLdwmprtCohX4HUtlOcQjLYCu+fzGJH2QRKvP3UNz8bWk1qMxjGTOMThZ3kvgLI5AzFfo379UAAAAASUVORK5CYII=",
                            "instructions" => [
                                "string"
                            ],
                            "customerDataTextEntries" => [
                                "string"
                            ],
                            "function" => "import",
                            "totalNetWeight" => 999999999999,
                            "totalGrossWeight" => 999999999999,
                            "customerReferences" => [
                                [
                                    "typeCode" => "ACL",
                                    "value" => "string"
                                ]
                            ],
                            "termsOfPayment" => "100 days"
                        ],
                        "remarks" => [
                            [
                                "value" => "declaration remark"
                            ]
                        ],
                        "additionalCharges" => [
                            [
                                "value" => 10,
                                "caption" => "fee",
                                "typeCode" => "admin"
                            ]
                        ],
                        "destinationPortName" => "port details",
                        "placeOfIncoterm" => "port of departure or destination details",
                        "payerVATNumber" => "12345ED",
                        "recipientReference" => "recipient reference",
                        "exporter" => [
                            "id" => "123",
                            "code" => "EXPCZ"
                        ],
                        "packageMarks" => "marks",
                        "declarationNotes" => [
                            [
                                "value" => "up to three declaration notes"
                            ]
                        ],
                        "exportReference" => "export reference",
                        "exportReason" => "export reason",
                        "exportReasonType" => "permanent",
                        "licenses" => [
                            [
                                "typeCode" => "export",
                                "value" => "license"
                            ]
                        ],
                        "shipmentType" => "personal",
                        "customsDocuments" => [
                            [
                                "typeCode" => "972",
                                "value" => "string"
                            ]
                        ]
                    ],
                    "description" => "shipment description",
                    "USFilingTypeValue" => "12345",
                    "incoterm" => "DAP",
                    "unitOfMeasurement" => "metric"
                ],
                "documentImages" => [
                    [
                        "typeCode" => "INV",
                        "imageFormat" => "PDF",
                        "content" => "JVBERi0xLjcKCjEgMCBvYmogICUgZW50cnkgcG9pbnQKPDwKICAvVHlwZSAvQ2F0YWxvZwogIC9QYWdlcyAyIDAgUgo+PgplbmRvYmoKCjIgMCBvYmoKPDwKICAvVHlwZSAvUGFnZXMKICAvTWVkaWFCb3ggWyAwIDAgMjAwIDIwMCBdCiAgL0NvdW50IDEKICAvS2lkcyBbIDMgMCBSIF0KPj4KZW5kb2JqCgozIDAgb2JqCjw8CiAgL1R5cGUgL1BhZ2UKICAvUGFyZW50IDIgMCBSCiAgL1Jlc291cmNlcyA8PAogICAgL0ZvbnQgPDwKICAgICAgL0YxIDQgMCBSIAogICAgPj4KICA+PgogIC9Db250ZW50cyA1IDAgUgo+PgplbmRvYmoKCjQgMCBvYmoKPDwKICAvVHlwZSAvRm9udAogIC9TdWJ0eXBlIC9UeXBlMQogIC9CYXNlRm9udCAvVGltZXMtUm9tYW4KPj4KZW5kb2JqCgo1IDAgb2JqICAlIHBhZ2UgY29udGVudAo8PAogIC9MZW5ndGggNDQKPj4Kc3RyZWFtCkJUCjcwIDUwIFRECi9GMSAxMiBUZgooSGVsbG8sIHdvcmxkISkgVGoKRVQKZW5kc3RyZWFtCmVuZG9iagoKeHJlZgowIDYKMDAwMDAwMDAwMCA2NTUzNSBmIAowMDAwMDAwMDEwIDAwMDAwIG4gCjAwMDAwMDAwNzkgMDAwMDAgbiAKMDAwMDAwMDE3MyAwMDAwMCBuIAowMDAwMDAwMzAxIDAwMDAwIG4gCjAwMDAwMDAzODAgMDAwMDAgbiAKdHJhaWxlcgo8PAogIC9TaXplIDYKICAvUm9vdCAxIDAgUgo+PgpzdGFydHhyZWYKNDkyCiUlRU9G"
                    ]
                ],
                "requestOndemandDeliveryURL" => false,
                "shipmentNotification" => [
                    [
                        "typeCode" => "email",
                        "receiverId" => "receiver@email.com",
                        "languageCode" => "eng",
                        "languageCountryCode" => "US",
                        "bespokeMessage" => "message to be included in the notification"
                    ]
                ],
                "getOptionalInformation" => false,
                "parentShipment" => [
                    "productCode" => "s",
                    "packagesCount" => 1
                ],
                "valueAddedServices" => [
                    [
                        "serviceCode" => "WY"
                    ]
                ]
            ];
            */
            echo $dhl->createShipment($request);
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