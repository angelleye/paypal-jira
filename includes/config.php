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
//$sandbox = $host_split[0] == 'sandbox' && $host_split[1] == 'domain' ? TRUE : FALSE;
$sandbox=TRUE;
$domain = $sandbox ? '' : '';

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
 * PayPal API Version
 * ------------------
 * The library is currently using PayPal API version 109.0.  
 * You may adjust this value here and then pass it into the PayPal object when you create it within your scripts to override if necessary.
 */
$api_version = '1.0.0';

/**
 * PayPal Application ID
 * --------------------------------------
 * The application is only required with Adaptive Payments applications.
 * You obtain your application ID but submitting it for approval within your 
 * developer account at http://developer.paypal.com
 *
 * We're using shorthand if/else statements here to set both Sandbox and Production values.
 * Your sandbox values go on the left and your live values go on the right.
 * The sandbox value included here is a global value provided for developrs to use in the PayPal sandbox.
 */
$application_id = $sandbox ? 'APP-80W284485P519543T' : '';

/**
 * PayPal Developer Account Email Address
 * This is the email address that you use to sign in to http://developer.paypal.com
 */
$developer_account_email = '';

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
$api_username = $sandbox ? 'sandbo_1215254764_biz_api1.angelleye.com' : 'LIVE_API_USERNAME';
$api_password = $sandbox ? '1215254774' : 'LIVE_API_PASSWORD';
$api_signature = $sandbox ? 'AiKZhEEPLJjSIccz.2M.tbyW5YFwAb6E3l6my.pY9br1z2qxKx96W18v' : 'LIVE_API_SIGNATURE';



//$api_username = $sandbox ? 'kiritpatel571989-facilitator_api1.ymail.com' : 'info_api1.kidsunite4hope.org';
//$api_password = $sandbox ? '2HZ9RN3RAYKNJQ96' : '7E4F5HLE5AK2YYLV';
//$api_signature = $sandbox ? 'AFcWxV21C7fd0v3bYYYRCpSSRl31AD2wwjg4RmifHyynGrE4ye1JNszU' : 'AFcWxV21C7fd0v3bYYYRCpSSRl31AA-fwsa0gzQ2px-Bi1StTHys.1Nq';


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
$payflow_username = $sandbox ? 'TESTIPSUSER' : 'LIVE_PAYFLOW_USERNAME';
$payflow_password = $sandbox ? 'Tejas111' : 'LIVE_PAYFLOW_PASSWORD';
$payflow_vendor = $sandbox ? 'IPSPAYPALTJ' : 'LIVE_PAYFLOW_VENDOR';
$payflow_partner = $sandbox ? 'PayPal' : 'LIVE_PAYFLOW_PARTNER';

/**
 * PayPal REST API Credentials
 * ---------------------------
 * These are the API credentials used for the PayPal REST API.
 * These are used any time you're working with the REST API child class.
 * 
 * You may obtain these credentials from within your account at http://developer.paypal.com
 */
//$rest_client_id = $sandbox ? 'AZjkAvQ2WpmtLg1q2fMCMiQrdGXpQWoRWfb2xGGPmuDvJ1R-Ot7IFizAiWpSZ7Ce2Cx4-7C3sIL9BRV7' : 'LIVE_CLIENT_ID';
//$rest_client_secret = $sandbox ? 'ECG5lq0euJS9NgrAcAFE1Q5ogLyh9W6od8M9SQ79I7wemp4UrLdGhC--mmhfKuD5a1e0C-92UzCvM3vT' : 'LIVE_SECRET_ID';
$rest_client_id = $sandbox ? 'AacwX_JLDp8RPX64Fylr_oPZaJajy5uh4Xgpz4bjylEY0HO2fryMGW7mAoXDIJuIpcYeKIXtbBZ7mxUP' : 'LIVE_CLIENT_ID';
$rest_client_secret = $sandbox ? 'EI9fqz8TQmYVpv_WTdre-XSXMJcAkduSSDaRnsDIMWQTWdSVAs-X4nqT4uikE4wJ6YX-R-3XCtEeTklo' : 'LIVE_SECRET_ID';

/**
 * PayPal Finance Portal API
 * -------------------------
 * These are credentials used for obtaining a PublisherID used in Bill Me Later Banner code.
 * As of now, these are specialized API's and you must obtain credentials directly from a PayPal rep.
 */
$finance_access_key = $sandbox ? 'SANDBOX_ACCESS_KEY' : 'LIVE_ACCESS_KEY';
$finance_client_secret = $sandbox ? 'SANDBOX_CLIENT_SECRET' : 'LIVE_CLIENT_SECRET';

/**
 * Third Party User Values
 * These can be setup here or within each caller directly when setting up the PayPal object.
 */
$api_subject = '';	// If making calls on behalf a third party, their PayPal email address or account ID goes here.
$device_id = '';
$device_ip_address = $_SERVER['REMOTE_ADDR'];

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
