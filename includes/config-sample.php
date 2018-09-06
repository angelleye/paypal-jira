<?php
/**
 * Timezone Setting
 * List of Supported Timezones: http://www.php.net/manual/en/timezones.php
 */
date_default_timezone_set('America/Chicago');

/**
  * Enable Sessions
  * Checks to see if a session_id exists.  If not, a new session is started.
  */
if(!session_id()) session_start();

/** 
 * Sandbox Mode - TRUE/FALSE
 * Check the domain of the current page and set $sandbox accordingly.
 * This allows you to automatically use Sandbox or Live credentials throughout 
 * your application based on what server the app is running from.
 * 
 * I like to do this so I don't forget to update Sandbox credentials to Live
 * prior to uploading files to a production server.
 * 
 * In this case, it's checking to see if the current URL is http://sandbox.domain.*
 * If so, $sandbox is true and the PayPal sandbox will be used throughout.  If not, 
 * we'll assume it must be a live transaction and will use live credentials throughout.
 *
 * Following this pattern will allow you to create your own http://sandbox.domain.com test server, 
 * and then any time your code runs from that server, PayPal's sandbox will be used automatically.
 * 
 * If you would rather just set $sandbox to true/false on your own that's fine, 
 * but you have to make sure your live server always uses false and your test server
 * always uses true.  It's easy to forget this and up with real customers processing 
 * payments from your live site on the PayPal sandbox.
 */
$host_split = explode('.',$_SERVER['HTTP_HOST']);
$sandbox = $host_split[0] == 'sandbox' && $host_split[1] == 'domain' ? TRUE : FALSE;
$domain = $sandbox ? 'https://sandbox.domain.com/' : 'https://www.domain.com/';

/**
 * Enable error reporting if running in sandbox mode.
 */
if($sandbox)
{
	error_reporting(E_ALL|E_STRICT);
	ini_set('display_errors', '1');	
}

/*
 *   CURL OPTION VERBOSE SETUP 
 *   ------------------ 
 *   Set true. if you want to add CURLOPT_VERBOSE in CURL Request.
 *   Set false. if you don't want to add CURLOPT_VERBOSE in CURL Request.
 */
$curl_opt_verbose = true;

/**
 * PayPal Gateway API Credentials
 * ------------------------------
 * These are your PayPal API credentials for working with the PayPal gateway directly.
 * These are used any time you're using the parent PayPal class within the library.
 * 
 * We're using shorthand if/else statements here to set both Sandbox and Production values.
 * Your sandbox values go on the left and your live values go on the right.
 * 
 * You may obtain these credentials by logging into the following with your PayPal account: https://www.paypal.com/us/cgi-bin/webscr?cmd=_login-api-run
 */
$api_username = $sandbox ? 'SANDBOX_API_USERNAME' : 'LIVE_API_USERNAME';
$api_password = $sandbox ? 'SANDBOX_API_PASSWORD' : 'LIVE_API_PASSWORD';
$api_signature = $sandbox ? 'SANDBOX_API_SIGNATURE' : 'LIVE_API_SIGNATURE';

/**
 * Payflow Gateway API Credentials
 * ------------------------------
 * These are the credentials you use for your PayPal Manager:  http://manager.paypal.com
 * These are used when you're working with the PayFlow child class.
 * 
 * We're using shorthand if/else statements here to set both Sandbox and Production values.
 * Your sandbox values go on the left and your live values go on the right.
 * 
 * You may use the same credentials you use to login to your PayPal Manager, 
 * or you may create API specific credentials from within your PayPal Manager account.
 */
$payflow_username = $sandbox ? 'SANDBOX_PAYFLOW_USERNAME' : 'LIVE_PAYFLOW_USERNAME';
$payflow_password = $sandbox ? 'SANDSBOX_PAYFLOW_PASSWORD' : 'LIVE_PAYFLOW_PASSWORD';
$payflow_vendor = $sandbox ? 'SANDBOX_PAYFLOW_VENDOR' : 'LIVE_PAYFLOW_VENDOR';
$payflow_partner = $sandbox ? 'SANDBOX_PAYFLOW_PARTNER' : 'LIVE_PAYFLOW_PARTNER';

/**
 * PayPal REST API Credentials
 * ---------------------------
 * These are the API credentials used for the PayPal REST API.
 * These are used any time you're working with the REST API child class.
 * 
 * You may obtain these credentials from within your account at http://developer.paypal.com
 */
$rest_client_id = $sandbox ? 'SANDBOX_CLIENT_ID' : 'LIVE_CLIENT_ID';
$rest_client_secret = $sandbox ? 'SANDBOX_CLIENT_SECRET' : 'LIVE_SECRET_ID';

/**
 * Enable Headers
 * Option to print headers to screen when dumping results or not.
 */
$print_headers = false;

/**
 * Enable Logging
 * Option to log API requests and responses to log file.
 */
$log_results = true;
$log_path = $_SERVER['DOCUMENT_ROOT'].'/paypal-php-library/logs/';