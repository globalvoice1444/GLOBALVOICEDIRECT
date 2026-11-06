<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use DateTime;
use Auth;
use Hash;
use App\Classes\clickatell\Rest;
use App\Classes\clickatell\ClickatellException;
use DB;
use File;
use SoapClient;
use SoapFault;
use SoapHeader;
use SoapVar;

class TestController extends Controller
{
    public function sendemail(Request $req){
    	ini_set('max_execution_time', '0'); // for infinite time of execution 
    	
    	// Send Email For Varification   
                  $htmlStr = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

                    <html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:v="urn:schemas-microsoft-com:vml">
                    <head>
                    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
                    <meta content="width=device-width" name="viewport"/>
                    <meta content="IE=edge" http-equiv="X-UA-Compatible"/>
                    <title></title>
                    <link href="https://fonts.googleapis.com/css?family=sans-serif" rel="stylesheet" type="text/css"/>
                    <style type="text/css">
                                body {
                                      margin: 0;
                                      padding: 0;
                                }

                                table,
                                td,
                                tr {
                                      vertical-align: top;
                                      border-collapse: collapse;
                                }

                                * {
                                      line-height: inherit;
                                }

                                a[x-apple-data-detectors=true] {
                                      color: inherit !important;
                                      text-decoration: none !important;
                                }
                                p {
								    padding-left: 20px !important;
								    padding-right: 20px !important;
								    text-align: justify !important;
								}
                          </style>
                    <style id="media-query" type="text/css">
                                @media (max-width: 620px) {

                                      .block-grid,
                                      .col {
                                            min-width: 320px !important;
                                            max-width: 100% !important;
                                            display: block !important;
                                      }

                                      .block-grid {
                                            width: 100% !important;
                                      }

                                      .col {
                                            width: 100% !important;
                                      }

                                      .col>div {
                                            margin: 0 auto;
                                      }

                                      img.fullwidth,
                                      img.fullwidthOnMobile {
                                            max-width: 100% !important;
                                      }

                                      .no-stack .col {
                                            min-width: 0 !important;
                                            display: table-cell !important;
                                      }

                                      .no-stack.two-up .col {
                                            width: 50% !important;
                                      }

                                      .no-stack .col.num4 {
                                            width: 33% !important;
                                      }

                                      .no-stack .col.num8 {
                                            width: 66% !important;
                                      }

                                      .no-stack .col.num4 {
                                            width: 33% !important;
                                      }

                                      .no-stack .col.num3 {
                                            width: 25% !important;
                                      }

                                      .no-stack .col.num6 {
                                            width: 50% !important;
                                      }

                                      .no-stack .col.num9 {
                                            width: 75% !important;
                                      }

                                      .video-block {
                                            max-width: none !important;
                                      }

                                      .mobile_hide {
                                            min-height: 0px;
                                            max-height: 0px;
                                            max-width: 0px;
                                            display: none;
                                            overflow: hidden;
                                            font-size: 0px;
                                      }

                                      .desktop_hide {
                                            display: block !important;
                                            max-height: none !important;
                                      }
                                }
                          </style>
                    </head>
                    <body class="clean-body" style="margin: 0; padding: 0; -webkit-text-size-adjust: 100%; background-color: #e2eace;">
                    <table bgcolor="#e2eace" cellpadding="0" cellspacing="0" class="nl-container" role="presentation" style="table-layout: fixed; vertical-align: top; min-width: 320px; Margin: 0 auto; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #e2eace; width: 100%;" valign="top" width="100%">
                    <tbody>
                    <tr style="vertical-align: top;" valign="top">
                    <td style="word-break: break-word; vertical-align: top;" valign="top">
                    <div style="background-color:transparent;">
                    <div class="block-grid" style="Margin: 0 auto; min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: transparent;">
                    <div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
                    <div class="col num12" style="min-width: 320px; max-width: 600px; display: table-cell; vertical-align: top; width: 600px;">
                    <div style="width:100% !important;">
                    <div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:5px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;">
                    <div align="center" class="img-container center fullwidth" style="padding-right: 0px;padding-left: 0px;">
                    <div style="font-size:1px;line-height:25px"> </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    <div style="background-color:transparent;">
                    <div class="block-grid" style="Margin: 0 auto; min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: #FFFFFF;">
                    <div style="border-collapse: collapse;display: table;width: 100%;background-color:#FFFFFF;">
                    <div class="col num12" style="min-width: 320px; max-width: 600px; display: table-cell; vertical-align: top; width: 600px;">
                    <div style="width:100% !important;">
                    <div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:5px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;">
                    <div align="center" class="img-container center" style="padding-right: 0px;padding-left: 0px;">
                    <img align="center" alt="Image" border="0" class="center" src="https://globalvoicedirect.com/public/assets/img/modal/1.png" style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: 0; width: 100%; max-width: 95%; display: block;" title="Image" width="150"/>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    <div style="background-color:transparent;">
                    <div class="block-grid" style="Margin: 0 auto; min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: #FFFFFF;">
                    <div style="border-collapse: collapse;display: table;width: 100%;background-color:#FFFFFF;">
                    <div class="col num12" style="min-width: 320px; max-width: 600px; display: table-cell; vertical-align: top; width: 600px;">
                    <div style="width:100% !important;">
                    <div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;">
                    <div style="color:#0D0D0D;font-family:Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif;line-height:1.5;padding-top:20px;padding-right:10px;padding-bottom:10px;padding-left:10px;">
                    <div style="font-size: 12px; line-height: 1.5; color: #0D0D0D; font-family: Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif; mso-line-height-alt: 18px;">
                    <p style="font-size: 14px; line-height: 1.5; text-align: center; word-break: break-word; mso-line-height-alt: 21px; margin: 0;"><span style="font-size: 16px;">From all of us at Global Voice Direct</span></p>
                    <p style="font-size: 14px; line-height: 1.5; text-align: center; word-break: break-word; mso-line-height-alt: 21px; margin: 0;"><span style="font-size: 16px;">We would like to wish you a very Happy New Year 2025, and let you know that we are thankful for you—our valued clients and referral partners.<br/><br/>May your all the days ahead be filled with health, happiness, and joy!<br/></span></p>
                    <p style="font-size: 14px; line-height: 1.5; text-align: center; word-break: break-word; mso-line-height-alt: 21px; margin: 0;"><span style="font-size: 16px;"></span></p>
                    </div>
                    </div>


                    <div style="color:#0D0D0D;font-family:Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif;line-height:1.5;padding-top:20px;padding-right:10px;padding-bottom:30px;padding-left:10px;">
                    <div style="font-size: 12px; line-height: 1.5; color: #0D0D0D; font-family: Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif; mso-line-height-alt: 18px;">
                    <p style="text-align: center; line-height: 1.5; word-break: break-word; font-size: 16px; mso-line-height-alt: 24px; margin: 0;"><span style="font-size: 16px;"><br/>Kind regards,<br /><a href="https://globalvoicedirect.com/" target="_blank">Globalvoicedirect Team</a><br /></span></p>
                    <p style="text-align: center; line-height: 1.5; word-break: break-word; font-size: 16px; mso-line-height-alt: 24px; margin: 0;"><span style="font-size: 16px;"></span></p>
                    </div>
                    </div>

                    </div>

                    </div>
                    </div>

                    </div>
                    </div>
                    </div>
                    <div style="background-color:transparent;">
                    <div class="block-grid three-up" style="Margin: 0 auto; min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: #61251b;">
                    <div style="border-collapse: collapse;display: table;width: 100%;background-color:#61251b;">

                    <div class="col num4" style="max-width: 320px; min-width: 200px; display: table-cell; vertical-align: top; width: 200px;">
                    <div style="width:100% !important;">
                    <div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:5px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;">

                    <div style="color:#a8bf6f;font-family:Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif;line-height:1.2;padding-top:18px;padding-right:0px;padding-bottom:0px;padding-left:0px;">
                    <div style="font-size: 12px; line-height: 1.2; color: #a8bf6f; font-family: Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif; mso-line-height-alt: 14px;">
                    <p style="font-size: 12px; line-height: 1.2; text-align: center; word-break: break-word; mso-line-height-alt: 14px; margin: 0;"><span style="color: #ffffff; font-size: 11px;"></span></p>
                    </div>
                    </div>

                    </div>

                    </div>
                    </div>


                    <div class="col num4" style="max-width: 320px; min-width: 200px; display: table-cell; vertical-align: top; width: 200px;">
                    <div style="width:100% !important;">

                    <div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:5px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;">

                     

                    </div>

                    </div>
                    </div>


                    <div class="col num4" style="max-width: 320px; min-width: 200px; display: table-cell; vertical-align: top; width: 200px;">
                    <div style="width:100% !important;">

                    <div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:5px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;">

                    <div style="color:#a8bf6f;font-family:Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif;line-height:1.2;padding-top:18px;padding-right:0px;padding-bottom:0px;padding-left:0px;">
                    <div style="font-size: 12px; line-height: 1.2; color: #a8bf6f; font-family: Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif; mso-line-height-alt: 14px;">
                    <p style="font-size: 12px; line-height: 1.2; text-align: center; word-break: break-word; mso-line-height-alt: 14px; margin: 0;"><span style="color: #ffffff; font-size: 11px;"></span></p>
                    </div>
                    </div>

                    </div>

                    </div>
                    </div>

                    </div>
                    <div style="color: #ffffff;text-align: center; font-size: 11px; margin: 0;padding: 0 0 10px;">© 2010 - 2020 GlobalVoiceDirect.com, All Rights Reserved.</div>
                    </div>
                    </div>
                    <div style="background-color:transparent;">
                    <div class="block-grid" style="Margin: 0 auto; min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: transparent;">
                    <div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">

                    <div class="col num12" style="min-width: 320px; max-width: 600px; display: table-cell; vertical-align: top; width: 600px;">
                    <div style="width:100% !important;">

                    <div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;">

                    <table border="0" cellpadding="0" cellspacing="0" class="divider" role="presentation" style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;" valign="top" width="100%">
                    <tbody>
                    <tr style="vertical-align: top;" valign="top">
                    <td class="divider_inner" style="word-break: break-word; vertical-align: top; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; padding-top: 30px; padding-right: 30px; padding-bottom: 30px; padding-left: 30px;" valign="top">
                    <table align="center" border="0" cellpadding="0" cellspacing="0" class="divider_content" role="presentation" style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-top: 0px solid transparent; width: 100%;" valign="top" width="100%">
                    <tbody>
                    <tr style="vertical-align: top;" valign="top">
                    <td style="word-break: break-word; vertical-align: top; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;" valign="top"><span></span></td>
                    </tr>
                    </tbody>
                    </table>
                    </td>
                    </tr>
                    </tbody>
                    </table>

                    </div>

                    </div>
                    </div>

                    </div>
                    </div>
                    </div>

                    </td>
                    </tr>
                    </tbody>
                    </table>

                    </body>
                    </html>';
                  
                    $userDet=DB::table('wpgc_users')->where('active',0)->where('user_status',1)->where('ID','>',736)->select('user_email')->orderBy('id', 'DESC')->get();
                    // dd($userDet);
                    //exit;
                    $name = "Happy New Year 2025";
                    $email_sender = "no-reply@globalvoicedirect.com";
                    $subject = "Happy New Year 2025";
                    $body = $htmlStr;
                    foreach ($userDet as $value) {
                    	$recipient_email = $value->user_email;
                    	\Mail::send(array(), array(), function ($message) use ($htmlStr,$recipient_email,$subject) {
		                    $message->to($recipient_email)
		                    ->subject($subject)
		                    ->from(config('mail.from.address'),'Happy New Year 2025')
		                    ->html($htmlStr); 
		                  });
                    }


                    dd("Done");
                 
        // dd(\Mail::send(array(), array(), function ($message) {
        //   $message->to('bestsolutions4017@gamil.com')
        //   ->subject("Test Test")
        //   ->from(config('mail.from.address'))
        //   ->setBody("<h2>Ali Raza</h2>", 'text/html'); 
        // }));
    }  
    
