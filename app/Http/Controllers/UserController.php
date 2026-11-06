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
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;


class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function sendSMS($from, $to, $message){
        
        $username = config('bulkVs.username');
        $password = config('bulkVs.password');
         
        $url = 'https://portal.bulkvs.com/api/v1.0/messageSend';
        
        $data = [
            'From' => $from,
            'To' => $to,
            'Message' => $message
        ];
        
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>json_encode($data),
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($username . ':' . $password)
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        
        return $response;
    }
    public function smsCampaignSendSms(Request $req)
    {

      
      $smsCredit=DB::table('sms_compaign_credit')->where('user_id',session()->get('user_id'))->first();
        
          $totalCredit = (int)$smsCredit->total_sms;

          switch ($req->input('form-method')) {
            case '1':
       
              if(isset($req->campaignName) and $req->campaignName != ""){
                
                $campainGrpDet=DB::table('sms_contact')->where('group_id',$req->campaignName)->where('user_id',session()->get('user_id'))->where('deleted',0)->orderBy('created_at','desc')->get();
                
                $campainGrpDetCount =count($campainGrpDet);

                $contactsCount = 0;
                if($campainGrpDetCount > 0){

                  if($campainGrpDetCount < $totalCredit){

                    $contactArr = array();
                    foreach($campainGrpDet as $result1) {
                      $num = str_replace("+","",$result1->contact_number);
                      array_push($contactArr, '+1'.$num);
                    }
                 
                    $cmessage = $req->smstext . " You Recieved This Message From ".session()->get('nice_name');
                    
                    $result = $this->sendSMS('18332164500', $contactArr, $cmessage);
                    // $restObj = new Rest('XU3E2ss2R16O3W4EgtSPFw=='); 
                    // $result = $restObj->sendMessage(['from' => '+19542105756', 'to' => $contactArr, 'content' => $cmessage, 'charset' => 'UTF-8']);
                    
                    $result = json_decode($result);
                    
                    if($result and isset($result->Results)){
                       foreach ($result->Results as $message) {
                          if($message->Status == 'SUCCESS'){
                            try{
    
                                DB::table('sms_sent')->insert(['user_id'=>session()->get('user_id'),'sent_sms'=>$cmessage,'sent_to'=>$message->To,'apiMessageId'=>$result->RefId,'accepted'=>true]);
                              }catch(\Exception $e){
                                return redirect()->route('smscampaign',['message'=>'Please upload csv file according to the sample given below! Please click on it to check the format.']);
                              }
                         
                            $contactsCount = $contactsCount + 1;
                          }else{
                            try{
    
                              DB::table('sms_sent')->insert(['user_id'=>session()->get('user_id'),'sent_sms'=>$cmessage,'sent_to'=>$message->TO,'apiMessageId'=>$result->RefId,'accepted'=>false]);
                            }catch(\Exception $e){
                              return redirect()->route('smscampaign',['message'=>'Please upload csv file according to the sample given below! Please click on it to check the format.']);
                            }
                          }
                        }
                   }else{
                       return redirect()->route('smscampaign',['message'=>'Please try agagin, something went wrong. Thanks!!']);
                   }
                    
                   
                    \DB::table('sms_compaign_credit')->where('user_id',session()->get('user_id'))->update(['updated_at'=>date("Y-m-d H:i:s"),'total_sms'=>\DB::raw('total_sms - '.$contactsCount.'')]);
                   
                    return redirect()->route('smscampaign',['message'=>"Your SMS Process Successfully. For Status Please <a href='/smscampaign?view_type=viewSmsDetail'>click here</a> or check sent SMS. Thanks!!"]);
               
                  }else{   
                    return redirect()->route('smscampaign',['message'=>'Your Credit is not enough. Please charge your Account First. Thanks!!']);
                  } 

                }else{
                  return redirect()->route('smscampaign',['message'=>'No Contacts found in this group. Please add contacts first']);
                }
              }else{
                return redirect()->route('smscampaign',['message'=>'Select The Campaign Group!']);
              }
              break;
            case '2':
                if($totalCredit > 0){
                    $contact_num = $req->smscontactnumber;
                    $contact_num = ["+1".str_replace("+","",$contact_num)];
                    $cmessage = $req->smstext . " You Recieved This Message From ".session()->get('nice_name');
                    
                    $result = $this->sendSMS('18332164500', $contact_num, $cmessage);
                
                
                    // $restObj = new Rest('XU3E2ss2R16O3W4EgtSPFw==');
                    // // $result = $clickatell->sendMessage(['from' => '+19542105756', 'to' => [$contact_num], 'content' => $cmessage, 'binary' =>'false', 'charset' => 'UTF-8']);
                    // $result = $restObj->sendMessage(['from' => '+19542105756', 'to' => [$contact_num], 'content' => $cmessage, 'clientMessageId' =>'IT Company', 'binary' =>'false', 'charset' => 'UTF-8']);
                 
                    $contactsCount = 0;
                    $result = json_decode($result);
                    
                    if($result and isset($result->Results)){
                        foreach ($result->Results as $message) {
        
                            if($message->Status == 'SUCCESS'){
                                try{
                                    DB::table('sms_sent')->insert(['user_id'=>session()->get('user_id'),'sent_sms'=>$cmessage,'sent_to'=>$message->To,'apiMessageId'=>$result->RefId,'accepted'=>true]);
                                    
                                    // DB::table('sms_sent')->insert(['user_id'=>session()->get('user_id'),'sent_sms'=>$cmessage,'sent_to'=>$message['to'],'apiMessageId'=>$message['apiMessageId'],'accepted'=>true]); 
                                   
                                      $contactsCount = $contactsCount + 1;
                                  
                                  $contactsCount = $contactsCount + 1;
                                }catch(Exception $e){
                                    return redirect()->route('smscampaign',['message'=>'Message was not sent! Please try again leter']);
                                }
                            }else{
                                try{
                                    DB::table('sms_sent')->insert(['user_id'=>session()->get('user_id'),'sent_sms'=>$cmessage,'sent_to'=>$message->To,'apiMessageId'=>$result->RefId,'accepted'=>false]);
                                    //   DB::table('sms_sent')->insert(['user_id'=>session()->get('user_id'),'sent_sms'=>$cmessage,'apiMessageId'=>$message['apiMessageId'],'accepted'=>false]);
                                }catch(\Exception $e){
                                  return redirect()->route('smscampaign',['message'=>'Message was not sent! Please try again leter']);
                                }
                            }
                             
                        }
                        \DB::table('sms_compaign_credit')->where('user_id',session()->get('user_id'))->update(['updated_at'=>date("Y-m-d H:i:s"),'total_sms'=>\DB::raw('total_sms - '.$contactsCount.'')]);

                        return redirect()->route('smscampaign',['message'=>"Your SMS Process Successfully. For Status Please <a href='/smscampaign?view_type=viewSmsDetail'>click here</a> or check sent SMS. Thanks!!"]);
                    }else{
                        return redirect()->route('smscampaign',['message'=>'Something went wrong. Please try again. Thanks!!']);
                    }
                }else{
                  return redirect()->route('smscampaign',['message'=>'Your Credit is not enough. Please charge your Account First. Thanks!!']);
                 
                }
              break;
            case '3':
            //parseCSV:

            $path = request()->file('campaignContacts')->getRealPath();

            $csv_data=\file($path);
            $csv_file_count=count($csv_data);

              if($csv_file_count > 0){

                $noOfContacts = 0;
                $contactArr = array();
                for($j=0;$j<count($csv_data);$j++)  {
              
                  $contact_num1 = str_replace("+","",$csv_data[$j]);
                  array_push($contactArr, "+1".$contact_num1);
                  $noOfContacts = $noOfContacts + 1;
                }
                // $restObj = new Rest('XU3E2ss2R16O3W4EgtSPFw==');

                if($noOfContacts < $totalCredit){
                    $cmessage = $req->smstext . " You Recieved This Message From ".session()->get('nice_name');
                    $result = $this->sendSMS('18332164500', $contactArr, $cmessage);
                    //$result = $restObj->sendMessage(['from' => '+19542105756','to' => $contactArr, 'content' => $cmessage, 'binary' =>'false', 'charset' => 'UTF-8']);
                    $contactsCount = 0;
                    $result = json_decode($result);
                    
                    if($result and isset($result->Results)){
                        foreach ($result->Results as $message) {
                            if($message->Status == 'SUCCESS'){
                                try{
                                  DB::table('sms_sent')->insert(['user_id'=>session()->get('user_id'),'sent_sms'=>$cmessage,'sent_to'=>$message->TO,'apiMessageId'=>$result->RefId,'accepted'=>true]);
                                    $contactsCount = $contactsCount + 1;
                                 }catch(\Exception $e){
        
                                    return redirect()->route('smscampaign',['message'=>'Please upload csv file according to the sample given below! Please click on it to check the format.']);
                                 }
    
                            }else{
                                try{
                                  
                                  DB::table('sms_sent')->insert(['user_id'=>session()->get('user_id'),'sent_sms'=>$cmessage,'sent_to'=>$message->TO,'apiMessageId'=>$result->RefId,'accepted'=>false]);
                                }catch(\Exception $e){
                                    return redirect()->route('smscampaign',['message'=>'Please upload csv file according to the sample given below! Please click on it to check the format.']);
                                }
                        }
                           
                      }
                    }else{
                        return redirect()->route('smscampaign',['message'=>'Something went wrong. Please try again. Thanks!!']);
                    }
                   
                   \DB::table('sms_compaign_credit')->where('user_id',session()->get('user_id'))->update(['updated_at'=>date("Y-m-d H:i:s"),'total_sms'=>\DB::raw('total_sms - '.$contactsCount.'')]);

                   return redirect()->route('smscampaign',['message'=>"Your SMS Process Successfully. For Status Please <a href='/smscampaign?view_type=viewSmsDetail'>click here</a> or check sent SMS. Thanks!!"]);
                }else{
                  return redirect()->route('smscampaign',['message'=>'Your Credit is not enough. Please charge your Account First. Thanks!!']);
                 
                }

              }else{

                 return redirect()->route('smscampaign',['message'=>'Upload the CSV file!']);
               
              }
              break;
            case '4':

            $path = request()->file('campaignContacts')->getRealPath();
            $csv_data=\file($path);
            $csv_file_count=count($csv_data);
             if($csv_file_count > 0){

                $noOfContacts = 0;

              for($j=0;$j<count($csv_data);$j++)  {
                  $noOfContacts = $noOfContacts + 1;
                }

                if($noOfContacts < $totalCredit){
                  
                    //$restObj = new Rest('XU3E2ss2R16O3W4EgtSPFw==');
                  for($j=0;$j<count($csv_data);$j++) {
                  $data=explode(',',$csv_data[$j]);
                   
                    $cmessage = $data[1] . " You Recieved This Message From ".session()->get('nice_name');
                    $cnumber = $data[0];
                    $cnumber = "+1".str_replace("+","",$cnumber);
                    // $result = $restObj->sendMessage(['from' => '+19542105756','to' => [$cnumber], 'content' => $cmessage, 'binary'=> 'false', 'charset' => 'UTF-8']);
                    $result = $this->sendSMS('18332164500', $cnumber, $cmessage);
                    $result = json_decode($result);
                    $contactsCount = 0;
                    if($result and isset($result->Results)){
                        foreach ($result->Results as $message) {
                            if($message->Status == 'SUCCESS'){
                                try{
                                    DB::table('sms_sent')->insert(['user_id'=>session()->get('user_id'),'sent_sms'=>$cmessage,'sent_to'=>$message->TO,'apiMessageId'=>$result->RefId,'accepted'=>true]);
                                }catch(Exception $e){
                                      return redirect()->route('smscampaign',['message'=>'Please upload csv file according to the sample given below! Please click on it to check the format.']);
                                }
                            
                                $contactsCount = $contactsCount + 1;
                            }else{
                                try{
                                  DB::table('sms_sent')->insert(['user_id'=>session()->get('user_id'),'sent_sms'=>$cmessage,'apiMessageId'=>$result->RefId,'accepted'=>false,'sent_to'=>$cnumber]);
                                }catch(Exception $e){
                                  return redirect()->route('smscampaign',['message'=>'Please upload csv file according to the sample given below! Please click on it to check the format.']);
                                }
                            }
                        }
                    }else{
                        
                    }
                  }
                  \DB::table('sms_compaign_credit')->where('user_id',session()->get('user_id'))->update(['updated_at'=>date("Y-m-d H:i:s"),'total_sms'=>\DB::raw('total_sms - '.$contactsCount.'')]);

                   

                    return redirect()->route('smscampaign',['message'=>"Your SMS Process Successfully. For Status Please <a href='/smscampaign?view_type=viewSmsDetail'>click here</a> or check sent SMS. Thanks!!"]);
                  }else{

                    return redirect()->route('smscampaign',['message'=>"Your Credit is not enough. Please charge your Account First. Thanks!!"]);    
                  }

              }else{

                 return redirect()->route('smscampaign',['message'=>"Upload the Csv File!"]);
            
              }
              break;
            default:
              # code...
              break;
          }
        

    //smscompain_send_sms ends here    
    }
    public function contactAction(Request $req)
    {
     // dd($req);
      try {
          if( $req->input('form-method') != ""){
            switch ($req->input('form-method')) {
              case '1':
                //dd('1');
                DB::table('compaign_group')->insert(['group_name'=>$req->campaignName,'user_id'=>session()->get('user_id')]);
               
                 return redirect()->route('smscampaign',['message'=>'Campaign Group Added Successfully.']);
              
                break;
              case '2':
                $path = request()->file('campaignContacts')->getRealPath();
                $csv_data=\file($path);
                $csv_file_count=count($csv_data);
                if( $csv_data > 0 and isset($req->campaignName) and $req->campaignName != ""){
                  $message = '';
                  for($j=0;$j<count($csv_data);$j++)  {
                    $data=explode(',',$csv_data[$j]);
                      DB::table('sms_contact')->insert(['user_id'=>session()->get('user_id'),'contact_name'=>$data[0]. ",". $data[1],'contact_number'=>$data[2],'group_id'=>$req->campaignName]);
                  }
                 
                  return redirect()->route('smscampaign',['message'=>'All Contacts Added Successfully']);    
                }else{
                   return redirect()->route('smscampaign',['message'=>'CSV file or campaign group not select!']);  
                }
                break;
              case '3':
              //dd($req);
                if(isset($req->contactfirstName) and isset($req->contactlastName) and $req->campaignName != "" ){
                // dd($req->campaignName);
                 DB::table('sms_contact')->insert(['user_id'=>session()->get('user_id'),'contact_name'=>$req->contactfirstName . "," . $req->contactlastName,'contact_number'=>$req->contactNumber,'group_id'=>$req->campaignName]);
                 
              
                  return redirect()->route('smscampaign',['message'=>'Contact Added Successfully.']);    

                }else{
                   return redirect()->route('smscampaign',['message'=>'Campaign Group not Selected!']); 
              
                }
                break;
              case '4':
                if( isset($req->contactfirstName) and isset($req->contactlastName) and isset($req->contactNumber) ){
                 
                 DB::table('sms_contact')->insert(['user_id'=>session()->get('user_id'),'contact_name'=>$req->contactfirstName . "," . $req->contactlastName,'contact_number'=>$req->contactNumber]);
                    return redirect()->route('smscampaign',['message'=>'Contact Added Successfully.']);             
                  

                }else{
                  return redirect()->route('smscampaign',['message'=>'Form Not Exist!']);    
                  
                }
                break;
              default:
              return redirect()->route('smscampaign',['message'=>'Form Not Exist!']);  
                
                break;
            }
          }else{
            return redirect()->route('smscampaign',['message'=>'Form Method not Valid!']);  
          
          }

        }catch(exception $e){
          //var_dump($e);
         // exit();
         return redirect()->route('smscampaign',['message'=>$e->getMessage()]);  
        }
    }
    public function orderDetail(){
      $userID = session()->get('user_id');
      $orderDet = \DB::table('orders')->leftjoin('shippinginfo', 'orders.OrderID', '=', 'shippinginfo.order_id')->where('OrderUserID', $userID)->get();
      //dd(count($orderDet)); 
      return view('users.orders', compact('orderDet'));  
    }
    public function viewOrderDetail($id){

      $userID = session()->get('user_id');
        $orderDet = \DB::table('orders')->leftjoin('shippinginfo', 'orders.OrderID', '=', 'shippinginfo.order_id')->where('OrderUserID', $userID)->where('orders.OrderID',$id)->select('orders.*','shippinginfo.*')->first();

        if($orderDet){
          $orderProducts = DB::table('invoices')->join('products','invoices.product_id','=','products.ProductID')->where('invoices.order_id','=', $id)->select('products.*','invoices.*')->get();
          
        }else{
            return redirect()->back()->with('error', 'Invalid...!');
        }
       
        return view('users.view_order_detail', compact('orderDet', 'orderProducts'));
    }
    public function unpaidOrder($id){
      if(session()->get("user_login")==true){
        $userID = session()->get('user_id');
        $orderDet = \DB::table('orders')->leftjoin('shippinginfo', 'orders.OrderID', '=', 'shippinginfo.order_id')->where('OrderUserID', $userID)->where('orders.OrderID',$id)->where('orders.paid',0)->select('orders.*','shippinginfo.*')->first();

         
        if($orderDet){
          $orderProducts = DB::table('invoices')->join('products','invoices.product_id','=','products.ProductID')->where('invoices.order_id','=', $id)->select('products.*','invoices.*')->get();
        }else{
            return redirect()->back()->with('error', 'Invalid...!');
        }
        return view('users.unpaid_payment_page', compact('orderDet', 'orderProducts'));
      } 
      
    }
    public function submit_unpaid_payment(Request $req){
      if(session()->get("user_login")==true){
        $validatedData = $req->validate([
          'cardNumber' => 'required|max:16|min:13',
          'cvv' => 'required|max:4|min:3',
          'month' => 'required',
          'year' => 'required',
          'oid' => 'required'

      ]);

      $orderid = $req->oid;

      $order = DB::table('orders')->where('OrderID', $orderid)->where('OrderUserID', session()->get('user_id'))->where('paid',0)->first();

      if($order){
          //Credit Card Detail
          $cardnumber = $req->cardnumber;
          $cardcvv = $req->cvv;
          $month = $req->month;
          $year = $req->year;

          $orderid = $order->OrderID;


          //Payment
          // Common setup for API credentials  
          $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();

          if($_SERVER['REMOTE_ADDR'] == "72.255.36.31"){
              $merchantAuthentication->setName('77Np2yKDr');
              $merchantAuthentication->setTransactionKey('5cYDE94TN4Zpw45z');
          }else{
            $merchantAuthentication->setName(config('services.authorize.login'));
            $merchantAuthentication->setTransactionKey(config('services.authorize.key'));
          }
          $refId = 'ref'.time();

          // Create the payment data for a credit card
          $creditCard = new AnetAPI\CreditCardType();
          $creditCard->setCardNumber($req->cardNumber);  
          $creditCard->setExpirationDate( $req->month."-".$req->year );
          $paymentOne = new AnetAPI\PaymentType();
          $paymentOne->setCreditCard($creditCard);

          $customer = new AnetAPI\CustomerDataType();
          $customer->setEmail($order->OrderEmail);

          //Ship To Info
          $shipto = new AnetAPI\NameAndAddressType();
          $shipto->setFirstName($order->OrderShipName);
          $shipto->setAddress($order->OrderShipAddress);
          $shipto->setCity($order->OrderCity);
          $shipto->setState($order->OrderState);
          $shipto->setZip($order->OrderZip);
          $shipto->setCountry($order->OrderCountry);

          // Bill To
          $billto = new AnetAPI\CustomerAddressType();
          //Get User Billing detail from Database
          $query='SELECT * from user_shipping_address WHERE user_id = ?';
          $billingDet  =DB::select($query, [session()->get('user_id')]);
          $billingDetCount = count($billingDet);
         
          if($billingDetCount > 0){
    
            //  $billingDetail = $billingDet->fetch
            $billto->setFirstName($billingDet[0]->firstname);
            $billto->setLastName($billingDet[0]->lastname);
            $billto->setCompany($billingDet[0]->company_name);
            $billto->setAddress($billingDet[0]->address);
            $billto->setCity($billingDet[0]->city);
            $billto->setState($billingDet[0]->state);
            $billto->setZip($billingDet[0]->zip);
            $billto->setCountry($billingDet[0]->country);
            $billto->setPhoneNumber($order->OrderPhone);
           
          }

          // Create a transaction
          $transactionRequestType = new AnetAPI\TransactionRequestType();
          $transactionRequestType->setTransactionType("authCaptureTransaction");

          $totalAmount = $order->OrderAmount;
          $downpayment = number_format($totalAmount  / 2 ,2);
          $remainingAmount = $totalAmount - $downpayment;
          $amount = strval($downpayment);

          $transactionRequestType->setAmount($amount);
          $transactionRequestType->setPayment($paymentOne);
          $transactionRequestType->setCustomer($customer);
          $transactionRequestType->setBillTo($billto);
          $transactionRequestType->setShipTo($shipto);
          $request = new AnetAPI\CreateTransactionRequest();
          $request->setMerchantAuthentication($merchantAuthentication);
          $request->setRefId($refId);
          $request->setTransactionRequest($transactionRequestType);
          $controller = new AnetController\CreateTransactionController($request);
      
          if($_SERVER['REMOTE_ADDR'] == "72.255.36.31"){
            $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
          }else{
            $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
          }

          if ($response != null) {
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode() == "Ok") {
                // Since the API request was successful, look for a transaction response
                // and parse it to display the results of authorizing the card
                $tresponse = $response->getTransactionResponse();
            
                if ($tresponse != null && $tresponse->getMessages() != null) {

                  $authcode = $tresponse->getAuthCode();
                  $transId = $tresponse->getTransId();
                  $created_at = date("Y-m-d H:i:s");

                  $user_id = session()->get('user_id');

                  DB::statement("UPDATE orders set paid=1, updated_at=? where OrderID = ? ", [$created_at, $orderid]);
            
                  DB::table('transactions')->insert(['transaction_id'=>$transId,'authentication_code'=>$authcode,'transaction_amount'=>$downpayment,'created_at'=>$created_at,'user_id'=>session()->get('user_id'), 'order_id'=>$orderid]);

                  $payablebalanceDet=DB::table('payable_amount')->where('user_id',session()->get('user_id'))->get();
              
                  $payableDetCount = count($payablebalanceDet);

                  if($payableDetCount > 0){
                    DB::statement("UPDATE payable_amount set r_amount=r_amount + ? where user_id = ? ", [$remainingAmount, $user_id]);
                  }else{
                    DB::table('payable_amount')->insert(['user_id'=>$user_id,'order_id'=>$orderid,'r_amount'=>$remainingAmount]);
                  }

                  // Send Email For Varification   
                  $htmlStr = "";
                  $htmlStr .= "Hi " . $order->OrderShipName . ",<br /><br />";

                  $htmlStr .= "Your online order transaction successfully submitted. Thank you!<br/><br/>";
                   

                  $htmlStr .= "<br/>Kind regards,<br />";
                  $htmlStr .= "<a href='https://globalvoicedirect.com/' target='_blank'>Globalvoicedirect Team</a><br />";


                  $name = "Order Submitted";
                  $email_sender = "no-reply@globalvoicedirect.com";
                  $subject = "Order Transaction Submitted";
                  $recipient_email = $order->OrderEmail;
        
                  $body = $htmlStr;
        $__mailOk = true;
        try {
                  \Mail::send(array(), array(), function ($message) use ($htmlStr,$recipient_email,$subject) {
                    $message->to($recipient_email)
                    ->subject($subject)
                    ->from(config('mail.from.address'),'Order Transaction Submitted')
                    ->html($htmlStr); 
                  });
        } catch (\Throwable $__e) {
            $__mailOk = false;
            \Log::error('Mail send failed in ' . __METHOD__ . ': ' . $__e->getMessage());
        }
                  if($__mailOk){
                      return \Response::json(array('success'=>true,'result'=>"Your online order transaction successfully submitted. Thank you!"));
                  }else{
                    return \Response::json(array('success'=>true,'result'=>"Email not sent.But Your online order transaction successfully submitted. Thank you!"));
                  }

                } else {
                  $errorMessage = "Transaction Failed. ";
                  if ($tresponse->getErrors() != null) {
                    $errorMessage.= $tresponse->getErrors()[0]->getErrorText();
                  }
                  return \Response::json(array('success'=>false,'result'=>$errorMessage));
                }
                // Or, print errors if the API request wasn't successful
              } else {
                $errorMessage = "Transaction Failed. ";
                 
                $tresponse = $response->getTransactionResponse();
            
                if ($tresponse != null && $tresponse->getErrors() != null) {
                  $errorMessage.= $tresponse->getErrors()[0]->getErrorText();
                } else {
                  $errorMessage.= $response->getMessages()->getMessage()[0]->getText();
                }
                return \Response::json(array('success'=>false,'result'=>$errorMessage));
            }
          } else {
            $errorMessage = "No response returned";
            return \Response::json(array('success'=>false,'result'=>$errorMessage));
          }

        }else{
          return \Response::json(array('success'=>false,'result'=>"This Order not exist. Please try again!"));
        }
      }
    }
    public function fax_mod(Request $req)
    {
      $stateData=$this->faxUserDetails();
      $response=$this->getAllSentFax();
      $incomingfaxes=$this->getAllIncomingInformation();
      
       
        $vfaxuserData=session()->get('vfaxuserData');

      return view('users.fax_mod',compact('stateData','incomingfaxes','response','vfaxuserData'));
    }
    public function  billPay()
    {
    
      if(session()->get('user_login')==true){

        $balanceDet = \DB::table('user_balance')->where('user_id',session()->get('user_id'))->get();
      
        $balanceDetCount = \DB::table('user_balance')->where('user_id',session()->get('user_id'))->count();

        if($balanceDetCount > 0){
          $balanceDetail = \DB::table('user_balance')->where('user_id',session()->get('user_id'))->get();
           
        }else{
          $balanceDetail = false;
           
        }

        $payableDetCount = \DB::table('payable_amount')->where('user_id',session()->get('user_id'))->count();
        //dd($balanceDetCount);
        if($balanceDetCount > 0){
          $payableDetail = \DB::table('payable_amount')->where('user_id',session()->get('user_id'))->first();
             
        }else{
          $payableDetail = false;
           
        }
     
      }

    //   $result=\DB::table('Account_details')->select('account_number')->where('user_id',session()->get('user_id'))->first();
    
    $result = [];

      return view('users.billingDetail',compact('result','balanceDetail','payableDetail'));

    }
 
    public function  payment(Request $req)
    {
      $rowLists=\DB::table('transactions')->where('user_id',session()->get('user_id'))->get();
      return view('users.payment',compact('rowLists'));
    }
    public function faxUserDetails()
    {
      if(session()->get("user_login")==true){
  
        $vfaxuserDetCount = \DB::table('vfaxsignup')->where('user_id',session()->get('user_id'))->count();

        $vfaxuserData = \DB::table('vfaxsignup')->where('user_id',session()->get('user_id'))->first();

        session()->put('vfaxuserData',$vfaxuserData);
        session()->put('vfaxuserDetCount',$vfaxuserDetCount);
        
        return [];
        $customer_login=config('services.vitelity.login'); 
        $customer_password=config('services.vitelity.pass');
        
        $result= file_get_contents("https://api.vitelity.net/fax.php?login=".$customer_login."&pass=".$customer_password."&cmd=faxliststates&xml=yes");

        $xml = simplexml_load_string($result);

        if( $xml->status == 'ok'){
          $response['success']=1;
          foreach ($xml ->states as $states) {
            $response['data']=$states;
          }
          $array = json_decode(json_encode((array)$xml), TRUE);
          $stateData = $array["states"]["state"];
          return $stateData;
        }else{
          $response['message']=$xml->error;
        }
      }
    }
    public  function  getAllIncomingInformation()
    {
      if(session()->get("user_login")==true){

        $incomingfaxes=array();
        $incomingfaxes['success']= 0;
        $incomingfaxes['message']='';
        $incomingfaxes['data']=[];  

        $customer_login=config('services.vitelity.login'); 
        $customer_password=config('services.vitelity.pass');   

        $userDetCount12 = \DB::table('vfaxsignup')->where('user_id',session()->get('user_id'))->count();
        $faxaccDetail12 = \DB::table('vfaxsignup')->where('user_id',session()->get('user_id'))->first();
        session()->put('userDetCount12',$userDetCount12);
        session()->put('faxaccDetail12',$faxaccDetail12);
         
        return [];

        if($faxaccDetail12){
          
          $result= file_get_contents("https://api.vitelity.net/fax.php?login=".$customer_login."&pass=".$customer_password."&cmd=listincomingfaxes&xml=yes&vfaxacc=".$faxaccDetail12->acclogin);
          
          $xml = simplexml_load_string($result);

          $faxid=[];
          $date=[];
          $source=[];
          $destination=[];
          $status=[];

          if( $xml->status == 'ok'){
            $incomingfaxes['success']=1;
            foreach ($xml->faxes->faxid as $key =>$value) {
              $faxid[]=(string)$value;
                    
            }
            
            foreach ($xml->faxes->date as $key =>$value) {
              $date[]=(string)$value;
            }
            foreach ($xml->faxes->source as $key =>$value) {
              $source[]=(string)$value;
            }
            foreach ($xml->faxes->destination as $key =>$value) {
              $destination[]=(string)$value;
            }
            foreach ($xml->faxes->status as $key =>$value) {
              $status[]=(string)$value;
            }
            foreach ($faxid as $key =>$value) {
              // print_r( $faxes);
              $faxes11[]=[
                'faxid' => $value,
                'date' => $date[$key],
                'source' => $source[$key],
                'destination' => $destination[$key],
                'status' => $status[$key]
              ];
            }
             
            $incomingfaxes['data']=$faxes11;      
          }else{
            $incomingfaxes['message']=$xml->error;
          }
          return $incomingfaxes;
          // die(json_encode($faxid));
        }
      }
    }
  public function getAllSentFax()
  {
   
   if(session()->get("user_login")==true){ 

    $response=array();
    $response['success']= 0;
    $response['message']='';
    $response['data']=[]; 

    $customer_login=config('services.vitelity.login'); 
    $customer_password=config('services.vitelity.pass');
  
    $userDetCount = \DB::table('vfaxsignup')->select('did')->where('user_id',session()->get('user_id'))->count();
    $faxaccDetail = \DB::table('vfaxsignup')->select('did')->where('user_id',session()->get('user_id'))->first();
    //dd($faxaccDetail);
    session()->put('userDetCount',$userDetCount);
    if($userDetCount==0)
    {
     $response['message']="User not found";
     return $response;
     exit();
    }
    return [];
    $result= file_get_contents("https://api.vitelity.net/fax.php?login=".$customer_login."&pass=".$customer_password."&xml=yes&cmd=sentfaxstatus&showxmlstatus=yes&did=".$faxaccDetail->did);

     
    $xml = simplexml_load_string($result);    

    if( $xml->status == 'ok'){
      $response['success']=1;
      
      foreach ($xml->sentfaxes->fax as $fax) {
        $faxes[]=[
          'jobid' => (string)$fax->jobid,
          'date' => (string)$fax->date,
          'source' => (string)$fax->source,
          'destination' => (string)$fax->destination,
          'status' => (string)$fax->status          
        ];
      }       
      $response['data']=$faxes;     
    }else{
      $response['message']=$xml->error;
    }
    return $response;
    // dd($response);
    //$sentfaxes = json_encode($response);
     
  } 




  }

  public function unpaid_order($id)
  {

    if(session()->get("user_login")==true){
        $userID = session()->get('user_id');
        $orderDet = \DB::table('orders')->leftjoin('shippinginfo', 'orders.OrderID', '=', 'shippinginfo.order_id')->where('OrderUserID', $userID)->where('orders.OrderID',$id)->where('orders.paid',0)->select('orders.*','shippinginfo.*')->first();
         
        if($orderDet){
          $orderProducts = DB::table('invoices')->join('products','invoices.product_id','=','products.ProductID')->where('invoices.order_id','=', $id)->select('products.*','invoices.*')->get();

        }else{
            return redirect()->back()->with('error', 'Invalid...!');
        }
        return view('users.unpaid_payment_page', compact('orderDet', 'orderProducts'));
      } 
    return view('users.unpaid_user');
  }



