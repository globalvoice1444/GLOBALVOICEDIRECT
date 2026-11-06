<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use DateTime;
use Auth;
use Hash;
use DB;

class storeController extends Controller
{

   public function store(Request $request)
   {
   	$pageProductlimit = 9;
	//$page = $_GET['page'];
	if(!isset($_GET['page'])){
		$page = 1;
	}
	else{
		$page = max(1, (int)$_GET['page']);
	}
    $stmtLists = DB::table('productcategories')->get();
	$rowListsCount =DB::table('productcategories')->count();
	
	if(isset($_GET['catId'])){

		//DB::select(DB::raw($query));
		$catId=$_GET['catId'];
		$query='SELECT pro.*,pr.* from products pro LEFT JOIN productad_on pr ON pro.ProductID = pr.product_id WHERE ProductCategoryID = ? and ProductLive = 0';
		//dd($query);
		$execute=DB::select($query, [$catId]);

	     
		 $totalRecords=count($execute);
	     $totalPages = ceil($totalRecords/$pageProductlimit);
	     $startLimit = ($page - 1) * $pageProductlimit;
		
		$productListsQuery ='SELECT pro.*,pr.* from products pro LEFT JOIN productad_on pr ON pro.ProductID = pr.product_id where ProductCategoryID = ? and ProductLive = 0  LIMIT '.(int)$startLimit.','.(int)$pageProductlimit.'';
	
		$productListsQueryExecute=DB::select($productListsQuery, [$catId]);
		//dd($productListsQueryExecute);
		$productListsCount=count($productListsQueryExecute);
		//dd($productListsCount);

	}else {
		$query='SELECT pro.*,pr.* from products pro LEFT JOIN productad_on pr ON pro.ProductID = pr.product_id where ProductLive = 0';
		$execute=DB::select($query);

	     $totalRecords=count($execute);
	     $totalPages = ceil($totalRecords/$pageProductlimit);
	     $startLimit = ($page - 1) * $pageProductlimit;
		
		$productListsQuery ='SELECT pro.*,pr.* from products pro LEFT JOIN productad_on pr ON pro.ProductID = pr.product_id where ProductLive = 0 order by ProductUpdateDate LIMIT '.(int)$startLimit.','.(int)$pageProductlimit.'';
		$productListsQueryExecute=DB::select($productListsQuery);
		//dd($productListsQueryExecute);
		$productListsCount=count($productListsQueryExecute);
		//$rowListsCount = $productLists->rowCount();
	}


	return view('store.index',compact('stmtLists','rowListsCount',
		'productListsCount','productListsQueryExecute','totalPages','page'));
	
   }
   public function cart(Request $req)
   {
		if(isset($req->id) & !empty($req->id)){
			$itemArray = array($req->id=>array('code'=>$req->id, 'quantity'=> '1'));

			if(session()->has('cart_item')) {

				$cartItemSession =  session()->get('cart_item');

				if(in_array($req->id,array_keys($cartItemSession))) {
					foreach($cartItemSession as $k => $v) {
						if($req->id == $v["code"]) {
							if(empty($cartItemSession[$k]["quantity"])) {
								$cartItemSession[$k]["quantity"] = 0;
							}
							$cartItemSession[$k]["quantity"] += 1;
						}
					}
					session()->put('cart_item', $cartItemSession);
					if (session()->has('coupon')) {
						CouponsController::updateTotal();
					}
					return redirect()->route('cartItems',['status'=>'success']);
					//header('location: cartItems.php?status=success');

				} else {
					$cartItemSession = $cartItemSession + $itemArray;
					session()->put('cart_item', $cartItemSession);
					if (session()->has('coupon')) {
						CouponsController::updateTotal();
					}
					return redirect()->route('cartItems',['status'=>'success']);
				}
			} else {
				session()->put('cart_item', $itemArray);
				if (session()->has('coupon')) {
					CouponsController::updateTotal();
				}	
				return redirect()->route('cartItems',['status'=>'success']);
			}
		}else{
			return redirect()->route('store',['status'=>'failed']);

		}
   		//function end	
   }
   public static function getTotal(){
   		$total = 0.0;
   		if(session()->has('cart_item')){
			      
		    foreach (session()->get('cart_item') as $item){
		     	$id = $item["code"];
		     	//dd($id);
		     	$query='SELECT pro.*,pr.* from products pro LEFT JOIN productad_on pr ON pro.ProductID = pr.product_id WHERE ProductID = ?';
   	              $stmtLists =DB::select($query, [$id]);
		  
				$rowListsCount = count($stmtLists);

				if($rowListsCount > 0){
					foreach($stmtLists as $rowLists){
						if($rowLists->ProductID == $rowLists->product_id)
						{
							if($rowLists->addonFixedPrice != null)
							{
								$total = $total + ($item["quantity"] * $rowLists->addonFixedPrice);
							}
							else
							{
								$discount = $rowLists->addonDiscount * $rowLists->ProductPrice/100;
								$proPrice = $rowLists->ProductPrice-$discount;
								$total = $total + ($item["quantity"] * $proPrice);
							}
						}
						else
						{
							$total = $total + ($item["quantity"] * $rowLists->ProductPrice);
						}
					}
				}
			}
		}
   		return $total;
   }
   public function postCart(Request $req)
   {

		if(isset($req->id) & !empty($req->id)){
			$itemArray = array($req->id=>array('code'=>$req->id, 'quantity'=> $req->quantity));

			if(session()->has('cart_item')) {

				$cartItemSession =  session()->get('cart_item');

				if(in_array($req->id,array_keys($cartItemSession))) {
					foreach($cartItemSession as $k => $v) {
						if($req->id == $v["code"]) {
							if(empty($cartItemSession[$k]["quantity"])) {
								$cartItemSession[$k]["quantity"] = 0;
							}
							$cartItemSession[$k]["quantity"] += $req->quantity;
						}
					}
					session()->put('cart_item', $cartItemSession);
					if (session()->has('coupon')) {
						CouponsController::updateTotal();
					}
					return redirect()->route('cartItems',['status'=>'success']);
					//header('location: cartItems.php?status=success');

				} else {
					$cartItemSession = $cartItemSession + $itemArray;
					session()->put('cart_item', $cartItemSession);
					if (session()->has('coupon')) {
						CouponsController::updateTotal();
					}
					return redirect()->route('cartItems',['status'=>'success']);
				}
			} else {
				session()->put('cart_item', $itemArray);	
				if (session()->has('coupon')) {
					CouponsController::updateTotal();
				}	
				return redirect()->route('cartItems',['status'=>'success']);
			}
		}else{
			return redirect()->route('store',['status'=>'failed']);

		}
   		//function end	
   }