    public function create_customer(){
        ini_set("soap.wsdl_cache_enabled", "1"); 
        date_default_timezone_set('UTC'); 
        
        $service = 'Admin'; 
        $host = 'mybilling.telinta.com'; // your domain should be placed here, like mybilling.your-domain.com
         
        $login = 'aliraza'; #API user login
        $password = env('TELINTA_API_PASSWORD'); #API user token
        
        $soap_options = array('trace' => 1,
        	'exceptions' => 1,
        	'cache_wsdl' => WSDL_CACHE_BOTH,
        	'connection_timeout' => 600,
        	'stream_context' => stream_context_create(array(
        		'ssl' => array(
        			'verify_peer' => false,
        			'verify_peer_name' => false,
        			'allow_self_signed' => true,
        			'ciphers' => 'AES256-SHA',
        		),
        	)),
        	'user_agent' => 'PHP-SOAP-API-script-'.$login,
        );
        
        $session = new SoapClient ("https://" . $host . "/wsdl/Session{$service}Service.wsdl", $soap_options); //initialize API client
        $session_id = $session->login(array('login' => $login, 'token' => $password))->session_id; //obtain session ID by logging in
        
        //add the auth_info header for further requests to be authorized right away
        $headers = NULL; 
        $headers[] = new SoapVar ("<session_id>$session_id</session_id>", XSD_ANYXML, "session_id", "https://" . $host . "/Porta/SOAP/Session");
        $auth_info = new SoapHeader ("https://" . $host . "/Porta/SOAP/Session", "auth_info", $headers); 
        
        $name = "Some customer name"; //new customer name
        $iso_4217 = 'USD'; //currency which will be used by the customer and its accounts
        
        #build request
        $Request = array( 
        	'customer_info' => array( 
        		'name' => $name,
        		'email' => 'bestsolutions4017@gmail.com',
        		'iso_4217' => $iso_4217,
        		'i_customer_type' => '1',
        		'opening_balance' => '10',
        		'i_balance_control_type' => '1',
        	), 
        ); 
        
        
        $service = new SoapClient("https://" . $host . "/wsdl/Customer{$service}Service.wsdl"); //switch API client to the Customer service
        $service->__setSoapHeaders($auth_info); 
        
        //execute request and print response
        try { 
        	  $add = $service->add_customer($Request);
        	  print_r($add->i_customer);
        } 
        
        // handle faults
        catch (SoapFault $e){ 
        	Log::error("Error: ".$e->getMessage()."\n"); 
        	return 0;
        } 
    }
    
