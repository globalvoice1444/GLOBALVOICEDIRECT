<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use DateTime;
use Auth;
use Hash;
use DB;
use SoapClient;
use App\AdditionalCreditAppInfo;
use SoapFault;
use SoapHeader;
use SoapVar;
use Log;
use GuzzleHttp\Exception\ClientException;



class UserPanelController extends Controller
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
    public function marketingForm(Request $req)
    {
    	//dd($req);

        $userFormDetail=DB::table('marketing_info')->where('user_id',session()->get('user_id'))->first();	
        $userFormDetCount=DB::table('marketing_info')->where('user_id',session()->get('user_id'))->count();
        return view('users.marketing',compact('userFormDetail','userFormDetCount'));  
    }
    public function act_submitMarForm(Request $req){
      // dd($req->all());
      $userID = session()->get('user_id');
      $this->validate($req, [
            'form-image' => 'required|mimes:pdf,jpeg,png,gif|max:2048',
        ]);
      $validated = $req->validate([
            'firstname' => 'required|max:100',
            'lastname' => 'required|max:100',
            'phone' => 'required|numeric|digits_between:2,50',
            'email' => 'required|email|max:100',
            'b_hour' => 'required|max:50',
            'b_website' => 'required|max:100',
            'b_description' => 'required|max:3000',
            'b_category' => 'required|max:200',
            'short_description' => 'required|max:300',
            'address' => 'required|max:100',
            'city' => 'required|max:50',
            'state' => 'required|max:50',
            'zip' => 'required|max:50',
            'buisness_name' => 'required|max:100',
        ]);
      if($req->hasFile('form-image')) {
          $image = $req->file('form-image');
          $imageName = $userID.'.'.$image->getClientOriginalExtension();
           
          $filePath ="marketImages/"; //.session()->get('user_id');
          $destinationPath = public_path($filePath);

          if($image->move($destinationPath,$imageName)){
              
          }else{
            return redirect()->back()->with('status', 'image was not upload');
          }

        }else
        {
          $imageName = "";
        }
        $userData = DB::table('wpgc_users')->where('ID', '=', $userID)->first();
        //dd($userData);
        //dd($userData->display_name);
        $userName = $userData->display_name;
        DB::table('marketing_info')->insert([
          'user_id' => $userID,
          'buisness_name' => $req->buisness_name,
          'street_address'=> $req->address,
          'city' => $req->city,
          'state' => $req->state,
          'zip_code'=> $req->zip,
          'first_name' => $req->firstname,
          'last_name' => $req->lastname,
          'buisness_phone' => $req->phone,
          'buisness_email' =>$req->email,
          'buisness_hour' => $req->b_hour,
          'buisness_website' => $req->b_website,
          'buisness_desc' => $req->b_description,
          'buisness_keyword' => $req->b_category,
          'short_description' => $req->short_description,
          'social_sites' => $req->social_sites,
          'image_path' => $imageName
        ]);

        $htmlStr = "";
        $htmlStr .= "Here is the Detail of Business Information<br /><br /><br />";

        $htmlStr .= "<strong>User Name:</strong> " . $userName;

        $htmlStr .= "<br /> <br/>";

        $htmlStr .= "<strong>Business Name: </strong>".$req->buisness_name."<br />";
        $htmlStr .= "<strong>Street Address: </strong>".$req->address."<br />";
        $htmlStr .= "<strong>City: </strong>".$req->city."<br />";
        $htmlStr .= "<strong>State: </strong>".$req->state."<br />";
        $htmlStr .= "<strong>Zip Code: </strong>".$req->zip."<br />";
        $htmlStr .= "<strong>Business First Name: </strong>".$req->firstname."<br />";
        $htmlStr .= "<strong>Business Last Name: </strong>".$req->lastname."<br />";
        $htmlStr .= "<strong>Business Phone: </strong>".$req->phone."<br />";
        $htmlStr .= "<strong>Business Email: </strong>".$req->email."<br />";
        $htmlStr .= "<strong>Business Hours: </strong>".$req->b_hour."<br />";
        $htmlStr .= "<strong>Business Website: </strong>".$req->b_website."<br />";
        $htmlStr .= "<strong>Business Description: </strong>".$req->b_description."<br />";
        $htmlStr .= "<strong>Business Category/Keywords: </strong>".$req->b_category."<br />";
        $htmlStr .= "<strong>Business Short Description: </strong>".$req->short_description."<br />";
        $htmlStr .= "<strong>Business Social Sites: </strong>".$req->social_sites."<br />";
         

        $htmlStr .= "<br/>Kind regards,<br />";
        $htmlStr .= "<a href='http://laravelgvd.globalvoicedirect.com/' target='_blank'>Globalvoicedirect Team</a><br />";


        $name = "globalvoicedirect";
        $email_sender = "no-reply@globalvoicedirect.com";
        $subject = "User Market Information";
        $recipient_email = "Info@globalvoicedirect.com";

        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
        $headers .= "From: {$name} <{$email_sender}> \n";

        $body = $htmlStr;

        if(self::gvdMail($recipient_email, $subject, $body, $headers)){
          return redirect()->back()->with('status', 'All detailed submitted successfully');
        }else{
          return redirect()->back()->with('status', 'All detailed submitted successfully. But email not sent!');
        }

    }
    public function additional_credit_app()
    {

      $userDetail=DB::table('additional_creditapp_info')->where('user_id',session()->get('user_id'))->first();
      if($userDetail)
      {
        $alreadysubmitted=true;
      }
      else{
        $alreadysubmitted=false;
      }
      return view('users.additionalcreditapp',compact('alreadysubmitted'));
    }

    public function act_another_submitMarForm(Request $req)
    {
       
       $validatedData = $req->validate([
        'duns_number' => 'required',
        'sic_code' => 'required',
        'naics_code' => 'required',
        'first_sale' => 'required',
        'last_sale' => 'required',
        'credit_limit' => 'required|regex:/^[0-9]+$/',
        'add_credit_email' => 'required',
        'business_address' => 'required',
        'business_name' => 'required'
      ]);

      $userID = session()->get('user_id');
      $mart = new AdditionalCreditAppInfo;
      $mart->user_id=$userID;
      $mart->duns_number=$req->duns_number;
      $mart->sic_code=$req->sic_code;
      $mart->naics_code=$req->naics_code;
      $mart->first_sale=$req->first_sale;
      $mart->last_sale=$req->last_sale;
      $mart->credit_limit=$req->credit_limit;
      $mart->business_address=$req->business_address;
      $mart->business_name =$req->business_name;
      $mart->email=$req->add_credit_email;
     
      $mart->save();

      return redirect()->back()->with('message', 'Additional Credit App Request Submitted Successfully...');
      
    }


    public function addPort(Request $req)
    {
    	return view('users.addport');
    }
    public function act_addport(Request $req)
    {
       $portNumber = $req->portnumber;
       $endPortNumber = $req->endportnumber;
       $partial = $req->partial;
       $additional = $req->additional;
       $wireless = $req->wireless;
       $ssnpin = $req->ssnpin;
       $carrier = $req->carrier;
       $company = $req->company;
       $accountNumber = $req->accnumber;
       $name = $req->name;
       $streetNumber = $req->streetnumber;
       $streetPrefix = $req->streetprefix;
       $streetName = $req->streetname;
       $streetSuffix = $req->streetsuffix;
       $unit = $req->unit;
       $billNumber = $req->billnumber;
       $contactNumber = $req->contactnumber;
       $routeto = $req->routeto;
       $ratePlan = $req->rateplan;
       $portType = $req->porttype;
       $city = $req->city;
       $state = $req->state;
       $zip = $req->zip;

      $customer_login=config('services.vitelity.login'); 
      $customer_password=config('services.vitelity.pass');

       $url = 'https://api.vitelity.net/lnp.php';

       $data = array('login' => $customer_login, 'pass' => $customer_password, 'cmd' => "addport", 'xml' => "yes", 'portnumber' => $portNumber, 'endportnumber' => $endPortNumber, 'partial' => $partial, 'additional' => $additional, 'wireless' => $wireless, 'ssnpin' => $ssnpin, 'carrier' => $carrier, 'company' => $company, 'accnumber' => $accountNumber, 'name' => $name, 'streetnumber' => $streetNumber,'streetprefix' => $streetPrefix,'streetname' => $streetName,'streetsuffix' => $streetSuffix,'unit' => $unit,'city' => $city,'state' => $state,'zip' => $zip,'billnumber' => $billNumber,'contactnumber' => $contactNumber, 'routeto'=> $routeto, 'rateplan'=> $ratePlan,'porttype'=> $portType);

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

        //dd($xml);
       if( $xml->status == 'ok'){


          /* Insert Into database */
          $userId = session()->get('user_id');
          DB::table('lnp_port')->insert(['portNumber'=>$portNumber,'endPortNumber'=>$endPortNumber,'partial'=>$partial,'additional'=>$additional,'wireless'=>$wireless,'ssnPin'=>$ssnpin,'carrier'=>$carrier,'company'=>$company,'accountNumber'=>$accountNumber,'userName'=>$name,'streetNumber'=>$streetNumber,'streetSuffix'=>$streetPrefix,'unit'=> $streetName,'billNumber'=>$billNumber,'contactNumber'=>$contactNumber,'routeTo'=>$routeto,'ratePlan'=>$ratePlan,'portType'=>$portType,'city'=>$city,'state'=>$state,'zip'=>$zip,'userID'=>session()->get('user_id')]);

          session()->flash('message','Port SuccessFully Added');

          return redirect()->back();



      }else{

        session()->flash('error','Invalid Port Type');

        return redirect()->back();
    }


}
public function listingbusinessname()
{
   return view('users.listingbusinessname');
}
public function act_Listingbusinessname(Request $req)
{

    $bussinesName = $req->businessname;
    $did = $req->did;
    $streetNumber = $req->streetnumber;
    $streetName = $req->streetname;
    $city = $req->city;
    $stateName = $req->state;
    $zipCode = $req->zip;
    $email = $req->email;
    $contactNumber = $req->contactphone;

    $customer_login=config('services.vitelity.login'); 
    $customer_password=config('services.vitelity.pass');

    $url = 'https://api.vitelity.net/dl.php';

    $data = array('login' => $customer_login, 'pass' => $customer_password, 'cmd' => "orderbusinesslisting", 'xml' => "yes", 'did' => $did, 'businessname' => $bussinesName, 'streetnumber' => $streetNumber, 'streetname' => $streetName, 'city' => $city, 'state' => $stateName, 'zip' => $zipCode, 'email' => $email, 'contact_phone' => $contactNumber);

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

        /* Insert into database */
        $userID = session()->get('user_id');
        DB::table('addon_business_listing')->insert(['businessname'=>$bussinesName,'did'=>$did,'streetname'=>$streetNumber,'streetname'=>$streetNumber,'city'=>$city,'state'=>$stateName,'zip'=>$zipCode,'email'=>$email,'contactphone'=>$contactNumber,'userID'=>$userID]);

        session()->flash('message','Port SuccessFully Added');
        return redirect()->back();
    }
    else{

        session()->flash('error','Invalid Port Type');

        return redirect()->back();
    }
}

