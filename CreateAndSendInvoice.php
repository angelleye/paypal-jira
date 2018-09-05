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
    'Number' => (isset($requestArray['Number'])) ? trim($requestArray['Number']) : '',
    'LogoUrl' => (isset($requestArray['LogoUrl'])) ? $requestArray['LogoUrl'] : '',
    'InvoiceDate' => (isset($requestArray['InvoiceDate'])) ? date('Y-m-d Z', strtotime($requestArray['InvoiceDate'])) : '',
    'Reference' => (isset($requestArray['Reference'])) ? trim($requestArray['Reference']) : '',
    'Note' => (isset($requestArray['Note'])) ? trim($requestArray['Note']) : '',
    'Terms' => (isset($requestArray['Terms'])) ? trim($requestArray['Terms']) : '',
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

$request['billingInfo']['Email'] = isset($requestArray['BillingEmail']) ? trim($requestArray['BillingEmail']) : '';
$request['ccInfo']['Email'] = isset($requestArray['ccEmail']) ? trim($requestArray['ccEmail']) : '';
$request['itemArray'] = array();
$i=0;
foreach ($requestArray['itemArray'] as $value) {
    $request['itemArray'][$i]['Name'] = trim($value['Name']);
    if(isset($value['Description']) && !empty($value['Description'])){
        $request['itemArray'][$i]['Description'] = trim($value['Description']);
    }    
    $request['itemArray'][$i]['Quantity'] = intval(trim($value['Quantity']));
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
                                                                    'Value' => number_format($requestArray['finalDiscountForInvoice']['Amount'],2)
                                                            ));
}
if(isset($requestArray['ShippingCost']) && $requestArray['ShippingCost'] > 0 ){
    $request['shippingCost']['type'] = 'Amount';
    $request['shippingCost']['Currency'] = array(
                'Currency' => 'USD',
                'Value' => number_format($requestArray['ShippingCost'],2)
            );
}
$configArray = array(
    'ClientID' => $rest_client_id,
    'ClientSecret' => $rest_client_secret
);

$PayPal = new \angelleye\PayPal\rest\invoice\InvoiceAPI($configArray);
$str = file_get_contents('includes/saved_configuration.json');
$marray = json_decode($str,true);

$request['merchantInfo'] = array(
    'Email' => $marray['email_address'], // The merchant email address. Maximum length is 260 characters.
    'FirstName' => $marray['fname'], // The merchant first name. Maximum length is 30 characters.
    'LastName' => $marray['lname'], // The merchant last name. Maximum length is 30 characters.        
    'BusinessName' => $marray['company_name'], // The merchant company business name. Maximum length is 100 characters.
);
$attachments = json_decode($requestArray['attachedFiles'],true);
$fileToAttach = array();
if(!empty($attachments['files']) && count($attachments['files']) > 0){
    $fileToAttach = $attachments['files'];
}
$request['attachments'] = $fileToAttach;
$file = 'logs/logs.txt';
$fh = fopen($file, 'a');
fwrite($fh,date('m-d-Y @ H:i:s -') . "PayPal Create Invoice Request " . print_r($request, true). "\n");
$returnArray = $PayPal->create_invoice($request);
fwrite($fh,date('m-d-Y @ H:i:s -') . "PayPal Create Invoice Response " . print_r($returnArray, true). "\n");
fclose($fh);
if(isset($returnArray['RESULT']) && $returnArray['RESULT'] == 'Success'){
    $invoice_id = $returnArray['INVOICE']['id'];
    if(isset($requestArray['save_as_draft']) && $requestArray['save_as_draft']  =='no'){
        // Save log request  
        $fh = fopen($file, 'a');
        fwrite($fh,date('m-d-Y @ H:i:s -') . "PayPal Send Invoice Request " . print_r($invoice_id, true). "\n");
        
        $send_invoice = $PayPal->send_invoice($invoice_id);    
        
        // Save log response  
        fwrite($fh,date('m-d-Y @ H:i:s -') . "PayPal Send Invoice Response " . print_r($send_invoice, true). "\n");
        fclose($fh);
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