    public function get_customer(){
        ini_set("soap.wsdl_cache_enabled", "1"); 
        date_default_timezone_set('UTC'); 
        
        $service = 'Admin'; 
        $host = 'mybilling.telinta.com'; // your domain should be placed here, like mybilling.your-domain.com
         
        $login = 'aliraza'; #API user login
        $password = env('TELINTA_API_PASSWORD'); #API user token
        
        $soap_options = array('trace' => 1,
        	'exceptions' => 1,
        	'cache_wsdl' => WSDL_CACHE_BOTH,
        	'connection_timeout' => 600,
        	'stream_context' => stream_context_create(array(
        		'ssl' => array(
        			'verify_peer' => false,
        			'verify_peer_name' => false,
        			'allow_self_signed' => true,
        			'ciphers' => 'AES256-SHA',
        		),
        	)),
        	'user_agent' => 'PHP-SOAP-API-script-'.$login,
        );
        
        $session = new SoapClient ("https://" . $host . "/wsdl/Session{$service}Service.wsdl", $soap_options); //initialize API client
        $session_id = $session->login(array('login' => $login, 'token' => $password))->session_id; //obtain session ID by logging in
        
        //add the auth_info header for further requests to be authorized right away
        $headers = NULL; 
        $headers[] = new SoapVar ("<session_id>$session_id</session_id>", XSD_ANYXML, "session_id", "https://" . $host . "/Porta/SOAP/Session");
        $auth_info = new SoapHeader ("https://" . $host . "/Porta/SOAP/Session", "auth_info", $headers); 
        
        $name = "Some customer name"; //new customer name
        $iso_4217 = 'USD'; //currency which will be used by the customer and its accounts
        
        #build request
        $Request = array( 
        	'email' => 'bestsolutions4017@gmail.com'
        ); 
        
        
        $service = new SoapClient("https://" . $host . "/wsdl/Customer{$service}Service.wsdl"); //switch API client to the Customer service
        $service->__setSoapHeaders($auth_info); 
        
        //execute request and print response
        try { 
        	  $customer_info = $service->get_customer_info(array('i_customer' => '270239'));
            dd($customer_info);
        } 
        
        // handle faults
        catch (SoapFault $e){ 
        	dd("Error: ".$e->getMessage()."\n"); 
        } 
    }
    