public function howtovideo()
{
   $orderDet=DB::table('orders')->where('OrderUserID',session()->get('user_id'))->get();
   $orderDetCount=DB::table('orders')->where('OrderUserID',session()->get('user_id'))->count();
   return view('users.vo-howto'); // 2026-11-01 VO design, same video sources (old view users.how_to_videos kept)
}
public function ot_editor_sendEmail()
{
    $smsGroupDetail = DB::table('email_campaign_group')->where('user_id',session()->get('user_id'))->get();
    $smsGroupDetailCount =DB::table('email_campaign_group')->where('user_id',session()->get('user_id'))->count();

    return view('users.ot_editor_sendEmail',compact('smsGroupDetailCount','smsGroupDetail'));
}
public function act_ot_sendEmail(Request $req)
{
 if (\App\Portal\Demo::on()) return redirect()->route('emailCampaign', ['message' => \App\Portal\Demo::READONLY]); // sample-data login: never saves or sends
 if(isset($req->grpId) and $req->grpId != "" and isset($req->editor1) and $req->editor1 != ""){   
    $campainGrpDet = DB::table('email_contact')->where('group_id',$req->grpId)->where('user_id',session()->get('user_id'))->where('deleted',0)->orderBy('created_at','DESC')->get();

    $campainGrpDetCount = DB::table('email_contact')->where('group_id',$req->grpId)->where('user_id',session()->get('user_id'))->where('deleted',0)->orderBy('created_at','DESC')->count();
    $contactsCount = 0;
    if($campainGrpDetCount > 0){
       $GrpDet=DB::table('email_campaign_group')->where('id',$req->grpId)->where('user_id',session()->get('user_id'))->where('deleted',0)->orderBy('created_at','DESC')->first();
       $contactArr = array();
       foreach($campainGrpDet as $result1) {
           $htmlStr=$req->editor1;
           if(isset($req->subject))
           {
            $subject=$req->subject;
        }
        else{
            $subject=$GrpDet->group_name;
        }
        $__mailOk = true;
        try {
        \Mail::send(array(), array(), function ($message) use ($htmlStr,$result1,$req,$subject) {
            $message->to($result1->contact_address)
            ->from(config('mail.from.address'))
            ->subject($subject)
            ->from(config('mail.from.address'))
            ->html($htmlStr); 
        });
        } catch (\Throwable $__e) {
            $__mailOk = false;
            \Log::error('Mail send failed in ' . __METHOD__ . ': ' . $__e->getMessage());
        }
        if($__mailOk)
        {
         return redirect()->route('ot_editor_sendEmailView',['message'=>'Email Send SuccessFully']);
     }
     else
     {
        return redirect()->route('ot_editor_sendEmailView',['message'=>'Email is not able to send']);

    }
}

}else{
   return redirect()->route('ot_editor_sendEmailView',['message'=>'There is no contact in this Group!']);
} 

}else{
               // dd('reach');
    return redirect()->route('ot_editor_sendEmailView',['message'=>'There is no contact in this Group!']);
                //header("Location: /users/ot_editor_sendEmail.php?message=There is no contact in this Group!");
    /*die();*/
}
//CONTROLLER 
}

public function send_email()
{
    //dd('reach');
 $smsGroupDetail=DB::table('email_campaign_group')->where('user_id',session()->get('user_id'))->get();    


 $smsGroupDetailCount = DB::table('email_campaign_group')->where('user_id',session()->get('user_id'))->count();

 $emailDetailCount = 0;

 $emailDetail=DB::table('email_template')->where('user_id',session()->get('user_id'))->where('editor_type',1)->where('deleted',0)->orderBy('created_at','desc')->get();

 $emailDetailCount = DB::table('email_template')->where('user_id',session()->get('user_id'))->where('editor_type',1)->where('deleted',0)->orderBy('created_at','desc')->count();

  return view('users.sendEmail',compact('smsGroupDetail','smsGroupDetailCount','emailDetailCount','emailDetailCount','emailDetail'));
}
public function sendEmailTemp(Request $req)
{
 if (\App\Portal\Demo::on()) return redirect()->route('emailCampaign', ['message' => \App\Portal\Demo::READONLY]); // sample-data login: never saves or sends
    //dd($req);
if(isset($req->grpId) and $req->grpId != "" and isset($req->subject) and $req->subject != ""){
 $campainGrpDet=DB::table('email_contact')->where('group_id',$req->grpId)->where('user_id',session()->get('user_id'))->where('deleted',0)->orderBy('created_at','desc')->get();
       
$campainGrpDetCount = DB::table('email_contact')->where('group_id',$req->grpId)->where('user_id',session()->get('user_id'))->where('deleted',0)->orderBy('created_at','desc')->count();
                $contactsCount = 0;
                if($campainGrpDetCount > 0){
       $GroupName= DB::table('email_campaign_group')->where('id',$req->grid)->where('user_id',session()->get('user_id'))->where('deleted',0)->orderBy('created_at','desc')->first();

                  
                    $userDetail=DB::select(" 
                        SELECT u.*, b.* from wpgc_users u LEFT JOIN user_shipping_address b ON u.ID=b.user_id where u.ID = ?", [session()->get('user_id')]
                    );
                   
                    $contactArr = array();
                    foreach($campainGrpDet as $result1) {
                        $footerHtml = $req->tmpHtml.'<div class="footer" style="text-align:center; border-top: 1px solid gray;"><div style="margin: 1% 2% 0 2%;padding: 10px; background: #c0c0c0;"><h4>'.$userDetail[0]->display_name.'</h4><p>'.$userDetail[0]->address.'</p><p>This message was sent to '.$result1->contact_address.' by '.$userDetail[0]->user_email.'</p><p>you can <a href="https://dev.globalvoicedirect.com/subscriber_action/unsubscribe_action.php?em_id='.$result1->id.'&em_email='.$result1->contact_address.'" target="_blank">opt-out completely</a> from all future mailings.</p></div></div>';
                         
                        $htmlStr='<html><head><style>'.$_POST["tmpCss"]. '</style></head>' . "<body>" . $footerHtml. "</body></html>";
                        $subject = $req->subject ? $req->subject : $GroupName->group_name;
        $__mailOk = true;
        try {
                        \Mail::send(array(), array(), function ($message) use ($htmlStr,$userDetail,$result1,$subject) {
                            $message->to($result1->contact_address)
                            ->from(config('mail.from.address'), $userDetail[0]->display_name ?: config('mail.from.name'))->replyTo($userDetail[0]->user_email)
                            ->subject($subject)
                            ->html($htmlStr); 
                        });
        } catch (\Throwable $__e) {
            $__mailOk = false;
            \Log::error('Mail send failed in ' . __METHOD__ . ': ' . $__e->getMessage());
        }
                        if($__mailOk){
                          echo 'Message has been sent.';
                         
                          die();
                        } else {
                            echo 'Message was not sent.';
                          
                          die();
                        }
                       
                    }
                     
                    
                }else{
                    echo "There is no contact in this Group!";
                    die();
                } 

            }else{
                echo "There is no contact in this Group!";
                die();
            }
}

