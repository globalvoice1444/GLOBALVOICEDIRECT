<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
class ProfileController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function updateCreditInfo(Request $req){
  		if ( isset($req->nicename) and isset($req->displayname) and isset($req->email) and isset($req->url) ) {
  			$nicename = $req->nicename;
    		$displayname = $req->displayname;
    		$email = $req->email;
    		$url = $req->url;
            $email_exist = DB::table('wpgc_users')
                            ->where('user_email', '=', $email)
                            ->where('id', '<>',session()->get('user_id'));
            if($email_exist->count() > 0){
                return redirect('user/profile')->withError('Email is already existed!');
            }else{
                DB::table('wpgc_users')->where('id',session()->get('user_id'))->update(['user_nicename'=>$nicename,'display_name'=>$displayname, 'user_url'=>$url,'user_email'=>$email]);
                return redirect('user/profile')->withSuccess('Credit line information Successfully updated!');
            }
           
  		}else{
            return redirect()->back()->withError('All fields must be filled!');
        } 
    }
    public function updateCreditCardInfo(Request $req) {
        if (isset($req->cardnumber) and $req->cardnumber != '' and isset($req->year) and $req->year != '' and isset($req->month) and $req->month != '' and isset($req->cvv) and $req->cvv != '') {
           
            $cardnumber = $req->cardnumber;
            $year = $req->year;
            $month = $req->month;
            $cvv = $req->cvv;
            $current_year = Date('Y');
            if($year < $current_year or $year > $current_year + 10 ){
                return redirect()->back()->withError('');
            }
            $user_exist = DB::table('Account_details')->select('user_id')->where('user_id','=',session()->get('user_id'))->count();
            if($user_exist){
                DB::table('Account_details')->where('user_id','=',session()->get('user_id'))->update(['account_number'=>$cardnumber,'expire_year'=>$year, 'expire_month'=>$month, 'cvc'=>$cvv]);
                return redirect('user/profile')->withSuccess('Credit card information successfully updated!');

            }else {
                return redirect()->back()->withError('User not exist');
            }
        }else {
            return redirect('user/profile')->withError('Error! Empty field detected!');
        }
    }
    public function updateLoginInfo(Request $req) {
        if(session()->get('user_login')==true){
            if ( isset( $req->password1 ) and $req->password1 != '' and isset( $req->password2 ) and $req->password2 != '' ) {
                 $password1 = $req->password1;
                $password2 = $req->password2;
                if ($password1 == $password2) {
                    DB::table('wpgc_users')->where('id',session()->get('user_id'))->update(['user_pass'=>bcrypt($password1)]);
                    return redirect('user/profile')->withSuccess('Password successfully updated!');
                }else {
                    return redirect('user/profile')->withError('Password fields not matched!');
                }
            }else {
                return redirect('user/profile')->withError('Password fields does not empty!');
            }
        }else{
            return redirect('user/profile')->withError('User must be logged!');
        }
    }
    public function updateBillingAddress(Request $req) {
        if(isset($req->firstname) and $req->firstname != '' and isset($req->lastname) and  $req->lastname != '' and isset($req->address) and $req->address != '' and isset($req->city) and $req->address != '' and isset($req->state) and $req->state != '' and isset($req->zip) and $req->zip != '' and isset($req->phone) and $req->phone != '') {
            $firstname = $req->firstname;
            $lastname = $req->lastname;
            $address = $req->address;
            $city = $req->city;
            $state = $req->state;
            $zip = $req->zip;

            if (strlen($zip) <> 5 ) {
                return redirect()->back()->withError('Please enter valid zip code');
            }
            $user_exist = DB::table('user_shipping_address')->select('user_id')->where('user_id','=',session()->get('user_id'))->count();
            if($user_exist){
                DB::table('user_shipping_address')->where('user_id','=',session()->get('user_id'))->update(['firstname'=>$firstname, 'lastname'=>$lastname,'address'=>$address, 'city'=>$city, 'state'=>$state, 'zip'=>$zip]);
                return redirect('user/profile')->withSuccess('Credit card billing info successfully updated!');

            }else {
                return redirect()->back()->withError('User not exist');
            }
        }else {
            return redirect('user/profile')->withSuccess('Billing Address successfully updated!');
        }
    }
}