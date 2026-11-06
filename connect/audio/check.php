<?php

$host = ''; // mybilling.yoursite.com or IP address
$um = ''; //UM web interface address of your environment, e.g., um.telinta.com
$login = ''; // admin/root login
$password = ''; // password
$limit = 100;

date_default_timezone_set("America/New_York");

if (isset($_FILES["file"]) && preg_match('/.*\.csv$/',$_FILES["file"]["name"]) && $_FILES["file"]["error"] == 0) {
	if ( $file = fopen( $_FILES["file"]["tmp_name"] , 'r' ) ) 
	{
	   	$numbers = array();
	   	$string = '';
		$i = 0;
		while ( $numbers[$i] = fgets ($file, 4096) ) {
			if ($i >= $limit) { break; }
			$numbers[$i] = preg_replace('/\s.*|\D/','',$numbers[$i]);
			if (!empty($numbers[$i])) {
				if ($i == 0) { $string .= $numbers[$i]; }
				else { $string .= "\r\n".$numbers[$i]; }
			}
        	$i++;
		}
		echo $string;
	}
	exit;
}
elseif (!empty($_GET['acc']) && !empty($_GET['pass'])) {
	$service = 'Admin';
	$account_id = $_GET['acc'];
	$voip_password = $_GET['pass'];
	
	$session = new SoapClient ("https://" . $host . "/wsdl/Session${service}Service.wsdl", array ('exceptions' => 0));

	$session_id = $session->login($login, $password);
	
	if (! is_soap_fault ($session_id)) {
		$headers = NULL;
		$headers[] = new SoapVar ("<session_id>$session_id</session_id>", XSD_ANYXML, "session_id", "https://" . $host . "/Porta/SOAP/Session");
		$auth_info = new SoapHeader ("https://" . $host . "/Porta/SOAP/Session", "auth_info", $headers);
	
		$account = new SoapClient("https://" . $host . "/wsdl/Account${service}Service.wsdl");
		$account->__setSoapHeaders($auth_info);
	
		$GetAccountInfoRequest = array('id' => $account_id);
		$GetAccountInfoResponse = $account->get_account_info($GetAccountInfoRequest);
	
		$account_info = $GetAccountInfoResponse->account_info;
	
		if ($account_info->blocked == "Y") {
			$response = "blocked";
			echo $response;
		}
		else if ($account_info->billing_model == 1 && $account_info->balance >= $account_info->credit_limit && isset($account_info->credit_limit)) {
			$response = "credit_exceeded";
			echo $response;
		}
		else if ((isset($account_info->expiration_date) && date('Y-m-d') >= $account_info->expiration_date) || (isset($account_info->life_time) && (date('Y-m-d') <= (strtotime('+' . $account_info->life_time . 'day', strtotime($account_info->activation_date)))))) {
			$response = "expired";
			echo $response;
		}
		else if ($account_info->h323_password == $voip_password) {
			$response = "ok" . "," . $um;
			echo $response;
		}
	}
	$session->logout($session_id);
}

?>