public function saveEmailTemp(Request $req)
{
 if (\App\Portal\Demo::on()) return redirect()->route('emailCampaign', ['message' => \App\Portal\Demo::READONLY]); // sample-data login: never saves or sends
   if(isset($req->name) and $req->name != ""){
         
        $html = $req->tmpHtml;
        $css =  $req->tmpCss;
      
      DB::table('email_template')->insert(['user_id'=>session()->get('user_id'),'email_name'=>$req->name,'email_content'=>$html,'css'=>$css,'editor_type'=>1]);
     

        echo "Email Template saved successfully.";
          die();
             
          
      }else{
          echo "Somthing went Wrong. Please try again!!";
          die();
        }
}
public function saveAndSendEmailTemplate(Request $req)
{
 if (\App\Portal\Demo::on()) return redirect()->route('emailCampaign', ['message' => \App\Portal\Demo::READONLY]); // sample-data login: never saves or sends
  if(isset($req->grpId) and $req->grpId != "" and isset($req->subject) and $req->subject != "" and isset($req->name) and $req->name != "" and $req->tmpHtml !=null and $req->tmpHtml !=""){

        $campainGrpDet = DB::select("SELECT * from email_contact where group_id = ? and user_id = ? and deleted = 0 order by created_at desc", [$req->grpId, session()->get('user_id')]);

            

        $campainGrpDetCount = DB::table('email_contact')->where('group_id',$req->grpId)->where('user_id',session()->get('user_id'))->where('deleted',0)->count();
        $contactsCount = 0;
        if($campainGrpDetCount > 0){
        /*  $GrpDet = $pdo->prepare('SELECT * from email_campaign_group where id = :grid and user_id = :id and deleted = :del order by created_at desc');
          $GrpDet->execute(array(':grid' => $_POST["grpId"], ':id' => $_SESSION["user_id"], ':del' => 0));*/

          $GroupName = DB::select("SELECT * from email_campaign_group  where id = ? and user_id = ? and deleted = 0 order by created_at desc", [$req->grpId, session()->get('user_id')]);
     $html = $req->tmpHtml;
        $css =  $req->tmpCss;
         
          DB::table('email_template')->insert(['user_id'=>session()->get('user_id'),'email_name'=>$req->name,'email_content'=>$html,'css'=>$css,'editor_type'=>1]);                                                  
    $userDetail=DB::select("SELECT u.*, b.* from wpgc_users u LEFT JOIN user_shipping_address b ON u.ID=b.user_id where u.ID = ?", [session()->get('user_id')]);
        /*  $userDet = $pdo->prepare('SELECT u.*, b.* from wpgc_users u LEFT JOIN user_shipping_address b ON u.ID=b.user_id where u.ID = :id');
          $userDet->execute(array(':id' => $_SESSION["user_id"]));
          $userDetail = $userDet->fetch();
          $contactArr = array();*/
         // dd($campainGrpDet);
          foreach( $campainGrpDet as $result1) {

            $footerHtml = $req->tmpHtml.'<div class="footer" style="text-align:center; border-top: 1px solid gray;"><div style="margin: 1% 20% 0 20%;padding: 10px; background: #c0c0c0;"><h4>'.$userDetail[0]->display_name.'</h4><p>'.$userDetail[0]->address.'</p><p>This message was sent to '.$result1->contact_address.' by '.$userDetail[0]->user_email.'</p><p>you can <a href="https://dev.globalvoicedirect.com/subscriber_action/unsubscribe_action.php?em_id='.$result1->id.'&em_email='.$result1->contact_address.'" target="_blank">opt-out completely</a> from all future mailings.</p></div></div>';
             
              $htmlStr='<html><head><style>'.$req->tmpCss. '</style></head>' . "<body>" . $footerHtml. "</body></html>";
                        $subject = $req->subject ? $req->subject : $GroupName->group_name;
        $__mailOk = true;
        try {
                        \Mail::send(array(), array(), function ($message) use ($htmlStr,$userDetail,$result1,$subject) {
                            $message->to($result1->contact_address)
                            ->from(config('mail.from.address'), $userDetail[0]->display_name ?: config('mail.from.name'))->replyTo($userDetail[0]->user_email)
                            ->subject($subject)
                            ->html($htmlStr); 
                        });
        } catch (\Throwable $__e) {
            $__mailOk = false;
            \Log::error('Mail send failed in ' . __METHOD__ . ': ' . $__e->getMessage());
        }
                       // dd(count(\Mail::failures()));
                        if($__mailOk){
                       //dd('1');
                      echo 'Message has been sent.';
              //echo 'Mailer error: ' . $mail->ErrorInfo;
                          die();
                         } else {
                         // dd('2');
                       echo 'Message was not sent.';
                        die();
                         }
           
          }
           
          
          }else{
            echo "There is no contact in this Group!";
            die();
          } 

      }else{
        echo "Please Complete the All required fields for save and send Email Campaign!";
         }

}

public function getTemplates(Request $req)
{
   if(isset($req->id)){
     
     $emailTempDet=DB::select("SELECT email_content, css from email_template where id = ? and editor_type=1 and deleted = 0 order by created_at desc", [$req->id]);
    /*$emailTempDet = $pdo->prepare('SELECT email_content, css from email_template where id = :id and editor_type=:tpe and deleted = :del order by created_at desc');*/
   
    $emailTempDetCount =count($emailTempDet);
    if($emailTempDetCount > 0){
      echo json_encode($emailTempDet);
      die();
    }else{
      echo "";
      die();
    }
 
  }else{
    echo "";
    die();
  }
}