public function act_submitPayment(Request $req)
{
  if(isset($req->cardnumber) and isset($req->amount1) and isset($req->year) and isset($req->month) and session()->get("user_login")==true){

    $amount = $req->amount1;
    $cardNum = $req->cardnumber;
    $year = $req->year;
    $month = $req->month;
    $cardcvv = $req->cvv;

     
  }else if(isset($req->NotifyCandidate)){
 
     
    return redirect()->route('user/billPay', ['error' =>'Card details not exist']);

  }else{
    return redirect()->route('user/billPay', ['error' =>'Please set all fields']);
  }

  if($amount < 5){
    return redirect()->route('user/billPay', ['error' =>'Entered amount should be greater or equal to $5!']);
  }
  if($amount >= 5){
    
    $userDetCount =\DB::table('wpgc_users')->where('ID',session()->get('user_id'))->count();
    if($userDetCount > 0){
      $userDetail =\DB::table('wpgc_users')->where('ID',session()->get('user_id'))->first();
       
    }else{
        
        $userDetail = false;
    }

    // Common setup for API credentials  
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();   
    $merchantAuthentication->setName(config('services.authorize.login'));
    $merchantAuthentication->setTransactionKey(config('services.authorize.key'));  
    $refId = 'ref' . time();

    // Create the payment data for a credit card
    $creditCard = new AnetAPI\CreditCardType();
    $creditCard->setCardNumber($cardNum);  
    $creditCard->setExpirationDate( $month."-".$year );
    $creditCard->setCardCode($cardcvv);

    $paymentOne = new AnetAPI\PaymentType();
    $paymentOne->setCreditCard($creditCard);

    // Create a transaction
    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType("authCaptureTransaction");   
    $transactionRequestType->setAmount($amount);
    $transactionRequestType->setPayment($paymentOne);
    $request = new AnetAPI\CreateTransactionRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setRefId($refId);
    $request->setTransactionRequest($transactionRequestType);
    $controller = new AnetController\CreateTransactionController($request);
    //replace with Snadbox
    $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);

    if ($response != null) {
      $tresponse = $response->getTransactionResponse();
      if (($tresponse != null) && ($tresponse->getErrors()==NULL)){
        
        $today = date("Ymd");
       
        $userReg = date("Y-m-d H:i:s");

        $authcode = $tresponse->getAuthCode();
        $transId = $tresponse->getTransId();
        $created_at = date("Y-m-d H:i:s");
    
        //increment
        \DB::table('transactions')->insert(['transaction_id'=>$transId,'authentication_code'=>$authcode,'transaction_amount'=>$amount,'created_at'=>$created_at,'user_id'=>session()->get('user_id')]);
    
        \DB::table('user_balance')->where('user_id',session()->get('user_id'))->update(['updated_at'=>$created_at,'balance'=>\DB::raw('balance + '.$amount.'')]);

        \DB::table('payable_amount')->where('user_id',session()->get('user_id'))->update(['updated_at'=>$created_at,'r_amount'=>\DB::raw('r_amount - '.$amount.'')]);
 
        $htmlStr = "";
        $htmlStr .= "Hi " . $userDetail->display_name . ",<br /><br />";

        $htmlStr .= "Your Transaction recieved successfully. Thank you!<br/><br/><strong>Transaction ID : </strong>".$transId;
         
        $htmlStr .= "<br/>Kind regards,<br />";
        $htmlStr .= "<a href='http://dev.globalvoicedirect.com/' target='_blank'>Globalvoicedirect Team</a><br />";

        $name = "Payment Transaction";
        $email_sender = "no-reply@globalvoicedirect.com";
        $subject = "Payment Transaction";
        $recipient_email = $userDetail->user_email;

        $body = $htmlStr;
        $__mailOk = true;
        try {
       \Mail::send(array(), array(), function ($message) use ($htmlStr,$recipient_email,$subject) {
          $message->to($recipient_email)
          ->subject($subject)
          ->from(config('mail.from.address'))
          ->html($htmlStr); 
        });
        } catch (\Throwable $__e) {
            $__mailOk = false;
            \Log::error('Mail send failed in ' . __METHOD__ . ': ' . $__e->getMessage());
        }
        if($__mailOk){
           return redirect()->route('user/billPay', ['message' =>'your Payment submitted successfully']);
        }
        else{
          return redirect()->route('user/billPay', ['message' =>'your order submitted successfully.But Email not sent.']);
        }
              // send email using the mail function, you can also use php mailer library if you want
      }
      else{
        return redirect()->route('user/billPay', ['error' =>'Charge Credit Card ERROR :  Invalid response. Please try again.']);
      }
    }  
    else{
     return redirect()->route('user/billPay', ['error' =>'Charge Credit  Card Null response returned. Please Try again.']);  
    }
  }else{
    $accountDetail = false;
    return redirect()->route('user/billPay', ['error' =>'Add Your Account Details First.']); 
  }
}
  public function credit_app(){
     if(session()->get('user_login')==true){
      $userID = session()->get('user_id');
      //dd($userID);
      $userFormDet = \DB::table('creditapp_info')->where('user_id', $userID)->get();
      //dd(count($userFormDetCount));
      return view('users.credit_app', compact('userFormDet'));
     }
  }
  public function act_submitcreditinfo(Request $req){
     $userID = session()->get('user_id');
      $this->validate($req, [
            'file' => 'required|mimes:pdf,jpeg,png,gif|max:2048',
            // 'duns_number' => 'required',
            // 'sic_code' => 'required',
            // 'naics_code' => 'required',
            // 'first_sale' => 'required',
            // 'last_sale' => 'required',
            // 'credit_limit' => 'required|regex:/^[0-9]+$/',
            //'add_credit_email' => 'required',
            // 'additional_bussiness_name' => 'required',
            // 'additional_bussines_address' => 'required'
        ]);
      if($req->hasFile('file')) {
         $image = $req->file('file');
         $name = time().'.'.$image->getClientOriginalExtension();
         //dd($name);
         //$destinationPath = public_path('/postimages');
         $filePath ="creditAppImages/".session()->get('user_id');
         $destinationPath = public_path($filePath);
         //dd($destinationPath);
         if(is_dir($destinationPath) === false )
          {
            \File::makeDirectory($destinationPath,0777,true);
            //dd("ok");
          }
          //dd($destinationPath."/".$name);
          $image->move($destinationPath, $name);
          //$dbImagePath = $destinationPath."/".$name;
          $dbImagePath = $filePath."/".$name;
         //dd($dbImagePath);
         //$path = public_path('creditAppImages/')
      }else {
        return redirect()->back()->with('status', 'File Not Upload');
      }
      /*$link = "https://laravelgvd.globalvoicedirect.com/public/creditAppImages/".$userID."/".$name;
      dd($link);*/

      \DB::table('creditapp_info')->insert([
        'buisness_name'=>$req->lbuisnessname,
        'buisness_doing'=>$req->doingbuisness,
        'bstreet_address1'=>$req->streetaddress,
        'bstreet_address2'=>$req->streetaddress1,
        'bcity'=>$req->city,
        'bstate'=>$req->state,
        'bzip'=>$req->zip_code,
        'bcountry'=> $req->country,
        'bphone'=> $req->phone_number,
        'monthly_purchase_volume'=>$req->monthlypurchase,
        'total_num_employees'=>$req->employees,
        'buisness_year_started'=>$req->buisnessyear,
        'taxid'=>$req->taxnumber,
        'billing_street_address1'=>$req->bstreetaddress,
        'billing_street_address2' => $req->bstreetaddress1,
        'billing_city' => $req->bcity1,
        'billing_state' =>$req->bstate1,
        'billing_zip' => $req->bzip_code1,
        'billing_country' =>$req->bcountry1,
        'buisness_title' => $req->buisnesstitle,
        'first_name' => $req->firstname,
        'last_name' => $req->lastname,
        'home_address1'=>$req->hstreetaddress,
        'home_address2' => $req->hstreetaddress1,
        'home_city' => $req->hcity,
        'home_state' =>$req->hstate,
        'home_zip' => $req->hzip_code,
        'home_country' => $req->hcountry,
        'home_phone' =>$req->homephone,
        'home_mobile' =>$req->mobile,
        'home_email' => $req->emailaddress,
        'birthday' => $req->birthday,
        'driver_license' => $req->driverlicence,
        'license_state' =>$req->licensestate,
        'user_id' => $userID,
        'file_path' => $dbImagePath
      ]);
      // \DB::table('additional_creditapp_info')->insert([
      //   'user_id' => $userID,
      //   'duns_number' => $req->duns_number,
      //   'sic_code' => $req->sic_code,
      //   'naics_code' => $req->naics_code,
      //   'first_sale' => $req->first_sale,
      //   'last_sale' => $req->last_sale,
      //   'credit_limit' => $req->credit_limit,
      //   'email' => $req->add_credit_email,
      //   'business_address' => $req->additional_bussines_address,
      //   'business_name' => $req->additional_bussiness_name

      // ]);

        $htmlStr = "";
        $htmlStr .= "Here the Credit Detail "  . ",<br /><br />";

        $htmlStr .="<strong>About Your Business</strong>" . "<br/><br/>";
        $htmlStr .="Legal Business name = ". $req->lbuisnessname. "<br/>";
        $htmlStr .="Doing business As = " . $req->doingbuisness. "<br/>";
        $htmlStr .="<strong>Business Address</strong>" . "<br/>";
        $htmlStr .="Street Address = " . $req->streetaddress. "<br/>";
        $htmlStr .="Address Line 2 = " . $req->streetaddress1. "<br/>";
        $htmlStr .="City = " . $req->city. "<br/>";
        $htmlStr .="State / Province / Region = " . $req->state. "<br/>";
        $htmlStr .="ZIP / Postal Code = " . $req->zip_code. "<br/>";
        $htmlStr .="Country = " . $req->country. "<br/>";
        $htmlStr .="Main Business Phone = " . $req->phone_number. "<br/>";
        $htmlStr .="Estimated Monthly Purchase Volume = $" . $req->monthlypurchase. "<br/>";
        $htmlStr .="Total Number of Employees = " . $req->employees. "<br/>";
        $htmlStr .="Year Business Started = " . $req->buisnessyear. "<br/>";
        $htmlStr .="Tax ID or (FEIN) Number = " . $req->taxnumber. "<br/>";
        $htmlStr .="<strong>Billing Address if Diferent From Above</strong>" . "<br/>";
        $htmlStr .="Street Address = " . $req->bstreetaddress. "<br/>";
        $htmlStr .="Address Line 2 = " . $req->bstreetaddress1. "<br/>";
        $htmlStr .="City = " . $req->bcity1. "<br/>";
        $htmlStr .="State / Province / Region = " . $req->bstate1. "<br/>";
        $htmlStr .="ZIP / Postal Code = " .$req->bzip_code1. "<br/>";
        $htmlStr .="Country = " . $req->bcountry1. "<br/>";
        $htmlStr .="Business Title = " .$req->buisnesstitle. "<br/>";
        $htmlStr .="First Name = " . $req->firstname. "<br/>";
        $htmlStr .="Last Name = " . $req->lastname. "<br/>";
        $htmlStr .="<strong>Home Address</strong>" . "<br/>";
        $htmlStr .="Street Address = " . $req->hstreetaddress. "<br/>";
        $htmlStr .="Address Line 2 = " .$req->hstreetaddress1. "<br/>";
        $htmlStr .="City = " . $req->hcity. "<br/>";
        $htmlStr .="State / Province / Region = " . $req->hstate. "<br/>";
        $htmlStr .="ZIP / Postal Code = " . $req->hzip_code. "<br/>";
        $htmlStr .="Country = " .$req->hcountry. "<br/>";
        $htmlStr .="Home Phone = " . $req->homephone. "<br/>";
        $htmlStr .="Mobile = " . $req->mobile. "<br/>";
        $htmlStr .="Email Address = " . $req->emailaddress . "<br/>";
        $htmlStr .="Birthday = " . $req->birthday . "<br />";
        $htmlStr .="<strong>License/Residence</strong>" . "<br/>";
        $htmlStr .="Driver License = " . $req->driverlicence . "<br/>";
        $htmlStr .="License State = " . $req->licensestate . "<br/>";

       //$link = "https://laravelgvd.globalvoicedirect.com/";
        $link = "https://laravelgvd.globalvoicedirect.com/public/creditAppImages/".$userID."/".$name;

        $htmlStr .= "Proof of business filling from your secretary of state link = <a href='".$link."'>Please click here</a><br/>";        

        $htmlStr .= "Kind regards,<br />";
        $htmlStr .= "<a href='https://www.globalvoicedirect.com/' target='_blank'>Globalvoicedirect Team</a><br />";


        $name = "Credit App Info";
        $email_sender = "no-reply@globalvoicedirect.com";
        $subject = "Credit App Info";
        $recipient_email ="info@globalvoicedirect.com";
        //$recipient_email = "bestsolutions4017@gmail.com";

        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
        $headers .= "From: {$name} <{$email_sender}> \n";
         //$Signaturepath='https://laravelgvd.globalvoicedirect.com/'.$fileNameat;
        $body = $htmlStr;
       // dd($htmlStr);
        $__mailOk = true;
        try {
         \Mail::send(array(), array(), function ($message) use ($htmlStr,$recipient_email,$subject) {
                  $message->to($recipient_email)
                  ->subject($subject)
                  ->from(config('mail.from.address'))
                  ->html($htmlStr); 
                });
        } catch (\Throwable $__e) {
            $__mailOk = false;
            \Log::error('Mail send failed in ' . __METHOD__ . ': ' . $__e->getMessage());
        }
        // dd(\Mail::failures());
          if($__mailOk)
        {
          return redirect()->back()->with('status','Credit App Information Submitted Successfully');
        }else{
            return redirect()->back()->with('status','Somthing went Wrong...Please try Again');
        }
  }

  public function scriptRecording(){
    //dd('reach');
    $userID = session()->get('user_id');
    //dd($userID);
   $userFormDetail = \DB::table('script_info')->where('user_id',$userID)->first();
    return view('users.scriptRecording', compact('userFormDetail'));
  }
  public function saveScriptInfo(Request $req){
    // Rule 2026-10-31: once a script request is approved (active=2) no new request is accepted.
    if (\DB::table('script_info')->where('user_id', session()->get('user_id'))->where('active', 2)->exists()) {
      \App\Support\AdminAlerts::log('script', 'info', 'Blocked new script request: account already has an approved script', ['action' => __FUNCTION__]);
      return redirect('/user/phone-scripts')->with('status', 'Your phone script is already approved and recorded. To request changes, please contact support.');
    }

      //dd($req->all());
    $userDet = \DB::table('wpgc_users')->where('ID', session()->get('user_id'))->first();
    //dd($userDet);
    if($userDet){
      $userName = $userDet->display_name;
    }
      //dd($username);
       
       if($req->hasFile('audioFile')) {
         $audio = $req->file('audioFile');
         $audioName = time().'.'.$audio->getClientOriginalExtension();
           if($audio->getClientOriginalExtension() != 'mp3' && $audio->getClientOriginalExtension() != 'ogg'){
              return redirect()->back()->with('status', 'File Must be mp3, ogg');
           }else {
              $filePath ="user-audios/".session()->get('user_id');
              $destinationPath = public_path($filePath);
              if(is_dir($destinationPath) === false )
              {
                \File::makeDirectory($destinationPath,0777,true);
                
              }
            $audio->move($destinationPath, $audioName);
            $dbAudioPath = "user-audios/".session()->get('user_id')."/".$audioName;
          }
            //dd($dbAudioPath);
        }else {
          $dbAudioPath = null;
        }

        $__sid = \DB::table('script_info')->insertGetId([
            'user_id'=>session()->get('user_id'),
            'main_greeting_script'=> $req->main_script,
            'after_hours_script' => $req->hours_script,
            'hold_script' => $req->message_onhold,
            'special_pronunciation_words' => $req->special_pronunciation_words,
            'audio_file_path' => $dbAudioPath
          ]);
          \App\Support\AdminAlerts::log('script', 'info', 'New script recording request', ['script_info_id' => $__sid, 'account' => $userName ?? null, 'audio' => (bool) $dbAudioPath]);
          $htmlStr = "";
          $htmlStr .= "Here is the Detail of Script Recording Information<br /><br /><br />";

          $htmlStr .= "<strong>User Name:</strong> " . $userName;

          $htmlStr .= "<br /> <br/>";

          $htmlStr .= "<strong>Main greeting script:: </strong>".$req->main_script."<br />";
          $htmlStr .= "<strong>After-hours greeting script: </strong>".$req->hours_script."<br />";
          $htmlStr .= "<strong>Message on hold scripts: </strong>".$req->message_onhold."<br />";
       
       
          $htmlStr .= "<br/>Kind regards,<br />";
          $htmlStr .= "<a href='https://globalvoicedirect.com/' target='_blank'>Globalvoicedirect Team</a><br />";


          $name = "globalvoicedirect";
          $email_sender = "no-reply@globalvoicedirect.com";
          $subject = "Script Recording";
          $recipient_email = "Info@globalvoicedirect.com";

          $headers  = "MIME-Version: 1.0\r\n";
          $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
          $headers .= "From: {$name} <{$email_sender}> \n";

          $body = $htmlStr;
        
        $__mailOk = true;
        try {
          \Mail::send(array(), array(), function ($message) use ($htmlStr,$recipient_email,$subject) {
            $message->to($recipient_email)
            ->subject($subject)
            ->from(config('mail.from.address'))
            ->html($htmlStr); 
          });
        } catch (\Throwable $__e) {
            $__mailOk = false;
            \Log::error('Mail send failed in ' . __METHOD__ . ': ' . $__e->getMessage());
        }
        // dd(\Mail::failures());
          if($__mailOk)
          {
            return redirect()->back()->with('status', 'All Detail submitted successfully');
          }else{
            return redirect()->back()->with('status', 'All Detail submitted successfully But mail Not Send');
          }
  }

  public function uploadvfacofficetypeeditor(Request $req){
    require_once 'mdf/vendor/autoload.php';
    if(isset($req->documentname)){
       
      if(session()->get('faxdocuments') and sizeof(session()->get('faxdocuments')) >= 5){
        session()->put('faxdocumentsMessage', 'You can upload Max 5 document at a time.');
        return redirect()->route('fax_mod');
        // header("Location: /users/fax_mod.php?message=You can upload Max 5 document at a time.");
      }else{
        $userId = session()->get('user_id');
        $dir = "faxdocuments/" . $userId;
        $date = new DateTime();
        $docname = $date->getTimestamp() . $userId;
         
        if( is_dir(public_path().'/'.$dir) === false )
        {
          File::makeDirectory(public_path().'/'.$dir,0777);
        }

        //$file_Name = "test.pdf";
        $user_filename =  $req->documentname;

        //$file_ext = strtolower(pathinfo(PATHINFO_EXTENSION));
        $mpdf = new \mPDF();
        $mpdf->WriteHTML($req->editor1);
        $mpdf->Output(public_path().'/'.$dir."/".$docname.".pdf",'F');

        $dbpath = "faxdocuments/" . $userId . "/".$docname.".pdf";

        \DB::table('fax_documents')->insert(['user_id'=>$userId,'document_source'=>'officetypeeditor','document_path'=>$dbpath]);

        $doc = [ 
            "docname" => $user_filename, 
            "docpath" => $dbpath, 
            "filename" => $docname.".pdf", 
            "src" => "officeeditor"
        ];
        if(session()->get('faxdocuments')){
          $faxDocumentArray = session()->get('faxdocuments');
          array_push($faxDocumentArray, $doc);
          session()->put('faxdocuments', $faxDocumentArray);
        }else{
          $docsarray = array();
          array_push($docsarray, $doc);
          session()->put('faxdocuments', $docsarray);
        }
        return redirect()->route('fax_mod');
      }

    }else{
      session()->put('faxdocumentsMessage', 'Something Went Wrong. Please try again.');
      return redirect()->route('fax_mod');
    }
    
  }

  public function coverpagecreatefax(Request $req){
       
    if(session()->get('faxdocuments') and sizeof(session()->get('faxdocuments')) >= 5){
      session()->put('faxdocumentsMessage', 'You can upload Max 5 document at a time.');
      return redirect()->route('fax_mod');
      // header("Location: /users/fax_mod.php?message=You can upload Max 5 document at a time.");
    }else{
      $userId = session()->get('user_id');
      $user_filename = "coverpage";
      $dir = "faxdocuments/" . $userId;
      $date = new DateTime();
      $docname = $date->getTimestamp() . $userId;
       
      if( is_dir(public_path().'/'.$dir) === false )
      {
        File::makeDirectory(public_path().'/'.$dir,0777);
      }
      require_once 'mdf/vendor/autoload.php';
      $mpdf = new \mPDF();
      $mpdf->WriteHTML('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
      <HTML>
      <HEAD>
      <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <META http-equiv="X-UA-Compatible" content="IE=8">
      <TITLE>Cover Sample</TITLE>
      <STYLE type="text/css">

      body {margin-top: 0px;margin-left: 0px;}

      #page_1 {position:relative; overflow: hidden;margin: 72px 0px 579px 0px;padding: 0px;border: none;width: 816px;}

      #page_1 #p1dimg1 {position:absolute;top:0px;left:128px;z-index:-1;width:616px;height:348px;}
      #page_1 #p1dimg1 #p1img1 {width:616px;height:348px;}

      .ft0{font: 11px "Helvetica";line-height: 14px;}
      .ft1{font: bold 11px "Helvetica";line-height: 14px;}
      .ft2{font: 21px "Arial";color: #ffffff;line-height: 24px;}
      .ft3{font: 72px "Arial";line-height: 72px;}
      .ft4{font: bold 13px "Arial";line-height: 15px;}
      .ft5{font: 13px "Helvetica";line-height: 16px;}
      .ft6{font: 12px "PMingLiU";line-height: 12px;}
      .ft7{font: bold 13px "Arial";line-height: 14px;}
      .ft8{font: bold 13px "Arial";line-height: 16px;}
      .ft9{font: 18px "Arial";line-height: 21px;}
      .ft10{font: bold 13px "Arial";margin-left: 4px;line-height: 16px;}

      .p0{text-align: left;padding-left: 128px;margin-top: 0px;margin-bottom: 0px;}
      .p1{text-align: right;padding-right: 322px;margin-top: 3px;margin-bottom: 0px;}
      .p2{text-align: left;padding-left: 72px;margin-top: 48px;margin-bottom: 0px;}
      .p3{text-align: left;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
      .p4{text-align: left;padding-left: 128px;margin-top: 82px;margin-bottom: 0px;border-top: 1px solid;padding-top: 15px}
      .p5{text-align: left;padding-left: 128px;margin-top: 13px;margin-bottom: 0px;}

      .td0{padding: 0px;margin: 0px;width: 78px;vertical-align: bottom;}
      .td1{padding: 0px;margin: 0px;width: 209px;vertical-align: bottom;}
      .td2{padding: 0px;margin: 0px;width: 87px;vertical-align: bottom;}
      .td3{padding: 0px;margin: 0px;width: 126px;vertical-align: bottom;}

      .tr0{height: 20px;}
      .tr1{height: 28px;}
      .tr2{height: 33px;}

      .t0{width: 500px;margin-left: 128px;margin-top: 31px;font: bold 13px "Arial";}

      </STYLE>
      </HEAD>

      <BODY>
      <DIV id="page_1">

      <P class="p0 ft1">From Fax Number<SPAN class="ft0">: '.$req->fromFax.'</SPAN></P>
      <P class="p1 ft2">' . $req->fromFaxname .'</P>
      <P class="p2 ft3">Fax</P>
      <TABLE cellpadding=0 cellspacing=0 class="t0">
      <TR>
        <TD class="tr0 td0"><P class="p3 ft4">To:</P></TD>
        <TD class="tr0 td1"><P class="p3 ft5">'.$req->faxReceiver .'</P></TD>
        <TD class="tr0 td2"><P class="p3 ft4"><SPAN class="ft6"></SPAN>From:</P></TD>
        <TD class="tr0 td3"><P class="p3 ft5">' . $req->fromFaxname .'</P></TD>
      </TR>
      <TR>
        <TD class="tr1 td0"><P class="p3 ft7">Fax:</P></TD>
        <TD class="tr1 td1"><P class="p3 ft5">'. $req->toFax .'</P></TD>
        <TD class="tr1 td2"><P class="p3 ft7"><SPAN class="ft6"></SPAN>Subject:</P></TD>
        <TD class="tr1 td3"><P class="p3 ft5">' . $req->coverpagesubject .'</P></TD>
      </TR>
      <TR>
        <TD class="tr2 td0"><P class="p3 ft8">Phone:</P></TD>
        <TD class="tr2 td1"><P class="p3 ft5">'. $req->pcontactnumber .'</P></TD>
        <TD class="tr2 td2"><P class="p3 ft8"><SPAN class="ft6"></SPAN>Date:</P></TD>
        <TD class="tr2 td3"><P class="p3 ft5">'. $req->date .'</P></TD>
      </TR>
      </TABLE>
      <P class="p4 ft8"></SPAN><SPAN class="ft10">Comments:</SPAN></P>
      <P class="p5 ft5">'. $req->comment .'</P>
      </DIV>
      </BODY>
      </HTML>
      ');
       
      $mpdf->Output(public_path().'/'.$dir."/".$docname.".pdf",'F');

      $dbpath = "faxdocuments/" . $userId . "/".$docname.".pdf";

      \DB::table('fax_documents')->insert(['user_id'=>$userId,'document_source'=>'coverpage','document_path'=>$dbpath]);

      $doc = [ 
          "docname" => $user_filename, 
          "docpath" => $dbpath, 
          "filename" => $docname.".pdf", 
          "src" => "coverpage"
      ];

      if(session()->get('faxdocuments')){
        $faxDocumentArray = session()->get('faxdocuments');
        array_push($faxDocumentArray, $doc);
        session()->put('faxdocuments', $faxDocumentArray);
      }else{
        $docsarray = array();
        array_push($docsarray, $doc);
        session()->put('faxdocuments', $docsarray);
      }
      session()->put('coverpage', true);
      return redirect()->route('fax_mod');
    }
  }

  public function uploadownfilevfax(Request $req){
    if($req->hasFile('file')) {

      require_once 'mdf/vendor/autoload.php';
       
      if(session()->get('faxdocuments') and sizeof(session()->get('faxdocuments')) >= 5){
        session()->put('faxdocumentsMessage', 'You can upload Max 5 document at a time.');
        return redirect()->route('fax_mod');
        // header("Location: /users/fax_mod.php?message=You can upload Max 5 document at a time.");
      }else{
        $userId = session()->get('user_id');
        $user_filename = "coverpage";
        $dir = "faxdocuments/" . $userId;
        $date = new DateTime();
        $docname = $date->getTimestamp() . $userId . '.' . $req->file('file')->getClientOriginalExtension();
         
        if( is_dir(public_path().'/'.$dir) === false )
        {
          File::makeDirectory(public_path().'/'.$dir,0777);
        }

        $req->file('file')->move(
            public_path().'/'.$dir, $docname
        );

        $dbpath = "faxdocuments/" . $userId . "/".$docname;

        \DB::table('fax_documents')->insert(['user_id'=>$userId,'document_source'=>'uploadownfile','document_path'=>$dbpath]);

        $doc = [ 
            "docname" => $docname, 
            "docpath" => $dbpath, 
            "filename" => $docname, 
            "src" => "uploadownfile"
        ];

        if(session()->get('faxdocuments')){
          $faxDocumentArray = session()->get('faxdocuments');
          array_push($faxDocumentArray, $doc);
          session()->put('faxdocuments', $faxDocumentArray);
        }else{
          $docsarray = array();
          array_push($docsarray, $doc);
          session()->put('faxdocuments', $docsarray);
        }
        return redirect()->route('fax_mod');
      }
    }else{
      session()->put('faxdocumentsMessage', 'Please upload the file.');
      return redirect()->route('fax_mod');
    }
  }


  public function uploadownfilevfaxCam(Request $req){
     
    if(isset($req->imageData) and sizeof($imageUri=json_decode($req->imageData)) > 0 ){
      require_once 'mdf/vendor/autoload.php';
       
      if(session()->get('faxdocuments') and sizeof(session()->get('faxdocuments')) >= 5){
        session()->put('faxdocumentsMessage', 'You can upload Max 5 document at a time.');
        return 0;
        //return redirect()->route('fax_mod');
        // header("Location: /users/fax_mod.php?message=You can upload Max 5 document at a time.");
      }else{
        $userId = session()->get('user_id');
        $user_filename = "uploadownfile";
        $dir = "faxdocuments/" . $userId;
        $counter = 0;
        foreach ($imageUri as $value) {
          if(session()->get('faxdocuments') and sizeof(session()->get('faxdocuments')) >= 5){
            session()->put('faxdocumentsMessage', 'You can upload Max 5 document at a time.');
            return 0;
          }else{
            $date = new DateTime();
            $docname = $date->getTimestamp() . $userId . $counter . '.jpeg';
            $counter++;
            
             
            if( is_dir(public_path().'/'.$dir) === false )
            {
              File::makeDirectory(public_path().'/'.$dir,0777);
            }
            $data = $value;
            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);
            file_put_contents(public_path().'/'.$dir.'/'.$docname, $data);
            // $data->move(
            //     public_path().'/'.$dir, $docname
            // );

            $dbpath = "faxdocuments/" . $userId . "/".$docname;

            \DB::table('fax_documents')->insert(['user_id'=>$userId,'document_source'=>'uploadownfile','document_path'=>$dbpath]);

            $doc = [ 
                "docname" => $docname, 
                "docpath" => $dbpath, 
                "filename" => $docname, 
                "src" => "uploadownfile"
            ];

            if(session()->get('faxdocuments')){
              $faxDocumentArray = session()->get('faxdocuments');
              array_push($faxDocumentArray, $doc);
              session()->put('faxdocuments', $faxDocumentArray);
            }else{
              $docsarray = array();
              array_push($docsarray, $doc);
              session()->put('faxdocuments', $docsarray);
            }
            // return redirect()->route('fax_mod');
          }
        }
        session()->put('faxdocumentsMessage', 'Uploaded Successfully!');
        return 1;
      }
      
    }else{
      session()->put('faxdocumentsMessage', 'Please upload the file.');
      //return redirect()->route('fax_mod');
      return 0;
    }
  }

  public function removefaxdocument(Request $req){

    if(session()->get('faxdocuments') != NULL and isset($req->name) and isset($req->type)){
      $docsarray = array();
      foreach (session()->get('faxdocuments') as $key => $value) {
        if($req->name == $value['filename']){
          \DB::table('fax_documents')->where('document_path', 'faxdocuments/'.session()->get('user_id').'/'.$req->name)->delete();
          if($req->type == "coverpage"){
            session()->forget('coverpage');
          }
        }else{
          array_push($docsarray, $value);
        }
      }
      if(sizeof($docsarray) > 0){
        session()->put('faxdocuments', $docsarray);
      }else{
        session()->forget('faxdocuments');
      }
    }
    return redirect()->route('fax_mod');
  }
  public function removeallfaxdocument(Request $req){

    if(session()->get('faxdocuments') != NULL){
      foreach (session()->get('faxdocuments') as $key => $value) {
          \DB::table('fax_documents')->where('document_path', 'faxdocuments/'.session()->get('user_id').'/'.$value['filename'])->delete();
      }
    }
    session()->forget('faxdocuments');
    session()->forget('coverpage');
    //dd(session()->get('faxdocuments'));
    return redirect()->route('fax_mod');
  }
  public function uploadvfaxall(Request $req){

    if(isset($req->formdata)){
        $formData = json_decode($req->formdata);
        $tableData = json_decode($req->tabledata);
        $docsarray = array();
        foreach ($tableData as $value) {
           $doc = [ 
              "docname" => $value->docname, 
              "docpath" => $value->docpath, 
              "filename" => $value->filename, 
              "src" => $value->src
          ];

          array_push($docsarray, $doc);
        }

        $tableData = $docsarray;

        $userDet = \DB::table('wpgc_users')->where('ID', session()->get('user_id'))->first();
        $faxDet = \DB::table('vfaxsignup')->where('user_id', session()->get('user_id'))->first();
        
        $from = $faxDet->accemails;
        $to = $formData[1]->value;
        $faxReceiver = $formData[2]->value;

        $doclength = sizeof($tableData);
        
        $name = "Global Voice Direct";
        $email_sender = "no-reply@globalvoicedirect.com";
        $subject = $to;
        $recipient_email = 'email2fax@um.globalvoicedirect.com';
    
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
        $headers .= "From: {$name} <{$email_sender}> \n";
            
        for($i=0; $i < $doclength; $i++){
            $file_Name = $tableData[$i]['filename'];
            $file_data = file_get_contents(public_path().'/'.$tableData[$i]['docpath']);
            //$file_data = base64_encode($file_data1);
        
        $__mailOk = true;
        try {
            $res = Mail::send(array(), array(), function ($message) use ($recipient_email,$subject,$file_data, $file_Name, $from) {
              $message->to($recipient_email)
              ->subject($subject)
              ->from(config('mail.from.address'), config('mail.from.name'))->replyTo($from)
              ->html('Please find the attached PDF for faxing.')
              ->attachData($file_data, $file_Name, ['mime' => 'application/pdf']);
            });
        } catch (\Throwable $__e) {
            $__mailOk = false;
            \Log::error('Mail send failed in ' . __METHOD__ . ': ' . $__e->getMessage());
        }
            
            DB::table('faxsent')->insert(['userid'=>session()->get('user_id'),'jobid'=>$faxDet->i_account]);
            
        }
        session()->forget('faxdocuments');
        session()->forget('coverpage');
        session()->save();
        session()->put('faxdocumentsMessage', "Your fax to ".$to." is now Processing. you'll recieve notification thorough email.");
        echo "Your fax to ".$to." is now Processing. you'll recieve notification thorough email.";
        die();
        
    }else{
      echo "Something went wrong. Try again!";
      die();
    }
  }
  //old vitelity code
  public function uploadvfaxall1(Request $req){

    if(isset($req->formdata)){
        $formData = json_decode($req->formdata);
        $tableData = json_decode($req->tabledata);
        $docsarray = array();
        foreach ($tableData as $value) {
           $doc = [ 
              "docname" => $value->docname, 
              "docpath" => $value->docpath, 
              "filename" => $value->filename, 
              "src" => $value->src
          ];

          array_push($docsarray, $doc);
        }

        $tableData = $docsarray;

        $userDet = \DB::table('wpgc_users')->where('ID', session()->get('user_id'))->first();

        $customer_login=config('services.vitelity.login'); 
        $customer_password=config('services.vitelity.pass'); 
        
        $from = $formData[0]->value;
        $to = $formData[1]->value;
        $faxReceiver = $formData[2]->value;

        $url = 'https://api.vitelity.net/fax.php';
         
        //$file_ext = strtolower(pathinfo(PATHINFO_EXTENSION));

        $doclength = sizeof($tableData);
        switch ($doclength) {
          case '1':
            $file_Name1 = $tableData[0]['filename'];
            $file_data1 = file_get_contents(public_path().'/'.$tableData[0]['docpath']);
            $file_data1 = base64_encode($file_data1);
            $data = array('login' => $customer_login, 'pass' => $customer_password, 'cmd' => "sendfax", 'xml' => "yes", 'faxnum' => $to, 'faxsrc' => $from, 'recname' => $faxReceiver, 'file1' => $file_Name1, 'data1' => $file_data1);
            break;

          case '2':
            $file_Name1 = $tableData[0]['filename'];
            $file_data1 = file_get_contents(public_path().'/'.$tableData[0]['docpath']);
            $file_data1 = base64_encode($file_data1);
            $file_Name2 = $tableData[1]['filename'];
            $file_data2 = file_get_contents(public_path().'/'.$tableData[1]['docpath']);
            $file_data2 = base64_encode($file_data2);
            $data = array('login' => $customer_login, 'pass' => $customer_password, 'cmd' => "sendfax", 'xml' => "yes", 'faxnum' => $to, 'faxsrc' => $from, 'recname' => $faxReceiver, 'file1' => $file_Name1, 'data1' => $file_data1, 'file2' => $file_Name2, 'data2' => $file_data2);
            break;
          case '3':
            $file_Name1 = $tableData[0]['filename'];
            $file_data1 = file_get_contents(public_path().'/'.$tableData[0]['docpath']);
            $file_data1 = base64_encode($file_data1);
            $file_Name2 = $tableData[1]['filename'];
            $file_data2 = file_get_contents(public_path().'/'.$tableData[1]['docpath']);
            $file_data2 = base64_encode($file_data2);
            $file_Name3 = $tableData[2]['filename'];
            $file_data3 = file_get_contents(public_path().'/'.$tableData[2]['docpath']);
            $file_data3 = base64_encode($file_data3);
            $data = array('login' => $customer_login, 'pass' => $customer_password, 'cmd' => "sendfax", 'xml' => "yes", 'faxnum' => $to, 'faxsrc' => $from, 'recname' => $faxReceiver, 'file1' => $file_Name1, 'data1' => $file_data1, 'file2' => $file_Name2, 'data2' => $file_data2, 'file3' => $file_Name3, 'data3' => $file_data3);
            break;
          case '4':
            $file_Name1 = $tableData[0]['filename'];
            $file_data1 = file_get_contents(public_path().'/'.$tableData[0]['docpath']);
            $file_data1 = base64_encode($file_data1);
            $file_Name2 = $tableData[1]['filename'];
            $file_data2 = file_get_contents(public_path().'/'.$tableData[1]['docpath']);
            $file_data2 = base64_encode($file_data2);
            $file_Name3 = $tableData[2]['filename'];
            $file_data3 = file_get_contents(public_path().'/'.$tableData[2]['docpath']);
            $file_data3 = base64_encode($file_data3);
            $file_Name4 = $tableData[3]['filename'];
            $file_data4 = file_get_contents(public_path().'/'.$tableData[3]['docpath']);
            $file_data4 = base64_encode($file_data4);
            $data = array('login' => $customer_login, 'pass' => $customer_password, 'cmd' => "sendfax", 'xml' => "yes", 'faxnum' => $to, 'faxsrc' => $from, 'recname' => $faxReceiver, 'file1' => $file_Name1, 'data1' => $file_data1, 'file2' => $file_Name2, 'data2' => $file_data2, 'file3' => $file_Name3, 'data3' => $file_data3, 'file4' => $file_Name4, 'data4' => $file_data4);
            break;
          case '5':
            $file_Name1 = $tableData[0]['filename'];
            $file_data1 = file_get_contents(public_path().'/'.$tableData[0]['docpath']);
            $file_data1 = base64_encode($file_data1);
            $file_Name2 = $tableData[1]['filename'];
            $file_data2 = file_get_contents(public_path().'/'.$tableData[1]['docpath']);
            $file_data2 = base64_encode($file_data2);
            $file_Name3 = $tableData[2]['filename'];
            $file_data3 = file_get_contents(public_path().'/'.$tableData[2]['docpath']);
            $file_data3 = base64_encode($file_data3);
            $file_Name4 = $tableData[3]['filename'];
            $file_data4 = file_get_contents(public_path().'/'.$tableData[3]['docpath']);
            $file_data4 = base64_encode($file_data4);
            $file_Name5 = $tableData[4]['filename'];
            $file_data5 = file_get_contents(public_path().'/'.$tableData[4]['docpath']);
            $file_data5 = base64_encode($file_data5);
            $data = array('login' => $customer_login, 'pass' => $customer_password, 'cmd' => "sendfax", 'xml' => "yes", 'faxnum' => $to, 'faxsrc' => $from, 'recname' => $faxReceiver, 'file1' => $file_Name1, 'data1' => $file_data1, 'file2' => $file_Name2, 'data2' => $file_data2, 'file3' => $file_Name3, 'data3' => $file_data3, 'file4' => $file_Name4, 'data4' => $file_data4, 'file5' => $file_Name5, 'data5' => $file_data5);
            break;
          default:
            # code...
            break;
        }

        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        $xml = simplexml_load_string($result);

        if( $xml->status == 'ok'){
            DB::table('faxsent')->insert(['userid'=>session()->get('user_id'),'jobid'=>$xml->jobid]);
            
            session()->forget('faxdocuments');
            session()->forget('coverpage');
            session()->save();
            echo "Your fax to ".$to." is now Processing.";
            die();
            // if($__mailOk)
            // {
            //   session()->forget('faxdocuments');
            //   session()->forget('coverpage');
            //   echo "Your fax to ".$to." is now Processing.";
            //         die();
            // }else{
            //   session()->forget('faxdocuments');
            //   session()->forget('coverpage');
            //   echo "Your fax to ".$to." is now Processing.";
            //         die();
            // }

        }else{
          echo $xml->error;
          die();
        }
    }else{
      echo "Something went wrong. Try again!";
      die();
    }
  }

  public function viewfaxattachment(Request $req){
    if(isset($req->faxid)){
      $faxid = $req->faxid;
      
      $customer_login=config('services.vitelity.login'); 
      $customer_password=config('services.vitelity.pass');
      
      $value = $req->faxid;
      $result= file_get_contents("https://api.vitelity.net/fax.php?login=".$customer_login."&pass=".$customer_password."&cmd=getfax&xml=yes&type=base64&faxid=".(string)$value);
      
      $xml = simplexml_load_string($result);
      if( $xml->status == 'ok' ){
        $str = $xml->data;
        $content = base64_decode($str);
        header("Content-type:application/pdf");
        header("Content-disposition: filename=test.pdf"); 
        header("Content-Length: " . strlen($content)); 
        echo($content);
        die();
      }else{
        session()->put('faxdocumentsMessage', 'Something Went Wrong. Please Try Again.');
        return redirect()->route('fax_mod');
      }
    }elseif (isset($req->jobid)){
        
      $customer_login=config('services.vitelity.login'); 
      $customer_password=config('services.vitelity.pass');
      
      $value = $req->jobid;
      $result= file_get_contents("https://api.vitelity.net/fax.php?login=".$customer_login."&pass=".$customer_password."&cmd=getsentfax&xml=yes&type=base64&faxid=".(string)$value);
      
      $xml = simplexml_load_string($result);
      if( $xml->status == 'ok' ){
        $str = $xml->data;
        $content = base64_decode($str);
        header("Content-type:application/pdf");
        header("Content-disposition: filename=test.pdf"); 
        header("Content-Length: " . strlen($content)); 
        echo($content);
        die();
      }else{
        session()->put('faxdocumentsMessage', 'Something Went Wrong. Please Try Again.');
        return redirect()->route('fax_mod');
      }
    }else{
      session()->put('faxdocumentsMessage', 'Fax Id Required. Please Try Again.');
      return redirect()->route('fax_mod');
    }
  }

  public function deleteselectedfax(Request $req){
    if(isset($req->faxid)){

      $faxid = $req->faxid;
     
      $customer_login=config('services.vitelity.login'); 
      $customer_password=config('services.vitelity.pass');
      
      $value = $req->faxid;
      $result= file_get_contents("https://api.vitelity.net/fax.php?login=".$customer_login."&pass=".$customer_password."&cmd=delfax&xml=yes&faxid=".(string)$value);
      
      $xml = simplexml_load_string($result);
      if( $xml->status == 'ok' ){
        session()->put('faxdocumentsMessage', 'You have deleted the Successfully.');
        return redirect()->route('fax_mod');
      }else{
        session()->put('faxdocumentsMessage', 'Something Went Wrong. Please try again.');
        return redirect()->route('fax_mod');
      }
    }else{
      session()->put('faxdocumentsMessage', 'Fax Id Required. Please Try Again.');
      return redirect()->route('fax_mod');
    }
  }
  public function uploadquickfax(Request $req){
    if(isset($req->fromFax)){
      $userDet = \DB::table('wpgc_users')->where('ID', session()->get('user_id'))->first();
      
      $faxDet = \DB::table('vfaxsignup')->where('user_id', session()->get('user_id'))->first();
      
      
      $from = $faxDet->accemails;
      //$from = 'info@globalvoicedirect.com';
      $to = $_POST['toFax'];
      $faxReceiver = $req->faxReceiver;
      $file_Name = $req->file('file')->getClientOriginalName();

      $file_data = file_get_contents($req->file('file')->getRealPath());
      //$file_data = chunk_split(base64_encode($file_data));
      
        
      
        $name = "Global Voice Direct";
        $email_sender = "no-reply@globalvoicedirect.com";
        $subject = $to;
        $recipient_email = 'email2fax@um.globalvoicedirect.com';
    
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
        $headers .= "From: {$name} <{$email_sender}> \n";
    
       
      
        $res = Mail::send(array(), array(), function ($message) use ($recipient_email,$subject,$file_data, $file_Name, $from) {
          $message->to($recipient_email)
          ->subject($subject)
          ->from(config('mail.from.address'), config('mail.from.name'))->replyTo($from)
          ->html('Please find the attached PDF for faxing.')
          ->attachData($file_data, $file_Name, ['mime' => 'application/pdf']);
        });
        
        DB::table('faxsent')->insert(['userid'=>session()->get('user_id'),'jobid'=>$faxDet->i_account]);
        session()->put('faxdocumentsMessage', "Your fax to ".$to." is now Processing. you'll recieve notification thorough email.");
        return redirect()->route('fax_mod');
    }else{
      session()->put('faxdocumentsMessage', 'Somrthing Went Wrong. Please try again.');
      return redirect()->route('fax_mod');
    }
  }
  public function uploadquickfax1(Request $req){
    if(isset($req->fromFax)){
      $userDet = \DB::table('wpgc_users')->where('ID', session()->get('user_id'))->first();
      
      $customer_login=config('services.vitelity.login'); 
      $customer_password=config('services.vitelity.pass');
      
      $from = $_POST['fromFax'];
      $to = $_POST['toFax'];
      $faxReceiver = $req->faxReceiver;
      $file_Name = $req->file('file')->getClientOriginalName();

      $file_data = file_get_contents($req->file('file')->getRealPath());
      //$file_data = chunk_split(base64_encode($file_data));
      
        
      
        $name = "Global Voice Direct";
        $email_sender = "no-reply@globalvoicedirect.com";
        $subject = $to;
        $recipient_email = 'email2fax@um.globalvoicedirect.com';
    
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
        $headers .= "From: {$name} <{$email_sender}> \n";
    
       
      
        $res = Mail::send(array(), array(), function ($message) use ($recipient_email,$subject,$file_data, $file_Name) {
          $message->to($recipient_email)
          ->subject($subject)
          ->from(config('mail.from.address'))
          ->html('Please find the attached PDF for faxing.')
          ->attachData($file_data, $file_Name, ['mime' => 'application/pdf']);
        });
        
        session()->put('faxdocumentsMessage', "Your fax to ".$to." is now Processing. you'll recieve notification thorough email.");
        return redirect()->route('fax_mod');


      $url = 'https://api.vitelity.net/fax.php';
      $data = array('login' => $customer_login, 'pass' => $customer_password, 'cmd' => "sendfax", 'xml' => "yes", 'faxnum' => $to, 'faxsrc' => $from, 'recname' => $faxReceiver, 'file1' => $file_Name, 'data1' => $file_data);

      // use key 'http' even if you send the request to https://...
      $options = array(
          'http' => array(
              'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
              'method'  => 'POST',
              'content' => http_build_query($data)
          )
      );
      $context  = stream_context_create($options);
      $result = file_get_contents($url, false, $context);

      $xml = simplexml_load_string($result);
      

      if( $xml->status == 'ok'){
        DB::table('faxsent')->insert(['userid'=>session()->get('user_id'),'jobid'=>$xml->jobid]);
        session()->put('faxdocumentsMessage', 'Your fax to '.$to.' is now Processing.');
        return redirect()->route('fax_mod');
         
      }else{
        session()->put('faxdocumentsMessage', (string)$xml->error);
        return redirect()->route('fax_mod');
      }
    }else{
      session()->put('faxdocumentsMessage', 'Somrthing Went Wrong. Please try again.');
      return redirect()->route('fax_mod');
    }
  }

  public function activateUserByEmail(Request $req){
    //dd($req->all());

    if(isset($req->code) AND isset($req->id)){
       DB::table('wpgc_users')->where('ID', '=', $req->id)->where('user_activation_key', '=', $req->code)->update(['user_status' => 1]);

       return redirect()->route('user-login', ['message' =>'You Successfully activate Your account Now Login']);
    }else{
      return redirect()->route('user-login', ['message' =>'Something went wrong']);
    }
  }

  public function samplecover(Request $req){
    require_once 'mdf/vendor/autoload.php';
    $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . 'mdfcashe']);
    $mpdf->WriteHTML('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
      <HTML>
      <HEAD>
      <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <META http-equiv="X-UA-Compatible" content="IE=8">
      <TITLE>Cover Sample</TITLE>
      <STYLE type="text/css">

      body {margin-top: 0px;margin-left: 0px;}

      #page_1 {position:relative; overflow: hidden;margin: 72px 0px 579px 0px;padding: 0px;border: none;width: 816px;}

      #page_1 #p1dimg1 {position:absolute;top:0px;left:128px;z-index:-1;width:616px;height:348px;}
      #page_1 #p1dimg1 #p1img1 {width:616px;height:348px;}

      .ft0{font: 11px "Helvetica";line-height: 14px;}
      .ft1{font: bold 11px "Helvetica";line-height: 14px;}
      .ft2{font: 21px "Arial";color: #ffffff;line-height: 24px;}
      .ft3{font: 72px "Arial";line-height: 72px;}
      .ft4{font: bold 13px "Arial";line-height: 15px;}
      .ft5{font: 13px "Helvetica";line-height: 16px;}
      .ft6{font: 12px "PMingLiU";line-height: 12px;}
      .ft7{font: bold 13px "Arial";line-height: 14px;}
      .ft8{font: bold 13px "Arial";line-height: 16px;}
      .ft9{font: 18px "Arial";line-height: 21px;}
      .ft10{font: bold 13px "Arial";margin-left: 4px;line-height: 16px;}

      .p0{text-align: left;padding-left: 128px;margin-top: 0px;margin-bottom: 0px;}
      .p1{text-align: right;padding-right: 322px;margin-top: 3px;margin-bottom: 0px;}
      .p2{text-align: left;padding-left: 72px;margin-top: 48px;margin-bottom: 0px;}
      .p3{text-align: left;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
      .p4{text-align: left;padding-left: 128px;margin-top: 82px;margin-bottom: 0px;border-top: 1px solid;padding-top: 15px}
      .p5{text-align: left;padding-left: 128px;margin-top: 13px;margin-bottom: 0px;}

      .td0{padding: 0px;margin: 0px;width: 78px;vertical-align: bottom;}
      .td1{padding: 0px;margin: 0px;width: 209px;vertical-align: bottom;}
      .td2{padding: 0px;margin: 0px;width: 87px;vertical-align: bottom;}
      .td3{padding: 0px;margin: 0px;width: 126px;vertical-align: bottom;}

      .tr0{height: 20px;}
      .tr1{height: 28px;}
      .tr2{height: 33px;}

      .t0{width: 500px;margin-left: 128px;margin-top: 31px;font: bold 13px "Arial";}

      </STYLE>
      </HEAD>

      <BODY>
      <DIV id="page_1">

      <P class="p0 ft1">From Fax Number<SPAN class="ft0">: [Fax Source]</SPAN></P>
      <P class="p1 ft2">[Fax From]</P>
      <P class="p2 ft3">Fax</P>
      <TABLE cellpadding=0 cellspacing=0 class="t0">
      <TR>
        <TD class="tr0 td0"><P class="p3 ft4">To:</P></TD>
        <TD class="tr0 td1"><P class="p3 ft5">[Name Fax To]</P></TD>
        <TD class="tr0 td2"><P class="p3 ft4"><SPAN class="ft6"></SPAN>From:</P></TD>
        <TD class="tr0 td3"><P class="p3 ft5">[Fax From]</P></TD>
      </TR>
      <TR>
        <TD class="tr1 td0"><P class="p3 ft7">Fax:</P></TD>
        <TD class="tr1 td1"><P class="p3 ft5">[Number Faxing To]</P></TD>
        <TD class="tr1 td2"><P class="p3 ft7"><SPAN class="ft6"></SPAN>Subject:</P></TD>
        <TD class="tr1 td3"><P class="p3 ft5">[Cover Page Subject]</P></TD>
      </TR>
      <TR>
        <TD class="tr2 td0"><P class="p3 ft8">Phone:</P></TD>
        <TD class="tr2 td1"><P class="p3 ft5">[Persons Contact Number]</P></TD>
        <TD class="tr2 td2"><P class="p3 ft8"><SPAN class="ft6"></SPAN>Date:</P></TD>
        <TD class="tr2 td3"><P class="p3 ft5">[Date]</P></TD>
      </TR>
      </TABLE>
      <P class="p4 ft8"></SPAN><SPAN class="ft10">Comments:</SPAN></P>
      <P class="p5 ft5">[Comments]</P>
      </DIV>
      </BODY>
      </HTML>');
    $mpdf->Output();
  }

  public function updatescriptinfo(Request $req){
    // Rule 2026-10-31: once a script request is approved (active=2) no new request is accepted.
    if (\DB::table('script_info')->where('user_id', session()->get('user_id'))->where('active', 2)->exists()) {
      \App\Support\AdminAlerts::log('script', 'info', 'Blocked new script request: account already has an approved script', ['action' => __FUNCTION__]);
      return redirect('/user/phone-scripts')->with('status', 'Your phone script is already approved and recorded. To request changes, please contact support.');
    }

    // Ownership check: the record must belong to the logged-in account.
    $__own = \DB::table('script_info')->where('id', (int) $req->sid)->first(['id', 'user_id']);
    if (!$__own || (int) $__own->user_id !== (int) session()->get('user_id')) {
      \App\Support\AdminAlerts::log('security', 'security', 'Blocked script-recording update for another account', [
        'action' => 'updatescriptinfo', 'requested_sid' => $req->sid, 'record_owner_user_id' => $__own->user_id ?? null, 'record_exists' => (bool) $__own]);
      abort(403);
    }

    $userDet = \DB::table('wpgc_users')->where('ID', session()->get('user_id'))->first();
    //dd($userDet);
    if($userDet){
      $userName = $userDet->display_name;
    }
      
    if($req->hasFile('audioFile')) {
     $audio = $req->file('audioFile');
     $audioName = time().'.'.$audio->getClientOriginalExtension();
       if($audio->getClientOriginalExtension() != 'mp3' && $audio->getClientOriginalExtension() != 'ogg'){
          return redirect()->back()->with('status', 'File Must be mp3, ogg');
       }else {
          $filePath ="user-audios/".session()->get('user_id');
          $destinationPath = public_path($filePath);
          if(is_dir($destinationPath) === false )
          {
            \File::makeDirectory($destinationPath,0777,true);
          }
        $audio->move($destinationPath, $audioName);
        $dbAudioPath = "user-audios/".session()->get('user_id')."/".$audioName;
      }
        //dd($dbAudioPath);
    }else {
      $dbAudioPath = null;
    }

    DB::table('script_info')->where('id','=',$req->sid)->update([
      'main_greeting_script'=> $req->main_script,
      'after_hours_script' => $req->hours_script,
      'hold_script' => $req->message_onhold,
      'special_pronunciation_words' => $req->special_pronunciation_words,
      'audio_file_path' => $dbAudioPath,
      "active" => 1
    ]);
    \App\Support\AdminAlerts::log('script', 'info', 'Script recording request updated', ['script_info_id' => (int) $req->sid, 'account' => $userName ?? null, 'audio' => (bool) $dbAudioPath]);
      
    $htmlStr = "";
    $htmlStr .= "Here is the Detail of Script Recording Information<br /><br /><br />";

    $htmlStr .= "<strong>User Name:</strong> " . $userName;

    $htmlStr .= "<br /> <br/>";

    $htmlStr .= "<strong>Main greeting script:: </strong>".$req->main_script."<br />";
    $htmlStr .= "<strong>After-hours greeting script: </strong>".$req->hours_script."<br />";
    $htmlStr .= "<strong>Message on hold scripts: </strong>".$req->message_onhold."<br />";
 
    $htmlStr .= "<br/>Kind regards,<br />";
    $htmlStr .= "<a href='https://globalvoicedirect.com/' target='_blank'>Globalvoicedirect Team</a><br />";


    $name = "Global Voice Direct";
    $email_sender = "no-reply@globalvoicedirect.com";
    $subject = "Script Recording";
    $recipient_email = "Info@globalvoicedirect.com";

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
    $headers .= "From: {$name} <{$email_sender}> \n";

    $body = $htmlStr;
  
        $__mailOk = true;
        try {
    \Mail::send(array(), array(), function ($message) use ($htmlStr,$recipient_email,$subject) {
      $message->to($recipient_email)
      ->subject($subject)
      ->from(config('mail.from.address'),'Global Voice Direct')
      ->html($htmlStr); 
    });
        } catch (\Throwable $__e) {
            $__mailOk = false;
            \Log::error('Mail send failed in ' . __METHOD__ . ': ' . $__e->getMessage());
        }

    if($__mailOk)
    {
      return redirect()->back()->with('status', 'All Detail submitted successfully');
    }else{
      return redirect()->back()->with('status', 'All Detail submitted successfully But mail Not Sent');
    }
  }
  
}