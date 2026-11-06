<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\User;
use Auth;
use Hash;
use Date;
use App\AffiliateUser;
use App\AffiliateReferral;
use App\Coupon;
use App\AdditionalCreditAppInfo;
use SoapClient;
use SoapFault;
use SoapHeader;
use SoapVar;
use Log;
use GuzzleHttp\Exception\ClientException;

class AdminController extends Controller
{
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return View
     */


    public function index(){

        return view('admin.login');
    }

    public function login(Request $req){

        if($req['user'] != null){
            $username = $req['user'];
            $password = $req['pass'];
            $data = DB::table('admin')->where('username', $username)->first();
            
            if($data != null){

                if(Hash::check($password,$data->password)){
                    session(['admin_id' => $data->id , 'admin_name' => $data->username, 'admin_email' => $data->email]);
                    return redirect('admin/categories');

                }
                else{
                    session()->flash('message','You provide wrong Credentials.');
                    return redirect('adminlogin');
                }

            }
            else{
                session()->flash('message','You provide wrong Credentials.');
                return redirect('adminlogin');
            }
                
        }

    }
    public function logout(){
        session()->forget(['admin_id','admin_name','admin_email']);
        return redirect('adminlogin');
    }

    public function requestforresetpassword(Request $req){

        if(\DB::table('admin')->where('email',$req->email)->exists()){

           $activationKey = sha1(mt_rand(10000,99999).time().$req->email);
           \DB::table('admin')->where('email',$req->email)->update(['varification_code'=>$activationKey]);
           $userDetail=\DB::table('admin')->where('email',$req->email)->first();
           /*Email Body*/
           $verificationLink = "https://app.globalvoicedirect.com/setadminnewpassword?code=" . $activationKey. "&em=" . $userDetail->email;
     
            $htmlStr = '<!doctype html>
            <html>
              <head>
                <meta name="viewport" content="width=device-width">
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <title>Simple Transactional Email</title>
                <style>
                /* -------------------------------------
                    INLINED WITH htmlemail.io/inline
                ------------------------------------- */
                /* -------------------------------------
                    RESPONSIVE AND MOBILE FRIENDLY STYLES
                ------------------------------------- */
                @media only screen and (max-width: 620px) {
                  table[class=body] h1 {
                    font-size: 28px !important;
                    margin-bottom: 10px !important;
                  }
                  table[class=body] p,
                        table[class=body] ul,
                        table[class=body] ol,
                        table[class=body] td,
                        table[class=body] span,
                        table[class=body] a {
                    font-size: 16px !important;
                  }
                  table[class=body] .wrapper,
                        table[class=body] .article {
                    padding: 10px !important;
                  }
                  table[class=body] .content {
                    padding: 0 !important;
                  }
                  table[class=body] .container {
                    padding: 0 !important;
                    width: 100% !important;
                  }
                  table[class=body] .main {
                    border-left-width: 0 !important;
                    border-radius: 0 !important;
                    border-right-width: 0 !important;
                  }
                  table[class=body] .btn table {
                    width: 100% !important;
                  }
                  table[class=body] .btn a {
                    width: 100% !important;
                  }
                  table[class=body] .img-responsive {
                    height: auto !important;
                    max-width: 100% !important;
                    width: auto !important;
                  }
                }

                /* -------------------------------------
                    PRESERVE THESE STYLES IN THE HEAD
                ------------------------------------- */
                @media all {
                  .ExternalClass {
                    width: 100%;
                  }
                  .ExternalClass,
                        .ExternalClass p,
                        .ExternalClass span,
                        .ExternalClass font,
                        .ExternalClass td,
                        .ExternalClass div {
                    line-height: 100%;
                  }
                  .apple-link a {
                    color: inherit !important;
                    font-family: inherit !important;
                    font-size: inherit !important;
                    font-weight: inherit !important;
                    line-height: inherit !important;
                    text-decoration: none !important;
                  }
                  #MessageViewBody a {
                    color: inherit;
                    text-decoration: none;
                    font-size: inherit;
                    font-family: inherit;
                    font-weight: inherit;
                    line-height: inherit;
                  }
                  .btn-primary table td:hover {
                    background-color: #34495e !important;
                  }
                  .btn-primary a:hover {
                    background-color: #34495e !important;
                    border-color: #34495e !important;
                  }
                }
                </style>
              </head>
              <body class="" style="background-color: #f6f6f6; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
                <table border="0" cellpadding="0" cellspacing="0" class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background-color: #f6f6f6;">
                  <tr>
                    <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
                    <td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; Margin: 0 auto; max-width: 580px; padding: 10px; width: 580px;">
                      <div class="content" style="box-sizing: border-box; display: block; Margin: 0 auto; max-width: 580px; padding: 10px;">

                        <!-- START CENTERED WHITE CONTAINER -->
                        <span class="preheader" style="color: transparent; display: none; height: 0; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; mso-hide: all; visibility: hidden; width: 0;">This is preheader text. Some clients will show this text as a preview.</span>
                        <table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background: #ffffff; border-radius: 3px;">

                          <!-- START MAIN CONTENT AREA -->
                          <tr>
                            <td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;">
                              <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                                <tr>
                                  <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">
                                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Hi '.$userDetail->name.',</p>
                                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Please click the button below to reset your admin password.</p>
                                    <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; box-sizing: border-box;">
                                      <tbody>
                                        <tr>
                                          <td align="left" style="font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 15px;">
                                            <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: auto;">
                                              <tbody>
                                                <tr>
                                                  <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; background-color: #3498db; border-radius: 5px; text-align: center;"> <a href="'.$verificationLink.'" target="_blank" style="display: inline-block; color: #ffffff; background-color: #3498db; border: solid 1px #3498db; border-radius: 5px; box-sizing: border-box; cursor: pointer; text-decoration: none; font-size: 14px; font-weight: bold; margin: 0; padding: 12px 25px; text-transform: capitalize; border-color: #3498db;">Call To Action</a> </td>
                                                </tr>
                                              </tbody>
                                            </table>
                                          </td>
                                        </tr>
                                      </tbody>
                                    </table>
                                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">You user name for Admin login is <strong>'.$userDetail->username.' </strong>.</p>
                                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Good luck! Hope it works.</p>
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>

                        <!-- END MAIN CONTENT AREA -->
                        </table>

                        <!-- START FOOTER -->
                        <div class="footer" style="clear: both; Margin-top: 10px; text-align: center; width: 100%;">
                          <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                            <tr>
                              <td class="content-block" style="font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; font-size: 12px; color: #999999; text-align: center;">
                                <span class="apple-link" style="color: #999999; font-size: 12px; text-align: center;">Global Voice Direct. 2211, 2nd Avenue North, Suite 6 Lake Worth, FL 33461</span>
                              </td>
                            </tr>
                             
                          </table>
                        </div>
                        <!-- END FOOTER -->

                      <!-- END CENTERED WHITE CONTAINER -->
                      </div>
                    </td>
                    <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
                  </tr>
                </table>
              </body>
            </html>';
             
                  
            $name = "";
            $email_sender = "no-reply@globalvoicedirect.com";
            $subject = "Admin Reset Password";
            $recipient_email = $userDetail->email;
     
     
            $body = $htmlStr;
            $email=$recipient_email;
        $__mailOk = true;
        try {
            \Mail::send(array(), array(), function ($message) use ($htmlStr,$email) {
              $message->to($email)
              ->subject('Admin Reset Password')
              ->from(config('mail.from.address'))
              ->html($htmlStr); 
            });
        } catch (\Throwable $__e) {
            $__mailOk = false;
            \Log::error('Mail send failed in ' . __METHOD__ . ': ' . $__e->getMessage());
        }
            if($__mailOk){
                session()->flash('message','Please Visit your Email for Further Process.');
                return redirect('adminforgotpasswod');
            }else{
                session()->flash('message','Something Went Wrong. Please try again!');
                return redirect('adminforgotpasswod', ['message' =>'Something Went Wrong']);
            }
                       /*end here*/
            
        }else{
            session()->flash('message','This Email is not Registered!');
            return redirect('adminforgotpasswod');
        }
            //dd($req);
    } 

    public function adminsetnewpassword(Request $req){
  
       if(isset($req->code) and isset($req->em) ){

            if(\DB::table('admin')->where('email',$req->em)->where('varification_code',$req->code)->exists()){
               session()->flash('message','Please Reset Your Password.');
               return view('setnewadminpassword');
        
            }else{
         
                session()->flash('message','Please use the link which provided you in your email address.');
                return view('setnewadminpassword');
          
            }
        }else{
            session()->flash('message','Please use the link which provided you in your email address.');
            return view('setnewadminpassword');
        }
    }
    public function actionresetnewpasswordadmin(Request $req){

        if(isset($req->password) and isset($req->email) ){

            if($req->password==$req->repassword){

                $status=\DB::table('admin')->where('email',$req->email)->where('varification_code',$req->token)->update(['password'=>bcrypt($req->password)]);

                if($status==true){
                    session()->flash('message','Your Password is updated kindly try to login.');
                    return redirect('adminlogin');
               
                }else{
                    session()->flash('message','Something went wrong. Please use the link which is provided you in your email address. Thanks!');
                    return view('setnewadminpassword');
                }
            }else{
                session()->flash('message','Password Does not Match. Please use the link which is provided you in your email address. Thanks!');
                return view('setnewadminpassword');
            }
        }else{
            session()->flash('message','Varification Code does is invalid. Please use the link which is provided you in your email address. Thanks!');
            return view('setnewadminpassword');
        }

    }


    // Categories Starts
    public function categories()
    {
        
        $cat = DB::table('productcategories')->get();
        return view('admin.categories')->with('categories', $cat);
    }

    public function updateCategories(Request $req){

        DB::table('productcategories')->where('CategoryID', $req->input('categorySel'))->update(['CategoryName' => $req->input('updatename')]);
        return redirect()->back();

    }
    public function addCategory(Request $req){
        DB::table('productcategories')->insert(['CategoryName' => $req['categoryname']]);
        return redirect()->back();
    }


    // Categories Ends



    // Addons Strats

    public function addonProducts(){
        
        $productData = DB::table('productad_on')->join('products', 'productad_on.product_id','=','products.ProductID')->select('productad_on.*','products.*')->get();
        
        return view('admin.addon')->with('productData',$productData);
    }

    public function removeAddon(Request $req){

        $productData = DB::table('productad_on')->where('id','=',$req->pid)->delete();
        return redirect('admin/addon');
    }

    public function getUpdateAddon(Request $req){
        $productData = DB::table('productad_on')->where('id','=',$req->id)->first();
        $jsonData = json_encode($productData);
        return response()->json($jsonData);
    }

    public function updateAddon(Request $req){

        if(isset($req->addProductAddons))
        {
            $productId = $req->productid;
            $addonpro_id = $req->proid;
            $addonsTitle = $req->title;
            $addonsDescription = $req->description;
            $addonsDiscount = $req->descountAddons;
            $startDate = $req->startDate;
            $endDate = $req->endDate;
            $discountAddon = $req->DiscountAddonsProduct;
            

            if( $discountAddon== 0)
            {
                session()->flash('message','Sorry! Please Select Discount Methed');
                return redirect('admin/addon');
                
            }
            if($discountAddon == 1)
            {
                $fixedPrice = $addonsDiscount;
                $discountAddon = null;
            }
            if($discountAddon == 2)
            {
                $discountAddon = $addonsDiscount;
                $fixedPrice = null;
            }

            if($productId = null)
            {
                session()->flash('message','Product Not Found!.');
                return redirect('admin/addon');
                
            }

            //$imgpath = "/home/globodmv/public_html/productImages/imglogo.png";
            $imgpath = "/home/globodmv/public_html/productImages/imglogo.png";
                
            
            

            DB::table('productad_on')->where('id','=',$req->productid)->update(['addonTitle' => $addonsTitle, 'addonDescription' => $addonsDescription, 'addonDiscount'=>$discountAddon, 'addonFixedPrice'=> $fixedPrice, 'imgPath' => $imgpath, 'start_date' => $startDate, 'end_date' => $endDate, 'product_id' => $addonpro_id]);    
            session()->flash('message','Addons Updated to Product Successfuly!');
            return redirect('admin/addon');

            
        }  
        else
        {
            session()->flash('message','Addons Not Added To Product Something Went Wrong!');
            return redirect('admin/addon');
            
        }





    }

    // Addons Ends

    // Products Starts

    public function products(){

        $cat = DB::table('productcategories')->get();
        $productDetail = DB::table('products')->where('ProductLive','=','0')->get();
        $productRDetail = DB::table('products')->where('ProductLive','=','1')->get();

        return view('admin.products')->with('categories',$cat)->with('productDetail',$productDetail)->with('productRDetail',$productRDetail);
    }
    public function addProduct(Request $request){
        $this->validate($request,[
            'image' => 'required|image|mimes:jpeg,jpg,gif,png,svg|max:2048',
            'price' => 'required|integer',
            'quantity' => 'required|integer'
        ]);

        
        $file = $request->file('image');
        $ImageName =time() . '.' . $file->getClientOriginalExtension();
        
        $DirPath = public_path('/productsImages');
        $file->move($DirPath, $ImageName);
        
        DB::table('products')->insert(['ProductName'=> $request->productname, 'ProductCartDesc'=>$request->description, 'ProductShortDesc'=> $request->shortDesc, 'ProductLongDesc'=>$request->longDesc, 'ProductImage'=>$ImageName, 'ProductCategoryID'=>$request->categorySel, 'ProductStock'=>$request->quantity,'ProductPrice'=>$request->price,'ProductWeight'=>$request->weight]);
        session()->flash('message','Product Added Successfully.');
        return redirect()->back()->with('status', 'Product Added successfully');
    }

    public function getUpdateProduct(Request $req){
        $pro = DB::table('products')->where('ProductID','=',$req->id)->first();
        $jsonData = json_encode($pro);
        return response()->json($jsonData);
    }

    public function actUpdataProduct(Request $req){
        

        

        if(!empty($req->file('image'))){


            $this->validate($req,[
                'image' => 'image|mimes:jpeg,jpg,gif,png,svg|max:2048'
            ]);

            
            $file = $req->file('image');
            $ImageName =time() . '.' . $file->getClientOriginalExtension();
            
            $DirPath = public_path('/productsImages');
            $file->move($DirPath, $ImageName);

            $productname = $req->uproductname;
            $cartDescription = $req->udescription;
            $shortDesc = $req->ushortDesc;
            $longDesc = $req->ulongDesc;
            $price = $req->uprice;
            $weight = $req->uweight; 
            $categorySel = $req->ucategorySel;
            $quantity = $req->uquantity;
            if($categorySel == -1){
               session()->flash('message','Sorry, Category Must Select.');
               return redirect('admin/products');
               
            }else{
                DB::table('products')->where('ProductID','=',$req->productId)->update([
                   "ProductName" => $productname,
                   "ProductCartDesc" => $cartDescription,
                   "ProductShortDesc" => $shortDesc,
                   "ProductLongDesc" => $longDesc,
                   "ProductImage" => $ImageName,
                   "ProductCategoryID" => $categorySel,
                   "ProductStock" => $quantity,
                   "ProductPrice" => $price,
                   "ProductWeight" => $weight
               ]);
               session()->flash('message','Product Updated Successfully.');
               return redirect('admin/products');
               
            }
        }else{

            $productname = $req->uproductname;
            $cartDescription = $req->udescription;
            $shortDesc = $req->ushortDesc;
            $longDesc = $req->ulongDesc;
            $price = $req->uprice;
            $weight = $req->uweight; 
            $categorySel = $req->ucategorySel;
            $quantity = $req->uquantity; 
            $productname = $req->uproductname;
            $cartDescription = $req->udescription;
            $shortDesc = $req->ushortDesc;
            $longDesc = $req->ulongDesc;
            $price = $req->uprice;
            $weight = $req->uweight; 
            $categorySel = $req->ucategorySel;
            $quantity = $req->uquantity;


            DB::table('products')->where('ProductID','=',$req->productId)->update([
                "ProductName" => $productname,
                "ProductCartDesc" => $cartDescription,
                "ProductShortDesc" => $shortDesc,
                "ProductLongDesc" => $longDesc,
                "ProductCategoryID" => $categorySel,
                "ProductStock" => $quantity,
                "ProductPrice" => $price,
                "ProductWeight" => $weight
            ]);
            session()->flash('message','Product Updated Successfully.');
            return redirect('admin/products');
             
        }
    }

    public function removeProduct(Request $req){

        DB::table('products')->where('ProductID','=',$req->pid)->update(['ProductLive' => 1]);
        session()->flash('message','Product Removed Successfully.');
        return redirect()->back();
    }


    public function backToLive(Request $req){

        DB::table('products')->where('ProductID','=',$req->pid)->update(['ProductLive' => 0]);
        session()->flash('message','Product Added to Stock Successfully.');
        return redirect()->back();
    }

    public function productAddonAdd(Request $req){
        if(isset($req->addProductAddons))
        {
            $productId = $req->productid;
            $addonsTitle = $req->title;
            $addonsDescription = $req->description;
            $addonsDiscount = $req->descountAddons;
            $startDate = $req->startDate;
            $endDate = $req->endDate;
            $discountAddon = $req->DiscountAddonsProduct;
            
            

            if( $discountAddon== 0)
            {
                session()->flash('message','Sorry! Please Select Discount Methed');
                return redirect('admin/products');
            }
            if($discountAddon == 1)
            {
                $fixedPrice = $addonsDiscount;
                $discountAddon = null;
            }
            if($discountAddon == 2)
            {
                $discountAddon = $addonsDiscount;
                $fixedPrice = null;
            }

            if($productId = null)
            {
                session()->flash('message','Product Not Found!.');
                return redirect('admin/products');

            }
            else
            {
                
                $pro = DB::table('productad_on')->where('product_id','=',$req->productid)->first();
                
                if($pro != null)
                {
                    session()->flash('message','This Product Have Already Addons!');
                    return redirect('admin/products');
                }
            }

            

            DB::table('productad_on')->where('product_id','=',$req->productid)->delete();

            

            DB::table('productad_on')->insert([
                'addonTitle' => $addonsTitle,
                'addonDescription' => $addonsDescription,
                'addonDiscount' => $discountAddon,
                'addonFixedPrice' => $fixedPrice,
                'start_date' => $startDate,
                "end_date" => $endDate,
                "product_id" => $req->productid

            ]);

            
        
            
            session()->flash('message','Addons Added to Product Successfuly!');
            return redirect('admin/products');

            
        }  
        else
        {
            session()->flash('message','Addons Not Added To Product Something Went Wrong!');
            return redirect('admin/products');
        }
    }




    //Products Ends

    //Orders Starts

    public function emailforpayment($id)
    {
    
      $orders = DB::table('orders')->join('wpgc_users', 'orders.orderUserID', '=','wpgc_users.ID')->select('wpgc_users.user_email','wpgc_users.user_nicename')->where('orders.OrderID',$id)->first();

      
      if($orders){
        $htmlStr = '<!doctype html>
            <html>
              <head>
                <meta name="viewport" content="width=device-width">
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <title>Simple Transactional Email</title>
                <style>
                /* -------------------------------------
                    INLINED WITH htmlemail.io/inline
                ------------------------------------- */
                /* -------------------------------------
                    RESPONSIVE AND MOBILE FRIENDLY STYLES
                ------------------------------------- */
                @media only screen and (max-width: 620px) {
                  table[class=body] h1 {
                    font-size: 28px !important;
                    margin-bottom: 10px !important;
                  }
                  table[class=body] p,
                        table[class=body] ul,
                        table[class=body] ol,
                        table[class=body] td,
                        table[class=body] span,
                        table[class=body] a {
                    font-size: 16px !important;
                  }
                  table[class=body] .wrapper,
                        table[class=body] .article {
                    padding: 10px !important;
                  }
                  table[class=body] .content {
                    padding: 0 !important;
                  }
                  table[class=body] .container {
                    padding: 0 !important;
                    width: 100% !important;
                  }
                  table[class=body] .main {
                    border-left-width: 0 !important;
                    border-radius: 0 !important;
                    border-right-width: 0 !important;
                  }
                  table[class=body] .btn table {
                    width: 100% !important;
                  }
                  table[class=body] .btn a {
                    width: 100% !important;
                  }
                  table[class=body] .img-responsive {
                    height: auto !important;
                    max-width: 100% !important;
                    width: auto !important;
                  }
                }

                /* -------------------------------------
                    PRESERVE THESE STYLES IN THE HEAD
                ------------------------------------- */
                @media all {
                  .ExternalClass {
                    width: 100%;
                  }
                  .ExternalClass,
                        .ExternalClass p,
                        .ExternalClass span,
                        .ExternalClass font,
                        .ExternalClass td,
                        .ExternalClass div {
                    line-height: 100%;
                  }
                  .apple-link a {
                    color: inherit !important;
                    font-family: inherit !important;
                    font-size: inherit !important;
                    font-weight: inherit !important;
                    line-height: inherit !important;
                    text-decoration: none !important;
                  }
                  #MessageViewBody a {
                    color: inherit;
                    text-decoration: none;
                    font-size: inherit;
                    font-family: inherit;
                    font-weight: inherit;
                    line-height: inherit;
                  }
                  .btn-primary table td:hover {
                    background-color: #34495e !important;
                  }
                  .btn-primary a:hover {
                    background-color: #34495e !important;
                    border-color: #34495e !important;
                  }
                }
                </style>
              </head>
              <body class="" style="background-color: #f6f6f6; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
                <table border="0" cellpadding="0" cellspacing="0" class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background-color: #f6f6f6;">
                  <tr>
                    <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
                    <td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; Margin: 0 auto; max-width: 580px; padding: 10px; width: 580px;">
                      <div class="content" style="box-sizing: border-box; display: block; Margin: 0 auto; max-width: 580px; padding: 10px;">

                        <!-- START CENTERED WHITE CONTAINER -->
                        <span class="preheader" style="color: transparent; display: none; height: 0; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; mso-hide: all; visibility: hidden; width: 0;">This is preheader text. Some clients will show this text as a preview.</span>
                        <table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background: #ffffff; border-radius: 3px;">

                          <!-- START MAIN CONTENT AREA -->
                          <tr>
                            <td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;">
                              <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                                <tr>
                                  <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">
                                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Hi '.$orders->user_nicename.',</p>
                                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">We receive your order but payment not done. Please complete your payment. Following is the payment Url.</p>
                                    <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; box-sizing: border-box;">
                                      <tbody>
                                        <tr>
                                          <td align="left" style="font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 15px;">
                                            <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: auto;">
                                              <tbody>
                                                <tr>
                                                  <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; background-color: #3498db; border-radius: 5px; text-align: center;"> <a href="'.$_SERVER['SERVER_NAME'].'/user/unpaid_order/'.$id.'" target="_blank" style="display: inline-block; color: #ffffff; background-color: #3498db; border: solid 1px #3498db; border-radius: 5px; box-sizing: border-box; cursor: pointer; text-decoration: none; font-size: 14px; font-weight: bold; margin: 0; padding: 12px 25px; text-transform: capitalize; border-color: #3498db;">Call To Action</a> </td>
                                                </tr>
                                              </tbody>
                                            </table>
                                          </td>
                                        </tr>
                                      </tbody>
                                    </table>
                                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Please click above link after user login.</p>
                                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Good luck! Hope it works.</p>
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>

                        <!-- END MAIN CONTENT AREA -->
                        </table>

                        <!-- START FOOTER -->
                        <div class="footer" style="clear: both; Margin-top: 10px; text-align: center; width: 100%;">
                          <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                            <tr>
                              <td class="content-block" style="font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; font-size: 12px; color: #999999; text-align: center;">
                                <span class="apple-link" style="color: #999999; font-size: 12px; text-align: center;">Global Voice Direct. 2211, 2nd Avenue North, Suite 6 Lake Worth, FL 33461</span>
                              </td>
                            </tr>
                             
                          </table>
                        </div>
                        <!-- END FOOTER -->

                      <!-- END CENTERED WHITE CONTAINER -->
                      </div>
                    </td>
                    <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
                  </tr>
                </table>
              </body>
            </html>';
        $recipient_email=$orders->user_email;
        $subject = "Reminder for Payment";
        \Mail::send(array(), array(), function ($message) use ($htmlStr,$recipient_email,$subject) {
          $message->to($recipient_email)
          ->subject($subject)
          ->from(config('mail.from.address'),'Global Voice Direct')
          ->html($htmlStr); 
        });

        // \Mail::send('emailforpayment', $data,
        // function ($message) use ($email)
        // {
        //      $message
        //          ->from(config('mail.from.address'))
        //          ->to($email)->subject('Reminder for Payment');
                 
        // });
      }
      return redirect()->back()->with('message', 'Email sent successfully to client.');
    }


    public function orders(){
       
        $orders = DB::table('orders')->join('wpgc_users', 'orders.orderUserID', '=','wpgc_users.ID')->select('orders.*', 'wpgc_users.user_login','wpgc_users.user_nicename', 'wpgc_users.user_email', 'wpgc_users.display_name')->orderBy('orders.OrderDate', 'desc')->get();
        $trackDetail = [];
        foreach($orders as $ord){
            $temp = DB::table('shippinginfo')->where('order_id','=',$ord->OrderID)->first();
            array_push($trackDetail, $temp);
        }
       
        return view('admin.order')->with('orders',$orders)->with('trackDetail',$trackDetail);
    }
    public function generateSlip(Request $req){

        $orderDetail = DB::table('orders')->join('wpgc_users','orders.OrderUserID','=','wpgc_users.ID')->where('orders.OrderID','=',$req->id)->select('orders.*','wpgc_users.display_name')->first();

        if($orderDetail != null){

            $orderProduct = DB::table('invoices')->join('products','invoices.product_id','=','products.ProductID')->where('invoices.order_id','=', $orderDetail->OrderID)->select('products.*','invoices.*')->get();
            if($orderProduct!=null && isset($orderProduct[0]))
            {
              $userid=$orderProduct[0]->user_id;
                $shippingDetails=DB::table('user_shipping_address')->where('user_id',$userid)->first();
            }
            else{
              $shippingDetails=false;
            }
            
            return view('admin/orderslip')->with('orderDetail',$orderDetail)->with('orderProduct',$orderProduct)->with('shippingDetails',$shippingDetails);
        }
        else{
            session()->flash('message','Order Not Exist.');
            return redirect('admin/orders');

        }



    }

    public function getOrderDetails(Request $req){


        $orderDetail = DB::table('orders')->where('OrderID','=',$req->id)->first();
        
        $orderDetailInvoice = DB::table('invoices')->where('order_id','=',$orderDetail->OrderID)->get();

        $products = array();

        foreach($orderDetailInvoice as $value) {

            $orderDetailtemp = DB::table('products')->where('ProductID','=',$value->product_id)->first();

            array_push($products, $orderDetailtemp);

        }
        

        $query = array();

        array_push($query, $orderDetail);
        array_push($query, $orderDetailInvoice);
        array_push($query, $products);
        
         
        $jsonData = json_encode($query);

        return response()->json($jsonData);

    }
    public function updateOrderStatus(Request $req){



        DB::table('orders')->where('OrderID','=',$req->oid)->update([
            'OrderShipped' => $req->status
        ]);
        session()->flash('message','Order Status Updated Successfully.');

        return redirect('admin/orders');
        
    }

    public function assignTrackNumber(Request $req){

        $track_number = $req->tracknumber;
        $parcel_company = $req->parcelAg;
        $order_id = $req->orderid;

        $trackDetail = DB::table('shippinginfo')->where('order_id','=',$order_id)->first();
        
        if($trackDetail != null){
            $assigntrack = DB::table('shippinginfo')->where('order_id','=',$order_id)->update([
                'company_name' => $parcel_company,
                'tracking_number' => $track_number
            ]);
            session()->flash('message','Track Number Updated Successfully.');
            return redirect('admin/orders');
            
        }else{

            DB::table('shippinginfo')->insert([
                
                'order_id' => $order_id,
                'company_name' => $parcel_company,
                'tracking_number' => $track_number

            ]);
            session()->flash('message','Track Number Assigned Successfully.');
            return redirect('admin/orders');
        }
    }

    // Orders Ends

    // Starts Payments

    public function payments(){

        $paymentDetail = DB::table('transactions')->join('wpgc_users','transactions.user_id','=','wpgc_users.ID')->select('transactions.*','wpgc_users.user_login','wpgc_users.user_nicename','wpgc_users.user_email','wpgc_users.display_name')->get();
        return view('admin.payments')->with('paymentDetail',$paymentDetail);
    }

    // Ends Payments

    // Starts Payments gateway
    public function paymentsGateways()
    {
        // Fetch all payment gateways
        $paymentGateways = DB::table('payment_gateways')->get();

        return view('admin.payment_gateways')->with('paymentGateways', $paymentGateways);
    }
    
    public function paymentgatewayactivateOnly($id)
    {
        // Set all inactive
        DB::table('payment_gateways')->update(['is_active' => 0]);

        // Activate selected one
        DB::table('payment_gateways')->where('id', $id)->update(['is_active' => 1]);

        return response()->json(['message' => 'Gateway activated successfully']);
    }

    // end Payments gateway

    // Starts Marketing Form

    public function getMarkrtingDetail(){

        $userFormDet = DB::table('marketing_info')->orderBy('created_at','desc')->get();
        $temp = $userFormDet;
        foreach($userFormDet as $det){
            //array_push($temp, $det);
        }
        return view('admin.marketingform')->with('userFormDet',$temp);
        
    }

    public function getMarketInfo(Request $req){

        $userFormDet = DB::table('marketing_info')->where('id','=',$req->id)->first();
        $jsonData = json_encode($userFormDet);   

        return response()->json($jsonData);

    }

    // Ends Marketing Form

    //  add credit form
    public function addcreditform()
    {
         $userFormDet = DB::table('additional_creditapp_info')->join('wpgc_users','additional_creditapp_info.user_id','wpgc_users.id')->get();
        $temp = $userFormDet;
        foreach($userFormDet as $det){
            //array_push($temp, $det);
        }

        return view('admin.addcreditform')->with('userFormDet',$temp); 
    }

    public function discreditdetail($id)
    {
      $userForm =AdditionalCreditAppInfo::find($id);
      return view('admin.discreditdetail',compact('userForm'));
    }

    // Script Recording Starts

    public function scriptRecording(){

        $userFormDet = DB::table('script_info')->join('wpgc_users','script_info.user_id','=','wpgc_users.ID')->select('script_info.*','wpgc_users.user_login', 'wpgc_users.user_nicename', 'wpgc_users.user_email', 'wpgc_users.display_name')->orderBy('created_at','desc')->get();
        return view('admin.scriptRecording')->with('userDet',$userFormDet);
        
    }

    public function approvescriptrecording(Request $req){

        DB::table('script_info')->where('id','=',$req->sid)->update([
            "active" => 2 
        ]);

        $userFormDet = DB::table('script_info')->join('wpgc_users','script_info.user_id','=','wpgc_users.ID')->select('script_info.*','wpgc_users.user_login', 'wpgc_users.user_nicename', 'wpgc_users.user_email', 'wpgc_users.display_name')->where('script_info.id',$req->sid)->orderBy('created_at','desc')->first();

        $htmlStr = "";
        $htmlStr .= "Hi " . $userFormDet->display_name . ",<br /><br />";

        $htmlStr .= "Conguratulations. Your Script Recording Information approved successfully.";
             

        $htmlStr .= "<br/><br/>Kind regards,<br />";
        $htmlStr .= "<a href='https://globalvoicedirect.com/' target='_blank'>Global Voice Direct Team</a><br />";

        $name = "Global Voice Direct";
        $email_sender = "no-reply@globalvoicedirect.com";
        $subject = "Script Recording";
        $recipient_email = $userFormDet->user_email;

        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
        $headers .= "From: {$name} <{$email_sender}> \n";

        $body = $htmlStr;
      
        \Mail::send(array(), array(), function ($message) use ($htmlStr,$recipient_email,$subject) {
          $message->to($recipient_email)
          ->subject($subject)
          ->from(config('mail.from.address'), "Global Voice Direct")
          ->html($htmlStr); 
        });

        return redirect()->back()->with('message' ,'Script Approved Successesfully.');
        
    }
    public function removescriptrecording(Request $req){

        DB::table('script_info')->where('id','=',$req->sid)->update([
            "active" => 0 
        ]);

        $userFormDet = DB::table('script_info')->join('wpgc_users','script_info.user_id','=','wpgc_users.ID')->select('script_info.*','wpgc_users.user_login', 'wpgc_users.user_nicename', 'wpgc_users.user_email', 'wpgc_users.display_name')->where('script_info.id',$req->sid)->orderBy('created_at','desc')->first();

        $htmlStr = "";
        $htmlStr .= "Hi " . $userFormDet->display_name . ",<br /><br />";

        $htmlStr .= "Unfortunately. Your Script Recording Information was not approved successfully. Please update your script recording information and submit again from your control panel.";
             

        $htmlStr .= "<br/><br/>Kind regards,<br />";
        $htmlStr .= "<a href='https://globalvoicedirect.com/' target='_blank'>Global Voice Direct Team</a><br />";

        $name = "Global Voice Direct";
        $email_sender = "no-reply@globalvoicedirect.com";
        $subject = "Script Recording";
        $recipient_email = $userFormDet->user_email;

        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
        $headers .= "From: {$name} <{$email_sender}> \n";

        $body = $htmlStr;
      
        \Mail::send(array(), array(), function ($message) use ($htmlStr,$recipient_email,$subject) {
          $message->to($recipient_email)
          ->subject($subject)
          ->from(config('mail.from.address'), "Global Voice Direct")
          ->html($htmlStr); 
        });
        return redirect()->back()->with('message' ,'Script DisApproved Successesfully.');
        
    }

    public function getScriptRec(Request $req){

        $userDet = DB::table('script_info')->where('id','=',$req->id)->first();
        $jsonData = json_encode($userDet);
        return response()->json($jsonData);     

    }

    // Script Recording Ends


    // Credit Application Starts

    public function getCreditInfo(){
        $creditInfo = DB::table('creditapp_info')->orderBy('created_at','desc')->get();
        return view('admin.creditinfo')->with('creditInfo',$creditInfo);
    }

    public function getAllDetail(Request $req){
        $allDet = DB::table('creditapp_info')->where('id','=',$req->fid)->first();
        if($allDet != null){
            return view('admin.dis_all_detail')->with('allDet',$allDet);
        }
        else{
            return redirect()->back();
        }
        
    }

    // Credit Application Ends


    // Fax Management Starts
    
    public function faxMan(){

        $faxDet = DB::table('vfaxsignup')->join('wpgc_users','vfaxsignup.user_id','=','wpgc_users.ID')->select('vfaxsignup.*', 'wpgc_users.user_login', 'wpgc_users.user_nicename', 'wpgc_users.user_email', 'wpgc_users.display_name')->orderBy('created_at','desc')->get();

        $userFaxDet = DB::select('SELECT * from wpgc_users where ID NOT IN(select user_id from vfaxsignup) order by user_registered desc');
        //get fax states

         
        $stateData = [];
        
        return view('admin.faxmanagement')->with('userFaxDet',$userFaxDet)->with('stateData',$stateData)->with('faxDet',$faxDet);
    }

    public function faxMan_vitelity_code(){

        $faxDet = DB::table('vfaxsignup')->join('wpgc_users','vfaxsignup.user_id','=','wpgc_users.ID')->select('vfaxsignup.*', 'wpgc_users.user_login', 'wpgc_users.user_nicename', 'wpgc_users.user_email', 'wpgc_users.display_name')->orderBy('created_at','desc')->get();

        $userFaxDet = DB::select('SELECT * from wpgc_users where ID NOT IN(select user_id from vfaxsignup) order by user_registered desc');
        //get fax states

        $customer_login=config('services.vitelity.login'); 
        $customer_password=config('services.vitelity.pass');   
        
        $result= file_get_contents("https://api.vitelity.net/fax.php?login=".$customer_login."&pass=".$customer_password."&cmd=faxliststates&xml=yes");



        $xml = simplexml_load_string($result);
        
        
        if( $xml->status == 'ok'){
            $response['success']=1;
            foreach ($xml ->states as $states) {
                $response['data']=$states;
            }                   
        }else{
            $response['message']=$xml->error;
        }
       
        $array = json_decode(json_encode((array)$xml), TRUE);
        
        $stateData = $array["states"]["state"] ;
        
        return view('admin.faxmanagement')->with('userFaxDet',$userFaxDet)->with('stateData',$stateData)->with('faxDet',$faxDet);
    }

    public function smsManagement(){
        $customer_login=config('services.vitelity.login'); 
        $customer_password=config('services.vitelity.pass'); 
        $smsNotice = null;
        if (empty($customer_login) || empty($customer_password)) {
            $smsNotice = 'SMS provider not connected yet. Messaging will be enabled once the SMS provider account is linked.';
        } else {
            try {
                $ctx = stream_context_create(['http' => ['timeout' => 10, 'ignore_errors' => true]]);
                $result = @file_get_contents("https://api.vitelity.net/api.php?login=".urlencode($customer_login)."&pass=".urlencode($customer_password)."&cmd=smsdids&xml=yes", false, $ctx);
                if ($result === false) {
                    $smsNotice = 'SMS provider could not be reached right now. Please try again later.';
                }
            } catch (\Throwable $e) {
                \Log::warning('SMS management: provider request failed');
                $smsNotice = 'SMS provider could not be reached right now. Please try again later.';
            }
        }
        return view('admin.smsmanagement', compact('smsNotice'));
    }

    public function addhttpSMS(Request $req){

        $httpurl=$req->httpurl;
        $customer_login=config('services.vitelity.login'); 
        $customer_password=config('services.vitelity.pass'); 

        $result= file_get_contents("https://api.vitelity.net/api.php?login=".$customer_login."&pass=".$customer_password."&cmd=smsenableurl&url=".$httpurl."&xml=yes");

        dd($result);
        $xml = simplexml_load_string($result);
        $message = "";
        
        if( $xml->status == 'ok'){
            $message = $xml->status;             
        }else{
            $message = $xml->error;
        }

        return redirect()->back()->with('message' ,$message);

    }


    public function getUserDetailFax(Request $req){

        $orderDet = DB::table('wpgc_users')->where('ID','=',$req->uid)->select('user_email','display_name')->first();
        
        if($orderDet != null){
            $jsonData = json_encode($orderDet);
            return response()->json($jsonData);
            
        }
        else{
            return response()->json("");
        }
    }
    public function getDid(Request $req){
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
    public function getDid_vitelity(Request $req){
        $response=array();
        $response['success']= 0;
        $response['message']='';
        $response['data']=[];

        $customer_login=config('services.vitelity.login'); 
        $customer_password=config('services.vitelity.pass');   
        
        $state=$req->state;     

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
            $jsonData = json_encode($response);
            return response()->json($jsonData);
        }

    }
    public function updateFaxAcc(Request $req){

        $response=array();
        $response['success']= 0;
        $response['message']='';
        $response['data']=[];

        $customer_login=config('services.vitelity.login'); 
        $customer_password=config('services.vitelity.pass');   

        $did=(string)$req->did;
        // $name=(string)$_POST['accountName'];
        // $new_email=(string)$_POST['accountEmail'];

        $userDet = DB::table('vfaxsignup')->join('wpgc_users','vfaxsignup.user_id','=','wpgc_users.ID')->where('vfaxsignup.user_id','=',$req->update_id)->first();
        
         
        if($did != -1){
            $olddid = $userDet->did;

            //release previose did number
            $releasedidresult= file_get_contents("https://api.vitelity.net/api.php?login=".$customer_login."&pass=MySon@@1444&cmd=removedid&did=".(string)$userDet->did);
             
            $xmlresult = simplexml_load_string($releasedidresult);
            // $did = (int)$did;
            // var_dump($did);
            // exit;
            if( $xmlresult->status == 'ok' ){
                $result1= file_get_contents("https://api.vitelity.net/fax.php?login=".$customer_password."&pass=".$customer_password."&cmd=faxgetdid&xml=yes&did=".(string)$did);
    
                $xml1 = simplexml_load_string($result1);
                if( $xml1->status == 'ok' ){
                    $url = 'https://api.vitelity.net/fax.php';
                    $data = array('login' => $customer_login, 'pass' => $customer_password, 'cmd' => "setfaxacc", 'xml' => "yes", 'acclogin' => (string)$userDet->acclogin, 'did' => (string)$did);

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
                
                    if( $xml->status == 'ok' ){
                        $response['success']=1; 
                        DB::table('vfaxsignup')->where('user_id','=',$req->update_id)->update([

                            "did" => $did,
                            "updated_at" => date('Y-m-d h:i:s')
                        ]);
                        
                        $htmlStr = "";
                        $htmlStr .= "Dear " . $userDet->display_name . ",<br /><br />";
         
                        $htmlStr .= "A new Fax number ".$did." has been assigned to your business.";
                         
                        $htmlStr .= "<br />Thank you for choosing Global Voice Direct  for your virtual phone solution! Below you will find important information regarding your new account, plus some helpful tips to get your new account up and running.";
                        
                        $htmlStr .= "<br />We'd love to know more about you and your business! Help us improve our product to better service you.";

                        $htmlStr .= "<br />Please don't forget to keep your account up to date with us. please complete the complete the credit application if you haven't done so by visiting https://globalvoicedirect.com/users/credit_app.php";

                        $htmlStr .= "<br />Please use the button below to schedule some time with us.";

                        $htmlStr .= "<br />https://my.timetrade.com/book/HHQKP%20";

                        $htmlStr .= "<br/>Kind regards,<br />";

                        $htmlStr .= "GLOBAL TECH DIRECT
                                    SOUTH FLORIDA REGIONAL
                                    933 SOUTH MILITARY TRAIL WAREHOUSE E-7
                                    WEST PALM BEACH, FLORIDA 33415
                                    1877 331 0492 EXT 5
                                    http://www.globaltechdirect.com/";



                        $htmlStr .="<br />Notice of Confidentiality:<br />";

                        $htmlStr .="<br />The information contained in this communication is intended solely for the use of the individual or entity to whom it is addressed and others authorized to receive it. It may contain confidential or legally privileged information. If you are not the intended recipient you are hereby notified that any disclosure, copying, distribution or taking any action in reliance on the contents of this information is strictly prohibited and may be unlawful. If you have received this communication in error, please notify us immediately by responding to this email and then delete it from your system.";
         
         
                        $name = "Global Voice Direct";
                        $email_sender = "no-reply@globalvoicedirect.com";
                        $subject = $did." fax number has been assigned to you at Global Voice Direct";
                        $recipient_email = $userDet->user_email;
         
                        $headers  = "MIME-Version: 1.0\r\n";
                        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
                        $headers .= "From: {$name} <{$email_sender}> \n";
         
                        $body = $htmlStr;
         
                        // send email using the mail function, you can also use php mailer library if you want
                        if( self::gvdMail($recipient_email, $subject, $body, $headers) ){
         
                            // tell the user a verification email were sent
                            
                            session()->flash('message', 'You have updated successfully DID.');
                            return redirect('admin/fax_management');
                             

                             
                        }else{

                            session()->flash('message','You have updated successfully DID. But have issue in email sending.');
                            return redirect('admin/fax_management');
                              
                        }
                    }
                    else{
                        $response['message']=$xml->error;
                        session()->flash('message',$xml->error);
                        return redirect('admin/fax_management');
                        
                    }
                }else{
                    $response['message']=$xml1->error;
                    session()->flash('message',$xml->error);
                    return redirect('admin/fax_management');
                    
                }
            }else{
                $response['message']=$xmlresult->error;
                session()->flash('message',$xml->error);
                return redirect('admin/fax_management');
                
            }
            
        }else{
            session()->flash('message','Please select the did number.');
            return redirect('admin/fax_management');
            
        }
    }
    public function updateRegDidFaxAcc_vitelity(Request $req){

        $response=array();
        $response['success']= 0;
        $response['message']='';
        $response['data']=[];

        $customer_login=config('services.vitelity.login'); 
        $customer_password=config('services.vitelity.pass');   

        $did=(string)$req->account_number_update;
        // $name=(string)$_POST['accountName'];
        // $new_email=(string)$_POST['accountEmail'];

        $userDet = DB::table('vfaxsignup')->join('wpgc_users','vfaxsignup.user_id','=','wpgc_users.ID')->where('vfaxsignup.user_id','=', $req->new_reg_id)->first();
         
        if($did != -1){
            $olddid = $userDet->did;

            //release previose did number
            $releasedidresult= file_get_contents("https://api.vitelity.net/api.php?login=".$customer_login."&pass=".$customer_password."&xml=yes&cmd=removedid&did=".(string)$userDet->did);
            $xmlresult = simplexml_load_string($releasedidresult);

            if( $xmlresult->status == 'ok' or (isset($xmlresult->error) and $xmlresult->error == 'unavailable')){
                 
                    $url = 'https://api.vitelity.net/fax.php';
                    $data = array('login' => $customer_login, 'pass' => $customer_password, 'cmd' => "setfaxacc", 'xml' => "yes", 'acclogin' => (string)$userDet->acclogin, 'did' => (string)$did);

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
                
                    if( $xml->status == 'ok' ){
                        $response['success']=1; 
                        DB::table('vfaxsignup')->where('user_id','=',$req->new_reg_id)->update([

                            "did" => $did,
                            "updated_at" => date('Y-m-d h:i:s')
                        ]);
                        
                        $htmlStr = "";
                        $htmlStr .= "Dear " . $userDet->display_name . ",<br /><br />";
         
                        $htmlStr .= "A new Fax number ".$did." has been assigned to your business.";
                         
                        $htmlStr .= "<br />Thank you for choosing Global Voice Direct  for your virtual phone solution! Below you will find important information regarding your new account, plus some helpful tips to get your new account up and running.";
                        
                        $htmlStr .= "<br />We'd love to know more about you and your business! Help us improve our product to better service you.";

                        $htmlStr .= "<br />Please don't forget to keep your account up to date with us. please complete the complete the credit application if you haven't done so by visiting https://globalvoicedirect.com/users/credit_app.php";

                        $htmlStr .= "<br />Please use the button below to schedule some time with us.";

                        $htmlStr .= "<br />https://my.timetrade.com/book/HHQKP%20";

                        $htmlStr .= "<br/>Kind regards,<br />";

                        $htmlStr .= "GLOBAL TECH DIRECT
                                    SOUTH FLORIDA REGIONAL
                                    933 SOUTH MILITARY TRAIL WAREHOUSE E-7
                                    WEST PALM BEACH, FLORIDA 33415
                                    1877 331 0492 EXT 5
                                    http://www.globaltechdirect.com/";



                        $htmlStr .="<br />Notice of Confidentiality:<br />";

                        $htmlStr .="<br />The information contained in this communication is intended solely for the use of the individual or entity to whom it is addressed and others authorized to receive it. It may contain confidential or legally privileged information. If you are not the intended recipient you are hereby notified that any disclosure, copying, distribution or taking any action in reliance on the contents of this information is strictly prohibited and may be unlawful. If you have received this communication in error, please notify us immediately by responding to this email and then delete it from your system.";
         
         
                        $name = "Global Voice Direct";
                        $email_sender = "no-reply@globalvoicedirect.com";
                        $subject = $did." fax number has been assigned to you at Global Voice Direct";
                        $recipient_email = $userDet->user_email;
         
                        $headers  = "MIME-Version: 1.0\r\n";
                        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
                        $headers .= "From: {$name} <{$email_sender}> \n";
         
                        $body = $htmlStr;
         
                        // send email using the mail function, you can also use php mailer library if you want
                        if( self::gvdMail($recipient_email, $subject, $body, $headers) ){
         
                            // tell the user a verification email were sent
                            
                            session()->flash('message', 'You have updated successfully DID.');
                            return redirect('admin/fax_management');
                             

                             
                        }else{

                            session()->flash('message','You have updated successfully DID. But have issue in email sending.');
                            return redirect('admin/fax_management');
                              
                      }
                }else{
                    $response['message']=$xml->error;
                    session()->flash('message',$xml->error);
                    return redirect('admin/fax_management');
                    
                }
            }else{
                $response['message']=$xmlresult->error;
                session()->flash('message',$xml->error);
                return redirect('admin/fax_management');
                
            }
            
        }else{
            session()->flash('message','Please select the did number.');
            return redirect('admin/fax_management');
            
        }
    }
    
    public function updateRegDidFaxAcc(Request $req){

        $response=array();
        $response['success']= 0;
        $response['message']='';
        $response['data']=[];

        $customer_login=config('services.vitelity.login'); 
        $customer_password=config('services.vitelity.pass');   

        $did=(string)$req->account_number_update;
        // $name=(string)$_POST['accountName'];
        // $new_email=(string)$_POST['accountEmail'];

        $userDet = DB::table('vfaxsignup')->join('wpgc_users','vfaxsignup.user_id','=','wpgc_users.ID')->where('vfaxsignup.user_id','=', $req->new_reg_id)->first();
         
        if($did != -1){
            $olddid = $userDet->did;

            //release previose did number
            $releasedidresult= file_get_contents("https://api.vitelity.net/api.php?login=".$customer_login."&pass=".$customer_password."&xml=yes&cmd=removedid&did=".(string)$userDet->did);
            $xmlresult = simplexml_load_string($releasedidresult);

            if( $xmlresult->status == 'ok' or (isset($xmlresult->error) and $xmlresult->error == 'unavailable')){
                 
                    $url = 'https://api.vitelity.net/fax.php';
                    $data = array('login' => $customer_login, 'pass' => $customer_password, 'cmd' => "setfaxacc", 'xml' => "yes", 'acclogin' => (string)$userDet->acclogin, 'did' => (string)$did);

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
                
                    if( $xml->status == 'ok' ){
                        $response['success']=1; 
                        DB::table('vfaxsignup')->where('user_id','=',$req->new_reg_id)->update([

                            "did" => $did,
                            "updated_at" => date('Y-m-d h:i:s')
                        ]);
                        
                        $htmlStr = "";
                        $htmlStr .= "Dear " . $userDet->display_name . ",<br /><br />";
         
                        $htmlStr .= "A new Fax number ".$did." has been assigned to your business.";
                         
                        $htmlStr .= "<br />Thank you for choosing Global Voice Direct  for your virtual phone solution! Below you will find important information regarding your new account, plus some helpful tips to get your new account up and running.";
                        
                        $htmlStr .= "<br />We'd love to know more about you and your business! Help us improve our product to better service you.";

                        $htmlStr .= "<br />Please don't forget to keep your account up to date with us. please complete the complete the credit application if you haven't done so by visiting https://globalvoicedirect.com/users/credit_app.php";

                        $htmlStr .= "<br />Please use the button below to schedule some time with us.";

                        $htmlStr .= "<br />https://my.timetrade.com/book/HHQKP%20";

                        $htmlStr .= "<br/>Kind regards,<br />";

                        $htmlStr .= "GLOBAL TECH DIRECT
                                    SOUTH FLORIDA REGIONAL
                                    933 SOUTH MILITARY TRAIL WAREHOUSE E-7
                                    WEST PALM BEACH, FLORIDA 33415
                                    1877 331 0492 EXT 5
                                    http://www.globaltechdirect.com/";



                        $htmlStr .="<br />Notice of Confidentiality:<br />";

                        $htmlStr .="<br />The information contained in this communication is intended solely for the use of the individual or entity to whom it is addressed and others authorized to receive it. It may contain confidential or legally privileged information. If you are not the intended recipient you are hereby notified that any disclosure, copying, distribution or taking any action in reliance on the contents of this information is strictly prohibited and may be unlawful. If you have received this communication in error, please notify us immediately by responding to this email and then delete it from your system.";
         
         
                        $name = "Global Voice Direct";
                        $email_sender = "no-reply@globalvoicedirect.com";
                        $subject = $did." fax number has been assigned to you at Global Voice Direct";
                        $recipient_email = $userDet->user_email;
         
                        $headers  = "MIME-Version: 1.0\r\n";
                        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
                        $headers .= "From: {$name} <{$email_sender}> \n";
         
                        $body = $htmlStr;
         
                        // send email using the mail function, you can also use php mailer library if you want
                        if( self::gvdMail($recipient_email, $subject, $body, $headers) ){
         
                            // tell the user a verification email were sent
                            
                            session()->flash('message', 'You have updated successfully DID.');
                            return redirect('admin/fax_management');
                             

                             
                        }else{

                            session()->flash('message','You have updated successfully DID. But have issue in email sending.');
                            return redirect('admin/fax_management');
                              
                      }
                }else{
                    $response['message']=$xml->error;
                    session()->flash('message',$xml->error);
                    return redirect('admin/fax_management');
                    
                }
            }else{
                $response['message']=$xmlresult->error;
                session()->flash('message',$xml->error);
                return redirect('admin/fax_management');
                
            }
            
        }else{
            session()->flash('message','Please select the did number.');
            return redirect('admin/fax_management');
            
        }
    }
    public function newFaxAcc(Request $req){
         
        $response=array();
        $response['success']= 0;
        $response['message']='';
        $response['data']=[];

        $did=(string)$req->did;
        $name=(string)$req->accountName;
        $name = (strlen($name) > 25) ? substr($name,0,24) : $name;
        $new_email=(string)$req->accountEmail;

        $userDetail = DB::table('wpgc_users')->where('ID','=',$req->new_id)->first();
        
        
        $password  = substr(md5(uniqid(mt_rand(), true)) , 0, 15);
        $password = "Gv".$password."!";
        
         
        if($did != -1){
            $customer_id = 0;
            $user_id = $req->new_id;
            $userDet = DB::table('user_phone_numbers')->where('user_id',$user_id)->first();
            if($userDet and $userDet->i_customer != null){
                $customer_id = $userDet->i_customer;
            }else {
                $customer_id = $this->create_customer($name, $new_email);
                if($customer_id != 0 ){
                    if($userDet){
                        $userDet->i_customer = $customer_id;
                        $userDet->save();
                    }else{
                        DB::table('user_phone_numbers')->insert(['user_id'=>$user_id, 'i_customer'=> $customer_id, 'i_name' => $name, 'i_email' => $new_email]);
                    }
                }else{
                    session()->flash('message','Somthing went wrong to create an account. Contact to technical team.');
                    return redirect('admin/fax_management');
                }
            }
            
            $Lidb = substr(str_shuffle(md5(time())),0,5);
            $pin = substr(str_shuffle(md5(time())),0,5);
            
            $response = $this->order_did_bulkvs($did, $Lidb, $pin);
           
            if(!isset($response['Status'])){
                session()->flash('message',$response['Description']);
                return redirect('admin/fax_management');
            }elseif(isset($response['Status']) and $response['Status'] != "Active"){
                session()->flash('message',$response['Description']);
                return redirect('admin/fax_management');
            }
            $Lidb = $response['Lidb'];
            $pin = $response['Portout Pin'];
            $did = $response['TN'];
            $id = 'fax' . time(); //generate new account ID
            $i_password = substr(str_shuffle(md5(time())),0,8);
            $i_login = substr(str_shuffle(md5(time())),0,5);
            $h323_password = substr(str_shuffle(md5(time())),0,10); //generate random password
            $product_id = '40281';
            
            $account_id = $this->create_account($id, $did, $customer_id, $product_id, $new_email, $h323_password, $i_login, $i_password);
             
            if($account_id != 0){
                $response['success']=1; 
                $vfaxaccount = DB::table('vfaxsignup')->where('user_id', $req->new_id)->first();
                if($vfaxaccount){
                    $vfaxaccount->i_account = $account_id;
                    $vfaxaccount->did = $did;
                    $vfaxaccount->Lidb = $Lidb;
                    $vfaxaccount->pin = $pin;
                    $vfaxaccount->accemails = $new_email;
                    $vfaxaccount->save();
                }else{
                    
                    DB::table('vfaxsignup')->insert([
                        "user_id" =>  $req->new_id,
                        "acclogin" => $i_login,
                        "accpass" => $i_password,
                        "accname" => $name,
                        "did" => $did,
                        "accemails" => $new_email,
                        "i_account" => $account_id, 
                        "h323_password" => $h323_password,
                        "Lidb" => $Lidb,
                        "pin" => $pin
                    ]);
                    
                }
                     
                $htmlStr = "";
                $htmlStr .= "Dear " . $userDetail->display_name . ",<br /><br />";
 
                $htmlStr .= "A new Fax number ".$did." has been assigned to your business.";
                 
                $htmlStr .= "<br />Thank you for choosing Global Voice Direct  for your virtual phone solution! Below you will find important information regarding your new account, plus some helpful tips to get your new account up and running.";
                
                $htmlStr .= "<br />We'd love to know more about you and your business! Help us improve our product to better service you.";

                $htmlStr .= "<br />Please use the button below to schedule some time with us.";

                $htmlStr .= "<br/>Kind regards,<br />";

                $htmlStr .= "GLOBAL TECH DIRECT
                            SOUTH FLORIDA REGIONAL
                            933 SOUTH MILITARY TRAIL WAREHOUSE E-7
                            WEST PALM BEACH, FLORIDA 33415
                            1877 331 0492 EXT 5
                            http://www.globaltechdirect.com/";

                $htmlStr .="<br />Notice of Confidentiality:<br />";

                $htmlStr .="<br />The information contained in this communication is intended solely for the use of the individual or entity to whom it is addressed and others authorized to receive it. It may contain confidential or legally privileged information. If you are not the intended recipient you are hereby notified that any disclosure, copying, distribution or taking any action in reliance on the contents of this information is strictly prohibited and may be unlawful. If you have received this communication in error, please notify us immediately by responding to this email and then delete it from your system.";
 
 
                $name = "Global Voice Direct";
                $email_sender = "no-reply@globalvoicedirect.com";
                $subject = $did." fax number has been assigned to you at Global Voice Direct";
                $recipient_email = $userDetail->user_email;
 
                $headers  = "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
                $headers .= "From: {$name} <{$email_sender}> \n";
 
                $body = $htmlStr;
     
                // send email using the mail function, you can also use php mailer library if you want
                if( self::gvdMail($recipient_email, $subject, $body, $headers) ){
 
                    // tell the user a verification email were sent
                    
                    session()->flash('message','You have created successfully Account.');
                    return redirect('admin/fax_management');
                }else{
                    session()->flash('message','');
                    return redirect('admin/fax_management','You have created successfully Account but have issue in email sending.');
                }
            }else{
                session()->flash('message','');
                    return redirect('admin/fax_management','something went wrong to create a fax account. Please contact to technical team.');
            }
        }else{
            session()->flash('message','Please select the did number.');
            return redirect('admin/fax_management');
        }
    }
    
    public function newExistingFaxAcc(Request $req){
        $response=array();
        $response['success']= 0;
        $response['message']='';
        $response['data']=[];

        $customer_login=config('services.vitelity.login'); 
        $customer_password=config('services.vitelity.pass'); 

        $did=(string)$req->accountNumber;
        $name=(string)$req->accountName;
        $new_email=(string)$req->accountEmail;

        $userDetail = DB::table('wpgc_users')->where('ID','=',$req->new_existint_id)->first();
        
        
        $password  = substr(md5(uniqid(mt_rand(), true)) , 0, 8);
        
         
        if($did != -1){
            $customer_id = 0;
            $user_id = $req->new_existint_id;
            $userDet = DB::table('user_phone_numbers')->where('user_id',$user_id)->first();
            if($userDet and $userDet->i_customer != null){
                $customer_id = $userDet->i_customer;
            }else {
                $customer_id = $this->create_customer($name, $new_email);
                if($customer_id != 0 ){
                    if($userDet){
                        $userDet->i_customer = $customer_id;
                        $userDet->save();
                    }else{
                        DB::table('user_phone_numbers')->insert(['user_id'=>$user_id, 'i_customer'=> $customer_id, 'i_name' => $name, 'i_email' => $new_email]);
                    }
                }else{
                    session()->flash('message','Somthing went wrong to create an account. Contact to technical team.');
                    return redirect('admin/fax_management');
                }
            }
            
            $Lidb = substr(str_shuffle(md5(time())),0,5);
            $pin = substr(str_shuffle(md5(time())),0,5);
             
            $id = 'fax' . time(); //generate new account ID
            $i_password = substr(str_shuffle(md5(time())),0,8);
            $i_login = substr(str_shuffle(md5(time())),0,5);
            $h323_password = substr(str_shuffle(md5(time())),0,10); //generate random password
            $product_id = '40281';
            
            $account_id = $this->create_account($id, $did, $customer_id, $product_id, $new_email, $h323_password, $i_login, $i_password);
             
            if($account_id != 0){
                $response['success']=1; 
                $vfaxaccount = DB::table('vfaxsignup')->where('user_id', $req->new_existint_id)->first();
                if($vfaxaccount){
                    $vfaxaccount->i_account = $account_id;
                    $vfaxaccount->did = $did;
                    $vfaxaccount->Lidb = $Lidb;
                    $vfaxaccount->pin = $pin;
                    $vfaxaccount->accemails = $new_email;
                    $vfaxaccount->save();
                }else{
                    
                    DB::table('vfaxsignup')->insert([
                        "user_id" =>  $req->new_existint_id,
                        "acclogin" => $i_login,
                        "accpass" => $i_password,
                        "accname" => $name,
                        "did" => $did,
                        "accemails" => $new_email,
                        "i_account" => $account_id, 
                        "h323_password" => $h323_password,
                        "Lidb" => $Lidb,
                        "pin" => $pin
                    ]);
                    
                }
                     
                $htmlStr = "";
                $htmlStr .= "Dear " . $userDetail->display_name . ",<br /><br />";
 
                $htmlStr .= "A new Fax number ".$did." has been assigned to your business.";
                 
                $htmlStr .= "<br />Thank you for choosing Global Voice Direct  for your virtual phone solution! Below you will find important information regarding your new account, plus some helpful tips to get your new account up and running.";
                
                $htmlStr .= "<br />We'd love to know more about you and your business! Help us improve our product to better service you.";

                $htmlStr .= "<br />Please use the button below to schedule some time with us.";

                $htmlStr .= "<br/>Kind regards,<br />";

                $htmlStr .= "GLOBAL TECH DIRECT
                            SOUTH FLORIDA REGIONAL
                            933 SOUTH MILITARY TRAIL WAREHOUSE E-7
                            WEST PALM BEACH, FLORIDA 33415
                            1877 331 0492 EXT 5
                            http://www.globaltechdirect.com/";

                $htmlStr .="<br />Notice of Confidentiality:<br />";

                $htmlStr .="<br />The information contained in this communication is intended solely for the use of the individual or entity to whom it is addressed and others authorized to receive it. It may contain confidential or legally privileged information. If you are not the intended recipient you are hereby notified that any disclosure, copying, distribution or taking any action in reliance on the contents of this information is strictly prohibited and may be unlawful. If you have received this communication in error, please notify us immediately by responding to this email and then delete it from your system.";
 
 
                $name = "Global Voice Direct";
                $email_sender = "no-reply@globalvoicedirect.com";
                $subject = $did." fax number has been assigned to you at Global Voice Direct";
                $recipient_email = $userDetail->user_email;
 
                $headers  = "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
                $headers .= "From: {$name} <{$email_sender}> \n";
 
                $body = $htmlStr;
     
                // send email using the mail function, you can also use php mailer library if you want
                if( self::gvdMail($recipient_email, $subject, $body, $headers) ){
 
                    // tell the user a verification email were sent
                    
                    session()->flash('message','You have created successfully Account.');
                    return redirect('admin/fax_management');
                }else{
                    session()->flash('message','');
                    return redirect('admin/fax_management','You have created successfully Account but have issue in email sending.');
                }
            }else{
                session()->flash('message','');
                    return redirect('admin/fax_management','something went wrong to create a fax account. Please contact to technical team.');
            }
        }else{
            session()->flash('message','Please select the did number.');
            return redirect('admin/fax_management');
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
    
    public function newFaxAcc_vitelity(Request $req){
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

        $userDetail = DB::table('wpgc_users')->where('ID','=',$req->new_id)->first();
        
        
        $password  = substr(md5(uniqid(mt_rand(), true)) , 0, 15);
        $password = "Gv".$password."!";
        
         
        if($did != -1){
             
            $login = $userDetail->user_login;
            $login = str_replace(" ","",$login);
            $login = (strlen($login) > 10) ? substr($login,0,9) : $login;
            $login = (strlen($login) < 8) ? $login.substr(md5(uniqid(mt_rand(), true)) , 0, 8-strlen($login)): $login;
            
            $result1= file_get_contents("https://api.vitelity.net/fax.php?login=".$customer_login."&pass=".$customer_password."&cmd=faxgetdid&xml=yes&did=".(string)$did);

            $xml1 = simplexml_load_string($result1);
            
            if( $xml1->status == 'ok' or $xml1->status == 'fail'){
                 
                $url = 'https://api.vitelity.net/fax.php';
                $data = array('login' => (string)$customer_login, 'pass' => (string)$customer_password, 'cmd' => "newfaxacc", 'xml' => "yes", 'acclogin' => (string)$login, 'accpass' => (string)$password, 'accname' => (string)$name, 'did' => (string)$did, 'accemails' => (string)$new_email);

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
                
                
                // $result= file_get_contents("https://api.vitelity.net/fax.php?login=".$customer_login."&pass=".$customer_password."&xml=yes&cmd=newfaxacc&acclogin=".$new_login."&accpass=".$new_password."&accname=".$new_name."&did=".$new_did);
                
                $xml = simplexml_load_string($result);
                
                if( $xml->status == 'ok' ){

                    $response['success']=1; 
                    DB::table('vfaxsignup')->insert([
                        "user_id" =>  $req->new_id,
                        "acclogin" => $login,
                        "accpass" => $password,
                        "accname" => $name,
                        "did" => $did,
                        "accemails" => $new_email

                    ]);
                     
                    $htmlStr = "";
                    $htmlStr .= "Dear " . $userDetail->display_name . ",<br /><br />";
     
                    $htmlStr .= "A new Fax number ".$did." has been assigned to your business.";
                     
                    $htmlStr .= "<br />Thank you for choosing Global Voice Direct  for your virtual phone solution! Below you will find important information regarding your new account, plus some helpful tips to get your new account up and running.";
                    
                    $htmlStr .= "<br />We'd love to know more about you and your business! Help us improve our product to better service you.";

                    $htmlStr .= "<br />Please don't forget to keep your account up to date with us. please complete the complete the credit application if you haven't done so by visiting https://globalvoicedirect.com/users/credit_app.php";

                    $htmlStr .= "<br />Please use the button below to schedule some time with us.";

                    $htmlStr .= "<br />https://my.timetrade.com/book/HHQKP%20";

                    $htmlStr .= "<br/>Kind regards,<br />";

                    $htmlStr .= "GLOBAL TECH DIRECT
                                SOUTH FLORIDA REGIONAL
                                933 SOUTH MILITARY TRAIL WAREHOUSE E-7
                                WEST PALM BEACH, FLORIDA 33415
                                1877 331 0492 EXT 5
                                http://www.globaltechdirect.com/";

                    $htmlStr .="<br />Notice of Confidentiality:<br />";

                    $htmlStr .="<br />The information contained in this communication is intended solely for the use of the individual or entity to whom it is addressed and others authorized to receive it. It may contain confidential or legally privileged information. If you are not the intended recipient you are hereby notified that any disclosure, copying, distribution or taking any action in reliance on the contents of this information is strictly prohibited and may be unlawful. If you have received this communication in error, please notify us immediately by responding to this email and then delete it from your system.";
     
     
                    $name = "Global Voice Direct";
                    $email_sender = "no-reply@globalvoicedirect.com";
                    $subject = $did." fax number has been assigned to you at Global Voice Direct";
                    $recipient_email = $userDetail->user_email;
     
                    $headers  = "MIME-Version: 1.0\r\n";
                    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
                    $headers .= "From: {$name} <{$email_sender}> \n";
     
                    $body = $htmlStr;
     
                    // send email using the mail function, you can also use php mailer library if you want
                    if( self::gvdMail($recipient_email, $subject, $body, $headers) ){
     
                        // tell the user a verification email were sent
                        
                        session()->flash('message','You have created successfully Account.');
                        return redirect('admin/fax_management');
                         

                         
                    }else{

                        session()->flash('message','');
                        return redirect('admin/fax_management','You have created successfully Account but have issue in email sending.');
                          
                    }

                     

                }else{
                    $response['message']=(string)$xml->error;
                    session()->flash('message',(string)$xml->error);
                    return redirect('admin/fax_management');
                }
            }else{
                 
                $response['message']=(string)$xml1->error;
                session()->flash('message',(string)$xml1->error);
                return redirect('admin/fax_management');
                
            }
            
        }else{
            session()->flash('message','Please select the did number.');
            return redirect('admin/fax_management');
            
        }
    }

    public function newExistingFaxAcc_vitelity(Request $req){
        $response=array();
        $response['success']= 0;
        $response['message']='';
        $response['data']=[];

        $customer_login=config('services.vitelity.login'); 
        $customer_password=config('services.vitelity.pass'); 

        $did=(string)$req->accountNumber;
        $name=(string)$req->accountName;
        $new_email=(string)$req->accountEmail;

        $userDetail = DB::table('wpgc_users')->where('ID','=',$req->new_existint_id)->first();
        
        
        $password  = substr(md5(uniqid(mt_rand(), true)) , 0, 8);
        
         
        if($did != -1){
             
            $login = $userDetail->user_login;
              
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
            
            
            // $result= file_get_contents("https://api.vitelity.net/fax.php?login=".$customer_login."&pass=".$customer_password."&xml=yes&cmd=newfaxacc&acclogin=".$new_login."&accpass=".$new_password."&accname=".$new_name."&did=".$new_did);
            
            $xml = simplexml_load_string($result);
            
            if( $xml->status == 'ok' ){

                $response['success']=1; 
                DB::table('vfaxsignup')->insert([
                    "user_id" =>  $req->new_existint_id,
                    "acclogin" => $login,
                    "accpass" => $password,
                    "accname" => $name,
                    "did" => $did,
                    "accemails" => $new_email

                ]);
                 

                $htmlStr = "";
                $htmlStr .= "Dear " . $userDetail->display_name . ",<br /><br />";
 
                $htmlStr .= "A new Fax number ".$did." has been assigned to your business.";
                 
                $htmlStr .= "<br />Thank you for choosing Global Voice Direct  for your virtual phone solution! Below you will find important information regarding your new account, plus some helpful tips to get your new account up and running.";
                
                $htmlStr .= "<br />We'd love to know more about you and your business! Help us improve our product to better service you.";

                $htmlStr .= "<br />Please don't forget to keep your account up to date with us. please complete the complete the credit application if you haven't done so by visiting https://globalvoicedirect.com/users/credit_app.php";

                $htmlStr .= "<br />Please use the button below to schedule some time with us.";

                $htmlStr .= "<br />https://my.timetrade.com/book/HHQKP%20";

                $htmlStr .= "<br/>Kind regards,<br />";

                $htmlStr .= "GLOBAL TECH DIRECT
                            SOUTH FLORIDA REGIONAL
                            933 SOUTH MILITARY TRAIL WAREHOUSE E-7
                            WEST PALM BEACH, FLORIDA 33415
                            1877 331 0492 EXT 5
                            http://www.globaltechdirect.com/";



                $htmlStr .="<br />Notice of Confidentiality:<br />";

                $htmlStr .="<br />The information contained in this communication is intended solely for the use of the individual or entity to whom it is addressed and others authorized to receive it. It may contain confidential or legally privileged information. If you are not the intended recipient you are hereby notified that any disclosure, copying, distribution or taking any action in reliance on the contents of this information is strictly prohibited and may be unlawful. If you have received this communication in error, please notify us immediately by responding to this email and then delete it from your system.";
 
 
                $name = "Global Voice Direct";
                $email_sender = "no-reply@globalvoicedirect.com";
                $subject = $did." fax number has been assigned to you at Global Voice Direct";
                $recipient_email = $userDetail->user_email;
 
                $headers  = "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
                $headers .= "From: {$name} <{$email_sender}> \n";
 
                $body = $htmlStr;
 
                // send email using the mail function, you can also use php mailer library if you want
                if( self::gvdMail($recipient_email, $subject, $body, $headers) ){
 
                    // tell the user a verification email were sent
                    
                    session()->flash('message','You have created successfully Account.');
                    return redirect('admin/fax_management');
                     

                     
                }else{

                    session()->flash('message','');
                    return redirect('admin/fax_management','You have created successfully Account but have issue in email sending.');
                      
                }

                 

            }else{
                $response['message']=$xml->error;
                session()->flash('message',$xml->error);
                return redirect('admin/fax_management');
                
            }
            
        }else{
            session()->flash('message','Please select the did number.');
            return redirect('admin/fax_management');
            
        }
    }

    // Fax Management Ends

    // Send Email Starts

    public function sendEmail(){
        return view('admin.sendmail');
    }

    public function sendMailToClient(Request $req){

        $this->validate($req,[
            'emailname' => 'required',
            'emailfrom' => 'required',
            'subject' => 'required',
            'editor1' => 'required'
        ]);
        
      $ActiveUsers = DB::table('wpgc_users')
        ->select('user_email')
        ->where('user_status', '=', 1)
        ->where('active', '=', 0)->get();
        
        $count = count($ActiveUsers);
        for($i=0; $i < $count; $i++){
            $to = $ActiveUsers[$i]->user_email;
            $subject = $req->subject;
            $message = $req->editor1;

            // Always set content-type when sending HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

            // More headers
            $headers .= 'From: Global Voice Direct<'.$_POST["emailfrom"].'>' . "\r\n";
            //$headers .= 'Cc: myboss@example.com' . "\r\n";

            self::gvdMail($to,$subject,$message,$headers);
            

        }
        session()->flash('message', 'Email Sent Successfully');
        return redirect()->back();
        //dd($ActiveUsers[0]->user_email);

    }


    // Send Email Ends


    // User Management Starts

    public function userManagement()
    {
        return view('admin.usermanagement');
    }

    public function activeUsers(Request $req)
    {
        $search = trim((string) $req->input('search'));

        $activeUserDetail = DB::table('wpgc_users as u')
            ->leftJoin('user_shipping_address as usa', 'usa.user_id', '=', 'u.ID')
            ->where('u.active', '0')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('u.user_login', 'like', "%{$search}%")
                        ->orWhere('u.user_email', 'like', "%{$search}%")
                        ->orWhere('u.user_nicename', 'like', "%{$search}%")
                        ->orWhere('u.display_name', 'like', "%{$search}%")
                        ->orWhere('u.phone', 'like', "%{$search}%");
                });
            })
            ->orderBy('u.user_registered', 'desc')
            ->select('u.*', 'usa.*')
            ->paginate(100)
            ->appends($req->query());

        return view('admin.ajax.active_users')
                ->with('activeUserDetail', $activeUserDetail);
    }

    public function deActiveUsers(Request $req)
    {
        $search = trim((string) $req->input('search'));

        $deActiveUserDetail = DB::table('wpgc_users')
            ->where('active', '1')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('user_login', 'like', "%{$search}%")
                        ->orWhere('user_email', 'like', "%{$search}%")
                        ->orWhere('user_nicename', 'like', "%{$search}%")
                        ->orWhere('display_name', 'like', "%{$search}%");
                });
            })
            ->orderBy('user_registered', 'desc')
            ->paginate(100)
            ->appends($req->query());

        return view('admin.ajax.deactive_users')
                ->with('deActiveUserDetail', $deActiveUserDetail);
    }

    public function emailActiveUsers()
    {
        $emailActiveUsers = DB::table('wpgc_users')
            ->where('user_status', '0')
            ->orderBy('user_registered', 'desc')
            ->paginate(100);

        return view('admin.ajax.email_active_users')
                ->with('emailActiveUsers', $emailActiveUsers);
    }

    public function passwordUsers()
    {
        $activeUserDetail1 = DB::table('wpgc_users')
            ->where('active', '0')
            ->orderBy('user_registered', 'desc')
            ->paginate(100);

        return view('admin.ajax.password_users')
                ->with('activeUserDetail1', $activeUserDetail1);
    }
    
    public function legalUsers()
    {
        $legalUserDetail = DB::table('wpgc_users')
            ->join('legal_signup_info', 'wpgc_users.ID', '=', 'legal_signup_info.user_id')
            ->orderBy('legal_signup_info.created_at', 'desc')
            ->paginate(100);

        return view('admin.ajax.legal_users')
                ->with('legalUserDetail', $legalUserDetail);
    }

    public function phoneUsers()
    {
        $userNumberDetail = DB::table('wpgc_users')
            ->join('user_phone_numbers', 'wpgc_users.ID', '=', 'user_phone_numbers.user_id')
            ->orderBy('user_phone_numbers.created_at', 'desc')
            ->paginate(100);

        return view('admin.ajax.phone_users')
                ->with('userNumberDetail', $userNumberDetail);
    }

    public function failureMembership()
    {
        $failuremembershipDetail = DB::table('unsuccess_membership')
            ->orderBy('created_at', 'desc')
            ->paginate(100);

        return view('admin.ajax.failure_membership')
                ->with('failuremembershipDetail', $failuremembershipDetail);
    }

    public function affiliates()
    {
        $affiliates = AffiliateUser::orderBy('id', 'desc')
                        ->paginate(100);

        return view('admin.ajax.affiliates')
                ->with('affiliates', $affiliates);
    }

    public function affiliateWithdraw()
    {
        $withdrawRequests = DB::table('affiliates_withdraw_requests')
            ->join(
                'affiliate_users',
                'affiliates_withdraw_requests.affiliate_id',
                '=',
                'affiliate_users.id'
            )
            ->select(
                'affiliate_users.name',
                'affiliate_users.email',
                'affiliate_users.phone',
                'affiliates_withdraw_requests.status',
                'affiliates_withdraw_requests.balance',
                'affiliates_withdraw_requests.id'
            )
            ->orderBy('affiliates_withdraw_requests.id', 'desc')
            ->paginate(100);

        return view('admin.ajax.affiliate_withdraw', compact('withdrawRequests'));
    }


    public function memberWithdraw()
    {
        $members_withdraw_request = DB::table('member_withdraw_request')
            ->orderBy('id', 'desc')
            ->paginate(100);

        return view(
            'admin.ajax.member_withdraw',
            compact('members_withdraw_request')
        );
    }


    // public function userManagement(){
    //   ini_set('display_errors', '1');
    //   ini_set('display_startup_errors', '1');
    //   error_reporting(E_ALL);
    //     // $activeUserDetail = DB::table('wpgc_users')->where('active','=','0')->orderBy('user_registered', 'desc')->get();
    //     $activeUserDetail = DB::table('wpgc_users as u')
    //         ->leftJoin('user_shipping_address as usa', 'usa.user_id', '=', 'u.ID')
    //         ->where('u.active', '0')
    //         ->orderBy('u.user_registered', 'desc')
    //         ->select('u.*', 'usa.*')
    //         ->get();
    //     $deActiveUserDetail = DB::table('wpgc_users')->where('active','=','1')->orderBy('user_registered','desc')->get();
    //     $emailActiveUsers = DB::table('wpgc_users')->where('user_status','=','0')->orderBy('user_registered','desc')->get();
        
    //     $activeUserDetail1 = DB::table('wpgc_users')->where('active','=','0')->orderBy('user_registered','desc')->get();

    //     $legalUserDetail = DB::table('wpgc_users')->join('legal_signup_info', 'wpgc_users.ID','=','legal_signup_info.user_id')->orderBy('legal_signup_info.created_at','desc')->get();

    //     $userNumberDetail = DB::table('wpgc_users')->join('user_phone_numbers','wpgc_users.ID','=','user_phone_numbers.user_id')->orderBy('user_phone_numbers.created_at','desc')->get();
        
    //     $failuremembershipDetail = DB::table('unsuccess_membership')->orderBy('created_at','desc')->get();
    //     $members_withdraw_request = DB::table('member_withdraw_request')->get();
    //     ///dd($failuremembershipDetail);
    //     $affiliates = AffiliateUser::get();
    //     $withdrawRequests = DB::table('affiliates_withdraw_requests')->join('affiliate_users','affiliates_withdraw_requests.affiliate_id','=','affiliate_users.id')->select('affiliate_users.name','affiliates_withdraw_requests.status','affiliate_users.email','affiliate_users.phone','affiliates_withdraw_requests.balance','affiliates_withdraw_requests.id')->get();
    //     return view('admin.usermanagement')->with('activeUserDetail',$activeUserDetail)->with('deActiveUserDetail',$deActiveUserDetail)->with('emailActiveUsers',$emailActiveUsers)->with('activeUserDetail1',$activeUserDetail1)->with('legalUserDetail',$legalUserDetail)->with('userNumberDetail',$userNumberDetail)->with('failuremembershipDetail',$failuremembershipDetail)->with('affiliates',$affiliates)->with('withdrawRequests',$withdrawRequests)->with('members_withdraw_request',$members_withdraw_request);
    // }
    public function getAffilateReferrals(Request $req){

      $affReferrals = DB::table('affliate_users_referals')
                          ->join('affiliate_users', 'affliate_users_referals.affliate_user_id', '=', 'affiliate_users.id')
                          ->join('wpgc_users','wpgc_users.ID','=','affliate_users_referals.user_id')
                          ->select('wpgc_users.user_nicename','affiliate_users.name','affliate_users_referals.reward_status','affliate_users_referals.created_at')
                          ->where('affliate_users_referals.affliate_user_id',$req->id)
                          ->get();
      $response = json_encode($affReferrals);
      return response()->json($response);
    }
    public function removeUser(Request $req){


        DB::table('wpgc_users')->where('ID','=',$req->uid)->update(['active' => 1]);

        session()->flash('message','User DeActivate Successfully.');

        return redirect('admin/usermanagement');
    }

    public function activateUser(Request $req){

        DB::table('wpgc_users')->where( 'ID', '=', $req->uid)->update([
            "active" => 0,
            "user_status" => 1
        ]);

        session()->flash('message','User Activate Successfully.');

        return redirect('admin/usermanagement');
        
    }

    public function activateEmail(Request $req){


        DB::table('wpgc_users')->where('ID','=',$req->uid)->update([
            'user_status' => 1
        ]);
        
        session()->flash('message','User Activated Successfully.');
        return redirect('admin/usermanagement');


    }

    public function changePass(Request $req){
        if(isset($req->password) and $req->password != "" and isset($req->againpassword) and $req->againpassword != "" and isset($req->uservalue) and $req->uservalue != ""){
             
            if($req->password == $req->againpassword){


                $password  = password_hash($req->password, PASSWORD_DEFAULT);
                // $password  = crypt($_POST['password'],$salt);
                DB::table('wpgc_users')->where('ID','=', $req->uservalue)->update([
                    "user_pass" => $password
                ]);
                

                session()->flash('message','Password reset successfully, please login!');
                return redirect('admin/usermanagement');
                

            }
            else{


                session()->flash('message','Password not matched.');
                return redirect('admin/usermanagement');
                

            }

        }else{


            session()->flash('message','Password not matched.');
            return redirect('admin/usermanagement');
            
        }
    }

    public function addNewUser(Request $req){
       

        if($req->pass1 != $req->pass2){
            session()->flash('message', 'Password must be same!');
            return redirect()->back();
        }
        $activation_key = sha1(mt_rand(10000,99999).time().$req->useremail);
        $userReg = date("Y-m-d H:i:s");

        //set uniq string key ////////////////
        $charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789/\\][{}\'";:?.>,<!@#$%^&*()-_=+|';
         $randStringLen = 64;

         $randString = "";
         for ($i = 0; $i < $randStringLen; $i++) {
             $randString .= $charset[mt_rand(0, strlen($charset) - 1)];
         }

        $salt = $randString;
        $password  = password_hash($req->pass1, PASSWORD_DEFAULT);
        try{
            DB::table('wpgc_users')->insert(['user_login'=> $req->userid, 'user_pass'=>$password, 'user_nicename'=>$req->bname, 'user_email'=> $req->useremail, 'user_url'=>$req->userurl, 'user_registered'=>$userReg, 'user_activation_key'=>$activation_key, 'display_name'=>$req->dname, 'salt' => $salt ]);
        }catch(\Exception $e){
            session()->flash('message', 'Username or email already Exist');
            return redirect()->back();
        }
        $userLastInsertedId = DB::getPdo()->lastInsertId();
       

        $verificationLink = rtrim(config('app.url'), '/') . "/action/activate?code=" . $activation_key. "&id=" . $userLastInsertedId;
        $htmlStr = "";
        $htmlStr .= "Hi " . $req->bname . ",<br /><br />";

        $htmlStr .= "<strong>Here is your User Name for Login : </strong> ".$req->userid."<br /><br />";
        $htmlStr .= "<strong>Here is your Password for Login : </strong> ".$req->pass1."<br /><br /><br />";

        $htmlStr .= "Please click the button below to verify your subscription and have access to the globalvoicedirect.com.<br /><br /><br />";
        $htmlStr .= "<a href='{$verificationLink}' target='_blank' style='padding:1em; font-weight:bold; background-color:blue; color:#fff;'>VERIFY EMAIL</a><br /><br /><br />";

        $htmlStr .= "Kind regards,<br />";
        $htmlStr .= "<a href='https://globalvoicedirect.com/' target='_blank'>Globalvoicedirect Team</a><br />";


        $name = "Varification";
        $email_sender = "no-reply@globalvoicedirect.com";
        $subject = "Verification Link";
        $recipient_email = $req->useremail;

        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
        $headers .= "From: {$name} <{$email_sender}> \n";

        $body = $htmlStr;

        if(self::gvdMail($recipient_email, $subject, $body, $headers)){
            session()->flash('message', 'User Added Successfully and the activation email is sent to your email Address. First Click the activation link to activate you account.');
            return redirect()->back();
        }else{
            session()->flash('message', 'Email Sending failed');
            return redirect()->back();
        }
    }

    public function tosPdf(Request $req){
        //dd($req->id);
        $pdfRecord = DB::table('wpgc_users')
        ->join('legal_signup_info', 'wpgc_users.ID', '=', 'legal_signup_info.user_id')
        ->where('legal_signup_info.user_id','=', $req->id)
        ->orderBy('legal_signup_info.created_at', 'desc')
        ->get();

        if(file_exists(public_path("/credit-app/".$pdfRecord[0]->esign_path))){
            $signaturePathPdf = public_path("/credit-app/".$pdfRecord[0]->esign_path);
        }
        else{
            $signaturePathPdf = "<b></b>";
        }
        //dd(public_path('credit-app/userSignatureImages/1550978113.png'));
        //dd($signaturePathPdf);
        //dd($pdfRecord[0]->user_nicename);
        //dd(public_path("/credit-app/".$pdfRecord[0]->esign_path));

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

      .fontOnly{font: "Arial"}
      .fontTime{font: "Times New Roman"}
      .ft99{font: 13px "Arial"; font-weight: 500;}
      .heading{font: 18px "Arial"}
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

      <h3 align="center">Terms and Conditions</h3>
      <p style="margin-top:50px; font-family: Times New Roman; font-size:12px;">Global Voice Direct does not discriminate on the basis of age, race, color, religion, national origin, gender or sexual orientation. For your benefit, we have posted a copy of its Equal Employment Opportunity (EEO) policy statement.</p>
      <p style="font-family: Times New Roman; font-size:12px;">This Document contains the Purchase Agreement and the Terms and Conditions of becoming an Account Holder with Global Voice Direct and its subsidiaries.</p>
      <p style="font-weight: 600; font-family: Times New Roman, Times, serif; font-size:12px;">BY ACCESSING AND/OR USING THE Global Voice Direct WEBSITE (THE "WEBSITE") AND/OR OUR SERVICE (THE "SERVICE"), YOU AGREE TO COMPLY WITH, AND BE BOUND BY, THE FOLLOWING TERMS AND CONDITIONS (THE "AGREEMENT</p>
      <p style="font-family: Times New Roman, Times, serif; font-size:12px;">I authorize Global Voice Direct to debit the Credit Card account indicated in this web form, for the noted amount on today’s date. I understand that because this is an electronic transaction, these funds may be withdrawn from my account as soon as the above noted transaction date. In the case of an Debit / Credit Card Transaction being rejected for Non Sufficient Funds (NSF) I understand that the business may at its discretion attempt to process the charge again within 30 days, and agree to an additional $0 charge for each attempt as a separate transaction from the authorized payment. I acknowledge that the origination of ACH transactions to my account must comply with the provisions of U.S. law. I will not dispute merchant debiting my debit / Credit card so long as the transaction corresponds to the terms indicated in this web form. Our billing cycle runs on a net 30 terms. We do not charge automatically by default unless you add your credit card on file with us. A net-30 account is one that extends you 30 days to pay the bill in full after you have purchased products.</p>
      <b style="font-family:Arial, Helvetica, sans-serif" font-size: 10px;>CREDIT REPORTING:</b>
      <p class="ft99">YOUR ACCOUNT WITH Global Voice Direct WILL BE ASSIGNED TO A Commercial FINANCE COMPANY WITHIN APPROXIMATELY 30 DAYS FROM THE DATE OF THAT YOUR ACCOUNT IS APPROVED AND A PURCHASE OF PRODUCTS IS MADE IF THE ACCOUNT IS ACTIVE AT THAT TIME. WHILE PAYMENTS ARE DUE ON YOUR ACCOUNT DURING THIS FIRST Sixty (30) DAYS, YOUR ACCOUNT WILL NOT BE REPORTED TO ANY CREDIT BUREAU UNTIL THE SALE OF YOUR ACCOUNT TO THE Commercial FINANCE COMPANY IS COMPLETED. CREDIT REPORTING DURING THE TERM OF THIS AGREEMENT IS NOT GUARANTEED.</p>
      <b class="fontOnly">REFUND POLICY ACCETED</b>
      <p style="font-size: 13px;">We have a 7 days refund policy. We do not provide refund after your account has been successfully reported to the commercial business bureaus. We report positive and negative accounts to the Commercial Business bureaus.</p>
      <b class="fontOnly">SHIPPING POLICY</b>
      <p style="font-size: 13px;">All products are shipped within 14 business days of acceptance of order.</p>
      <b class="fontOnly">RIGHTS AND OBLIGATIONS OF THE BUYER</b>
      <p style="font-size: 13px;">The Buyer must pay the full consideration for this product that the Seller requires as the total price of the product. This consideration includes not only the purchase price, but other obligations that the Buyer accepts as well as potential rights the Buyer agrees to forego. By accepting this Purchase Agreement, the Buyer agrees to receive continuing follow-up contact from the Seller including email, mail, newsletters, product updates, product recall notices, product improvements, telephone calls from the Seller and/or telemarketing organizations and/or pollsters for the purpose of solicitation related to the instant product or any other product or service. Buyer agrees to post-sale contact from joint venture partners of the Seller or from others who have a commercial relationship with the Seller. Buyer agrees that all personal information about the buyer or his or her buying habits and preferences, including address and phone number, may be placed in a general database and agrees that this information may be shared, rented or sold to third parties. However, Buyer shall at all times be fully empowered to sever contact with the Seller by notification using the unsubscribe link in solicitations. Moreover, the Buyer retains the right to refuse specific contact with some third party solicitors and maintain it with others. The Buyer retains the right to have his or her name removed from a general solicitation database. The Buyers agreement to accept solicitation and contact may be reduced, enhanced, limited or terminated by notification to anyone contacting the Buyer. The burden is on the Buyer to prove that such communication was made to and received by the person making contact. Buyer agrees that Seller is not liable for communications made to the Buyer by parties unrelated to this purchase even though referred by the Seller. Buyer accepts full responsibility for limiting unsolicited contact and Buyer understands that he retains all rights to directly restrict communication or solicitation from any party including the Seller.</p>
      <p class="fontTime" style="font-size: 13px;">Buyer agrees that in lieu of closing the Account, in the event after 90 days of nonpayment the account will be closed and any balance due will be reported to the commercial credit bureaus , active and reporting until Buyer notifies Global Voice Direct to Close the account or makes the qualifying purchase</p>
      <b class="fontOnly">CREDIT CARD CHARGES AND CREDIT CARD FRAUD</b>
      <p class="fontTime" style="font-size: 13px;">Buyer warrants that he or she is over 18 years of age, not subject to the Child Online Privacy Act, of legal age to enter into contractual agreements in the state in which he is present when he makes this purchase, and is the true and authorized owner of the credit card used to make this purchase. Any Buyer who violates any of these requirements may be liable for civil or criminal prosecution, plus actual damages, and agrees that all information collected by this website may be used for prosecution and may be turned over to law enforcement agencies or to credit card companies and merchant service providers</p>
      <p>If the true and/or authorized owner of the credit card attempts to commit fraud upon the Seller, he authorizes each and every credit card company or merchant service provider to disclose to the Seller all information that could be construed as proof of credit card fraud.</p>
      <p>Any Buyer who attempts to perpetrate a fraud upon Seller involving the use of a credit card herewith gives authorization for the Seller to access all credit information about the Buyer from credit reporting agencies and also authorizes the Seller to discover all relevant information from any source about the fraudulent practices of the Buyer and to reveal such information to credit reporting agencies, credit card companies, merchant service providers, and law enforcement agencies.</p>
      <b>ASSUMPTION OF RISK</b>
      <p>Buyer agrees to accept all risk associated with the use of this product, including but not limited to, ingestion of or application to Buyer&rsquo;s person, the use of the product personally or in business, all taxes and regulations applicable to this product, all legal compliance issues related to this product. Buyer warrants an understanding that the Seller is disclaiming all liability from harm of any kind or nature caused directly or indirectly from this product. Buyer agrees, as part of the consideration required to purchase this product, to carefully review and test this product during the refund period and to immediately request a refund if the product is not satisfactory.</p>
      <b>LIMITATION ON THE LIABILITY LIMITATION</b>
      <p>Buyer understands that some states do not allow limitation of liability.</p>
      <b>REFUND POLICY ACCEPTED</b>
      <p>We have a 7 days refund policy. We do not provide refund after your account has been successfully reported to the commercial business bureaus. We report positive and negative accounts to the Commercial Business bureaus.</p>
      <b>PRIVACY POLICY ACCEPTED</b>
      <p>Buyer expressly accepts the terms of the Privacy Policy of Seller&rsquo;s website.</p>
      <b>TERMS OF USE ACCEPTED</b>
      <p>Buyer expressly accepts the Terms of Use of the Seller&rsquo;s website.</p>
      <b>RIGHT TO PUBLISH SUBMISSIONS</b>
      <p>Buyer agrees that Seller may publish for commercial purposes the full or partial content of any and all communication with Buyer at the Seller&rsquo;s sole discretion.</p>
      <b>INDEMNIFICATION</b>
      <p>Buyer agrees to indemnify Seller for any and all damage that Buyer causes by using the product or information contained on this website that results in a damage award against the Seller.</p>
      <b>RIGHT TO STOP SELLING OR SERVICING PRODUCT OR MEMBERSHIP</b>
      <p>Buyer agrees to indemnify Seller for any and all damage that Buyer causes by using the product or information contained on this website that results in a damage award against the Seller.</p>
      <b>CALIFORNIA RESIDENTS NOTE</b>
      <p>You are entering into a contract that may modify, restrict, or eliminate rights you may have under the California Online Privacy Protection Act of 2003 (OPPA). Under the Privacy Policy and this Purchase Agreement you waive any right to view or modify the content of our database. You waive any right to force this business or website to divulge when or to whom your information may have been provided to third parties. In the event the website elects at its sole discretion to release information to you, you must clearly identify yourself to the.</p>
      <p>In addition, Global Voice Direct hereby certifies that it does not maintain or provide for their employees any segregated facilities at any establishment. As used in this Certification, the term &rsquo;Segregated Facilities&rsquo; means any waiting rooms, work area, rest rooms and wash rooms, restaurants and other eating areas, time clocks, locker rooms and other storage or dressing areas, transportation, and housing facilities provided for employees which are segregated by explicit directive or are in fact segregated on the basis of race, creed, color or national origin, because of habit, local custom or otherwise.</p>
      <b>'.$pdfRecord[0]->user_nicename.'&rsquo;s Signature</b>
      <p><img src="'.$signaturePathPdf.'"><p>
      </DIV>
      </BODY>
      </HTML>');
      $mpdf->Output();

        //dd($pdfRecord);
    }

    public function deactivateAffiliate($id){
      $affiliate = AffiliateUser::find($id);
      if($affiliate){
        $affiliate->is_active = 0;
        $affiliate->update();
        session()->flash('message', 'Affiliate de-activated successfully!');
        return redirect()->back();
      }else{
        session()->flash('message', 'Affiliate does not found!');
        return redirect()->back();
      }
    }

    public function approvRequest(Request $req)
    {
        $parameters = $req->all();
        $request = DB::table('member_withdraw_request')->where('id', $parameters['request_id'])->first();
        if ($parameters['reward_status'] == 0) //BONUS
        {
            if ($request->req_type == 0) //pay-bill with Bonus
            {
                $member = DB::table('wpgc_users')->where('id', $request->member_id)->first();
                $member_balance = DB::table('payable_amount')
                    ->where('user_id', $request->member_id)->select('r_amount')->first();
                DB::table('payable_amount')
                    ->where('user_id', $request->member_id)  // find your user by their id
                    ->update(array( 'r_amount' => $member_balance->r_amount - $member->bonus));
                /*!IMPORTANT here will be the MEMBER bonus_status Update*/
                DB::table('wpgc_users')
                    ->where('id', $request->member_id)  // find your user by their id
                    ->update(array( 'bonus_status' => 3));
            }elseif($request->req_type == 1) //transfer bonus
            {
                DB::table('wpgc_users')
                    ->where('id', $request->member_id)  // find your user by their id
                    ->update(array( 'bonus_status' => 3));
            }
        }elseif($parameters['reward_status'] == 1) //REWARD
        {
            if ($request->req_type == 0) //pay-bill with Reward
            {
                $member = DB::table('wpgc_users')->where('id', $request->member_id)->first();
                $member_balance = DB::table('payable_amount')
                    ->where('user_id', $request->member_id)->select('r_amount')->first();
                DB::table('payable_amount')
                    ->where('user_id', $request->member_id)  // find your user by their id
                    ->update(array( 'r_amount' => $member_balance->r_amount - $member->bonus));
                /*!IMPORTANT here will be the REFERRAL reward_status Update*/
                DB::table('wpgc_users')
                    ->where('id', $request->referral_id)  // find your user by their id
                    ->update(array( 'reward_status' => 3));
            }elseif($request->req_type == 1) //transfer reward
            {
                DB::table('wpgc_users')
                    ->where('id', $request->referral_id)  // find your user by their id
                    ->update(array( 'reward_status' => 3));
            }
        }
        DB::table('member_withdraw_request')->where('id', $parameters['request_id'])->update(array( 'req_status' => 1));
        $email = DB::table('wpgc_users')->select('user_email')->where('id', $request->member_id)->first();
        $this->withdrawRequestEmail($email->user_email);
            session()->flash('message', 'Withdraw request has been updated');
            return redirect()->back();
    }

    public function AfiliateWithdrawRequestAction($id){
      if ($id) {
        DB::table('affiliates_withdraw_requests')
            ->where('id', $id)  // find your user by their id
            ->update(array( 'status' => 1));
        session()->flash('message', 'Withdraw request has been updated');
        return redirect()->back();
      }
    }

    public function withdrawRequestEmail($email)
    {
        $useremail = $email;

        $htmlStr = "";
        $htmlStr .= "Hi Dear Candidate ,<br /><br />";

        $htmlStr .= "Your request has been approved.<br /><br /><br />"; 
        $htmlStr .= "Kind regards,<br />";
        $htmlStr .= "<a href='https://globalvoicedirect.com' target='_blank'>Globalvoicedirect Team</a><br />";
       /*end here*/
       /*send email*/
        $name = "";
        $email_sender = "no-reply@globalvoicedirect.com";
        $subject = "Request";
        $recipient_email = $email;
        $body = $htmlStr;
        $email=$recipient_email;

        // \Mail::send(array(), array(), function ($message) use ($htmlStr,$email) {
        //   $message->to($email)
        //   ->subject('Request')
        //   ->from(session()->get('user_email'))
        //   ->html($htmlStr);
        // });

        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
        $headers .= "From: {$name} <{$email_sender}> \n";
        self::gvdMail($recipient_email, $subject, $body, $headers);
        return true;
    }

    // User Management Ends

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
