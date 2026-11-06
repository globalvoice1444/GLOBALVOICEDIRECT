<?php
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
  $activeCallsResults = "";
  if(isset($_GET['ivrnumber'])){
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
	$session_id = $session->login(array('login'=>$login, 'password'=>$password))->session_id;
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
	} catch(SoapFault $e) {var_dump($e);}
}else{
	$activeCallsResults = "";
}
?>