public function emailCampaign(Request $req)
{
 if (\App\Portal\Demo::on()) { $smsContactDet = \App\Portal\Demo::emailContacts(); $smsContactDetCount = $smsContactDet->count(); $smsGroupDetail = \App\Portal\Demo::emailGroups(); $smsGroupDetailCount = $smsGroupDetail->count(); return view('users.emailCompain', compact('smsContactDet','smsContactDetCount','smsGroupDetail','smsGroupDetailCount')); } // sample-data login only
 $userIdEmail = session()->get('user_id');
 $smsContactDet=DB::table('email_contact')->where('user_id',$userIdEmail)->where('deleted',0)->orderBy('created_at','DESC')->get();
 $smsContactDetCount=count($smsContactDet);
 $smsGroupDetail=DB::table('email_campaign_group')->where('user_id',$userIdEmail)->get();
  $smsGroupDetailCount=count($smsGroupDetail);
  return view('users.emailCompain',compact('smsContactDet','smsContactDetCount','smsGroupDetail','smsGroupDetailCount'));
 
}
public static function GetcampainGrpDet($group_id)
{
 if (\App\Portal\Demo::on()) return \App\Portal\Demo::emailGroup($group_id); // sample-data login only
 
  return DB::table('email_campaign_group')->where('id',$group_id)->where('deleted',0)->orderBy('created_at','desc')->first();
 // return $group_id;
}
public function act_add_email_contacts(Request $req)
{
 if (\App\Portal\Demo::on()) return redirect()->route('emailCampaign', ['message' => \App\Portal\Demo::READONLY]); // sample-data login: never saves or sends
  //dd($req->input('form-method'));
try {
      if($req->input('form-method')!=null ){
        switch ($req->input('form-method')) {
          case '1':
          //dd('reach');
          DB::table('email_campaign_group')->insert(['group_name'=>$req->campaignName,'user_id'=>session()->get('user_id')]);
          //dd('');
          return redirect()->route('emailCampaign',['message'=>'Campaign Group Added Successfully.']);
           /* header("Location: /users/emailCampaign.php?message=Campaign Group Added Successfully.");*/
            break;
          case '2':
            if( $_FILES["campaignContacts"]["size"] > 0 and isset($req->campaignName) and $req->campaignName != ""){

              $fileName = $_FILES["campaignContacts"]["tmp_name"];
              $file = fopen($fileName, "r");
              while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
              $contact_name=$column[0]. ",". $column[1];
               DB::table('email_contact')->insert(['user_id'=>session()->get('user_id'),'contact_name'=>$contact_name,'contact_address'=>$column[2],'group_id'=>$req->campaignName]);
            
              }
              return redirect()->route('emailCampaign',['message'=>'All Contacts Added Successfully.']);
             

            }else{
              return redirect()->route('emailCampaign',['message'=>'CSV file or campaign group not select!']);
             
            }
            break;
          case '3':
            if( isset($req->contactfirstName1) and isset($req->contactlastName1) and isset($req->contactEmail1) and $req->campaignName != "" ){

              $counter = 1;
              while (isset($_POST['contactEmail'.$counter])){
                
                DB::table('email_contact')->insert(['user_id'=>session()->get('user_id'),'contact_name'=>$_POST['contactfirstName'.$counter] . "," . $_POST['contactlastName'.$counter],'contact_address'=>$_POST['contactEmail'.$counter],'group_id'=>$req->campaignName]);
             /*   $statement1 = $pdo->prepare("INSERT INTO email_contact(user_id, contact_name, contact_address, group_id)
                  VALUES(:u_id, :cont_name, :cont_num, :g_id)");
                $statement1->execute(array(
                    "u_id" => $_SESSION["user_id"],
                    "cont_name" => $_POST['contactfirstName'.$counter] . "," . $_POST['contactlastName'.$counter],
                    "cont_num" => $_POST['contactEmail'.$counter],
                    "g_id" => $_POST['campaignName']
                ));*/
                $counter = $counter + 1;
              }
              return redirect()->route('emailCampaign',['message'=>'Contact Added Successfully.']);
              //header("Location: /users/emailCampaign.php?message=Contact Added Successfully.");

            }else{
              return redirect()->route('emailCampaign',['message'=>'Campaign Group not Selected!']);
              //header("Location: /users/emailCampaign.php?message=Campaign Group not Selected!");
            }
            break;
          case '4':
            if( isset($_POST['contactfirstName']) and isset($_POST['contactlastName']) and isset($_POST['contactEmail']) ){

              $statement1 = $pdo->prepare("INSERT INTO email_contact(user_id, contact_name, contact_address)
                  VALUES(:u_id, :cont_name, :cont_num)");

              $statement1->execute(array(
                  "u_id" => $_SESSION["user_id"],
                  "cont_name" => $_POST['contactfirstName'] . "," . $_POST['contactlastName'],
                  "cont_num" => $_POST['contactEmail']
              ));
              header("Location: /users/emailCampaign.php?message=Contact Added Successfully.");

            }elseif( isset($_POST['contactfirstName']) and isset($_POST['contactEmail']) ){
              $statement1 = $pdo->prepare("INSERT INTO email_contact(user_id, contact_name, contact_address)
                  VALUES(:u_id, :cont_name, :cont_num)");

              $statement1->execute(array(
                  "u_id" => $_SESSION["user_id"],
                  "cont_name" => $_POST['contactfirstName'],
                  "cont_num" => $_POST['contactEmail']
              ));
              header("Location: /users/emailCampaign.php?message=Contact Added Successfully.");
            }elseif( isset($_POST['contactlastName']) and isset($_POST['contactEmail']) ){
              $statement1 = $pdo->prepare("INSERT INTO email_contact(user_id, contact_name, contact_address)
                  VALUES(:u_id, :cont_name, :cont_num)");

              $statement1->execute(array(
                  "u_id" => $_SESSION["user_id"],
                  "cont_name" => $_POST['contactlastName'],
                  "cont_num" => $_POST['contactEmail']
              ));
              header("Location: /users/emailCampaign.php?message=Contact Added Successfully.");
            }

            else{
              header("Location: /users/emailCampaign.php?message=Form Not Exist!");
            }
            break;
          default:
            header("Location: /users/emailCampaign.php?message=Form Method not Valid!");
            break;
        }
      }else{
        return redirect()->route('emailCampaign',['message'=>'Form Method not Valid!']);
       /* header("Location: /users/emailCampaign.php?message=Form Method not Valid!");*/
      }

    }catch(exception $e){
       return redirect()->route('emailCampaign',['message'=>'Somthing went Wrong. Please try again!']);
    
    }
//function ends here
    }
    public function audioCampaign(Request $req)
    {
      $audioGroupDetail = DB::table('audio_campaign_group')->where('user_id',session()->get('user_id'))->get();
      //dd($audioGroupDetail);
  

      $audioGroupDetailCount =count($audioGroupDetail);
     
    $numberDet = DB::select('SELECT a.*, g.group_name from audio_broadcast_numbers a LEFT JOIN audio_campaign_group g ON a.type=g.id where a.user_id = ? and a.deleted = 0 order by created_at desc', [session()->get('user_id')]);
    $numberDetCount=count($numberDet);

    $numberDetdropdown=DB::select('SELECT a.*, g.group_name from audio_broadcast_numbers a LEFT JOIN audio_campaign_group g ON a.type=g.id where a.user_id = ? and a.deleted = 0 order by created_at desc', [session()->get('user_id')]);
    //dd($numberDetCount);
    return view('users.audioCompain',compact('audioGroupDetail','audioGroupDetailCount','numberDet','numberDetCount','numberDetdropdown'));
    }
   public function check(Request $req)
   {
    $host = 'mybilling.globalvoicedirect.com'; // mybilling.yoursite.com or IP address
	  $um = 'mybilling.globalvoicedirect.com'; //UM web interface address of your environment, e.g., um.telinta.com
	 $login = 'max21'; // admin/root login
	 $password = env('TELINTA_API_PASSWORD'); // password or token
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

$service = 'Admin';
$account_id = "12392061647";
$voip_password = env('TELINTA_VOIP_PASSWORD');

$session = new SoapClient ("https://" . $host . "/wsdl/Session${service}Service.wsdl", $soap_options);
//dd($session);

//MR50 password auth
$session_id = $session->login(array('login'=>$login, 'password'=>$password))->session_id;
//MR50 token auth
if (empty($session_id)) { # MR50 token
  $session_id = $session->login(array('login'=>$login, 'token'=>$password))->session_id;
}
//MR45 password auth
if (empty($session_id)) { # MR50 token
  $session_id = $session->login($login, $password);
}

if (! is_soap_fault ($session_id)) {
  $headers = NULL;
  $headers[] = new SoapVar ("<session_id>$session_id</session_id>", XSD_ANYXML, "session_id", "https://" . $host . "/Porta/SOAP/Session");
  $auth_info = new SoapHeader ("https://" . $host . "/Porta/SOAP/Session", "auth_info", $headers);

  $account = new SoapClient("https://" . $host . "/wsdl/Account${service}Service.wsdl", $soap_options);
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
public function getaudionumbers(Request $req)
{
 if (\App\Portal\Demo::on()) return redirect()->route('emailCampaign', ['message' => \App\Portal\Demo::READONLY]); // sample-data login: never saves or sends
  $numbers = json_decode($req->ids);
    $numbers = implode(",",$numbers);
    
    $audioGroupDetail = DB::select('SELECT number from audio_broadcast_numbers where type in ('.implode(',', array_map('intval', explode(',', $numbers))).') and deleted=0');
   

    $audioGroupDetailCount = count($audioGroupDetail);

    if($audioGroupDetailCount > 0){
      $arr = [];
      foreach($audioGroupDetail as $rowlist) {
        array_push($arr, $rowlist->number);
      }
      echo json_encode($arr);
      die();
    }else{
      echo "";
      die();
    }
}
  public function active_calls(Request $req)
  {
    $active_calls=file_get_contents("https://globalvoicedirect.com/public/ActiveCall.php?ivrnumber=".$req->ivrnumber."");
    $active_call=json_decode($active_calls);
    $activeCallsResults=$active_call;
     return view('users.getActiveCalls',compact('activeCallsResults'));
}
public function smscampaign(Request $req)
{
  $userIDSms = session()->get('user_id');
  $smsGroupDetail =   DB::table('compaign_group')
                      ->where('user_id',$userIDSms)
                      ->where('deleted',0)
                      ->orderBy('created_at','desc')
                      ->get();
  $smsGroupDetailCount = count($smsGroupDetail);
  $smsSentDet=DB::table('sms_sent')->where('user_id',$userIDSms)->where('deleted',0)->orderBy('created_at','desc')->get();
  $smsSentDetCount=count($smsSentDet);
  $smsContactDet =DB::table('sms_contact')->where('user_id',$userIDSms)->where('deleted',0)->orderBy('created_at','desc')->get();
  $smsContactDetCount =count($smsContactDet);

  $smsCreditDetail= DB::table('sms_compaign_credit_history')->where('user_id',$userIDSms)->orderBy('created_at','desc')->get();
  $smsCreditDetailCount = count($smsCreditDetail);

  $smsCreditDet = DB::table('sms_compaign_credit')->where('user_id',$userIDSms)->where('active',0)->first();
 
  $smsCreditDetCount= DB::table('sms_compaign_credit')->where('user_id',$userIDSms)->where('active',0)->count();
  //dd($smsCreditDetCount);
  if($smsCreditDetCount > 0){
    $smsCreditDet->total_sms;
    $TotNumOfSMS = $smsCreditDet->total_sms;
  }else{
    DB::table('sms_compaign_credit')->insert(['total_sms'=>10,'user_id'=>session()->get('user_id')]);
  
    $TotNumOfSMS = 10;
  }
  $smsDet=DB::table('sms_sent')->where('user_id',$userIDSms)->where('accepted',1)->where('created_at','>=',date('Y-m-d'))->get();

  $smsDetCount = count($smsDet);

  //dd($smsCreditDetailCount);
  if($req->view_type=='viewSmsDetail')
  {
     return view('users.viewSmsDetail',compact('smsGroupDetail','smsGroupDetailCount','smsSentDet','smsSentDetCount','smsContactDet','smsContactDetCount','smsCreditDetail','smsCreditDetailCount','smsDet','smsDetCount','TotNumOfSMS'));
  }
  else{
    return view('users.smsCompainView',compact('smsGroupDetail','smsGroupDetailCount','smsSentDet','smsSentDetCount','smsContactDet','smsContactDetCount','smsCreditDetail','smsCreditDetailCount','smsDet','smsDetCount','TotNumOfSMS'));
  }
}
public function viewSmsDetail()
{

  return redirect()->route('smscampaign',['view_type'=>'viewSmsDetail']);
  //$this->smscampaign($view_type);
  //dd('reach');

}
public static function getCampainGrpDetByGroupId($group_id)
{
  //return $group_id;
  return DB::select('SELECT * from compaign_group where id = ? and deleted = 0 order by created_at desc', [$group_id]);
  
} 
public function getSmsSentDet(Request $req)
{
  if(isset($req->id)){
    
    $smsDetail=DB::table('sms_sent')->where('id',$req->id)->first(); 
  
    echo json_encode($smsDetail);
    exit;
  }
}
  public function deleteContact(Request $req)
  {
      //dd($req);
     /*  $statement1 = $pdo->prepare("update sms_sent set deleted = :del where id=:id");*/
      $status=DB::table('sms_sent')->where('id',$req->delId)->update(['deleted'=>1]);
      
      if($status==true)
      {
       return redirect()->route('smscampaign',['message'=>'SMS deleted Successfully!','view_type'=>'viewSmsDetail']);
      }
      else
      {
        return redirect()->route('smscampaign',['message'=>'Something Went Wrong. Please try Again!','view_type'=>'viewSmsDetail']);

      }
  }
  public function deleteEmailContact(Request $req)
  {
 if (\App\Portal\Demo::on()) return redirect()->route('emailCampaign', ['message' => \App\Portal\Demo::READONLY]); // sample-data login: never saves or sends
     
      $status=DB::table('email_contact')->where('id',$req->cid)->update(['deleted'=>1]);
      
      if($status==true)
      {
       return redirect()->route('emailCampaign',['message'=>'Email Contact deleted Successfully!']);
      }
      else
      {
        return redirect()->route('emailCampaign',['message'=>'Something Went Wrong. Please try Again!']);

      }
  }
   public function removeSmsText($id)
  { 
      dd($id);

      $status=DB::table('sms_sent')->where('id',$req->delId)->update(['deleted'=>1]);
      
      if($status==true)
      {
       return redirect()->route('smscampaign',['message'=>'SMS deleted Successfully!','view_type'=>'viewSmsDetail']);
      }
      else
      {
        return redirect()->route('smscampaign',['message'=>'Something Went Wrong. Please try Again!','view_type'=>'viewSmsDetail']);

      }
  }
   public function deleteSmsContact(Request $req)
   { 
      $status=DB::table('sms_contact')->where('id',$req->cid)->update(['deleted'=>1]);
      
      if($status==true)
      {
       return redirect()->route('smscampaign',['message'=>'Contact deleted Successfully!']);
      }
      else
      {
        return redirect()->route('smscampaign',['message'=>'Something Went Wrong. Please try Again!']);

      }
   }

public function buy_credit()
{
    $smsCreditDet =DB::table('sms_compaign_credit')->where('user_id',session()->get('user_id'))->where('active',0)->first(); 

    $smsCreditDetCount = DB::table('sms_compaign_credit')->where('user_id',session()->get('user_id'))->where('active',0)->count();
    if($smsCreditDetCount > 0){
     // $smsCreditDet->total_sms;
      $TotNumOfSMS = $smsCreditDet->total_sms;
    }else{
      DB::table('sms_compaign_credit')->insert(['total_sms'=>10,'user_id'=>session()->get('user_id')]);
  
      $TotNumOfSMS = 10;
    }
    $smsDet =DB::table('sms_sent')->where('user_id',session()->get('user_id'))->where('accepted',1)->where('created_at',date('Y-m-d'))->count(); 
   
    $smsDetCount = $smsDet;
    return view('users.buy_credit',compact('smsDetCount','smsDet','TotNumOfSMS','smsCreditDetCount'));
    //dd('reach');
}
public function smsCreditAction(Request $req)
{
   $flag = false;
    $message = "<ul class='return-message'>";
    if(!isset($req->credit_amout) or $req->credit_amout < 20){
      $message = $message . "<li>Credit amount must be greater than 20.<li>";
      $flag = true;
    }
    if(!isset($req->cardNumber) or $req->cardNumber == ""){
      $message = $message . "<li>Credit card  number must be require.<li>";
      $flag = true;
    }
    if(!isset($req->month) or $req->month == ""){
      $message = $message . "<li>Credit card expiry month must be require.<li>";
      $flag = true;
    }
    if(!isset($req->year) or $req->year == ""){
      $message = $message . "<li>Credit card expiry year must be require.<li>";
      $flag = true;
    }
    if(!isset($req->firstname) or $req->firstname == ""){
      $message = $message . "<li>First name must be require.<li>";
      $flag = true;
    }
    if(!isset($req->lastname) or $req->lastname == ""){
      $message = $message . "<li>Last name must be require.<li>";
      $flag = true;
    }
    if(!isset($req->email) or $req->email == ""){
      $message = $message . "<li>Email Must be require.<li>";
      $flag = true;
    }
    if(!isset($req->phoneNumber) or $req->phoneNumber == ""){
      $message = $message . "<li>Phone Number Must be require.<li>";
      $flag = true;
    }
    if($flag){
      $message = $message . "</ul>";
      return redirect()->route('buy_credit',['message'=>$message]);
    }else{

      $cardnumber = $req->cardNumber;
      $cardcvv = $req->cardCVC;
      $month = $req->month;
      $year = $req->year;
      $amount = $req->credit_amout;
      // Common setup for API credentials  
      $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();   
      $merchantAuthentication->setName(config('services.authorize.login'));
      $merchantAuthentication->setTransactionKey(config('services.authorize.key'));    
      $refId = 'ref' . time();

      // Create the payment data for a credit card
      $creditCard = new AnetAPI\CreditCardType();
      $creditCard->setCardNumber($cardnumber);  
      $creditCard->setExpirationDate( $month."-".$year );
      $creditCard->setCardCode($cardcvv);
      $paymentOne = new AnetAPI\PaymentType();
      $paymentOne->setCreditCard($creditCard);

      $customer = new AnetAPI\CustomerDataType();
      $customer->setEmail($_POST['email']);

      // Create order information
      $order = new AnetAPI\OrderType();
      //$order->setInvoiceNumber("10101");
      $order->setDescription("SMS Campaign");
        
      //Bill To
      $billto = new AnetAPI\CustomerAddressType();  
      $billto->setFirstName($req->firstname);
      $billto->setLastName($req->lastname);
      $billto->setCompany($req->companyName);
      $billto->setPhoneNumber($req->phoneNumber);
         
      // Create a transaction
      $transactionRequestType = new AnetAPI\TransactionRequestType();
      $transactionRequestType->setTransactionType("authCaptureTransaction");   
      $transactionRequestType->setAmount($amount);
      $transactionRequestType->setOrder($order);
      $transactionRequestType->setPayment($paymentOne);
      $transactionRequestType->setCustomer($customer);
      $transactionRequestType->setBillTo($billto);
      $request = new AnetAPI\CreateTransactionRequest();
      $request->setMerchantAuthentication($merchantAuthentication);
      $request->setRefId( $refId);
      $request->setTransactionRequest($transactionRequestType);
      $controller = new AnetController\CreateTransactionController($request);
      $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);   
      // dd($response);
      if ($response != null) {

        $tresponse = $response->getTransactionResponse();

        if (($tresponse != null) && ($tresponse->getResponseCode()=="1")){
          $totalcredit = round(($amount * 100)/2.5);
           \DB::table('sms_compaign_credit')->where('user_id',session()->get('user_id'))->update(['updated_at'=>date("Y-m-d H:i:s"),'total_sms'=>\DB::raw('total_sms + '.$totalcredit.'')]);

          $authcode = $tresponse->getAuthCode();
          $transId = $tresponse->getTransId();
       
          DB::table('sms_compaign_credit_history')->insert(['transaction_id'=>$transId,'authentication_code'=>$authcode,'transaction_amount'=>$amount,'user_id'=>session()->get('user_id')]);

          // Send Email For Varification

          $htmlStr = "";
          $date=date_create(date("Y-m-d H:i:s"));
          $emailDate = date_format($date,"D, M d, Y");
                    
          $htmlStr .= "payment receipt text Order Confirmation Order #". $transId."<br/>";
          $htmlStr .= "Hello ".$req->lastname.",<br /> <br/>Thank you for shopping with us. We’ve successfully processed your payment for order# ". $transId.".<br /> This $".$amount."  charge was approved when you originally placed your order on ".$emailDate.".  The order is now complete. You were only charged once. <br />";
                      
          $htmlStr .= "<br/>Kind regards,<br />";
          $htmlStr .= "<a href='http://dev.globalvoicedirect.com/' target='_blank'>Globalvoicedirect Team</a><br />";

          $name = "SMS Compaign";
          $email_sender = "no-reply@globalvoicedirect.com";
          $subject = "SMS Compaign Credit";
          $recipient_email = $req->email;

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
          if($__mailOk){
            return redirect()->route('buy_credit',['message'=>'your SMS Campaign credit request submitted successfully']);
          }
          else{
            return redirect()->route('buy_credit',['message'=>' your SMS Campaign credit request submitted successfully.But Email not sent.']);
         
          }
        }else{
          return redirect()->route('buy_credit',['message'=>'your credit card information is not valid...Please try again.']);
      
        }
      }else{
       return redirect()->route('buy_credit',['message'=>'your credit card information is not valid...Please try again.']);
      }
    }
//function ends here  
}
public function audiosubscriber(Request $req)
{
    $userID = session()->get('user_id');
    $numberDet=DB::select('SELECT a.*, g.group_name from audio_broadcast_numbers a LEFT JOIN audio_campaign_group g ON a.type=g.id where a.user_id = ? and a.deleted = 0 order by created_at desc', [$userID]);
    
    $numberDetCount =count($numberDet);

    $numberDetdropdown = $numberDet;
  
    $numberDetdropdownCount = $numberDetCount;
    
    $audioGroupDetail=DB::select('SELECT * from audio_campaign_group where user_id = ?', [$userID]);

    $audioGroupDetailCount =count($audioGroupDetail); 

    $audioGroupDetail2 = $audioGroupDetail;

    return view('users.audiosubscriber',compact('numberDet','numberDetCount','numberDetdropdown','numberDetdropdownCount','audioGroupDetail','audioGroupDetailCount','audioGroupDetail2'));
 }
public function saveAudioBroadcastNumber(Request $req)
{
 if (\App\Portal\Demo::on()) return redirect()->route('emailCampaign', ['message' => \App\Portal\Demo::READONLY]); // sample-data login: never saves or sends
	try {
	    if($req->input('form-method') != ""){
	        switch ($req->input('form-method')) {
	        case '1':
	            DB::table('audio_campaign_group')->insert(['group_name'=>$req->campaignName,'user_id'=>session()->get('user_id')]);
	            return redirect()->route('audiosubscriber',['message'=>'Audio Campaign Group Added Successfully.']);
	            break;
	        case '2':
	            if( $req->hasFile('campaignContacts') and isset($req->campaignName) and $req->campaignName != ""){
	                $path = request()->file('campaignContacts')->getRealPath();
	                $csv_data=\file($path);
	                $csv_file_count=count($csv_data);
	                for($i=0;$i<count($csv_data);$i++){
	                    $data=explode(',',$csv_data[$i]);
	                    DB::table('audio_broadcast_numbers')->insert(['user_id'=>session()->get('user_id'),'name'=>$data[0]. ",". $data[1],'number'=>$data[2],'type'=>$req->campaignName]);

	                return redirect()->route('audiosubscriber',['message'=>'All Subscribers for Audio Campaign Added Successfully.']);
	           		}
	            }else{
	         
	              return redirect()->route('audiosubscriber',['message'=>'CSV file or audio campaign group not select!']);
	            
	            }
	            break;
	          case '3':
	            if( isset($req->contactfirstName) and isset($req->contactlastName) and isset($req->contactNumber) and $req->campaignName != "" ){

	              for($i=0;$i<count($req->contactfirstName);$i++){
	                 DB::table('audio_broadcast_numbers')->insert(['user_id'=>session()->get('user_id'),'name'=>$req->contactfirstName[$i]. ",".$req->contactlastName[$i],'number'=>$req->contactNumber[$i],'type'=>$req->campaignName]);
	              }
	              return redirect()->route('audiosubscriber',['message'=>'Subscribers Added Successfully.']);
	            }else{
	               return redirect()->route('audiosubscriber',['message'=>'Campaign Group not Selected!']);
	            }
	          break;
	          case '4':
	            if( isset($_POST['contactfirstName']) and isset($_POST['contactlastName']) and isset($_POST['contactEmail']) ){

	              $statement1 = $pdo->prepare("INSERT INTO email_contact(user_id, contact_name, contact_address)
	                  VALUES(:u_id, :cont_name, :cont_num)");

	              $statement1->execute(array(
	                  "u_id" => $_SESSION["user_id"],
	                  "cont_name" => $_POST['contactfirstName'] . "," . $_POST['contactlastName'],
	                  "cont_num" => $_POST['contactEmail']
	              ));
	              header("Location: /users/audiosubscriber.php?message=Contact Added Successfully.");

	            }elseif( isset($_POST['contactfirstName']) and isset($_POST['contactEmail']) ){
	              $statement1 = $pdo->prepare("INSERT INTO email_contact(user_id, contact_name, contact_address)
	                  VALUES(:u_id, :cont_name, :cont_num)");

	              $statement1->execute(array(
	                  "u_id" => $_SESSION["user_id"],
	                  "cont_name" => $_POST['contactfirstName'],
	                  "cont_num" => $_POST['contactEmail']
	              ));
	              header("Location: /users/audiosubscriber.php?message=Contact Added Successfully.");
	            }elseif( isset($_POST['contactlastName']) and isset($_POST['contactEmail']) ){
	              $statement1 = $pdo->prepare("INSERT INTO email_contact(user_id, contact_name, contact_address)
	                  VALUES(:u_id, :cont_name, :cont_num)");

	              $statement1->execute(array(
	                  "u_id" => $_SESSION["user_id"],
	                  "cont_name" => $_POST['contactlastName'],
	                  "cont_num" => $_POST['contactEmail']
	              ));
	              header("Location: /users/audiosubscriber.php?message=Contact Added Successfully.");
	            }

	            else{
	              header("Location: /users/audiosubscriber.php?message=Form Not Exist!");
	            }
	            break;
	          default:
	          return redirect()->route('audiosubscriber',['message'=>'Form Method not Valid!']);
	         
	            break;
	        }
	      }else{
	        
	        return redirect()->route('audiosubscriber',['message'=>'Form Method not Valid!']);
	      }

	  }catch(exception $e){
	     
	     return redirect()->route('audiosubscriber',['message'=>$e->getMessage()]);
	    
	  }
  //function ends here
 }
public function audiosubscriberDelete(Request $req){
 if (\App\Portal\Demo::on()) return redirect()->route('emailCampaign', ['message' => \App\Portal\Demo::READONLY]); // sample-data login: never saves or sends
  
    DB::table('audio_broadcast_numbers')->where('id',session()->get('user_id'))->update(['deleted'=>1]);
    return redirect()->route('audiosubscriber',['message'=>'Number deleted Successfully!']);

    //header("Location: /users/?message=Number deleted Successfully!");
}
public function calls_history(Request $req)
{
  
  $activeCallsResults='';
    
  if(isset($req->ivr)){

    $active_calls=file_get_contents("https://globalvoicedirect.com/public/audiocallhistory.php?ivr=".$req->ivr."&from=".$req->from."&to=".$req->to);
    $active_call=json_decode($active_calls);
    $activeCallsResults=$active_call;
      	 
	}
    return view('users.call_history',compact('activeCallsResults'));
        //fun
}
public function later_calls_campaign(Request $req){

    $numberDet = DB::select('SELECT a.*, g.group_name from audio_broadcast_numbers a LEFT JOIN audio_campaign_group g ON a.type=g.id where a.user_id = ? and a.deleted = 0 order by created_at desc', [session()->get('user_id')]);
 
    $numberDetCount =count($numberDet);
    $numberDetdropdown =  $numberDet ;
    $numberDetdropdownCount =count($numberDetdropdown);

    $audioGroupDetail = DB::select('SELECT * from audio_campaign_group where user_id = ?', [session()->get('user_id')]);
    $audioGroupDetailCount =count($audioGroupDetail);
    $audioGroupDetail2 =$audioGroupDetail;
    $campaignDet = DB::select('SELECT * from call_schedule_list
                        WHERE user_id = ? and active = 0 and deleted = 0', [session()->get('user_id')]);
    $campaignDetCount =count($campaignDet);

    return view('users.later_call_campian',compact('campaignDetCount','campaignDet','audioGroupDetail2','audioGroupDetailCount','audioGroupDetail','numberDetdropdownCount','numberDetdropdown','numberDetCount','numberDet'));
}

public function submitcallschedule(Request $req){
    //dd($req);
    $ivr = $req->IVR_Number;
    $datetime = $req->datetime;
    $timestamp = strtotime($datetime);
    $datetime = date("Y-m-d H:i:s", $timestamp);

    if(isset($req->List_of_Numbers) and $req->List_of_Numbers != ""){
      $number_array = explode("\n", $req->List_of_Numbers);
 
    // dd('reach');
     $last_id=DB::table('call_schedule_list')->insertGetId(['user_id'=>session()->get('user_id'),'type'=>2,'ivr'=>$ivr,'scheduale_time'=>$datetime]);
      
     //dd($number_array);
     // $last_id = $pdo->lastInsertId();

        foreach ($number_array as $value) {
            DB::table('call_schedule_contacts')->insert(['cs_id'=>$last_id,'number'=>$value]);
      
        }
    }else{
        $campaign = $req->List_of_Campaigns;
       
        $campaignDet = DB::select('SELECT * from audio_broadcast_numbers
            WHERE type in ('.implode(',', array_map('intval', explode(',', trim($campaign, '() ')))).')');
     
        $campaignDetCount =count($campaignDet);
        if($campaignDetCount > 0){

            DB::table('call_schedule_list')->insert(['user_id'=>session()->get('user_id'),'type'=>1,'ivr'=>$ivr,'campaign_id'=>$campaign,'scheduale_time'=>$datetime]);
        
        }else{
            return redirect()->route('later_calls_campaign',['message'=>'Your Campaign Group is empty! Please add the contact in this group.']);
     
        }
    }
    // dd('email');
    $userName = "";
   // DB::table('wpgc_users')->where('ID',session()->get('user_id'))->first();
    $userDet = DB::table('wpgc_users')->where('ID',session()->get('user_id'))->first();

    if($userDet !=''){
        $userDetail = $userDet;
        $userName = $userDetail->display_name;
    }
    //dd($userName);
     
    $htmlStr = "";
    $htmlStr .= "Hello ".$userName."<br /><br /><br />";


    $htmlStr .= "Your Audio Campaign schedualed at ".$req->datetime." for IVR: ".$req->IVR_Number." successfully<br />";
 
    $htmlStr .= "<br/>Kind regards,<br />";
    $htmlStr .= "<a href='https://globalvoicedirect.com/' target='_blank'>Globalvoicedirect Team</a><br />";


    $name = "Audio Campaign";
    $email_sender = "no-reply@globalvoicedirect.com";
    $subject = "Scheduale Audio Campaign";
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
    	return redirect()->route('later_calls_campaign',['message'=>"Your Audio Campaign schedualed at ".$req->datetime." for IVR: ".$req->IVR_Number." successfully."]);
    }
    else{
        return redirect()->route('later_calls_campaign',['message'=>"Your Audio Campaign schedualed at ".$req->datetime." for IVR: ".$req->IVR_Number." successfully."]);
    }
//end function 
}

public function act_getDids(Request $req)
{
    $response=array();
    $response['success']= 0;
    $response['message']='';
    $response['data']=[];
    
    $npa=$req->state;
    $client = new \GuzzleHttp\Client();
    $headers = [
    'Authorization' => 'Basic ' . base64_encode(config('bulkVs.username') . ':' . config('bulkVs.password'))
    ];
    $request = $client->request('GET', 'https://portal.bulkvs.com/api/v1.0/orderTn?Npa='.$npa.'&Lca=true&Limit=100', ['headers' => $headers]);
    
    $numbers = json_decode($request->getBody());
    foreach ($numbers as $number) {
        array_push($response['data'], $number->TN);
    }
    $response['success']= 1;
    die(json_encode($response));
  
}
public function act_getDids_vitelity(Request $req)
{
    $response=array();
    $response['success']= 0;
    $response['message']='';
    $response['data']=[];
    
    $customer_login=config('services.vitelity.login'); 
    $customer_password=config('services.vitelity.pass');
    
    $state=$_POST['state'];   

    if($customer_login and $customer_password and $state){
      
      $result = file_get_contents("https://api.vitelity.net/fax.php?login=".$customer_login."&pass=".$customer_password."&cmd=faxlistdids&xml=yes&state=".$state);

      $xml = simplexml_load_string($result);
      if( $xml->status == 'ok'){
        $response['success']=1;
        foreach ($xml ->numbers as $number) {
          $response['data']=$number;
        }
      }else{
        $response['message']=$xml->error;
      }
      die(json_encode($response));
    }
  
}

public function create_account($id, $did, $i_customer, $i_product, $email, $h323_password, $login_r, $password){
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
        $date = date('Y-m-d'); //activation_date for new account
        
        #build request
        $AddAccRequest = array( 
        	'account_info' => array( 
        		'id' => $id,
        		'email' =>$email,
        		'billing_model' => '1',
        		'activation_date' => $date,
        		'i_product' => $i_product,
        		'h323_password' => $h323_password,
        		'i_customer' => $i_customer,
        		'number' => $did, 
        		'faxnum' => $did
        	), 
        ); 
        
        $acc_service = new SoapClient("https://" . $host . "/wsdl/Account{$service}Service.wsdl"); //switch API client to the Account service
        $acc_service->__setSoapHeaders($auth_info); 
        
        //execute request and print response
        try { 
        	  $add_acc = $acc_service->add_account($AddAccRequest);
        	  return $add_acc->i_account;
        } 
        
        //handle faults
        catch (SoapFault $e){ 
        	Log::error("Error: ".$e->getMessage()); 
        	return 0;
        } 
    }
public function act_newfaxacc(Request $req){
    $did=(string)$req->did;
    $name=(string)$req->accountName;
    $new_email=(string)$req->accountEmail;
    $userDet = DB::table('user_phone_numbers')->where('user_id',session()->get('user_id'))->first(); 
    
    $userDetail = DB::table('wpgc_users')->where('ID',session()->get('user_id'))->first(); 
    
    $id = 'fax' . time(); //generate new account ID
    $i_password = substr(str_shuffle(md5(time())),0,8);
     
    $i_login = substr(str_shuffle(md5(time())),0,5);
    $h323_password = substr(str_shuffle(md5(time())),0,10); //generate random password
    $product_id = '40281';
    $customer_id = 0;
    if($did != -1){
        if($userDet and $userDet->i_customer != null){
            $customer_id = $userDet->i_customer;
        }else {
            $user_id = session()->get('user_id');
            $customer_id = $this->create_customer($name, $new_email);
            if($customer_id != 0 ){
                if($userDet){
                    $userDet->i_customer = $customer_id;
                    $userDet->save();
                }else{
                    DB::table('user_phone_numbers')->insert(['user_id'=>$user_id, 'i_customer'=> $customer_id, 'i_name' => $name, 'i_email' => $new_email]);
                }
            }else{
                return redirect()->route('fax_mod',['message'=>'Somthing went wrong to create an account. Contact to support team.']);
            }
        }
        
        $Lidb = substr(str_shuffle(md5(time())),0,5);
        $pin = substr(str_shuffle(md5(time())),0,5);
        
        $response = $this->order_did_bulkvs($did, $Lidb, $pin);
       
        if(!isset($response['Status'])){
            return redirect()->route('fax_mod',['message'=>$response['Description']]);
        }elseif(isset($response['Status']) and $response['Status'] != "Active"){
            return redirect()->route('fax_mod',['message'=>$response['Description']]);
        }
        $Lidb = $response['Lidb'];
        $pin = $response['Portout Pin'];
        $did = $response['TN'];
        
        $account_id = $this->create_account($id, $did, $customer_id, $product_id, $new_email, $h323_password, $i_login, $i_password);
        if($account_id != 0){
            $vfaxaccount = DB::table('vfaxsignup')->where('user_id', session()->get('user_id'))->first();
            if($vfaxaccount){
                $vfaxaccount->i_account = $account_id;
                $vfaxaccount->did = $did;
                $vfaxaccount->Lidb = $Lidb;
                $vfaxaccount->pin = $pin;
                $vfaxaccount->accemails = $new_email;
                $vfaxaccount->save();
            }else{
            
                DB::table('vfaxsignup')->insert(['user_id'=>session()->get('user_id'),'acclogin'=>$i_login,'accpass'=>$i_password,'accname'=>$name,"did" => $did,'i_account'=>$account_id,'accemails'=>$new_email, 'h323_password' => $h323_password, "Lidb" => $Lidb, "pin" => $pin]);
            }
            $htmlStr = "";
            $htmlStr .= "Dear " . $userDetail->display_name . ",<br /><br />";
    
            $htmlStr .= "A new Fax account ".$new_email." has been assigned to your business.";
             
            $htmlStr .= "<br /><br />Thank you for choosing Global Voice Direct  for your virtual phone solution! Below you will find important information regarding your new account, plus some helpful tips to get your new account up and running.";
          
              $htmlStr .= "<br /><br />We'd love to know more about you and your business! Help us improve our product to better service you.";
    
              $htmlStr .= "<br /><br />Please use the button below to schedule some time with us.";
    
              $htmlStr .= "<br/><br />Kind regards,<br />";
    
                $htmlStr .= "GLOBAL TECH DIRECT
                SOUTH FLORIDA REGIONAL
                933 SOUTH MILITARY TRAIL WAREHOUSE E-7
                WEST PALM BEACH, FLORIDA 33415
                1877 331 0492 EXT 5
                http://www.globaltechdirect.com/";
    
    
    
                $htmlStr .="<br /><br />Notice of Confidentiality:<br />";
    
                $htmlStr .="<br />The information contained in this communication is intended solely for the use of the individual or entity to whom it is addressed and others authorized to receive it. It may contain confidential or legally privileged information. If you are not the intended recipient you are hereby notified that any disclosure, copying, distribution or taking any action in reliance on the contents of this information is strictly prohibited and may be unlawful. If you have received this communication in error, please notify us immediately by responding to this email and then delete it from your system.";
    
    
                  $name = "Global Voice Direct";
                  $email_sender = "no-reply@globalvoicedirect.com";
                  $subject = $new_email." fax account has been assigned to you at Global Voice Direct";
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
         
                  if($__mailOk)
                  {
                     
                    // tell the user a verification email were sent
                    return redirect()->route('fax_mod',['message'=>'You have created successfully Account.']);    
                                  
                  }else{
                    return redirect()->route('fax_mod',['message'=>'You have created successfully Account but have issue in email sending.']);
                  }
        }else {
            return redirect()->route('fax_mod',['message'=>'Something went wrong. Please try again!']);
        }
    }else{
        return redirect()->route('fax_mod',['message'=>'Please Select did number.']);
    }
}
public function order_did_bulkvs($did, $Lidb, $pin){
        
    $client = new \GuzzleHttp\Client();
    $headers = [
    'Authorization' => 'Basic ' . base64_encode(config('bulkVs.username') . ':' . config('bulkVs.password'))
    ];
     
    $postData = [
        'TN' => $did,
        'Lidb' => $Lidb,
        'Portout Pin' => $pin,
        'Trunk Group' => 'Primary',
        'Sms' => true,
        'Mms' => false,
        'Webhook' => 'Default',
    ];
    try{
        // Making the POST request
        $response = $client->request('POST', 'https://portal.bulkvs.com/api/v1.0/orderTn', [
            'headers' => $headers,
            'json' => $postData,  // Sending JSON payload
        ]);
        $responseBody = $response->getBody();
        $responseArray = json_decode($responseBody, true);
        return $responseArray;
    }catch(ClientException $e){
        $response = $e->getResponse();
        $responseBody = $response->getBody();
        return json_decode($responseBody, true);
    }
}
    
//PortaBilling Soap Api Telinta
public function create_customer($customer_name, $customer_email ){
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
    
    $name = $customer_name; //new customer name
    $iso_4217 = 'USD'; //currency which will be used by the customer and its accounts
    
    #build request
    $Request = array( 
    	'customer_info' => array( 
    		'name' => $name,
    		'email' => $customer_email,
    		'iso_4217' => $iso_4217,
    		'i_customer_type' => '1',
    		'opening_balance' => '0',
    		'i_balance_control_type' => '1',
    	), 
    ); 
    
    
    $service = new SoapClient("https://" . $host . "/wsdl/Customer{$service}Service.wsdl"); //switch API client to the Customer service
    $service->__setSoapHeaders($auth_info); 
    
    //execute request and print response
    try { 
    	  $add = $service->add_customer($Request);
    	  return $add->i_customer;
    } 
    
    // handle faults
    catch (SoapFault $e){ 
    	Log.error("Error: ".$e->getMessage()); 
    	return 0;
    } 
}
//old vetilty code
public function act_newfaxacc1(Request $req)
{
    $response=array();
    $response['success']= 0;
    $response['message']='';
    $response['data']=[];

    $customer_login=config('services.vitelity.login'); 
    $customer_password=config('services.vitelity.pass');

    $did=(string)$req->did;
    $name=(string)$req->accountName;
    $name = (strlen($name) > 25) ? substr($name,0,24) : $name;
    $new_email=(string)$req->accountEmail;

                      
    $userDet = DB::table('wpgc_users')->where('ID',session()->get('user_id'))->first(); 
 
    $password  = substr(md5(uniqid(mt_rand(), true)) , 0, 8);
    
    if($userDet != ''){
      $userDetail = $userDet;
       
    }else{
       
    }
    
    if($did != -1){
     
      $login = $userDetail->user_login;
      $login = str_replace(" ","",$login);
      $login = (strlen($login) > 10) ? substr($login,0,9) : $login;
      
      $result1= file_get_contents("https://api.vitelity.net/fax.php?login=".$customer_login."&pass=".$customer_password."&cmd=faxgetdid&xml=yes&did=".(string)$did);
      
      $xml1 = simplexml_load_string($result1);
     
      if( $xml1->status == 'ok' ){
         
        $url = 'https://api.vitelity.net/fax.php';
        $data = array('login' => $customer_login, 'pass' => $customer_password, 'cmd' => "newfaxacc", 'xml' => "yes", 'acclogin' => (string)$login, 'accpass' => (string)$password, 'accname' => (string)$name, 'did' => (string)$did, 'accemails' => (string)$new_email);

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
         
                  
        /*end here*/
        $xml = simplexml_load_string($result);
         
        if($xml->status == 'ok' ){

           
          $response['success']=1;
          DB::table('vfaxsignup')->insert(['user_id'=>session()->get('user_id'),'acclogin'=>$login,'accpass'=>$password,'accname'=>$name,'did'=>$did,'accemails'=>$new_email]); 
         
            $htmlStr = "";
            $htmlStr .= "Dear " . $userDetail->display_name . ",<br /><br />";

            $htmlStr .= "A new Fax number ".$did." has been assigned to your business.";
             
            $htmlStr .= "<br /><br />Thank you for choosing Global Voice Direct  for your virtual phone solution! Below you will find important information regarding your new account, plus some helpful tips to get your new account up and running.";
          
              $htmlStr .= "<br /><br />We'd love to know more about you and your business! Help us improve our product to better service you.";

              $htmlStr .= "<br /><br />Please don't forget to keep your account up to date with us. please complete the complete the credit application if you haven't done so by visiting https://globalvoicedirect.com/users/credit_app.php";

              $htmlStr .= "<br /><br />Please use the button below to schedule some time with us.";

              $htmlStr .= "<br />https://my.timetrade.com/book/HHQKP%20";

              $htmlStr .= "<br/><br />Kind regards,<br />";

                  $htmlStr .= "GLOBAL TECH DIRECT
                SOUTH FLORIDA REGIONAL
                933 SOUTH MILITARY TRAIL WAREHOUSE E-7
                WEST PALM BEACH, FLORIDA 33415
                1877 331 0492 EXT 5
                http://www.globaltechdirect.com/";



                $htmlStr .="<br /><br />Notice of Confidentiality:<br />";

                $htmlStr .="<br />The information contained in this communication is intended solely for the use of the individual or entity to whom it is addressed and others authorized to receive it. It may contain confidential or legally privileged information. If you are not the intended recipient you are hereby notified that any disclosure, copying, distribution or taking any action in reliance on the contents of this information is strictly prohibited and may be unlawful. If you have received this communication in error, please notify us immediately by responding to this email and then delete it from your system.";
   
   
                  $name = "Global Voice Direct";
                  $email_sender = "no-reply@globalvoicedirect.com";
                  $subject = $did." fax number has been assigned to you at Global Voice Direct";
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
         
                  if($__mailOk)
                  {
                     
                    // tell the user a verification email were sent
                    return redirect()->route('fax_mod',['message'=>'You have created successfully Account.']);    
                                  
                  }else{
                    return redirect()->route('fax_mod',['message'=>'You have created successfully Account but have issue in email sending.']);
                  }

        }else{
           
             return redirect()->route('fax_mod',['message'=>''.$xml->error]);
        }
      }else{
         
        return redirect()->route('fax_mod',['message'=>''.$xml1->error]);
      }
      
    }else{
       return redirect()->route('fax_mod',['message'=>'Please select the did number.']);
    
    }
   
  //end function here
  }

  /**
  /* Check for old and new users
  /* @param Integer $userId
  */
  public static function getCreditAndAdditionalCreditInfo($userId){
    $credit_info = DB::table('creditapp_info')->where('user_id', $userId)->first();
    $additional_credit_info =  $userDetail=DB::table('additional_creditapp_info')->where('user_id',$userId)->first();
    if(!empty($credit_info) && $additional_credit_info==null){
      return true;
    }else{
      return false;
    }
  }


    /** Legacy mail() replacement: sends through Laravel Mail (Resend) with the same subject, body and recipients. */
    private static function gvdMail($to, $subject, $body, $headers = '')
    {
        $fromName = config('mail.from.name');
        $replyTo = null;
        if (preg_match('/^From:\s*(.*?)\s*<([^>]+)>/mi', (string) $headers, $m)) {
            $fromName = trim($m[1]) !== '' ? trim($m[1]) : $fromName;
            $replyTo = trim($m[2]);
        }
        $isHtml = !preg_match('/Content-type:\s*text\/plain/i', (string) $headers);
        try {
            \Mail::send([], [], function ($message) use ($to, $subject, $body, $fromName, $replyTo, $isHtml) {
                $message->to(array_map('trim', explode(',', $to)))
                    ->subject($subject)
                    ->from(config('mail.from.address'), $fromName);
                if ($replyTo && strcasecmp($replyTo, 'no-reply@globalvoicedirect.com') !== 0) {
                    $message->replyTo($replyTo);
                }
                $isHtml ? $message->html($body) : $message->text($body);
            });
            return true;
        } catch (\Throwable $e) {
            \Log::error('Mail send failed in ' . __METHOD__ . ': ' . $e->getMessage());
            return false;
        }
    }
}