   public function cartItems(Request $req)
   {
   		return view('store.cartItem');
   }

   //return cart detials in app.blade.php
   public static function fetchProductsById($id)
   {
   		$query="SELECT * from products
   				WHERE ProductID = ?";
    	$results=DB::select($query, [$id]);
   	 	return $results;

   }
   	public static function fetchItems($id)
   	{
   		$query='SELECT pro.*,pr.* from products pro LEFT JOIN productad_on pr ON pro.ProductID = pr.product_id WHERE ProductID = ?';
   	 	$results=DB::select($query, [$id]);
   	 	return $results;  
   	}
   	public function updateqty(Request $req)
   	{ 
		if(isset($req->qty1) and isset($req->id) and $req->qty1 !="" and $req->id != ""){

			if(session()->has('cart_item')) {
				$cartItemArr = session()->get('cart_item');
				foreach(session()->get('cart_item') as $k => $v) {
					if($req->id == $v["code"])
						$cartItemArr[$k]["quantity"] = $req->qty1;				
					if(empty($cartItemArr[$k]["quantity"]))
						$cartItemArr[$k]["quantity"] = $req->qty1;
				}
				session()->put('cart_item', $cartItemArr);
				if (session()->has('coupon')) {
					CouponsController::updateTotal();
				}
			}
			return redirect()->route('cartItems',['status'=>'success']);

		}else{
			return redirect()->route('cartItems',['status'=>'fail']);
		}
   }
	public function delcart(Request $req)
	{
		 
	if(session()->has('cart_item')) {
		$cartItemArr = array();
		foreach(session()->get('cart_item') as $k => $v) {
			 
			if($req->remove != $v["code"]){
				array_push($cartItemArr, $v);				
			}
		}
		if(sizeof($cartItemArr) > 0){
			session()->put('cart_item', $cartItemArr);
			if (session()->has('coupon')) {
				CouponsController::updateTotal();
			}
		}else{
			session()->forget('cart_item');
			if (session()->has('coupon')) {
				CouponsController::updateTotal();
			}
		}
	}
	return redirect()->route('cartItems',['status'=>'success']);
	}
   public function checkout(Request $req)
   {	
   		if(session()->get('cart_item') == NULL){
		   	return redirect()->route('main');
		}
	   	if(!isset($req->username)){
	   		if(session()->get('user_id')==NULL){
	   	    	return view('store.checkoutLogin');
	   	    }
	   	}

	   	$firstname = "";
		$lastname = "";
		$email = "";
		$phone = "";

	   	if(isset($req->password) and isset($req->username)){
		   	if(\DB::table('wpgc_users')->where('user_login',$req->username)->where('user_status',1)->exists()){
		    
		        $userData=\DB::table('wpgc_users')->where('user_login',$req->username)->first();
		       		//match password
		       	if(Hash::check($req->password,$userData->user_pass)){
		         
		         	Auth::loginUsingId($userData->ID);
		         	session()->put('login',true); 
		         	session()->put('user_login',true); 
		          	session()->put('user_email',Auth::user()->user_email);
		           	session()->put('user_phone',Auth::user()->phone);
		           	session()->put('user_id',Auth::user()->ID);
		           	session()->put('nice_name',Auth::user()->user_nicename);
		                     
		     	}else{

		     		session()->flash('message','Password does not match');
		        	return view('store.checkout',compact('firstname','lastname','email','firstname','lastname','phone'));

		     	}

	        }else{
	      
	     	 	session()->flash('message','Your account is not activated. Please visit your email and activate your account first');
	        	return view('store.checkout',compact('firstname','lastname','email','firstname','lastname','phone'));
	     	 }
		     	
		}
	     
		if(session()->get('user_login')==true){
			
			$results=DB::table('user_shipping_address')->where('user_id',session()->get('user_id'))->first();
				 
		      	if($results != null){
		      		$firstname = $results->firstname;
	      			$lastname = $results->lastname;
	      			$phone = $results->phone;
		      	}else{
		      		$firstname = "";
	      			$lastname = "";
		      		$phone = session()->get('user_phone');
		      	}

      			$email = session()->get('user_email');
	      	
		}

	   	return view('store.checkout',compact('firstname','lastname','email','phone'));
	     
    }
	public function submitOrder(Request $req){

		$validatedData = $req->validate([
	        'firstname' => 'required',
	        'lastname' => 'required',
	        'email' => 'required|email',
	        'mobileNo' => 'required',
	        'address1' => 'required|max:200',
	        'country' => 'required',
	        'city' => 'required',
	        'state' => 'required',
	        'zip' => 'required',
	        'cardnumber' => 'required|max:16|min:13',
	        'cardcvv' => 'required|max:4|min:3',
	        'month' => 'required',
	        'year' => 'required'

	    ]);

		if(session()->has('cart_item') and count(session()->get('cart_item')) > 0 and session()->get('user_login')==true){
			//Billing Detail
			$firstname = $req->firstname;
			$lastname = $req->lastname;
			$email = $req->email;
			$mobile = $req->mobileNo;
			$address1 = $req->address1;
			$address2 = $req->address2;
			$country = $req->country;
			$city = $req->city;
			$state = $req->state;
			$zip = $req->zip;

			//Credit Card Detail
			$cardnumber = $req->cardnumber;
			$cardcvv = $req->cardcvv;
			$month = $req->month;
			$year = $req->year;

			$user_id = session()->get('user_id');
			$orderid = 0;
		  	$downpayment = 0.0;
		  	$remainingAmount = 0.0;
		  	$totalAmount = 0;
	     
	    	$totalAmount = storeController::getTotal();

	    	if (session()->has('coupon')) {
				$coupon = session()->get('coupon');
				$totalAmount = $totalAmount - $coupon['discount'];
			}

			$downpayment = number_format($totalAmount / 2 ,2);
			$remainingAmount = $totalAmount - $downpayment;

			$remainingAmount=round(floatval($remainingAmount),2);
			//Preorder
			$today = date("Ymd");
			$rand = strtoupper(substr(uniqid(sha1(time())),0,4));
			$OrderTrackingNumber = $today . $rand;
			$userReg = date("Y-m-d H:i:s");

			$id= DB::table('orders')->insertGetId(['OrderUserID'=>$user_id,'OrderAmount'=>$totalAmount,'OrderShipName'=>$firstname." ".$lastname,'OrderShipAddress'=>$address1,'OrderShipAddress2'=>$address2,'OrderCity'=>$city,'OrderState'=>$state,'OrderZip'=>$zip,'OrderCountry'=>$country,'OrderPhone'=>$mobile,'OrderEmail'=>$email,'OrderTrackingNumber'=>$OrderTrackingNumber, 'paid' => 0]);
		    if (session()->has('coupon')) {
		    	$couponid = DB::table('coupon_orders')->insertGetId(['order_id'=>$id,'order_amount'=>$coupon['total'],'discount'=>$coupon['discount'],'coupon_code'=>$coupon['name']]);           	
		    }
		    //Cart Items
		    $item_total = 0;
		    $total = 0;
			$i=1;
			$orderid = $id;
		    foreach (session()->get('cart_item') as $item){
		     	$id = $item["code"];
		    
				$query='SELECT pro.*,pr.* from products pro LEFT JOIN productad_on pr ON pro.ProductID = pr.product_id WHERE ProductID = ?';
	              	$stmtLists =DB::select($query, [$id]);
				$rowListsCount = count($stmtLists);

				if($rowListsCount > 0){
					foreach($stmtLists as $rowLists)         {
						if($rowLists->ProductID == $rowLists->product_id)
					{
						if($rowLists->addonFixedPrice != null)
						{
							$proOrderPrice = $rowLists->addonFixedPrice;
						}
						else
						{
							
							$discount = $rowLists->addonDiscount * $rowLists->ProductPrice/100;
							$proOrderPrice = $rowLists->ProductPrice-$discount;
						}
					}
					else
					{
						$proOrderPrice = $rowLists->ProductPrice;
					}
					if (session()->has('coupon')) {
						if ($coupon['type'] == 'fixed') {
							$proOrderPrice -= $coupon['off'];
				   		}elseif ($coupon['type'] == 'percent') {
				   			$proOrderPrice = ($coupon['off'] / 100) * $proOrderPrice;
				   		}
					}
					DB::table('invoices')->insert(['user_id'=>$user_id,'order_id'=>$orderid,'product_id'=>$id,'quantity'=>$item['quantity'],'order_price'=>$proOrderPrice]);
					}
				}
			}

			//Flush sessions
			session()->forget('cart_item');
			session()->forget('coupon');

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
			$creditCard->setCardNumber($req->cardnumber);  
			$creditCard->setExpirationDate( $req->month."-".$req->year );
			$paymentOne = new AnetAPI\PaymentType();
			$paymentOne->setCreditCard($creditCard);

			$customer = new AnetAPI\CustomerDataType();
			$customer->setEmail($req->email);

			//Ship To Info
			$shipto = new AnetAPI\NameAndAddressType();
			$shipto->setFirstName($req->cfirstname);
			$shipto->setLastName($req->clastname);
			$shipto->setCompany($req->c_name);
			$shipto->setAddress($req->address1);
			$shipto->setCity($req->city);
			$shipto->setState($req->state);
			$shipto->setZip($req->zip);
			$shipto->setCountry($req->country);

			// Bill To
			$billto = new AnetAPI\CustomerAddressType();
			//Get User Billing detail from Database
	        $query='SELECT * from user_shipping_address WHERE user_id = ?';
	   	    $billingDet  =DB::select($query, [session()->get('user_id')]);
	   	    $billingDetCount = count($billingDet);
				 
			if($billingDetCount > 0){
		
				//	$billingDetail = $billingDet->fetch
				$billto->setFirstName($billingDet[0]->firstname);
				$billto->setLastName($billingDet[0]->lastname);
				$billto->setCompany($billingDet[0]->company_name);
				$billto->setAddress($billingDet[0]->address);
				$billto->setCity($billingDet[0]->city);
				$billto->setState($billingDet[0]->state);
				$billto->setZip($billingDet[0]->zip);
				$billto->setCountry($billingDet[0]->country);
				$billto->setPhoneNumber($mobile);
					 
			}

			// Create a transaction
			$transactionRequestType = new AnetAPI\TransactionRequestType();
			$transactionRequestType->setTransactionType("authCaptureTransaction");

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
			            $htmlStr .= "Hi " . $lastname . ",<br /><br />";

			            $htmlStr .= "Your online order was successfully submitted. Thank you!<br/><br/><strong>Order Tracking Number : </strong>".$OrderTrackingNumber;
			             

			            $htmlStr .= "<br/>Kind regards,<br />";
			            $htmlStr .= "<a href='https://globalvoicedirect.com/' target='_blank'>Globalvoicedirect Team</a><br />";


			            $name = "Order Submitted";
			            $email_sender = "no-reply@globalvoicedirect.com";
			            $subject = "Order Submitted";
			            $recipient_email = $email;
			  
			            $body = $htmlStr;
        $__mailOk = true;
        try {
			            \Mail::send(array(), array(), function ($message) use ($htmlStr,$recipient_email,$subject) {
				            $message->to($recipient_email)
				            ->subject($subject)
				            ->from(config('mail.from.address'),'Order Submitted')
				            ->html($htmlStr); 
			            });
        } catch (\Throwable $__e) {
            $__mailOk = false;
            \Log::error('Mail send failed in ' . __METHOD__ . ': ' . $__e->getMessage());
        }
		                if($__mailOk){
		                	return redirect()->route('checkout',['message'=>'your order submitted successfully.']);
		                }else{
		           			return redirect()->route('checkout',['error'=>'your order submitted successfully.But Email not sent']);
			            }

		            } else {
		            	$errorMessage = "Transaction Failed. ";
		                if ($tresponse->getErrors() != null) {
		                	$errorMessage.= $tresponse->getErrors()[0]->getErrorText();
		                }
		                return redirect()->route('checkout',['error'=>$errorMessage]);
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
		            return redirect()->route('checkout',['error'=>$errorMessage]);
		        }
		    } else {
		    	$errorMessage = "No response returned";
		    	return redirect()->route('checkout',['error'=>$errorMessage]);
		    }

		}else{
			return redirect()->route('checkout',['error'=>'Cart is empty']);
		}
		//function end
	}

//controller end
}

