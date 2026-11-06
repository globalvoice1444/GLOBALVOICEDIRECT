<?php

/*$host = 'mybilling.globalvoicedirect.com'; // mybilling.yoursite.com or IP address
$um = 'mybilling.globalvoicedirect.com'; //UM web interface address of your environment, e.g., um.telinta.com
$login = 'max21'; // admin/root login
$password = getenv('TELINTA_API_PASSWORD'); // password or token
$limit = 1000;
$soap_options = array(
  'trace' => 1,
  'exceptions' => 0,
  'cache_wsdl' => WSDL_CACHE_BOTH,
  'connection_timeout' => 600,
  'stream_context' => stream_context_create(array(
    'ssl' => array(
      //'verify_peer' => false,
      //'verify_peer_name' => false,
      'allow_self_signed' => true,
      'ciphers'=>'AES256-SHA',
    ),
  )),
  'user_agent' => 'AUDIO-BROADCAST-PHP-SOAP-'.$login,
);

date_default_timezone_set("America/New_York");



$service = 'Admin';
$account_id = "12392061647";
$voip_password = getenv('TELINTA_VOIP_PASSWORD');

$session = new SoapClient ("https://" . $host . "/wsdl/Session${service}Service.wsdl", $soap_options);*/
$host = 'mybilling.globalvoicedirect.com' ; // mybilling . yourdomain .com or I P address
	$login = 'max21' ; //SOAP login
	$password = getenv('TELINTA_API_PASSWORD') ; //SOAP password
	$service = 'Admin' ; //Admin or Re selle r se r vi ce
	$i_customer = '' ; //i_customer value
	$info_type = 'countries' ;
	$soap_options = array(
		'trace' => 1,
		'exceptions' => 0,
		'cache_wsdl' => WSDL_CACHE_BOTH,
		'connection_timeout' => 600,
		'stream_context' => stream_context_create(array(
			'ssl' => array(
				//'verify_peer' => false,
				//'verify_peer_name' => false,
				'allow_self_signed' => true,
				'ciphers'=>'AES256-SHA',
			),
		)),
	'user_agent' => 'AUDIO-BROADCAST-PHP-SOAP-'.$login,
	);

	date_default_timezone_set("America/New_York");
	$session = new SoapClient ("https://" . $host . "/wsdl/Session${service}Service.wsdl", $soap_options);
//MR50 password auth
$session_id =$session->login ($login,$password);
 
//MR50 token auth
if (empty($session_id)) { # MR50 token
	$session_id = $session->login(array('login'=>$login, 'token'=>$password))->session_id;
}
//MR45 password auth
if (empty($session_id)) { # MR50 token
	$session_id = $session->login($login, $password);
}
/*var_dump($session_id);
die();*/
$headers = NULL;
	$headers [] = new SoapVar("<session_id>$session_id</session_id>" ,
	XSD_ANYXML,
	"session_id " ,
	"https://" . $host . "/Porta/SOAP/BillingSession" );
	$auth_info = new SoapHeader("https://" . $host . "/Porta/SOAP/BillingSession" ,
	"auth_info",
	$headers);
	$internal_client = new SoapClient("https://" . $host . "/wsdl/BillingSessionAdminService.wsdl", $soap_options) ;
	try{
		$internal_client->__setSoapHeaders( $auth_info );
		$TransferToRequest = array(
		'i_customer' => '270239'
		) ;
		$TransferToResponse = $internal_client->get_active_calls_list($TransferToRequest);
		$TransferToResponse = $internal_client->get_active_calls_list($TransferToRequest);
		$responseArray = array();
		foreach ($TransferToResponse->active_calls_list as $value) {
			if($value->CLI === $_GET['ivrnumber']){
				array_push($responseArray, (object) ['CLD'=>$value->CLD, 'CLI' => $value->CLI, 'connect_time' => $value->connect_time]);
			}
		}
		$activeCallsResults = $responseArray;
		//var_dump('reach');
	} catch(SoapFault $e) {var_dump($e);}
	echo json_encode($activeCallsResults);
//	var_dump($activeCallsResults);
$session->logout($session_id);


?>
