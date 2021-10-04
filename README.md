# API DHL

Descargar el proyecto localmente y copiar el arvhibo ``.env.example`` y renombrarlo a ``.env``.
Luego configurar las variables correspondientes a:

```sh
APP_ENV=local
DHL_ENDPOINT_TEST=https://express.api.dhl.com/mydhlapi/test
DHL_ENDPOINT_PROD=https://express.api.dhl.com/mydhlapi
DHL_USERNAME=
DHL_PASSWORD=
```
Usuario de DHL y contraseña y las URL de comunicación con DHL.

Inicialmente existen 2 métodos que recibe el archivo ``index.php``

```php
    // Este parámetro recibe el ID del Shipment para traer su información correspondiente
    $_GET['shipment']
    // Este parámetro recibe los datos del Shipment para crearlo en DHL
    $_POST
    $shipment = [
        "plannedShippingDateAndTime" => "2019-08-04T14:00:31GMT+01:00",
        "pickup" => [
            "isRequested" => false,
            "closeTime" => "18:00",
            "location" => "reception",
            "specialInstructions" => [
                [
                    "value" => "please ring door bell",
                    "typeCode" => "TBD"
                ]
            ],
            "pickupDetails" => [
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
            ],
            "pickupRequestorDetails" => [
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
        "productCode" => "D",
        "localProductCode" => "D",
        "getRateEstimates" => false,
        "accounts" => [
            [
                "typeCode" => "shipper",
                "number" => "123456789"
            ]
        ],
        "valueAddedServices" => [
            [
                "serviceCode" => "II",
                "value" => 100,
                "currency" => "GBP",
                "method" => "cash",
                "dangerousGoods" => [
                    [
                        "contentId" => "908",
                        "dryIceTotalNetWeight" => 12,
                        "unCode" => "UN-7843268473"
                    ]
                ]
            ]
        ],
        "outputImageProperties" => [
            "printerDPI" => 300,
            "customerBarcodes" => [
                [
                    "content" => "barcode content",
                    "textBelowBarcode" => "text below barcode",
                    "symbologyCode" => "93"
                ]
            ],
            "customerLogos" => [
                [
                    "fileFormat" => "PNG",
                    "content" => "base64 encoded image"
                ]
            ],
            "encodingFormat" => "pdf",
            "imageOptions" => [
                [
                    "typeCode" => "label",
                    "templateName" => "ECOM26_84_001",
                    "isRequested" => true,
                    "hideAccountNumber" => false,
                    "numberOfCopies" => 1,
                    "invoiceType" => "commercial",
                    "languageCode" => "eng",
                    "encodingFormat" => "png",
                    "renderDHLLogo" => false
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
        "identifiers" => [
            [
                "typeCode" => "shipmentId",
                "value" => "1234567890"
            ]
        ],
        "customerDetails" => [
            "shipperDetails" => [
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
            ],
            "buyerDetails" => [
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
                    "email" => "buyer@domain.com",
                    "phone" => "+44123456789",
                    "mobilePhone" => "+42123456789",
                    "companyName" => "Customer Company Name",
                    "fullName" => "Mark Companer"
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
            "importerDetails" => [
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
            ],
            "exporterDetails" => [
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
            ],
            "sellerDetails" => [
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
            ],
            "payerDetails" => [
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
            "declaredValueCurrency" => "CZK",
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
                        "manufacturerCountry" => "CZ",
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
                    "signatureImage" => "Base64 encoded image",
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
                "content" => "base64 encoded image"
            ]
        ],
        "onDemandDelivery" => [
            "deliveryOption" => "servicepoint",
            "location" => "front door",
            "specialInstructions" => "ringe twice",
            "gateCode" => "1234",
            "whereToLeave" => "concierge",
            "neighbourName" => "Mr.Dan",
            "neighbourHouseNumber" => "777",
            "authorizerName" => "Newman",
            "servicePointId" => "SPL123",
            "requestedDeliveryDate" => "2020-04-20"
        ],
        "requestOndemandDeliveryURL" => false,
        "shipmentNotification" => [
            [
                "typeCode" => "email",
                "receiverId" => "receiver@email.com",
                "languageCode" => "eng",
                "languageCountryCode" => "UK",
                "bespokeMessage" => "message to be included in the notification"
            ]
        ],
        "prepaidCharges" => [
            [
                "typeCode" => "freight",
                "currency" => "CZK",
                "value" => 200,
                "method" => "cash"
            ]
        ],
        "getOptionalInformation" => false,
        "parentShipment" => [
            "productCode" => "s",
            "packagesCount" => 1
        ]
    ];
```