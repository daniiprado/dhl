# API DHL

Descargar el proyecto localmente y copiar el arvhibo ``.env.example`` y renombrarlo a ``.env``.
Luego configurar las variables correspondientes a:

```sh
APP_URL=http://localhost
APP_ENV=local
APP_TIMEZONE=America/Bogota
APP_DEBUG=true
DHL_ENDPOINT_MOCK=https://api-mock.dhl.com/mydhlapi/
DHL_ENDPOINT_TEST=https://express.api.dhl.com/mydhlapi/test/
DHL_ENDPOINT_PROD=https://express.api.dhl.com/mydhlapi/
DHL_API_KEY=
DHL_API_SECRET=
DHL_USERNAME=
DHL_PASSWORD=
DHL_SHIPPER_ACCOUNT=
BUSINESS_POSTAL_CODE=
BUSINESS_NAME=
BUSINESS_CLOSE_TIME=
BUSINESS_CITY=
BUSINESS_COUNTRY_CODE=
BUSINESS_PROVINCE_CODE=
BUSINESS_COUNTY_NAME=
BUSINESS_ADDRESS=
BUSINESS_EMAIL=
BUSINESS_PHONE=
BUSINESS_MOBILE=
BUSINESS_CONTACT_NAME=
BOOKS_CLIENT_ID=
BOOKS_CLIENT_SECRET=
BUSINESS_LOGO=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
BOOKS_REDIRECT=http://localhost?action=quickbooks-credentials
BOOKS_SCOPES="com.intuit.quickbooks.accounting com.intuit.quickbooks.payment openid profile email phone address"
#Development/Production
BOOKS_BASE_URL=Development
```
Usuario de DHL y contraseña y las URL de comunicación con DHL.

Inicialmente existen 2 métodos que recibe el archivo ``index.php``

```php
    // localhost?action=dhl-get-shipment-by-id&shipment=1234567890
    // Este parámetro recibe el ID del Shipment para traer su información correspondiente
    $_GET['shipment']
    
    // localhost?action=dhl-get-shipment-by-id-as-pdf&shipment=1234567890
    // Este parámetro recibe el ID del Shipment para traer su información correspondiente
    $_GET['shipment']
    
    // localhost?action=dhl-get-shipment-tracking-by-id&shipment=1234567890
    // Este parámetro recibe el ID del Shipment para traer su información correspondiente
    $_GET['shipment']
    
    // localhost?action=dhl-create-shipment
    // Este parámetro recibe los datos del Shipment para crearlo en DHL
    $_POST
    $productCode = [
            'K', // Express 9.00
            'T', // Express 12.00
            'Y', // Express 12.00
            'E', // Express 9.00
            'P', // Express Wordwilde
            'U', // Express Wordwilde (EU)
            'D', // Wordwilde
            'N', // Domestic Express
            'H', // Economy select
            'W', // Economy select (EU)
        ];
    $shipment = [
        'plannedShippingDateAndTime' => '2021-10-22T15:23:00GMT-04:00', // Fecha posterior a la actual
        'product_code' => 'N', // Cualquiera que aplique en $productCode
        'company'  => 'Company Name', // Nombre de la compañía del cliente de la API de apdprinting.com
        'contry'  => 'CA', // Solo 2 caracteres requeridos de la API de apdprinting.com
        'postalcode'  => 'N4S0C2', // Ejemplo de la API de apdprinting.com
        'city'  => 'Woodstock', // Ciudad del envío de la API de apdprinting.com
        'street'  => 'Ave. Evergreen 123', // Dirección del envío de la API de apdprinting.com
        'state'  => 'Ontario', // Estado del envío de la API de apdprinting.com
        'firstname'  => 'Jhon', // Nombre persona que recibe el envío de la API de apdprinting.com
        'lastname'  => 'Doe', // Apellido persona que recibe el envío de la API de apdprinting.com
        'product_name'  => 'Business Cards Soft Touch Lamination', // Nombre del producto a enviar de la API de apdprinting.com
        'product_weight'  => 1.9000, // Peso del producto a enviar de la API de apdprinting.com
        'id_order' => 5345, // Id de la orden API de apdprinting.com
        'mg_order' => 46666, // mg_order de la API de apdprinting.com
    ];
    // Retorna Label encontrado en la carpeta examples
```