    public function create_account(){
        ini_set("soap.wsdl_cache_enabled", "1"); 
        date_default_timezone_set('UTC'); 
        
        $service = 'Admin'; 
        $host = 'mybilling.telinta.com'; // your domain should be placed here, like mybilling.your-domain.com
         
        $login = 'aliraza'; #API user login
        $password = env('TELINTA_API_PASSWORD'); #API user token
        
        $soap_options = array('trace' => 1,
        	'exceptions' => 1,
        	'cache_wsdl' => WSDL_CACHE_BOTH,
        	'connection_timeout' => 600,
        	'stream_context' => stream_context_create(array(
        		'ssl' => array(
        			'verify_peer' => false,
        			'verify_peer_name' => false,
        			'allow_self_signed' => true,
        			'ciphers' => 'AES256-SHA',
        		),
        	)),
        	'user_agent' => 'IMPORT-ACC-WITH-FWD-PHP-SOAP-'.$login,
        );
        
        $session = new SoapClient ("https://" . $host . "/wsdl/Session{$service}Service.wsdl", $soap_options); //initialize API client
        $session_id = $session->login(array('login' => $login, 'token' => $password))->session_id; //obtain session ID by logging in
         
        //add the auth_info header for further requests to be authorized right away
        $headers = NULL; 
        $headers[] = new SoapVar ("<session_id>$session_id</session_id>", XSD_ANYXML, "session_id", "https://" . $host . "/Porta/SOAP/Session");
        $auth_info = new SoapHeader ("https://" . $host . "/Porta/SOAP/Session", "auth_info", $headers); 
        
        $id = 'fax020' . time(); //generate new account ID
        $date = date('Y-m-d'); //activation_date for new account
        $i_product = '40281'; //unique identificator of a product that should be assigned to the account upon creation. Can be obtained using the get_product_list method
        $i_customer = '605311'; //unique identificator of a customer under which an account should be created. Can be obtained using the get_customer_list method
        $h323_password = substr(str_shuffle(md5(time())),0,10); //generate random password
        
        #build request
        $AddAccRequest = array( 
        	'account_info' => array( 
        		'id' => $id,
        		'billing_model' => '1',
        		'activation_date' => $date,
        		'i_product' => $i_product,
        		'h323_password' => $h323_password,
        		'i_customer' => $i_customer,
        		'number' => '5619052922', 
        		'faxnum' => '5619052922'
        	), 
        ); 
        
        $acc_service = new SoapClient("https://" . $host . "/wsdl/Account{$service}Service.wsdl"); //switch API client to the Account service
        $acc_service->__setSoapHeaders($auth_info); 
        
        //execute request and print response
        try { 
        	  $add_acc = $acc_service->add_account($AddAccRequest);
        	  dd($add_acc->i_account);
        } 
        
        //handle faults
        catch (SoapFault $e){ 
        	print "Error: ".$e->getMessage()."\n"; 
        } 
    }
    
