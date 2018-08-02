<?php
require_once './vendor/autoload.php';
require_once './includes/config.php';


if(isset($_POST['create_invoice_request'])){
    $requestArray =  json_decode($_POST['create_invoice_request'], true);
    echo "<pre>";
    var_dump($requestArray);
    exit;
}
$configArray = array(
                'ClientID' => $rest_client_id,
                'ClientSecret' => $rest_client_secret
                );

$PayPal = new \angelleye\PayPal\rest\invoice\InvoiceAPI($configArray);

$merchantInfo = array(
    'Email' => 'tejasm-merchant@itpathsolutions.co.in',                                           // The merchant email address. Maximum length is 260 characters.
    'FirstName' => 'TJ',                                       // The merchant first name. Maximum length is 30 characters.
    'LastName'  => 'Mehta',                                       // The merchant last name. Maximum length is 30 characters.        
    'BusinessName' => 'Tj Mehta\'s Test Store',                                    // The merchant company business name. Maximum length is 100 characters.
);

$requestData =array(
    'merchantInfo'            => $merchantInfo,    
    'billingInfo'             => $requestArray['billingInfo'],
    'itemArray'               => $itemArray,
    'finalDiscountForInvoice' => $finalDiscountForInvoice,    
    'paymentTerm'             => $requestArray['paymentTerm'],
    'invoiceData'             => $requestArray['invoiceData']
);

echo "<pre>";
var_dump($_POST);
exit;