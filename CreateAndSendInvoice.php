<?php

require_once './vendor/autoload.php';
require_once './includes/config.php';


if (isset($_POST['create_invoice_request'])) {
    $requestArray = json_decode($_POST['create_invoice_request'], true);
} else {
    echo "No data received";
    exit;
}
$request = array();
$request['invoiceData'] = array(
    'Number' => (isset($requestArray['Number'])) ? $requestArray['Number'] : '',
    'LogoUrl' => (isset($requestArray['LogoUrl'])) ? $requestArray['LogoUrl'] : '',
    'InvoiceDate' => (isset($requestArray['InvoiceDate'])) ? date('Y-m-d Z', strtotime($requestArray['InvoiceDate'])) : '',
    'Reference' => (isset($requestArray['Reference'])) ? $requestArray['Reference'] : '',
    'Note' => (isset($requestArray['Note'])) ? $requestArray['Note'] : '',
    'Terms' => (isset($requestArray['Terms'])) ? $requestArray['Terms'] : '',
    'MerchantMemo' => (isset($requestArray['MerchantMemo']) && $requestArray['addMemoDesc'] == 'yes') ? $requestArray['MerchantMemo'] : '',
    'AllowPartialPayment' => (isset($requestArray['AllowPartialPayment']) && $requestArray['AllowPartialPayment'] == 'yes') ? true : '',
);

if ($request['invoiceData']['AllowPartialPayment'] === true) {
    $MinimumAmountDue = (isset($requestArray['MinimumAmountDue']) && floatval($requestArray['MinimumAmountDue']) > 0) ? number_format($requestArray['MinimumAmountDue'],2) : array();
    if(!empty($MinimumAmountDue)){
        $request['MinimumAmountDue'] = $MinimumAmountDue;
    }
}

$paymentTerm = array(
    'DueDate'  => isset($requestArray['DueDate']) ? date('Y-m-d Z', strtotime($requestArray['DueDate'])) : ''
);
if(!empty($paymentTerm['DueDate'])){
    $request['paymentTerm'] = $paymentTerm;
}

$request['billingInfo']['Email'] = isset($requestArray['BillingEmail']) ? $requestArray['BillingEmail'] : '';
$request['ccInfo']['Email'] = isset($requestArray['ccEmail']) ? $requestArray['ccEmail'] : '';
$request['itemArray'] = array();
$i=0;
foreach ($requestArray['itemArray'] as $value) {
    $request['itemArray'][$i]['Name'] = $value['Name'];
    if(isset($value['Description']) && !empty($value['Description'])){
        $request['itemArray'][$i]['Description'] = $value['Description'];
    }    
    $request['itemArray'][$i]['Quantity'] = $value['Quantity'];
    $request['itemArray'][$i]['UnitPrice'] = $value['UnitPrice'];
    if(isset($value['Tax']['Name']) && !empty($value['Tax']['Name']) && isset($value['Tax']['Percent']) && !empty($value['Tax']['Percent'])){
        $request['itemArray'][$i]['Tax'] = $value['Tax'];        
    } 
    $i++;
}
if($requestArray['finalDiscountForInvoice']['type'] == 'Percent' && $requestArray['finalDiscountForInvoice']['Amount'] > 0){
    $request['finalDiscountForInvoice'] = array('type' => 'Percent', 'Percent' => intval($requestArray['finalDiscountForInvoice']['Amount']));
}
if($requestArray['finalDiscountForInvoice']['type'] == 'Amount' && $requestArray['finalDiscountForInvoice']['Amount'] > 0){
    $request['finalDiscountForInvoice'] = array('type' => 'Amount', 'Amount' => array ('Currency' => 'USD',
                                                                    'Value' => $requestArray['finalDiscountForInvoice']['Amount']
                                                            ));
}
if(isset($requestArray['ShippingCost']) && $requestArray['ShippingCost'] > 0 ){
    $request['shippingCost']['type'] = 'Amount';
    $request['shippingCost']['Currency'] = array(
                'Currency' => 'USD',
                'Value' => $requestArray['ShippingCost']
            );
}
$configArray = array(
    'ClientID' => $rest_client_id,
    'ClientSecret' => $rest_client_secret
);

$PayPal = new \angelleye\PayPal\rest\invoice\InvoiceAPI($configArray);

$request['merchantInfo'] = array(
    'Email' => 'tejasm-merchant@itpathsolutions.co.in', // The merchant email address. Maximum length is 260 characters.
    'FirstName' => 'Drew', // The merchant first name. Maximum length is 30 characters.
    'LastName' => 'Angell', // The merchant last name. Maximum length is 30 characters.        
    'BusinessName' => 'AngellEYE', // The merchant company business name. Maximum length is 100 characters.
);
$attachments = json_decode($requestArray['attachedFiles'],true);
$fileToAttach = array();
if(!empty($attachments['files']) && count($attachments['files']) > 0){
    $fileToAttach = $attachments['files'];
}
$request['attachments'] = $fileToAttach;
$returnArray = $PayPal->create_invoice($request);
if(isset($returnArray['RESULT']) && $returnArray['RESULT'] == 'Success'){
    $invoice_id = $returnArray['INVOICE']['id'];
    if(isset($requestArray['save_as_draft']) && $requestArray['save_as_draft']  =='no'){
        $send_invoice = $PayPal->send_invoice($invoice_id);
        if(isset($send_invoice['RESULT']) && $send_invoice['RESULT'] === 'Success'){
            echo json_encode( array('success' =>'true','msg'=>'Your invoice has been saved to your PayPal account.','invoice_id'=>$invoice_id));
            exit;
        }
        else{
            echo json_encode( array('success' =>'successwithwarning','msg'=>'Your invoice has been saved to your PayPal account.','invoice_id'=>$invoice_id));
            exit;
        }
    }
    else{
        echo json_encode( array('success' =>'true','msg'=>'Your invoice has been saved to your PayPal account.','invoice_id'=>$invoice_id));
        exit;
    }
    
}
else{
    echo json_encode(array('success' =>'false','msg'=> 'Your invoice failed to be created.','error' => $returnArray));
    exit;
}