    public function get_product_list(){
        ini_set("soap.wsdl_cache_enabled", "1"); 
        date_default_timezone_set('UTC'); 
        
        $service = 'Admin'; 
        $host = 'mybilling.telinta.com'; // your domain should be placed here, like mybilling.your-domain.com
         
        $login = 'aliraza'; #API user login
        $password = env('TELINTA_API_PASSWORD'); #API user token
        
        $soap_options = array('trace' => 1,
        	'exceptions' => 1,
        	'cache_wsdl' => WSDL_CACHE_BOTH,
        	'connection_timeout' => 600,
        	'stream_context' => stream_context_create(array(
        		'ssl' => array(
        			'verify_peer' => false,
        			'verify_peer_name' => false,
        			'allow_self_signed' => true,
        			'ciphers' => 'AES256-SHA',
        		),
        	)),
        	'user_agent' => 'PHP-SOAP-API-script-'.$login,
        );
        
        $session = new SoapClient ("https://" . $host . "/wsdl/Session{$service}Service.wsdl", $soap_options); //initialize API client
        $session_id = $session->login(array('login' => $login, 'token' => $password))->session_id; //obtain session ID by logging in
        
        //add the auth_info header for further requests to be authorized right away
        $headers = NULL; 
        $headers[] = new SoapVar ("<session_id>$session_id</session_id>", XSD_ANYXML, "session_id", "https://" . $host . "/Porta/SOAP/Session");
        $auth_info = new SoapHeader ("https://" . $host . "/Porta/SOAP/Session", "auth_info", $headers); 
         
        $GetProductListRequest = array( 
        	'limit' => 10, 
        ); 
        
        $service = new SoapClient("https://" . $host . "/wsdl/Product{$service}Service.wsdl"); //switch API client to the Product service
        $service->__setSoapHeaders($auth_info); 
        
        //execute request and print response
        try { 
        	  $product_list = $service->get_product_list($GetProductListRequest);
        	  dd($product_list);
        	} 
        
        // handle faults
        catch (SoapFault $e){ 
        	print "Error: ".$e->getMessage()."\n"; 
        } 
    }
    
