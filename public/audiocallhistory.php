<?php

	$activeCallsResults='';
    if(isset($_GET['ivr'])){
      	$host = 'mybilling.globalvoicedirect.com' ; // mybilling . yourdomain .com or I P address
      	$login = 'max21' ; //SOAP login
      	$password = getenv('TELINTA_API_PASSWORD') ; //SOAP password
        //$password = 'REDACTED';
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
      $session_id =$session->login($login,$password);
	    // $session_id = $session->login(array('login'=>$login, 'password'=>$password))->session_id;
      if (empty($session_id)) { # MR50 token
        $session_id = $session->login(array('login'=>$login, 'token'=>$password))->session_id;
      }
      //MR45 password auth
      if (empty($session_id)) { # MR50 token
        $session_id = $session->login($login, $password);
      }
	    $headers = NULL;
	    $headers [] = new SoapVar("<session_id>$session_id</session_id>" , 
	    			XSD_ANYXML,
	      			"session_id " ,
	      			"https://" . $host . "/Porta/SOAP/Customer" );
	    $auth_info = new SoapHeader("https://" . $host . "/Porta/SOAP/Customer" ,
	    			"auth_info",
	      			$headers);
	    $internal_client = new SoapClient("https://" . $host . "/wsdl/CustomerAdminService.wsdl", $soap_options) ;
	       
	    try{
	        $internal_client->__setSoapHeaders( $auth_info );
	        $TransferToRequest = array(
	        'i_customer' => '270239',
	        'from_date' => Date('Y-m-d H:i:s',strtotime($_GET['from'])),
	        'to_date' => Date('Y-m-d H:i:s',strtotime($_GET['to']))
	        ) ;
	        $TransferToResponse = $internal_client->get_customer_xdrs($TransferToRequest);
	        // var_dump($TransferToResponse);
	        // die();
	        $responseArray = array();
	        foreach ($TransferToResponse->xdr_list as $value) {
	          if($value->CLI === $_POST['ivr']){
	            array_push($responseArray, (object) ['CLD'=>$value->CLD, 'CLI' => $value->CLI, 'connect_time' => $value->connect_time, 'disconnect_reason' => $value->disconnect_reason, 'disconnect_time' => $value->disconnect_time]);      }
	        }
	    	$activeCallsResults = $responseArray;
	    	echo json_encode($activeCallsResults);
	             
	    } catch(SoapFault $e) {var_dump($e);}
	}

?>