    public function get_product_info($i_product=null){
        ini_set("soap.wsdl_cache_enabled", "1"); 
        date_default_timezone_set('UTC'); 
        
        $service = 'Admin'; 
        $host = 'mybilling.telinta.com'; // your domain should be placed here, like mybilling.your-domain.com
        
        $login = 'aliraza'; #API user login
        $password = env('TELINTA_API_PASSWORD'); #API user token
        
        $soap_options = array('trace' => 1,
        	'exceptions' => 1,
        	'cache_wsdl' => WSDL_CACHE_BOTH,
        	'connection_timeout' => 600,
        	'stream_context' => stream_context_create(array(
        		'ssl' => array(
        			'verify_peer' => false,
        			'verify_peer_name' => false,
        			'allow_self_signed' => true,
        			'ciphers' => 'AES256-SHA',
        		),
        	)),
        	'user_agent' => 'PHP-SOAP-API-script-'.$login,
        );
        
        $session = new SoapClient ("https://" . $host . "/wsdl/Session{$service}Service.wsdl", $soap_options); //initialize API client
        $session_id = $session->login(array('login' => $login, 'token' => $password))->session_id; //obtain session ID by logging in
        
        //add the auth_info header for further requests to be authorized right away
        $headers = NULL; 
        $headers[] = new SoapVar ("<session_id>$session_id</session_id>", XSD_ANYXML, "session_id", "https://" . $host . "/Porta/SOAP/Session");
        $auth_info = new SoapHeader ("https://" . $host . "/Porta/SOAP/Session", "auth_info", $headers); 
         
        $GetProductInfoRequest = array( 
        	'i_product' => '40281', //set the i_product value of the Product to be checked
        ); 
        
        $service = new SoapClient("https://" . $host . "/wsdl/Product{$service}Service.wsdl"); //switch API client to the Product service
        $service->__setSoapHeaders($auth_info); 
        
        //execute request and print response
        try { 
        	  $product_info = $service->get_product_info($GetProductInfoRequest);
        	  print_r($product_info);
        	} 
        
        // handle faults
        catch (SoapFault $e){ 
        	print "Error: ".$e->getMessage()."\n"; 
        } 
    }
    
    public function assign_did_account($accountId=null, $didNumber=null) {
        ini_set("soap.wsdl_cache_enabled", "1"); 
        date_default_timezone_set('UTC'); 
        
        $service = 'Admin'; 
        $host = 'mybilling.telinta.com'; // your domain should be placed here, like mybilling.your-domain.com
        
        $login = 'aliraza'; #API user login
        $password = env('TELINTA_API_PASSWORD'); #API user token
        
        $soap_options = array('trace' => 1,
        	'exceptions' => 1,
        	'cache_wsdl' => WSDL_CACHE_BOTH,
        	'connection_timeout' => 600,
        	'stream_context' => stream_context_create(array(
        		'ssl' => array(
        			'verify_peer' => false,
        			'verify_peer_name' => false,
        			'allow_self_signed' => true,
        			'ciphers' => 'AES256-SHA',
        		),
        	)),
        	'user_agent' => 'PHP-SOAP-API-script-'.$login,
        );
        
        $session = new SoapClient ("https://" . $host . "/wsdl/Session{$service}Service.wsdl", $soap_options); //initialize API client
        $session_id = $session->login(array('login' => $login, 'token' => $password))->session_id; //obtain session ID by logging in
        
        //add the auth_info header for further requests to be authorized right away
        $headers = NULL; 
        $headers[] = new SoapVar ("<session_id>$session_id</session_id>", XSD_ANYXML, "session_id", "https://" . $host . "/Porta/SOAP/Session");
        $auth_info = new SoapHeader ("https://" . $host . "/Porta/SOAP/Session", "auth_info", $headers); 
         
        $GetProductInfoRequest = array( 
        	'i_master_account' => 45455,
            'i_did_number' => '5618726663'
        ); 
        
        $addNumberRequest = array( 
        	'number_info' => array('number'=>'5618726663', 'i_dv_batch' => '87686548654')
        ); 
        
        $service = new SoapClient("https://" . $host . "/wsdl/DID{$service}Service.wsdl"); //switch API client to the Product service
        $service->__setSoapHeaders($auth_info); 
        
        $functions = $service->__getFunctions();
        
        //execute request and print response
        try { 
              $product_info = $service->add_number($addNumberRequest);
        	  $product_info = $service->assign_did_to_account($GetProductInfoRequest);
        	  print_r($product_info);
        	} 
        
        // handle faults
        catch (SoapFault $e){ 
        	print "Error: ".$e->getMessage()."\n"; 
        } 
    }
    
    public function did_provider($accountId=null, $didNumber=null) {
        ini_set("soap.wsdl_cache_enabled", "1"); 
        date_default_timezone_set('UTC'); 
        
        $service = 'Admin'; 
        $host = 'mybilling.telinta.com'; // your domain should be placed here, like mybilling.your-domain.com
        
        $login = 'aliraza'; #API user login
        $password = env('TELINTA_API_PASSWORD'); #API user token
        
        $soap_options = array('trace' => 1,
        	'exceptions' => 1,
        	'cache_wsdl' => WSDL_CACHE_BOTH,
        	'connection_timeout' => 600,
        	'stream_context' => stream_context_create(array(
        		'ssl' => array(
        			'verify_peer' => false,
        			'verify_peer_name' => false,
        			'allow_self_signed' => true,
        			'ciphers' => 'AES256-SHA',
        		),
        	)),
        	'user_agent' => 'PHP-SOAP-API-script-'.$login,
        );
        
        $session = new SoapClient ("https://" . $host . "/wsdl/Session{$service}Service.wsdl", $soap_options); //initialize API client
        $session_id = $session->login(array('login' => $login, 'token' => $password))->session_id; //obtain session ID by logging in
        
        //add the auth_info header for further requests to be authorized right away
        $headers = NULL; 
        $headers[] = new SoapVar ("<session_id>$session_id</session_id>", XSD_ANYXML, "session_id", "https://" . $host . "/Porta/SOAP/Session");
        $auth_info = new SoapHeader ("https://" . $host . "/Porta/SOAP/Session", "auth_info", $headers); 
         
        $GetProductInfoRequest = array( 
        	'i_master_account' => 45455,
            'i_did_number' => '5618726663'
        ); 
        
        $addNumberRequest = array( 
        	'number_info' => array('number'=>'5618726663', 'i_dv_batch' => '87686548654')
        ); 
        
        $service = new SoapClient("https://" . $host . "/wsdl/DID_Provider{$service}Service.wsdl"); //switch API client to the Product service
        $service->__setSoapHeaders($auth_info); 
        
        $functions = $service->__getFunctions();
        
        //execute request and print response
        try { 
              $product_info = $service->get_numbers_list();
        	  dd($product_info);
        	} 
        
        // handle faults
        catch (SoapFault $e){ 
        	print "Error: ".$e->getMessage()."\n"; 
        } 
    }
  
}