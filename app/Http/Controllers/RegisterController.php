<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use DateTime;
use Auth;
use Hash;
use Date;
use DB;
use Response;
use APP\AffiliateUser;
use App\AffiliateReferral;
use App\AffiliateUsersBalance;
use Exception;
use Illuminate\Support\Facades\Log;
use SoapClient;
use SoapFault;
use SoapHeader;
use SoapVar;

class RegisterController extends Controller
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

  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    return view('home');
  }
  public function userLoginPage()
  {
    // Already signed in (e.g. after a slow Virtual Office login or a double tap): go straight to the panel.
    if (\Auth::check() && session('user_login')) return redirect('user/profile');
    return view('users.userLogin');
  }

  public function referral_program()
  {
    $user_id = session()->get('user_id');
    $referrer_token = \DB::table('wpgc_users')->select('referrer_token')->where('id', $user_id)->first();
    $referrer_url = "https://" . $_SERVER['SERVER_NAME'] . '/package-pricing?referal_token=' . $referrer_token->referrer_token;
    return view('users.referral_program', compact('referrer_url'));
  }

  public function referral_dashboard()
  {
    $userId = session()->get('user_id');
    $refferal = DB::table('wpgc_users')->where('wpgc_users.id', $userId)->first();
    $user_orders_amount = DB::table('orders')->where('OrderUserID', $userId)->sum('OrderAmount');

    if ($user_orders_amount >= 250 && $refferal->bonus_status == 0) {
      DB::table('wpgc_users')
        ->where('id', $userId)  // find your user by their id
        ->update(array('bonus' => 20, 'bonus_status' => 1));
    }

    $referred_users = DB::table('user_referrer')->join('wpgc_users', 'user_referrer.referral_id', '=', 'wpgc_users.ID')->select('user_referrer.referrer_id', 'user_referrer.referral_id', 'user_referrer.created_at', 'wpgc_users.user_nicename', 'wpgc_users.user_email', 'wpgc_users.reward', 'wpgc_users.reward_status', 'wpgc_users.bonus', 'wpgc_users.bonus_status')->where('user_referrer.referrer_id', $userId)->get();
    foreach ($referred_users as $key => $user) {
      $total_shopping = DB::table('orders')->where('OrderUserID', $referred_users[$key]->referral_id)->sum('OrderAmount');
      $referred_users[$key]->totalshopping = $total_shopping;
      if ($total_shopping >= 250 && $referred_users[$key]->reward_status == 0) {
        $referred_users[$key]->reward_status = 1;
      }
    }
    // dd($userId,$user_orders_amount,$refferal, $referred_users, $refferal->reward_status);
    return view('users.referral_dashboard', compact('refferal', 'referred_users', 'user_orders_amount'));
  }

  public function withdrawBewardRequest()
  {
    $useremail = session()->get('email');

    $htmlStr = "";
    $htmlStr .= "Hi globalvoicedirect Team ,<br /><br />";

    $htmlStr .= "Its humble request to you that, please transfer the Bonus as awarded in the response of shopping from globalvoicedirect.<br /><br /><br />";
    $htmlStr .= "Kind regards,<br />";
    $htmlStr .= "<a href='https://globalvoicedirect.com' target='_blank'>Globalvoicedirect Team</a><br />";
    // dd($htmlStr);
    /*end here*/
    /*send email*/
    $name = "";
    $email_sender = session()->get('user_email');
    $subject = "Withdraw Bonus Request";
    $recipient_email = "shawalhassan972@gmail.com";


    $body = $htmlStr;
    $email = $recipient_email;
    \Mail::send(array(), array(), function ($message) use ($htmlStr, $email) {
      $message->to($email)
        ->subject('Withdraw Bonus Request')
        ->from(session()->get('user_email'))
        ->html($htmlStr);
    });
    $userid = session()->get('user_id');
    DB::table('wpgc_users')
      ->where('id', $userid)  // find your user by their id
      ->update(array('bonus_status' => 2));
    return redirect()->back()->with('success', 'Email successfully sent to globalvoicedirect');
  }

  public function withdrawRewardRequest(Request $req)
  {
    $parameters = $req->all();
    if ($parameters['reward_status'] == 'bonus') {
      $member_id = $parameters['referral_id'];
      $member = DB::table('wpgc_users')->where('id', $member_id)->first();
      $total_shopping = DB::table('orders')->where('OrderUserID', $member_id)->sum('OrderAmount');
      if ($total_shopping >= 250) {
        $parameters['request_type'] == 'pay_bill' ? $req_type = 0 : $req_type = 1;
        $id = DB::table('member_withdraw_request')->insertGetId(
          ['member_id' => $member_id, 'referral_id' => 0, 'email' => $member->user_email, 'reward_amount' => $member->bonus, 'phone' => 0, 'shopping_amount' => $total_shopping, 'req_date' => now(), 'req_status' => 0, 'req_type' => $req_type, 'reward_status' => 0]
        );
        DB::table('wpgc_users')
          ->where('id', $member_id)  // find your user by their id
          ->update(array('bonus_status' => 2));
      } else {
        return back()->with('success', 'Please make sure, you have shopping of minimum $250?');
      }
    } elseif ($parameters['reward_status'] == 'reward') {

      $userid = session()->get('user_id');
      $referral_id = $parameters['referral_id'];
      $member = DB::table('wpgc_users')->where('wpgc_users.ID', $userid)->first();
      $referral = DB::table('wpgc_users')->where('id', $referral_id)->first();
      $total_shopping = DB::table('orders')->where('OrderUserID', $referral_id)->sum('OrderAmount');
      // $referral->reward == null ? $referral->reward = 0 : $referral->reward = $referral->reward;
      $parameters['request_type'] == 'pay_bill' ? $req_type = 0 : $req_type = 1;

      if ($total_shopping >= 250) {
        $id = DB::table('member_withdraw_request')->insertGetId(
          ['member_id' => $member->ID, 'referral_id' => $referral->ID, 'email' => $member->user_email, 'reward_amount' => $referral->reward, 'phone' => 0, 'shopping_amount' => $total_shopping, 'req_date' => now(), 'req_status' => 0, 'req_type' => $req_type, 'reward_status' => 1]
        );

        DB::table('wpgc_users')
          ->where('id', $referral->ID)  // find your user by their id
          ->update(array('reward_status' => 2));
      } else {
        return back()->with('success', 'Please make sure, Your referral has shopping of $250?');
      }
    }
    return back()->with('success', 'Request successfully sent to Admin');
  }

  public function AffiliateReferral()
  {
    return view('affiliate_referral');
  }
  public function AffiliateIndex()
  {
    return view('affiliate');
  }
  public function AffiliateCodeVerify(Request $req)
  {
    $this->validate($req, [
      'email' => 'required | exists:affiliate_users,email',
    ]);

    $affiliate = DB::table('affiliate_users')->where('email', $req->email)->first();

    $digits = 4;
    $verification_code = str_pad(rand(0, pow(10, $digits) - 1), $digits, '0', STR_PAD_LEFT);
    session(['affiliate.verification_token' => $verification_code]);
    $template = '
        <style type="text/css">
           
          p { margin: 0; padding: 0; }
          table.wrapper {
            width:100% !important;
            table-layout: fixed;
            -webkit-font-smoothing: antialiased;
            -webkit-text-size-adjust: 100%;
            -moz-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
          }
          img.max-width {
            max-width: 100% !important;
          }
          .column.of-2 {
            width: 50%;
          }
          .column.of-3 {
            width: 33.333%;
          }
          .column.of-4 {
            width: 25%;
          }
          @media screen and (max-width:480px) {
            .preheader .rightColumnContent,
            .footer .rightColumnContent {
              text-align: left !important;
            }
            .preheader .rightColumnContent div,
            .preheader .rightColumnContent span,
            .footer .rightColumnContent div,
            .footer .rightColumnContent span {
              text-align: left !important;
            }
            .preheader .rightColumnContent,
            .preheader .leftColumnContent {
              font-size: 80% !important;
              padding: 5px 0;
            }
            table.wrapper-mobile {
              width: 100% !important;
              table-layout: fixed;
            }
            img.max-width {
              height: auto !important;
              max-width: 100% !important;
            }
            a.bulletproof-button {
              display: block !important;
              width: auto !important;
              font-size: 80%;
              padding-left: 0 !important;
              padding-right: 0 !important;
            }
            .columns {
              width: 100% !important;
            }
            .column {
              display: block !important;
              width: 100% !important;
              padding-left: 0 !important;
              padding-right: 0 !important;
              margin-left: 0 !important;
              margin-right: 0 !important;
            }
          }
        </style>
         
              
                <center class="wrapper" data-link-color="#1188E6" data-body-style="font-size:14px; font-family:inherit; color:#000000; background-color:#FFFFFF;">
                  <div class="webkit">
                    <table cellpadding="0" cellspacing="0" border="0" width="100%" class="wrapper" bgcolor="#FFFFFF">
                      <tbody><tr>
                        <td valign="top" bgcolor="#FFFFFF" width="100%">
                          <table width="100%" role="content-container" class="outer" align="center" cellpadding="0" cellspacing="0" border="0">
                            <tbody><tr>
                              <td width="100%">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                  <tbody><tr>
                                    <td>
                                      <!--[if mso]>
                                          <center>
                                          <table><tr><td width="600">
                                        <![endif]-->
                                              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; max-width:600px;" align="center">
                                                <tbody><tr>
                                                  <td role="modules-container" style="padding:0px 0px 0px 0px; color:#000000; text-align:left;" bgcolor="#FFFFFF" width="100%" align="left"><table class="module preheader preheader-hide" role="module" data-type="preheader" border="0" cellpadding="0" cellspacing="0" width="100%" style="display: none !important; mso-hide: all; visibility: hidden; opacity: 0; color: transparent; height: 0; width: 0;">
                                <tbody><tr>
                                  <td role="module-content">
                                    <p></p>
                                  </td>
                                </tr>
                              </tbody></table><table border="0" cellpadding="0" cellspacing="0" align="center" width="100%" role="module" data-type="columns" style="padding:30px 20px 30px 20px;" bgcolor="#f6f6f6">
                                <tbody>
                                  <tr role="module-content">
                                    <td height="100%" valign="top">
                                      <table class="column" width="540" style="width:540px; border-spacing:0; border-collapse:collapse; margin:0px 10px 0px 10px;" cellpadding="0" cellspacing="0" align="left" border="0" bgcolor="">
                                        <tbody>
                                          <tr>
                                            <td style="padding:0px;margin:0px;border-spacing:0;"><table class="module" role="module" data-type="spacer" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;" data-muid="331cde94-eb45-45dc-8852-b7dbeb9101d7">
              <tbody>
                <tr>
                  <td style="padding:0px 0px 20px 0px;" role="module-content" bgcolor="">
                  </td>
                </tr>
              </tbody>
            </table><table class="wrapper" role="module" data-type="image" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;" data-muid="d8508015-a2cb-488c-9877-d46adf313282">
              <tbody>
                <tr>
                  <td style="font-size:6px; line-height:10px; padding:0px 0px 0px 0px;" valign="top" align="center">
                    <img class="max-width" border="0" style="display:block; color:#000000; text-decoration:none; font-family:Helvetica, arial, sans-serif; font-size:16px;width: 30%;height: 35%;" width="95" alt="" data-proportionally-constrained="true" data-responsive="false" src="https://globalvoicedirect.com/public/images/gvdlogo.png" height="33">
                  </td>
                </tr>
              </tbody>
            </table><table class="module" role="module" data-type="spacer" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;" data-muid="27716fe9-ee64-4a64-94f9-a4f28bc172a0">
              <tbody>
                <tr>
                  <td style="padding:0px 0px 30px 0px;" role="module-content" bgcolor="">
                  </td>
                </tr>
              </tbody>
            </table><table class="module" role="module" data-type="text" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;" data-muid="948e3f3f-5214-4721-a90e-625a47b1c957" data-mc-module-version="2019-10-22">
              <tbody>
                <tr>
                  <td style="padding:50px 30px 18px 30px; line-height:36px; text-align:inherit; background-color:#ffffff;" height="100%" valign="top" bgcolor="#ffffff" role="module-content"><div><div style="font-family: inherit; text-align: center"><span style="font-size: 30px">Use Code To Access The Dashboard!&nbsp;</span></div><div></div></div></td>
                </tr>
                <tr>
                  <td style="text-align: center;background-color: white;">
                    <span style="font-size: 34px;color: #3183d7;">' . $verification_code . '</span>
                  </td>
                </tr>
              </tbody>
            </table><table class="module" role="module" data-type="text" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;" data-muid="a10dcb57-ad22-4f4d-b765-1d427dfddb4e" data-mc-module-version="2019-10-22">
              <tbody>
                <tr>
                  <td style="padding:18px 30px 18px 30px; line-height:22px; text-align:inherit; background-color:#ffffff;" height="100%" valign="top" bgcolor="#ffffff" role="module-content"><div><div style="font-family: inherit; text-align: center"><span style="font-size: 18px">Please verify your email address to</span><span style="color: #000000; font-size: 18px; font-family: arial,helvetica,sans-serif"> use 4-digit code</span><span style="font-size: 18px">.</span></div>
          <div style="font-family: inherit; text-align: center"><span style="color: #3183d7; font-size: 18px"><strong>Thank you!&nbsp;</strong></span></div><div></div></div></td>
                </tr>
              </tbody>
            </table><table class="module" role="module" data-type="spacer" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;" data-muid="7770fdab-634a-4f62-a277-1c66b2646d8d">
              <tbody>
                <tr>
                  <td style="padding:0px 0px 20px 0px;" role="module-content" bgcolor="#ffffff">
                  </td>
                </tr>
              </tbody>
            </table><table border="0" cellpadding="0" cellspacing="0" class="module" data-role="module-button" data-type="button" role="module" style="table-layout:fixed;" width="100%" data-muid="d050540f-4672-4f31-80d9-b395dc08abe1">
                <tbody>
                  <tr>
                    <td align="center" bgcolor="#ffffff" class="outer-td" style="padding:0px 0px 0px 0px;">
                      <table border="0" cellpadding="0" cellspacing="0" class="wrapper-mobile" style="text-align:center;">
                        <tbody>
                          <tr>
                          <td align="center" bgcolor="#ffbe00" class="inner-td" style="border-radius:6px; font-size:16px; text-align:center; background-color:inherit;">
                            <a href="https://globalvoicedirect.com/affiliate-auth" style="background-color:#3183d7; border:1px solid #3183d7; border-color:#3183d7; border-radius:0px; border-width:1px; color:#ffffff; display:inline-block; font-size:14px; font-weight:normal; letter-spacing:0px; line-height:normal; padding:12px 40px 12px 40px; text-align:center; text-decoration:none; border-style:solid; font-family:inherit;cursor:pointer" target="_blank">Verify Email Now</a>
                          </td>
                          </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                </tbody>
              </table><table class="module" role="module" data-type="spacer" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;" data-muid="7770fdab-634a-4f62-a277-1c66b2646d8d.1">
              <tbody>
                <tr>
                  <td style="padding:0px 0px 50px 0px;" role="module-content" bgcolor="#ffffff">
                  </td>
                </tr>
              </tbody>
            </table><table class="module" role="module" data-type="text" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;" data-muid="a265ebb9-ab9c-43e8-9009-54d6151b1600" data-mc-module-version="2019-10-22">
              <tbody>
                <tr>
                  <td style="padding:50px 30px 50px 30px; line-height:22px; text-align:inherit; background-color:#6e6e6e;" height="100%" valign="top" bgcolor="#6e6e6e" role="module-content"><div><div style="font-family: inherit; text-align: center;font-weight: 600;color: #f3f3f3;">GLOBAL VOICE DIRECT</td>
                </tr>
              </tbody>
            </table><table border="0" cellpadding="0" cellspacing="0" class="module" data-role="module-button" data-type="button" role="module" style="table-layout:fixed;" width="100%" data-muid="d050540f-4672-4f31-80d9-b395dc08abe1.1">
                <tbody>
                  <tr>
                    <td align="center" bgcolor="#6e6e6e" class="outer-td" style="padding:0px 0px 0px 0px;">
                      <table border="0" cellpadding="0" cellspacing="0" class="wrapper-mobile" style="text-align:center;">
                        <tbody>
                          <tr>
                          <td align="center" bgcolor="#ffbe00" class="inner-td" style="border-radius:6px; font-size:16px; text-align:center; background-color:inherit;">
                            <a href="" style="background-color:#3183d7; border:1px solid #3183d7; border-color:#3183d7; border-radius:0px; border-width:1px; color:#eff3f3; display:inline-block; font-size:14px; font-weight:normal; letter-spacing:0px; line-height:normal; padding:12px 40px 12px 40px; text-align:center; text-decoration:none; border-style:solid; font-family:inherit;" target="_blank">Contact Support</a>
                          </td>
                          </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                </tbody>
              </table><table class="module" role="module" data-type="spacer" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;" data-muid="c37cc5b7-79f4-4ac8-b825-9645974c984e">
              <tbody>
                <tr>
                  <td style="padding:0px 0px 30px 0px;" role="module-content" bgcolor="6E6E6E">
                  </td>
                </tr>
              </tbody>
            </table></td>
                        </tr>
                      </tbody>
                    </table>
                    
                  </td>
                </tr>
              </tbody>
            </table><div data-role="module-unsubscribe" class="module" role="module" data-type="unsubscribe" style="color:#444444; font-size:12px; line-height:20px; padding:16px 16px 16px 16px; text-align:Center;" data-muid="4e838cf3-9892-4a6d-94d6-170e474d21e5">
                                              <div class="Unsubscribe--addressLine"><p class="Unsubscribe--senderName" style="font-size:12px; line-height:20px;">© 2010 - 2020 GLOBAL VOICE DIRECT</p><p style="font-size:12px; line-height:20px;"></p></div>
                                            </div></td>
                                        </tr>
                                      </tbody></table>
                                      <!--[if mso]>
                                    </td>
                                  </tr>
                                </table>
                              </center>
                              <![endif]-->
                            </td>
                          </tr>
                        </tbody></table>
                      </td>
                    </tr>
                  </tbody></table>
                </td>
              </tr>
            </tbody></table>
          </div>
        </center>';

    $email_sender = "no-reply@globalvoicedirect.com";
    $subject = "Here is Your Verification Code";
    $recipient_email = $req->email;

    $body = $template;

        $__mailOk = true;
        try {
    \Mail::send(array(), array(), function ($message) use ($body, $recipient_email, $subject) {
      $message->to($recipient_email)
        ->subject($subject)
        ->from(config('mail.from.address'))
        ->html($body);
    });
        } catch (\Throwable $__e) {
            $__mailOk = false;
            \Log::error('Mail send failed in ' . __METHOD__ . ': ' . $__e->getMessage());
        }

    if ($__mailOk) {
      session('affiliate.id', $affiliate->id);
      return view('affiliate-member');
    } else {
      return redirect()->route('affiliate', ['error' => 'Something went wrong in sending email!']);
    }
    session('affilate.id', $affiliate->id);
  }
  public function AffiliateVerifyCode(Request $req)
  {
    $affiliate_code = session('affiliate.verification_token');
    $user_code = $req->digit1 . $req->digit2 . $req->digit3 . $req->digit4;
    $affiliateid = session('affilate.id');
    $affiliate_code = (int)$affiliate_code;

    if ($affiliate_code == $user_code) {

      $affiliateid = session('affilate.id');
      $affiliateusers = DB::table('affliate_users_referals')->join('wpgc_users', 'affliate_users_referals.user_id', '=', 'wpgc_users.ID')->select('affliate_users_referals.reward_status as rewards_status', 'affliate_users_referals.created_at', 'wpgc_users.user_nicename')->where('affliate_users_referals.affliate_user_id', $affiliateid)->get();
      $affiliateBalance = DB::table('affiliate_users_balance')->select('balance', 'affiliate_id')->where('affiliate_id', $affiliateid)->first();
      return view('affiliate-dashboard', compact('affiliateusers', 'affiliateBalance'));
    } else {
      return redirect()->route('affiliate', ['error' => 'Verification code is not validated!']);
    }
  }
  public function AffiliateDashboard(Request $req)
  {
    $this->validate($req, [
      'email' => 'required | exists:affiliate_users,email',
    ]);
    $affiliate = DB::table('affiliate_users')->where('email', $req->email)->first();
    if ($affiliate->is_active == 1) {
      session(['affiliateid' => $affiliate->id]);
      $affiliateusers = DB::table('affliate_users_referals')->join('wpgc_users', 'affliate_users_referals.user_id', '=', 'wpgc_users.ID')->select('affliate_users_referals.reward_status as rewards_status', 'affliate_users_referals.created_at', 'wpgc_users.user_nicename')->where('affliate_users_referals.affliate_user_id', $affiliate->id)->get();
      $affiliateBalance = DB::table('affiliate_users_balance')->select('balance', 'affiliate_id')->where('affiliate_id', $affiliate->id)->first();
      return view('affiliate-dashboard', compact('affiliateusers', 'affiliateBalance'));
    } else {
      return redirect()->back()->with('error', 'User is not activated');
    }
  }
  public function AffiliateWithdraw(Request $req)
  {

    if (isset($req->affiliate_id)) {
      $affiliateBalance = DB::table('affiliate_users_balance')->select('balance')->where('affiliate_id', $req->affiliate_id)->first();
      if ($affiliateBalance->balance >= 30) {
        $id = DB::table('affiliates_withdraw_requests')->insertGetId(
          ['balance' => $affiliateBalance->balance, 'affiliate_id' => $req->affiliate_id, 'status' => 0]
        );
        if ($id) {
          DB::table('affiliate_users_balance')
            ->where('affiliate_id', $req->affiliate_id)  // find your user by their id
            ->update(array('balance' => 0));  // update the record in the DB. 
          return redirect()->back()->with('message', 'Your request for withdraw balance has been received. We will review and complete it within 7 business days');
        } else {
          return redirect()->back()->with('error', 'Something went wrong. Please try again!');
        }
      } else {
        return redirect()->back()->with('error', 'Your balance is less than thrashold of 30. The request could not be complete');
      }
    } else {
      return redirect()->route('affiliate', ['error' => 'Sorry affiliate not found']);
    }
  }
  public function AffiliateDashboardIndex()
  {
    $affiliateid = session('affiliateid');

    if ($affiliateid) {
      $affiliateusers = DB::table('affliate_users_referals')->join('wpgc_users', 'affliate_users_referals.user_id', '=', 'wpgc_users.ID')->select('affliate_users_referals.reward_status as rewards_status', 'affliate_users_referals.created_at', 'wpgc_users.user_nicename')->where('affliate_users_referals.affliate_user_id', $affiliateid)->get();
      $affiliateBalance = DB::table('affiliate_users_balance')->select('balance', 'affiliate_id')->where('affiliate_id', $affiliateid)->first();
      return view('affiliate-dashboard', compact('affiliateusers', 'affiliateBalance'));
    } else {
      return redirect()->route('affiliate', ['error' => 'Sorry affiliate not found']);
    }
  }
  public function getAffiliateLink(Request $req)
  {

    $this->validate($req, [
      'name' => 'required',
      'email' => 'required | unique:affiliate_users,email',
    ]);

    $response = [];
    $name = $req->name;
    $email = $req->email;
    $phone = isset($req->phone) ? $req->phone : '';
    $id = DB::table('affiliate_users')->insertGetId(
      ['name' => $name, 'email' => $email, 'phone' => $phone]
    );
    DB::table('affiliate_users_balance')->insert(
      ['affiliate_id' => $id, 'balance' => 0, 'updated_at' => date("Y-m-d h:i:s")]
    );
    $getUser = DB::table('affiliate_users')->where('id', $id)->first();

    $affliateLink = 'https://globalvoicedirect.com/package-pricing?non_memberreferal_token=' . $getUser->token;

    DB::table('affiliate_users')
      ->where('id', $id)  // find your user by their id
      ->update(array('link' => $affliateLink));  // update the record in the DB. 

    $template = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html style="width:100%;font-family:helvetica, "helvetica neue", arial, verdana, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0;">
         <head> 
          <meta charset="UTF-8"> 
          <meta content="width=device-width, initial-scale=1" name="viewport"> 
          <meta name="x-apple-disable-message-reformatting"> 
          <meta http-equiv="X-UA-Compatible" content="IE=edge"> 
          <meta content="telephone=no" name="format-detection"> 
          <title>New email template 2020-04-04</title> 
          <!--[if (mso 16)]>
            <style type="text/css">
            a {text-decoration: none;}
            </style>
            <![endif]--> 
          <!--[if gte mso 9]><style>sup { font-size: 100% !important; }</style><![endif]--> 
          <!--[if !mso]><!-- --> 
          <link href="https://fonts.googleapis.com/css?family=Lato:400,400i,700,700i" rel="stylesheet"> 
          <!--<![endif]--> 
          <style type="text/css">
        @media only screen and (max-width:600px) {p, ul li, ol li, a { font-size:17px!important; line-height:150%!important } h1 { font-size:30px!important; text-align:center; line-height:120%!important } h2 { font-size:26px!important; text-align:left; line-height:120%!important } h3 { font-size:20px!important; text-align:left; line-height:120%!important } h1 a { font-size:30px!important; text-align:center } h2 a { font-size:20px!important; text-align:left } h3 a { font-size:20px!important; text-align:left } .es-menu td a { font-size:16px!important } .es-header-body p, .es-header-body ul li, .es-header-body ol li, .es-header-body a { font-size:16px!important } .es-footer-body p, .es-footer-body ul li, .es-footer-body ol li, .es-footer-body a { font-size:17px!important } .es-infoblock p, .es-infoblock ul li, .es-infoblock ol li, .es-infoblock a { font-size:12px!important } *[class="gmail-fix"] { display:none!important } .es-m-txt-c, .es-m-txt-c h1, .es-m-txt-c h2, .es-m-txt-c h3 { text-align:center!important } .es-m-txt-r, .es-m-txt-r h1, .es-m-txt-r h2, .es-m-txt-r h3 { text-align:right!important } .es-m-txt-l, .es-m-txt-l h1, .es-m-txt-l h2, .es-m-txt-l h3 { text-align:left!important } .es-m-txt-r img, .es-m-txt-c img, .es-m-txt-l img { display:inline!important } .es-button-border { display:inline-block!important } a.es-button { font-size:14px!important; display:inline-block!important; border-width:15px 25px 15px 25px!important } .es-btn-fw { border-width:10px 0px!important; text-align:center!important } .es-adaptive table, .es-btn-fw, .es-btn-fw-brdr, .es-left, .es-right { width:100%!important } .es-content table, .es-header table, .es-footer table, .es-content, .es-footer, .es-header { width:100%!important; max-width:600px!important } .es-adapt-td { display:block!important; width:100%!important } .adapt-img { width:100%!important; height:auto!important } .es-m-p0 { padding:0px!important } .es-m-p0r { padding-right:0px!important } .es-m-p0l { padding-left:0px!important } .es-m-p0t { padding-top:0px!important } .es-m-p0b { padding-bottom:0!important } .es-m-p20b { padding-bottom:20px!important } .es-mobile-hidden, .es-hidden { display:none!important } .es-desk-hidden { display:table-row!important; width:auto!important; overflow:visible!important; float:none!important; max-height:inherit!important; line-height:inherit!important } .es-desk-menu-hidden { display:table-cell!important } table.es-table-not-adapt, .esd-block-html table { width:auto!important } table.es-social { display:inline-block!important } table.es-social td { display:inline-block!important } }
        #outlook a {
          padding:0;
        }
        .ExternalClass {
          width:100%;
        }
        .ExternalClass,
        .ExternalClass p,
        .ExternalClass span,
        .ExternalClass font,
        .ExternalClass td,
        .ExternalClass div {
          line-height:100%;
        }
        .es-button {
          mso-style-priority:100!important;
          text-decoration:none!important;
          transition:all 100ms ease-in;
        }
        a[x-apple-data-detectors] {
          color:inherit!important;
          text-decoration:none!important;
          font-size:inherit!important;
          font-family:inherit!important;
          font-weight:inherit!important;
          line-height:inherit!important;
        }
        .es-desk-hidden {
          display:none;
          float:left;
          overflow:hidden;
          width:0;
          max-height:0;
          line-height:0;
          mso-hide:all;
        }
        </style> 
         </head> 
         <body style="width:100%;font-family:helvetica, "helvetica neue", arial, verdana, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0;"> 
          <div class="es-wrapper-color" style="background-color:#F1F1F1;"> 
           <!--[if gte mso 9]>
              <v:background xmlns:v="urn:schemas-microsoft-com:vml" fill="t">
                <v:fill type="tile" color="#f1f1f1"></v:fill>
              </v:background>
            <![endif]--> 
           <table class="es-wrapper" width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top;"> 
             <tr style="border-collapse:collapse;"> 
              <td valign="top" style="padding:0;Margin:0;">  
               <table class="es-header" cellspacing="0" cellpadding="0" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top;"> 
                 <tr style="border-collapse:collapse;"> 
                  <td align="center" style="padding:0;Margin:0;"> 
                   <table class="es-header-body" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;" width="600" cellspacing="0" cellpadding="0" bgcolor="#ffffff" align="center"> 
                     <tr style="border-collapse:collapse;"> 
                      <td style="Margin:0;padding-top:30px;padding-bottom:30px;padding-left:40px;padding-right:40px;background-color:#333333;" bgcolor="#333333" align="left"> 
                       <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                         <tr style="border-collapse:collapse;"> 
                          <td width="520" valign="top" align="center" style="padding:0;Margin:0;"> 
                           <table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                             <tr style="border-collapse:collapse;"> 
                              <td align="center" style="padding:0;Margin:0;font-size:0px;"><a href="https://globalvoicedirect.com/" target="_blank" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:helvetica, "helvetica neue", arial, verdana, sans-serif;font-size:14px;text-decoration:underline;color:#FFFFFF;"><img src="https://globalvoicedirect.com/public/newtemplate/images/gvdlogo.png" alt="Global Voice Direct" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;" width="127" title="Global Voice Direct"></a></td> 
                             </tr> 
                           </table></td> 
                         </tr> 
                       </table></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table> 
               <table class="es-content" cellspacing="0" cellpadding="0" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;"> 
                 <tr style="border-collapse:collapse;"> 
                  <td align="center" style="padding:0;Margin:0;"> 
                   <table class="es-content-body" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#333333;" width="600" cellspacing="0" cellpadding="0" bgcolor="#333333" align="center"> 
                     <tr style="border-collapse:collapse;"> 
                      <td style="Margin:0;padding-top:40px;padding-bottom:40px;padding-left:40px;padding-right:40px;background-image:url(https://excmsg.stripocdn.email/content/guids/CABINET_85e4431b39e3c4492fca561009cef9b5/images/29331521207075228.png);background-repeat:no-repeat;" align="left" background="https://excmsg.stripocdn.email/content/guids/CABINET_85e4431b39e3c4492fca561009cef9b5/images/29331521207075228.png"> 
                       <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                         <tr style="border-collapse:collapse;"> 
                          <td width="520" valign="top" align="center" style="padding:0;Margin:0;"> 
                           <table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                             <tr style="border-collapse:collapse;"> 
                              <td align="center" style="padding:0;Margin:0;padding-bottom:10px;color: #fff;"><h1 style="Margin:0;line-height:36px;mso-line-height-rule:exactly;font-family:arial, helvetica, helvetica, sans-serif;font-size:30px;font-style:normal;font-weight:bold;color:#FFFFFF;">Welcome to Referral Program</h1></td> 
                             </tr> 
                           </table></td> 
                         </tr> 
                       </table></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table> 
               <table class="es-content" cellspacing="0" cellpadding="0" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;"> 
                 <tr style="border-collapse:collapse;"> 
                  <td align="center" style="padding:0;Margin:0;"> 
                   <table class="es-content-body" width="600" cellspacing="0" cellpadding="0" bgcolor="#ffffff" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;"> 
                     <tr style="border-collapse:collapse;"> 
                      <td align="left" style="padding:0;Margin:0;padding-top:40px;padding-left:40px;padding-right:40px;"> 
                       <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                         <tr style="border-collapse:collapse;"> 
                          <td width="520" valign="top" align="center" style="padding:0;Margin:0;"> 
                           <table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                             <tr style="border-collapse:collapse;"> 
                              <td align="left" style="padding:0;Margin:0;padding-top:5px;padding-bottom:15px;"><h2 style="Margin:0;line-height:24px;mso-line-height-rule:exactly;font-family:lato, helvetica, helvetica, arial, sans-serif;font-size:20px;font-style:normal;font-weight:bold;color:#333333;">Hi ' . $name . ',</h2></td> 
                             </tr> 
                             <tr style="border-collapse:collapse;"> 
                              <td align="left" style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px;"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:15px;font-family:helvetica , arial, verdana, sans-serif;line-height:23px;color:#555555;">Thank you to be a part of our non member referral program. </p><p>Let help us to grow our customers and get rewards of $30 for each sign up.</p></td> 
                             </tr>
                             <tr><td>
                               <label>Your Referral Link:</label>
                               <input style="height: 28px;width: 100%;margin-top: 11px;border-radius: 5px;border-color: transparent;background-color: #fbfbfb;box-shadow: 0px 2px 3px #a6c8d2;color: #929292;font-size: 17px;" type="text" name="referral-link" value="' . $affliateLink . '" id="referral_link">                         
                             </td>
                             <td></td>
                             </tr>
                             <tr>
                               <td><br>Use this link to share to your friends and family.</td>
                             </tr> 
                             <tr style="border-collapse:collapse;"> 
                              <td align="left" style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px;"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:15px;font-family:helvetica, arial, verdana, sans-serif;line-height:23px;color:#555555;">Regards,</p></td> 
                             </tr> 
                           </table></td> 
                         </tr> 
                       </table></td> 
                     </tr> 
                     <tr style="border-collapse:collapse;"> 
                      <td align="left" style="Margin:0;padding-top:10px;padding-bottom:40px;padding-left:40px;padding-right:40px;"> 
                       <table cellspacing="0" cellpadding="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                         <tr style="border-collapse:collapse;"> 
                          <td width="520" align="left" style="padding:0;Margin:0;"> 
                           <table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                             <tr style="border-collapse:collapse;"> 
                              <td align="left" style="padding:0;Margin:0;padding-top:10px;"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:14px;font-family:helvetica, arial, verdana, sans-serif;line-height:21px;color:#222222;">Customer Support Team</p></td> 
                             </tr> 
                             <tr style="border-collapse:collapse;"> 
                              <td align="left" style="padding:0;Margin:0;"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:14px;font-family:helvetica, arial, verdana, sans-serif;line-height:21px;color:#666666;">Global Voice Direct</p></td> 
                             </tr> 
                           </table></td> 
                         </tr> 
                       </table></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table> 
               <table class="es-content" cellspacing="0" cellpadding="0" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;"> 
                 <tr style="border-collapse:collapse;"> 
                  <td align="center" style="padding:0;Margin:0;"> 
                   <table class="es-content-body" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#26A4D3;" width="600" cellspacing="0" cellpadding="0" bgcolor="#26a4d3" align="center"> 
                     <tr style="border-collapse:collapse;"> 
                      <td style="Margin:0;padding-bottom:20px;padding-top:40px;padding-left:40px;padding-right:40px;background-color:#26A4D3;" bgcolor="#26a4d3" align="left"> 
                       <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                         <tr style="border-collapse:collapse;"> 
                          <td width="520" valign="top" align="center" style="padding:0;Margin:0;"> 
                           <table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                             <tr style="border-collapse:collapse;"> 
                              <td class="es-m-txt-c" align="center" style="padding:0;Margin:0;color:#FFFFFF;"><h2 style="Margin:0;line-height:24px;mso-line-height-rule:exactly;font-family:lato, helvetica, arial, sans-serif;font-size:20px;font-style:normal;font-weight:bold;color:#FFFFFF;">GLOBAL VOICE DIRECT</h2></td> 
                             </tr> 
                           </table></td> 
                         </tr> 
                       </table></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table> 
               <table class="es-content" cellspacing="0" cellpadding="0" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;"> 
                 <tr style="border-collapse:collapse;"> 
                  <td align="center" style="padding:0;Margin:0;"> 
                   <table class="es-content-body" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#292828;" width="600" cellspacing="0" cellpadding="0" bgcolor="#292828" align="center"> 
                     <tr style="border-collapse:collapse;"> 
                      <td align="left" style="Margin:0;padding-top:30px;padding-bottom:30px;padding-left:40px;padding-right:40px;"> 
                       <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                         <tr style="border-collapse:collapse;"> 
                          <td width="520" valign="top" align="center" style="padding:0;Margin:0;"> 
                           <table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                             <tr style="border-collapse:collapse;"> 
                              <td align="center" style="padding:0;Margin:0;font-size:0;"> 
                               <table class="es-table-not-adapt es-social" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                                 <tr style="border-collapse:collapse;"> 
                                  <td valign="top" align="center" style="padding:0;Margin:0;padding-right:10px;"><img title="Facebook" src="https://excmsg.stripocdn.email/content/assets/img/social-icons/circle-white/facebook-circle-white.png" alt="Fb" width="24" height="24" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;"></td> 
                                  <td valign="top" align="center" style="padding:0;Margin:0;padding-right:10px;"><img title="Twitter" src="https://excmsg.stripocdn.email/content/assets/img/social-icons/circle-white/twitter-circle-white.png" alt="Tw" width="24" height="24" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;"></td> 
                                  <td valign="top" align="center" style="padding:0;Margin:0;padding-right:10px;"><img title="Instagram" src="https://excmsg.stripocdn.email/content/assets/img/social-icons/circle-white/instagram-circle-white.png" alt="Inst" width="24" height="24" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;"></td> 
                                  <td valign="top" align="center" style="padding:0;Margin:0;"><img title="Linkedin" src="https://excmsg.stripocdn.email/content/assets/img/social-icons/circle-white/linkedin-circle-white.png" alt="In" width="24" height="24" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;"></td> 
                                 </tr> 
                               </table></td> 
                             </tr> 
                           </table></td> 
                         </tr> 
                       </table></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table> 
               <table class="es-content" cellspacing="0" cellpadding="0" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;"> 
                 <tr style="border-collapse:collapse;"></tr> 
                 <tr style="border-collapse:collapse;"> 
                  <td align="center" style="padding:0;Margin:0;"> 
                   <table class="es-footer-body" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;" width="600" cellspacing="0" cellpadding="0" bgcolor="#ffffff" align="center"> 
                     <tr style="border-collapse:collapse;"> 
                      <td align="left" style="Margin:0;padding-top:40px;padding-bottom:40px;padding-left:40px;padding-right:40px;"> 
                       <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                         <tr style="border-collapse:collapse;"> 
                          <td width="520" valign="top" align="center" style="padding:0;Margin:0;"> 
                           <table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">  
                             <tr style="border-collapse:collapse;"> 
                              <td align="center" style="padding:0;Margin:0;"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:12px;font-family:helvetica, arial, verdana, sans-serif;line-height:18px;color:#666666;">Copyright © 2020&nbsp;<strong>Global Voice Direct</strong>, All Rights Reserved.</p></td> 
                             </tr> 
                           </table></td> 
                         </tr> 
                       </table></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table></td> 
             </tr> 
           </table> 
          </div>
         
         </body>
        </html>';
    $email_sender = "no-reply@globalvoicedirect.com";
    $subject = "Non Member Referral Signup";
    $recipient_email = $email;

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
    $headers .= "From: {$name} <{$email_sender}> \n";

    $body = $template;

    \Mail::send(array(), array(), function ($message) use ($body, $recipient_email, $subject) {
      $message->to($recipient_email)
        ->subject($subject)
        ->from(config('mail.from.address'))
        ->html($body);
    });
    return Response::json($affliateLink);
  }
  public function getnonMemberAffiliateLink(Request $req)
  {

    $this->validate($req, [
      'email' => 'required | exists:affiliate_users,email',
    ]);

    $email = $req->email;

    $getUser = DB::table('affiliate_users')->where('email', $email)->first();

    $affliateLink = $getUser->link;

    return Response::json($affliateLink);
  }

  public function loginAction(Request $req)
  {

    if (\DB::table('wpgc_users')->where('user_login', $req->user)->exists()) {
      $email_activation_status = $this->checkUserStatus($req->user);
      $user_active_status = $this->checkUserActiveStatus($req->user);
      if ($email_activation_status == false) {

        return redirect()->route('user-login', ['message' => 'Your account is not activated. Please visit your email and activate your account first']);
      } elseif ($user_active_status == false) {
        return redirect()->route('user-login', ['message' => 'Your account is blocked. Please contact to support center']);
      }
      $userData = \DB::table('wpgc_users')->where('user_login', $req->user)->first();
      //match password
      if (Hash::check($req->pass, $userData->user_pass)) {


        /*dd(Auth::loginUsingId($userData->ID));*/
        Auth::loginUsingId($userData->ID, $req->boolean('remember'));
        $userDet = \DB::table('wpgc_users')->where('ID', Auth::user()->ID)->first();
        // $accountDet = \DB::table('Account_details')->where('user_id', Auth::user()->ID)->first();
        $balenceDet = \DB::table('user_balance')->where('user_id', Auth::user()->ID)->first();
        $payablebalanceDet = \DB::table('payable_amount')->where('user_id', Auth::user()->ID)->first();
        $billingDet = \DB::table('user_shipping_address')->where('user_id', Auth::user()->ID)->first();
        //dd($userDet);
        /*$userDetails = \DB::table('wpgc_users')
                            ->join('Account_details', 'wpgc_users.ID', '=', 'Account_details.user_id')
                            ->join('user_balance', 'wpgc_users.ID', '=', 'user_balance.user_id')
                            ->join('user_shipping_address', 'wpgc_users.ID', '=', 'user_shipping_address.user_id')
                            ->where('wpgc_users.ID','=', $userData->ID)->get();
            dd($userDetails);*/
        //dd($userData->user_nicename);
        session()->put('nice_name', Auth::user()->user_nicename);
        session()->put('user_id', Auth::user()->ID);
        session()->put('user_email', Auth::user()->user_email);
        session()->put('user_login', true);
        $currentYear = Date('Y');
        $creditCardYears = [];
        for ($i = 0; $i < 10; $i++) {
          $creditCardYears[$i] = $currentYear + $i;
        }
        // 2026-11-02 oauth: back to the AI Connect consent screen after signing in
        $oret = (string) session()->pull('oauth_return', '');
        if ($oret !== '' && str_starts_with($oret, '/oauth/authorize?') && config('ai_connect.oauth_enabled')) return redirect($oret);
        return redirect('user/profile');
        //return view('users.profile', compact('userDet', 'balenceDet', 'payablebalanceDet', 'billingDet', 'creditCardYears'));

      } else {
        if ($voUser = $this->virtualOfficeLogin($req)) { return $voUser; }
        return redirect()->route('user-login', ['message' => 'You provide wrong Credentials']);
      }
    } else {
      if ($voUser = $this->virtualOfficeLogin($req)) { return $voUser; }
        return redirect()->route('user-login', ['message' => 'You provide wrong Credentials']);
    }
    // app/config/auth.php
  }
  /** Virtual Office fallback: only runs after the normal check failed. */
  private function virtualOfficeLogin(Request $req)
  {
    if (!config('portal.vo_login_enabled')) return null;
    $uid = \App\Portal\VirtualOfficeLogin::attempt((string) $req->user, (string) $req->pass, (string) $req->ip());
    if (!$uid) return null;
    $u = \DB::table('wpgc_users')->where('ID', $uid)->first();
    if (!$u) return null;
    Auth::loginUsingId($u->ID, $req->boolean('remember'));
    session()->put('nice_name', $u->user_nicename);
    session()->put('user_id', $u->ID);
    session()->put('user_email', $u->user_email);
    session()->put('user_login', true);
    if (\App\Portal\VirtualOfficeLogin::$kept) session()->put('gvd_billing', \Illuminate\Support\Facades\Crypt::encryptString(json_encode(\App\Portal\VirtualOfficeLogin::$kept)));
    else session()->forget('gvd_billing');
    return redirect('user/profile');
  }
  public function logout()
  {

    session()->forget(['user_id', 'nice_name', 'user_login', 'gvd_billing']);
    Auth::logout(); // also clears the Remember me cookie


    return redirect('/');
  }
  public function profile()
  {
    // dd('reach');
    $userDet = \DB::table('wpgc_users')->where('ID', session()->get('user_id'))->first();
    //   $accountDet = \DB::table('Account_details')->where('user_id',session()->get('user_id'))->first();
    $balenceDet = \DB::table('user_balance')->where('user_id', session()->get('user_id'))->first();
    $payablebalanceDet = \DB::table('payable_amount')->where('user_id', session()->get('user_id'))->first();
    $billingDet = \DB::table('user_shipping_address')->where('user_id', session()->get('user_id'))->first();

    $currentYear = Date('Y');
    $creditCardYears = [];
    for ($i = 0; $i < 10; $i++) {
      $creditCardYears[$i] = $currentYear + $i;
    }

    return view('users.profile', compact('userDet', 'balenceDet', 'payablebalanceDet', 'billingDet', 'creditCardYears'));
  }
  public function checkUserStatus($username)
  {
    if (\DB::table('wpgc_users')->where('user_status', 1)->where('user_login', $username)->exists()) {
      return true;
    } else {
      return false;
    }
  }
  public function checkUserActiveStatus($username)
  {
    if (\DB::table('wpgc_users')->where('active', 0)->where('user_login', $username)->exists()) {
      return true;
    } else {
      return false;
    }
  }
  public function resetPassword(Request $req)
  {
    if (\DB::table('wpgc_users')->where('user_email', $req->email)->exists()) {
      $activationKey = sha1(mt_rand(10000, 99999) . time() . $req->email);
      \DB::table('wpgc_users')->where('user_email', $req->email)->update(['user_activation_key' => $activationKey]);
      $userDetail = \DB::table('wpgc_users')->where('user_email', $req->email)->first();
      /*Email Body*/
      $verificationLink = rtrim(config('app.url'), '/') . "/setNewPassword?code=" . $activationKey . "&em=" . $userDetail->user_email;

      $htmlStr = "";
      $htmlStr .= "Hi " . $userDetail->display_name . ",<br /><br />";

      $htmlStr .= "Please click the button below to reset your password.<br /><br /><br />";
      $htmlStr .= "<a href='{$verificationLink}' target='_blank' style='padding:1em; font-weight:bold; background-color:blue; color:#fff;'>VERIFY EMAIL</a><br /><br /><br />";

      $htmlStr .= "Kind regards,<br />";
      $htmlStr .= "<a href='https://globalvoicedirect.com' target='_blank'>Globalvoicedirect Team</a><br />";
      // dd($htmlStr);
      /*end here*/
      /*send email*/
      $name = "";
      $email_sender = "no-reply@globalvoicedirect.com";
      $subject = "Reset Password";
      $recipient_email = $userDetail->user_email;


      $body = $htmlStr;
      $email = $recipient_email;
      $mailOk = true;
      try {
      \Mail::send(array(), array(), function ($message) use ($htmlStr, $email) {
        $message->to($email)
          ->subject('Reset Password')
          ->from(config('mail.from.address'))
          ->html($htmlStr);
      });
      } catch (\Throwable $e) {
        $mailOk = false;
        \Log::error('Reset password mail failed: ' . $e->getMessage());
      }
      if ($mailOk) {
        return redirect()->route('forgotpassword', ['message' => 'Please Visit your Email for Further Process.']);
      } else {
        return redirect()->route('forgotpassword', ['message' => 'Something Went Wrong']);
      }
      /*end here*/
    } else {
      return redirect()->route('forgotpassword', ['message' => 'This Email is not Registered']);
    }
    //dd($req);
  }
  public function setNewPassword(Request $req)
  {

    if (isset($req->code) and isset($req->em)) {

      if (\DB::table('wpgc_users')->where('user_email', $req->em)->where('user_activation_key', $req->code)->exists()) {
        session()->flash('message', 'Reset Your Password');
        return view('setNewPassword');
      } else {

        session()->flash('message', 'Something Went Wrong');
        return view('setNewPassword');
      }
    }
  }
  public function act_resetnewPassword(Request $req)
  {
    if ($req->password == $req->repassword) {
      $status = \DB::table('wpgc_users')->where('user_email', $req->email)->where('user_activation_key', $req->token)->update(['user_pass' => bcrypt($req->password)]);
      if ($status == true) {
        return redirect()->route('main', ['message' => 'Your Password is updated kindly go login again']);
      } else {
        session()->flash('message', 'Something went wrong');
        return view('setNewPassword');
      }
    } else {
      session()->flash('message', 'Password Does not Match');
      return view('setNewPassword');
    }


    /* "password" => "REDACTED"
            "repassword" => "REDACTED"
            "recover-submit" => "REDACTED"
            "email" => "REDACTED"
            "token" => "REDACTED"*/
    //dd($req);
  }

  public function signupaction(Request $request)
  {
      if (session_status() === PHP_SESSION_NONE) {
          session_start();
      }
      $requestObjForErrors = $request;

      $nicename = $request->nicename;
      $display_name = $request->display_name;
      $user_name = $request->user_name;
      $email = $request->email;
      $user_url = $request->user_url;
      $password = $request->password;
      $password_confirmation = $request->password_confirmation;
      $cardNumber = $request->cardNumber;
      $cardExpiryMonth = $request->month;
      $cardExpiryYear = $request->year;
      $cardCVC = $request->cardCVC;
      $user_ip = request()->ip();
      $fileName = '';
      $fileNameat = '';
      $didInfo = [];

      // Referral tokens
      $referal_token = $_SESSION['referal_token'] ?? null;
      $non_member_referral_token = $request->non_memberreferal_token ?? $_SESSION['non_memberreferal_token'] ?? null;

      // Check if SelectNumberInfo is set
      if (isset($_SESSION['SelectNumberInfo'])) {
          $didInfo = $_SESSION['SelectNumberInfo'];
      } else {
          $msg = 'Please Select the Phone Package And Try Again.';
          if ($referal_token) return redirect()->route('credit-apps', ['message' => $msg, 'referal_token' => $referal_token]);
          elseif ($non_member_referral_token) return redirect()->route('credit-apps', ['message' => $msg, 'non-memberreferal_token' => $non_member_referral_token]);
          else return redirect()->route('credit-apps', ['message' => $msg]);
      }

      // Validate required fields before touching the DB or charging a card
      $redirect_back = false;
      $validator = \Validator::make($request->all(), [
          'email' => 'required|email',
          'password' => env('TELINTA_SIGNUP_PASSWORD'),
          'user_name' => 'required|string',
          'nicename' => 'required|string',
          'display_name' => 'required|string',
      ]);

      if ($validator->fails()) {
          $redirect_back = true;
          $reason = $validator->errors()->first();
      } elseif (\DB::table('wpgc_users')->where('user_email', $email)->exists()) {
          $redirect_back = true;
          $reason = 'This Email Already Registered';
      } elseif (\DB::table('wpgc_users')->where('user_login', $user_name)->exists()) {
          $redirect_back = true;
          $reason = 'UserName Already Present';
      }

  redirect:
      if ($redirect_back) {
          \DB::table('unsuccess_membership')->insert([
              'display_name' => $nicename,
              'email' => $email,
              'firstname' => $requestObjForErrors->display_name,
              'lastname' => $requestObjForErrors->user_name,
              'company_name' => $requestObjForErrors->companyname,
              'address' => $requestObjForErrors->address1,
              'phone' => $requestObjForErrors->bphone,
              'city' => $requestObjForErrors->city,
              'state' => $requestObjForErrors->state,
              'zip' => $requestObjForErrors->zip,
              'reason' => $reason,
              'phone_number' => $didInfo['number'] ?? '',
              'phone_state' => '',
              'phone_ratecenter' => $requestObjForErrors->phone_ratecenter ?? '',
              'phone_pkg' => $didInfo['package'] ?? ''
          ]);

          if ($referal_token) return redirect()->route('credit-apps', ['message' => $reason, 'referal_token' => $referal_token]);
          elseif ($non_member_referral_token) return redirect()->route('credit-apps', ['message' => $reason, 'non-memberreferal_token' => $non_member_referral_token]);
          else return redirect()->route('credit-apps', ['message' => $reason]);
      }

      // Handle signature upload
      if (isset($request->signature_img)) {
          $img = str_replace(['data:image/png;base64,', ' '], ['', '+'], $request->signature_img);
          $fileData = base64_decode($img);
          $ext = 'png';
          $date = new DateTime();
          $imgname = $date->getTimestamp();
          $targetfolder = "public/userSignatureImages/{$imgname}.png";

          if (file_put_contents($targetfolder, $fileData)) {
              $fileName = "userSignatureImages/{$imgname}.{$ext}";
              $fileNameat = "public/userSignatureImages/{$imgname}.{$ext}";
          } else {
              $redirect_back = true;
              $reason = 'Signature Not Uploaded. Please try again';
              goto redirect;
          }
      }
        
      $client_ip = request()->ip();
      
      $REMOTE_ADDR_ips = ['154.80.15.250', '139.135.36.160', '110.39.14.26', '154.81.239.162'];
      
      // Determine payment gateway (Stripe or Authorize.Net) from DB
      $payment_gateway = DB::table('payment_gateways')
          ->where('is_active', 1)
          ->value('gateway_name');
      $payment_gateway = $payment_gateway ? strtolower(trim($payment_gateway)) : $payment_gateway;

      if ($payment_gateway === 'stripe') {
          if (in_array($client_ip, $REMOTE_ADDR_ips)) {
              \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
          } else {
              \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
          }

          try {
              $paymentIntentData = [
                  'amount' => 14900, // $149.00
                  'currency' => 'usd',
                  'description' => 'Global Voice Direct Subscription',
                  'receipt_email' => $email,
                  'payment_method_types' => ['card'],
              ];

              if (!empty($request->stripe_payment_method_id)) {
                  $paymentIntentData['payment_method'] = $request->stripe_payment_method_id;
                  $paymentIntentData['confirmation_method'] = 'manual';
                  $paymentIntentData['confirm'] = true;
              }

              $charge = \Stripe\PaymentIntent::create($paymentIntentData);

              if ($charge->status !== 'succeeded') {
                  $redirect_back = true;
                  $reason = substr('Stripe Payment Failed: ' . $charge->status, 0, 255);
                  goto redirect;
              }

              $transId = $charge->id;
              $authcode = $charge->charges->data[0]->balance_transaction ?? '';
              $trans_amount = 149.00;
              $created_at = date('Y-m-d H:i:s');

          } catch (\Stripe\Exception\ApiErrorException $e) {
              $redirect_back = true;
              $reason = substr('Stripe Payment Error: ' . $e->getMessage(), 0, 255);
              goto redirect;
          }

      } else {
          // Authorize.Net setup
          $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();

          if (in_array($client_ip, $REMOTE_ADDR_ips)) {
              $merchantAuthentication->setName('77Np2yKDr');
              $merchantAuthentication->setTransactionKey('5cYDE94TN4Zpw45z');
              $anetEnv = \net\authorize\api\constants\ANetEnvironment::SANDBOX;
          } else {
              $merchantAuthentication->setName(config('services.authorize.login'));
              $merchantAuthentication->setTransactionKey(config('services.authorize.key'));
              $anetEnv = \net\authorize\api\constants\ANetEnvironment::PRODUCTION;
          }

          $refId = 'ref' . time();
          $creditCard = new AnetAPI\CreditCardType();
          $creditCard->setCardNumber($cardNumber);
          $creditCard->setExpirationDate("{$cardExpiryYear}-{$cardExpiryMonth}");

          $paymentOne = new AnetAPI\PaymentType();
          $paymentOne->setCreditCard($creditCard);

          $customer = new AnetAPI\CustomerDataType();
          $customer->setEmail($email);

          $billto = new AnetAPI\CustomerAddressType();
          $billto->setFirstName($request->fname);
          $billto->setLastName($request->lname);
          $billto->setCompany($request->companyname);
          $billto->setAddress($request->address1);
          $billto->setCity($request->city);
          $billto->setState($request->state);
          $billto->setZip($request->zip);
          $billto->setCountry($request->country);
          $billto->setPhoneNumber($request->bphone);

          $transactionRequestType = new AnetAPI\TransactionRequestType();
          $transactionRequestType->setTransactionType('authCaptureTransaction');
          $transactionRequestType->setAmount(149.00);
          $transactionRequestType->setPayment($paymentOne);
          $transactionRequestType->setCustomer($customer);
          $transactionRequestType->setBillTo($billto);

          $anetRequest = new AnetAPI\CreateTransactionRequest();
          $anetRequest->setMerchantAuthentication($merchantAuthentication);
          $anetRequest->setRefId($refId);
          $anetRequest->setTransactionRequest($transactionRequestType);

          $controller = new AnetController\CreateTransactionController($anetRequest);
          $response = $controller->executeWithApiResponse($anetEnv);

          if ($response && $response->getTransactionResponse()) {
              $tresponse = $response->getTransactionResponse();
              if (!in_array($tresponse->getResponseCode(), ['1','3','4'])) {
                  $redirect_back = true;
                  $reason = 'Authorize.Net Payment Failed';
                  goto redirect;
              }
              $transId = $tresponse->getTransId();
              $authcode = $tresponse->getAuthCode();
              $trans_amount = 149.00;
              $created_at = date('Y-m-d H:i:s');
          } else {
              $redirect_back = true;
              $reason = 'Authorize.Net Payment Error';
              goto redirect;
          }
      }

      // --- Registration & Referral Logic ---
      $activationKey = sha1(mt_rand(10000, 99999) . time() . $email);
      $userReg = date('Y-m-d H:i:s');
      $password = bcrypt($password);

      $bonus_for_referral = ($referal_token || $non_member_referral_token) ? 20 : 0;

      $userId = \DB::table('wpgc_users')->insertGetId([
          'user_login' => $user_name,
          'user_pass' => $password,
          'user_nicename' => $nicename,
          'reward' => $bonus_for_referral,
          'bonus_status' => 0,
          'user_email' => $email,
          'user_url' => $user_url,
          'user_registered' => $userReg,
          'user_activation_key' => $activationKey,
          'display_name' => $display_name,
          'salt' => 'bacrypt_laravel'
      ]);

      \DB::table('transactions')->insert([
          'transaction_id'      => $transId,
          'authentication_code' => $authcode,
          'transaction_amount'  => $trans_amount,
          'payment_gateway'     => $payment_gateway,
          'created_at'          => $created_at,
          'user_id'             => $userId
      ]);

      \DB::table('user_balance')->insert(['user_id' => $userId, 'balance' => 0.0]);

      // Shipping info
      \DB::table('user_shipping_address')->insert([
          'user_id' => $userId,
          'firstname' => $request->fname,
          'lastname' => $request->lname,
          'company_name' => $request->companyname,
          'address' => $request->address1,
          'phone' => $request->bphone,
          'city' => $request->city,
          'state' => $request->state,
          'zip' => $request->zip
      ]);

      // Referrals Processing
      if ($referal_token) {
          $referrer = \DB::table('wpgc_users')->where('referrer_token', $referal_token)->first();
          if ($referrer) {
              \DB::table('user_referrer')->insert([
                  'referrer_id' => $referrer->id,
                  'referral_id' => $userId,
                  'reward_status' => 0,
                  'updated_at' => now()
              ]);
          }
      } elseif ($non_member_referral_token) {
          $non_mem = \DB::table('affiliate_users')->where('token', $non_member_referral_token)->first();
          if ($non_mem) {
              \DB::table('affliate_users_referals')->insert([
                  'affliate_user_id' => $non_mem->id,
                  'user_id' => $userId,
                  'reward_status' => 1
              ]);
              $balance = \DB::table('affiliate_users_balance')->where('affiliate_id', $non_mem->id)->first();
              if ($balance) {
                  \DB::table('affiliate_users_balance')->where('affiliate_id', $non_mem->id)->update(['balance' => $balance->balance + 20]);
              }
          }
      }

      // Legal info & transactional emails
      \DB::table('legal_signup_info')->insert(['user_id' => $userId, 'esign_path' => $fileName, 'public_ip_address' => $request->ip()]);

      $verificationLink = rtrim(config('app.url'), '/') . "/activate?code={$activationKey}&id={$userId}";

      $htmlStr = "Hi {$nicename},<br /><br />Please click the button below to verify your subscription and have access to the globalvoicedirect.com.<br /><br />";
      $htmlStr .= "<a href='{$verificationLink}' target='_blank' style='padding:1em; font-weight:bold; background-color:blue; color:#fff;'>VERIFY EMAIL</a><br /><br />";
      $htmlStr .= "Kind regards,<br /><a href='https://globalvoicedirect.com/' target='_blank'>Globalvoicedirect Team</a><br />";
      
      try {
      
        \Mail::send([], [], function ($message) use ($htmlStr, $email) {
          $message->to($email)->subject('Verification Link')->from(config('mail.from.address'), 'Verification')->html($htmlStr);
      });
      
      } catch (\Throwable $e) {
      
        \Log::error('Signup Verification mail failed: ' . $e->getMessage());
      
      }

      $Signaturepath = 'https://globalvoicedirect.com/' . $fileNameat;
      $htmlStr = "Hi {$nicename},<br /><br><h3>Welcome!</h3><br>Your legal information:<br>";
      $htmlStr .= "<b>eSign:</b> <img src='{$Signaturepath}' /><br>IP Address: {$user_ip}<br>Joining Date: " . date('Y/m/d H:i:s');
      $htmlStr .= "<br><br>Kind regards,<br />Globalvoicedirect Team";

      try {

        \Mail::send([], [], function ($message) use ($htmlStr, $email) {
          $message->to($email)->subject('Legal Information')->from(config('mail.from.address'), 'Global Voice Direct')->html($htmlStr);
      });

      } catch (\Throwable $e) {

        \Log::error('Signup Legal mail failed: ' . $e->getMessage());

      }

      // ============================================================
      // RESTORED: Core Provisioning API & Remote Telecom Integrations
      // ============================================================
      if (isset($_SESSION["SelectNumberInfo"])) {
          $didInfo = $_SESSION["SelectNumberInfo"];
          $numbers = json_decode($didInfo['number']);
          
          // Canonical Target Endpoint URL to prevent 301 loop exceptions
          $url = 'https://register.globalvoicedirect.com/signup?task=submit&lang=en';
          $token = substr(md5(mt_rand() * time()), 0, 20);
          $i_password = substr(str_shuffle(md5(time())), 0, 5);
          $h323_password = substr(str_shuffle(md5(time())), 0, 10);

          $product_id = '40281';
          $account_id = 0;
          $customer_id = $this->create_customer($nicename, $email);

          if ($customer_id != 0) {
              $account_id = $this->create_account($customer_id, $product_id, $email, $h323_password, $user_name, $i_password);
          }

          // Standard dynamic fallback lookup array construction matching your sizing logic
          if (count($numbers) == 1) {
              $data = [
                  'Revision' => '2024-06-13 22090',
                  'package' => $didInfo['package'],
                  'pack' => '0',
                  'firstname' => $_POST['fname'] ?? '',
                  'lastname' => $_POST['lname'] ?? '',
                  'email' => $_POST['email'] ?? '',
                  'country' => $_POST['country'] ?? '',
                  'state' => $_POST['state'] ?? '',
                  'city' => $_POST['city'] ?? '',
                  'zip' => $_POST['zip'] ?? '',
                  'address' => $_POST['address1'] ?? '',
                  'companyname' => $_POST['companyname'] ?? '',
                  'salutation' => '', 'midinit' => '', 'phone1' => '', 'cont1' => '', 'fax' => '', 'phone2' => '', 'cont2' => '', 'i_time_zone' => '',
                  'payment_method_select' => '0',
                  'cc_number' => $cardNumber,
                  'cc_month' => $cardExpiryMonth,
                  'cc_year' => $cardExpiryYear,
                  'cc_cvv' => $cardCVC,
                  'cc_name' => $request->nameOnCard ?? '',
                  'note' => '',
                  'number' => $numbers[0],
                  'did_api[package]' => 'DIDAPI::ThinQ',
                  'did_api[city_id]' => '',
                  'did_api[ratecenter]' => '478',
                  'did_api[country]' => 'US',
                  'did_api[monthly]' => '',
                  'did_api[state]' => 'GA',
                  'subscription_token' => $token
              ];
          } elseif (count($numbers) == 2) {
              $data = [
                  'package' => $didInfo['package'],
                  'pack' => '0',
                  'firstname' => $_POST['fname'] ?? '',
                  'lastname' => $_POST['lname'] ?? '',
                  'email' => $_POST['email'] ?? '',
                  'country' => $_POST['country'] ?? '',
                  'state' => $_POST['state'] ?? '',
                  'city' => $_POST['city'] ?? '',
                  'zip' => $_POST['zip'] ?? '',
                  'address' => $_POST['address1'] ?? '',
                  'companyname' => $_POST['companyname'] ?? '',
                  'salutation' => '', 'midinit' => '', 'phone1' => '', 'cont1' => '', 'fax' => '', 'phone2' => '', 'cont2' => '', 'i_time_zone' => '', 'note' => '',
                  'number' => $numbers[0],
                  'alias[1]' => $numbers[1],
                  'did_api[package]' => 'ThinQ',
                  'did_api[city_id]' => '', 'did_api[ratecenter]' => '', 'did_api[country]' => '', 'did_api[monthly]' => '', 'did_api[state]' => '',
                  'subscription_token' => $token
              ];
          } else {
              $data = [
                  'Revision' => '2024-06-13 22090',
                  'package' => $didInfo['package'],
                  'pack' => '0',
                  'firstname' => $_POST['fname'] ?? '',
                  'lastname' => $_POST['lname'] ?? '',
                  'email' => $_POST["email"] ?? '',
                  'country' => $_POST['country'] ?? '',
                  'state' => $_POST['state'] ?? '',
                  'city' => $_POST['city'] ?? '',
                  'zip' => $_POST['zip'] ?? '',
                  'address' => $_POST['address1'] ?? '',
                  'companyname' => $_POST['companyname'] ?? '',
                  'salutation' => '', 'midinit' => '', 'phone1' => '', 'cont1' => '', 'fax' => '', 'phone2' => '', 'cont2' => '', 'i_time_zone' => '', 'note' => '',
                  'number' => $numbers[0] ?? '',
                  'alias[1]' => $numbers[1] ?? '',
                  'alias[2]' => $numbers[2] ?? '',
                  'did_api[package]' => 'DIDAPI::ThinQ',
                  'did_api[city_id]' => '', 'did_api[ratecenter]' => '', 'did_api[country]' => '', 'did_api[monthly]' => '', 'did_api[state]' => '',
                  'subscription_token' => $token,
                  'cc_number' => $cardNumber,
                  'cc_month' => $cardExpiryMonth,
                  'cc_year' => $cardExpiryYear,
                  'cc_cvv' => $cardCVC,
                  'cc_name' => $request->nameOnCard ?? ''
              ];
          }

          try {
              $ch = curl_init($url);
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_POST, true);
              curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
              curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
              curl_exec($ch);
              curl_close($ch);
          } catch (\Exception $e) {
              \Log::error("Provisioning Curl Failure: " . $e->getMessage());
          }

          \DB::table('user_phone_numbers')->insert([
              'user_id' => $userId, 
              'numbers' => $didInfo['number'], 
              'package_id' => $didInfo['package'], 
              'PortoutPin' => $token, 
              'ReferenceID' => $token, 
              'TrunkGroup' => 'Primary', 
              'Lidb' => $_POST['companyname'] ?? '', 
              'sms' => 1, 
              'mms' => 0, 
              'webhook' => 'default', 
              'i_customer' => $customer_id, 
              'i_account' => $account_id, 
              'i_login' => $user_name, 
              'i_password' => $i_password, 
              'i_email' => $email, 
              'h323_password' => $h323_password, 
              'i_product' => $product_id
          ]);
      }

      // Clear session entries strictly after all routines run successfully
      unset($_SESSION['SelectNumberInfo'], $_SESSION['non_memberreferal_token'], $_SESSION['referal_token']);

      $message = "Please check your email for further instructions on how to complete your account setup. Click the activation link to activate your account.";
      return redirect()->route('thank-you', ['message' => $message, 'orderid' => $transId]);
  }

  // public function signupaction(Request $request)
  // {
  //   session_start();
  //   $requestObjForErrors = $request;
  //   if (isset($_SESSION['referal_token']) && $_SESSION['referal_token']) {
  //     $referal_token = $_SESSION['referal_token'];
  //   }
  //   $nicename = $request->nicename;
  //   $display_name = $request->display_name;

  //   $user_name = $request->user_name;
  //   $email = $request->email;
  //   $user_url = $request->user_url;
  //   $password = $request->password;
  //   $password_confirmation = $request->password_confirmation;
  //   $cardNumber = $request->cardNumber;
  //   $cardExpiryMonth = $request->month;
  //   $cardExpiryYear = $request->year;
  //   $cardCVC = $request->cardCVC;
  //   $user_ip = request()->ip();
  //   $fileName = "";
  //   $fileNameat = "";
  //   $didInfo = "";

  //   if ($_SERVER['REMOTE_ADDR'] == "139.135.363.1725") {

  //     $didInfo = $_SESSION["SelectNumberInfo"];
  //     $numbers = json_decode($didInfo['number']);
  //     $host = 'https://globalvoicedirect.com';
  //     $url = 'https://register.globalvoicedirect.com/signup/?task=submit&lang=en';
  //     $token = substr(md5(mt_rand() * time()), 0, 20);

  //     if (sizeof($numbers) == 1) {
  //       $data = array(
  //         'Revision' => '2024-06-13 22090',
  //         'package' => $didInfo['package'],
  //         'pack' => '0',
  //         'firstname' => $_POST['fname'],
  //         'lastname' => $_POST['lname'],
  //         'email' => 'bestsolutions4017@gmail.com',
  //         'country' => $_POST['country'],
  //         'state' => $_POST['state'],
  //         'city' => $_POST['city'],
  //         'zip' => $_POST['zip'],
  //         'address' => $_POST['address1'],
  //         'companyname' => $_POST['companyname'],
  //         'salutation' => '',
  //         'midinit' => '',
  //         'phone1' => '',
  //         'cont1' => '',
  //         'fax' => '',
  //         'phone2' => '',
  //         'cont2' => '',
  //         'i_time_zone' => '',
  //         'payment_method_select' => '0',
  //         'cc_number' => '4111111111111111',
  //         'cc_month' => '01',
  //         'cc_year' => '2026',
  //         'cc_cvv' => '123',
  //         'cc_name' => 'test',
  //         'note' => '',
  //         'number' => $numbers[0],
  //         // 'did_api[package]' => 'DIDAPI::BulkVS',
  //         'did_api[package]' => 'DIDAPI::ThinQ',
  //         'did_api[city_id]' => '',
  //         'did_api[ratecenter]' => '478',
  //         'did_api[country]' => 'US',
  //         'did_api[monthly]' => '',
  //         'did_api[state]' => 'GA',
  //         'subscription_token' => $token
  //       );
  //     } elseif (sizeof($numbers) == 2) {
  //       $data = array(
  //         'package' => $didInfo['package'],
  //         'pack' => '0',
  //         'firstname' => $_POST['fname'],
  //         'lastname' => $_POST['lname'],
  //         'email' => $_POST['email'],
  //         'country' => $_POST['country'],
  //         'state' => $_POST['state'],
  //         'city' => $_POST['city'],
  //         'zip' => $_POST['zip'],
  //         'address' => $_POST['address1'],
  //         'companyname' => $_POST['companyname'],
  //         'salutation' => '',
  //         'midinit' => '',
  //         'phone1' => '',
  //         'cont1' => '',
  //         'fax' => '',
  //         'phone2' => '',
  //         'cont2' => '',
  //         'i_time_zone' => '',
  //         'note' => '',
  //         'number' => $numbers[0],
  //         'alias[1]' => $numbers[1],
  //         // 'did_api[package]' => 'BulkVS',
  //         'did_api[package]' => 'ThinQ',
  //         'did_api[city_id]' => '',
  //         'did_api[ratecenter]' => '',
  //         'did_api[country]' => '',
  //         'did_api[monthly]' => '',
  //         'did_api[state]' => '',
  //         'subscription_token' => $token
  //       );
  //     } else {
  //       $data = array(
  //         'package' => $didInfo['package'],
  //         'pack' => '0',
  //         'firstname' => $_POST['fname'],
  //         'lastname' => $_POST['lname'],
  //         'email' => $_POST['email'],
  //         'country' => $_POST['country'],
  //         'state' => $_POST['state'],
  //         'city' => $_POST['city'],
  //         'zip' => $_POST['zip'],
  //         'address' => $_POST['address1'],
  //         'companyname' => $_POST['companyname'],
  //         'salutation' => '',
  //         'midinit' => '',
  //         'phone1' => '',
  //         'cont1' => '',
  //         'fax' => '',
  //         'phone2' => '',
  //         'cont2' => '',
  //         'i_time_zone' => '',
  //         'note' => '',
  //         'number' => $numbers[0],
  //         'alias[1]' => $numbers[1],
  //         'alias[2]' => $numbers[2],
  //         // 'did_api[package]' => 'BulkVS',
  //         'did_api[package]' => 'ThinQ',
  //         'did_api[city_id]' => '',
  //         'did_api[ratecenter]' => '',
  //         'did_api[country]' => '',
  //         'did_api[monthly]' => '',
  //         'did_api[state]' => '',
  //         'subscription_token' => $token
  //       );
  //     }

  //     $data = http_build_query($data);
  //     $reply = $this->do_post_request($url, $data);
  //     dd($reply);
  //     exit;

  //     // Initialize cURL session
  //     $ch = curl_init($url);

  //     // Set the options for the cURL session
  //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response instead of outputting it
  //     curl_setopt($ch, CURLOPT_POST, true); // Set the method to POST
  //     curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // Set the POST data
  //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL verification if necessary

  //     // Execute the POST request
  //     $response = curl_exec($ch);
  //     dd($response);
  //     // Check for cURL errors
  //     if ($response === false) {
  //       $error = curl_error($ch);
  //       echo "cURL Error: $error";
  //     } else {
  //       // Process the response if needed
  //       dd($response);
  //       echo "Response from server: $response";
  //     }

  //     // Close cURL session
  //     curl_close($ch);

  //     exit;
  //   }



  //   if (isset($request->non_memberreferal_token) && $request->non_memberreferal_token) {
  //     $non_member_referral_token = $request->non_memberreferal_token;
  //   } elseif (isset($_SESSION['non_memberreferal_token']) && $_SESSION['non_memberreferal_token']) {
  //     $non_member_referral_token = $_SESSION['non_memberreferal_token'];
  //   }

  //   if (isset($_SESSION["SelectNumberInfo"])) {
  //     $didInfo = $_SESSION["SelectNumberInfo"];
  //   } else {
  //     if (isset($referal_token) && $referal_token) {
  //       return redirect()->route('credit-apps', ['message' => 'Please Select the Phone Package And Try Again.', 'referal_token' => $referal_token]);
  //     } elseif (isset($non_member_referral_token) && $non_member_referral_token) {
  //       return redirect()->route('credit-apps', ['message' => 'Please Select the Phone Package And Try Again.', 'non-memberreferal_token' => $non_member_referral_token]);
  //     } else {
  //       return redirect()->route('credit-apps', ['message' => 'Please Select the Phone Package And Try Again.']);
  //     }
  //   }

  //   $redirect_back = false;
  //   if (\DB::table('wpgc_users')->where('user_email', $email)->exists()) {
  //     $redirect_back = true;
  //     $reason = "This Email Already Registered";
  //   } else if (\DB::table('wpgc_users')->where('user_login', $user_name)->exists()) {
  //     $redirect_back = true;
  //     $reason = "UserName Already Present";
  //   }


  //   if (\DB::table('wpgc_users')->select('user_login')->where('user_login', $request->$user_name)->exists()) {
  //     $redirect_back = true;
  //     $reason = "This User Name Already Registered";
  //   }
  //   redirect:
  //   if ($redirect_back == true) {

  //     \DB::table('unsuccess_membership')->insert(['display_name' => $nicename, 'email' => $email, 'firstname' => $requestObjForErrors->display_name, 'lastname' => $requestObjForErrors->user_name, 'company_name' => $requestObjForErrors->companyname, 'address' => $requestObjForErrors->address1, 'phone' => $requestObjForErrors->bphone, 'city' => $requestObjForErrors->city, 'state' => $requestObjForErrors->state, 'zip' => $requestObjForErrors->zip, 'reason' => $reason, 'phone_number' => $didInfo['number'], 'phone_state' => '', 'phone_ratecenter' => $requestObjForErrors->phone_ratecenter, 'phone_pkg' => $didInfo['package']]);

  //     if (isset($referal_token) && $referal_token) {
  //       return redirect()->route('credit-apps', ['message' => $reason, 'referal_token' => $referal_token]);
  //     } elseif (isset($non_member_referral_token) && $non_member_referral_token) {
  //       return redirect()->route('credit-apps', ['message' => $reason, 'non-memberreferal_token' => $non_member_referral_token]);
  //     } else {
  //       return redirect()->route('credit-apps', ['message' => $reason]);
  //     }
  //   }

  //   if (isset($request->signature_img)) {

  //     $img = $request->signature_img;

  //     $img = str_replace('data:image/png;base64,', '', $img);
  //     $img = str_replace(' ', '+', $img);
  //     $fileData = base64_decode($img);


  //     // $path = $_FILES['signature_img']['name'];
  //     $ext = "png";
  //     $dir = "/userSignatureImages";
  //     $date = new DateTime();
  //     $imgname = $date->getTimestamp();

  //     $targetfolder = "public/userSignatureImages/" .  $imgname . ".png";
  //     // dd($targetfolder);
  //     if (file_put_contents($targetfolder, $fileData)) {
  //       $fileName = "userSignatureImages/" .  $imgname . "." . $ext;
  //       $fileNameat = "public/userSignatureImages/" .  $imgname . "." . $ext;
  //     } else {
  //       $redirect_back = true;
  //       $reason = "Signature Not Uploaded. Please try again";
  //       goto redirect;
  //       //
  //     }
  //   }

  //   // Common setup for API credentials
  //   $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
  //   if ($_SERVER['REMOTE_ADDR'] == "139.135.36.160") {
  //     $merchantAuthentication->setName('77Np2yKDr');
  //     $merchantAuthentication->setTransactionKey('5cYDE94TN4Zpw45z');
  //   } else {
  //     $merchantAuthentication->setName(config('services.authorize.login'));
  //     $merchantAuthentication->setTransactionKey(config('services.authorize.key'));
  //   }

  //   // $merchantAuthentication->setName(config('services.authorize.login'));
  //   // $merchantAuthentication->setTransactionKey(config('services.authorize.key'));

  //   $refId = 'ref' . time();
  //   // Create the payment data for a credit card
  //   $creditCard = new AnetAPI\CreditCardType();
  //   $creditCard->setCardNumber($request->cardNumber);
  //   // $creditCard->setExpirationDate( "2038-12");
  //   $expiry = $request->year . '-' . $request->month;
  //   $creditCard->setExpirationDate($expiry);
  //   $paymentOne = new AnetAPI\PaymentType();
  //   $paymentOne->setCreditCard($creditCard);

  //   //To Customer
  //   $customer = new AnetAPI\CustomerDataType();
  //   //$customer->setId("15");
  //   $customer->setEmail($request->email);
  //   //To Billing
  //   $billto = new AnetAPI\CustomerAddressType();
  //   $billto->setFirstName($request->fname);
  //   $billto->setLastName($request->lname);
  //   $billto->setCompany($request->companyname);
  //   $billto->setAddress($request->address1);
  //   $billto->setCity($request->city);
  //   $billto->setState($request->state);
  //   $billto->setZip($request->zip);
  //   $billto->setCountry($request->country);
  //   $billto->setPhoneNumber($request->bphone);
  //   // Create a transaction
  //   $transactionRequestType = new AnetAPI\TransactionRequestType();
  //   $transactionRequestType->setTransactionType("authCaptureTransaction");
  //   $transactionRequestType->setAmount(149.00);
  //   $transactionRequestType->setPayment($paymentOne);
  //   $transactionRequestType->setCustomer($customer);
  //   $transactionRequestType->setBillTo($billto);
  //   $requestUpdateObj = $request;
  //   $request = new AnetAPI\CreateTransactionRequest();
  //   $request->setMerchantAuthentication($merchantAuthentication);
  //   $request->setRefId($refId);

  //   $request->setTransactionRequest($transactionRequestType);
  //   $controller = new AnetController\CreateTransactionController($request);

  //   if ($_SERVER['REMOTE_ADDR'] == "139.135.36.160") {
  //     $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
  //   } else {
  //     $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
  //   }

  //   //$response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
  //   if ($response != null and $response->getTransactionResponse() != null) {
  //     $tresponse = $response->getTransactionResponse();
  //     if ($tresponse->getResponseCode() == "1" or $tresponse->getResponseCode() == "3" or $tresponse->getResponseCode() == "4") {
  //       $activationKey = sha1(mt_rand(10000, 99999) . time() . $email);
  //       $userReg = date("Y-m-d H:i:s");
  //       $password  = bcrypt($password);
  //       //$user_name="".str_random(32)."@gmail.com";
  //       //$email="".str_random(32)."@gmail.com";
  //       //uncomment this query after email is sent

  //       /*if user is referred by a member/non-member person, then the bonus will be paid to referral for this golden reference*/
  //       $bonus_for_referral = 0;
  //       if (isset($referal_token) && !empty($referal_token)) {
  //         $bonus_for_referral = 20;
  //       } elseif (isset($non_member_referral_token) && !empty($non_memberreferal_token)) {
  //         $bonus_for_referral = 20;
  //       }
  //       $lastInertId = \DB::table('wpgc_users')->insertGetId(['user_login' => $user_name, 'user_pass' => $password, 'user_nicename' => $nicename, 'reward' => $bonus_for_referral, 'bonus_status' => 0, 'user_email' => $email, 'user_url' => $user_url, 'user_registered' => $userReg, 'user_activation_key' => $activationKey, 'display_name' => $display_name, 'salt' => 'bacrypt_laravel']);

  //       $id = $lastInertId;
  //       $authcode = $tresponse->getAuthCode();
  //       $transId = $tresponse->getTransId();
  //       $trans_amount = 149.00;
  //       $created_at = date("Y-m-d H:i:s");

  //       \DB::table('user_shipping_address')->insert(['user_id' => $id, 'firstname' => $requestUpdateObj->fname, 'lastname' => $requestUpdateObj->lname, 'company_name' => $requestUpdateObj->companyname, 'address' => $requestUpdateObj->address1, 'phone' => $requestUpdateObj->bphone, 'city' => $requestUpdateObj->city, 'state' => $requestUpdateObj->state, 'zip' => $requestUpdateObj->zip]);

  //       \DB::table('transactions')->insert(['transaction_id' => $transId, 'authentication_code' => $authcode, 'transaction_amount' => $trans_amount, 'created_at' => $created_at, 'user_id' => $id]);

  //       //   \DB::table('Account_details')->insert(['user_id'=>$id,'account_number'=>$cardNumber,'CVC'=>$cardCVC,'expire_month'=>$cardExpiryMonth,'expire_year'=>$cardExpiryYear]);

  //       \DB::table('user_balance')->insert(['user_id' => $id, 'balance' => '0.0']);
  //       /*if user referred with referral_token of register member*/

  //       if (isset($referal_token) && $referal_token) {
  //         $referrer_id = \DB::table('wpgc_users')->select('id')->where('referrer_token', $referal_token)->get()->first();
  //         if ($referrer_id) {
  //           /*give reward for referral*/
  //           /*$referral_reward = 30;*/
  //           /*get referral_Balance of user*/
  //           /*$referral_user  = DB::table('user_balance')->select('balance','user_id')->where('user_id', $referrer_id->id)->first();*/
  //           /*update referral_Balance of user*/
  //           /*DB::table('user_balance')
  //               ->where('user_id', $referrer_id->id)  // find your user by their id
  //               ->update(array( 'balance' => $referral_user->balance+$referral_reward));*/

  //           $returnid1 = \DB::table('user_referrer')->insertGetId(['referrer_id' => $referrer_id->id, 'referral_id' => $id, 'reward_status' => 0, 'updated_at' => now()]);
  //         }
  //       }
  //       /*if user reffered with non_memberreferal_token of non_registered user*/
  //       if (isset($non_member_referral_token) && $non_member_referral_token) {

  //         $non_mem_id = \DB::table('affiliate_users')->select('id')->where('token', $non_member_referral_token)->get()->first();
  //         $referral_reward = 20;
  //         $returnid2 = \DB::table('affliate_users_referals')->insertGetId(['affliate_user_id' => $non_mem_id->id, 'user_id' => $id, 'reward_status' => 1]);
  //         $affiliateBalance = DB::table('affiliate_users_balance')->select('balance', 'affiliate_id')->where('affiliate_id', $non_mem_id->id)->first();
  //         DB::table('affiliate_users_balance')
  //           ->where('affiliate_id', $affiliateBalance->affiliate_id)  // find your user by their id
  //           ->update(array('balance' => $affiliateBalance->balance + $referral_reward));
  //         $affiliateBalance1 = DB::table('affiliate_users_balance')->select('balance')->where('affiliate_id', $non_mem_id->id)->first();
  //       }


  //       \DB::table('legal_signup_info')->insert(['user_id' => $id, 'esign_path' => $fileName, 'public_ip_address' => $requestUpdateObj->ip()]);


        // // Send Email For Varification

        // $verificationLink = "https://globalvoicedirect.com/activate?code=" . $activationKey . "&id=" . $id;

        // $htmlStr = "";
        // $htmlStr .= "Hi " . $nicename . ",<br /><br />";

        // $htmlStr .= "Please click the button below to verify your subscription and have access to the globalvoicedirect.com.<br /><br /><br />";
        // $htmlStr .= "<a href='{$verificationLink}' target='_blank' style='padding:1em; font-weight:bold; background-color:blue; color:#fff;'>VERIFY EMAIL</a><br /><br /><br />";

        // $htmlStr .= "Kind regards,<br />";
        // $htmlStr .= "<a href='https://globalvoicedirect.com/' target='_blank'>Globalvoicedirect Team</a><br />";


        // $name = "Varification";
        // $email_sender = "no-reply@globalvoicedirect.com";
        // $subject = "Verification Link";
        // $recipient_email = $email;

        // //comment it after testing

        // \Mail::send(array(), array(), function ($message) use ($htmlStr, $email, $name) {
        //   $message->to($email)
        //     ->subject('Verification Link')
        //     ->from(config('mail.from.address'), 'Varification')
        //     ->html($htmlStr);
        // });

  //       $Signaturepath = 'https://globalvoicedirect.com/' . $fileNameat;
  //       $htmlStr = "";
  //       $htmlStr .= "Hi " . $nicename . ",<br /><br /><h3>Welcome!</h3><br>You are member of Global Voice Direct. ";
  //       $htmlStr .= "Your legal information:<br>";
  //       $htmlStr .= "<h3>Legal information.</h3><br>";
  //       $htmlStr .= "<b>eSign:</b> <img src='" . $Signaturepath . "' />";
  //       $htmlStr .= "<br>IP Address: </b> " . $user_ip;
  //       $htmlStr .= "<br>Joining Date and Time: </b> " . date('Y/m/d H:i:s');

  //       $htmlStr .= "<br><br>Kind regards,<br />";
  //       $htmlStr .= "<a href='https://globalvoicedirect.com/' target='_blank'>Globalvoicedirect Team</a><br />";

  //       $body = $htmlStr;
  //       $subject = "Legal Information";

  //       \Mail::send(array(), array(), function ($message) use ($body, $Signaturepath, $email) {
  //         $message->to($email)
  //           ->subject('Legal Information')
  //           ->from(config('mail.from.address'), 'Global Voice Direct')
  //           ->html($body);
  //       });

  //       //it means mail is send successfully 

  //       if (isset($_SESSION["SelectNumberInfo"])) {
  //         $didInfo = $_SESSION["SelectNumberInfo"];
  //         $numbers = json_decode($didInfo['number']);
  //         $host = 'https://globalvoicedirect.com';
  //         $url = 'https://register.globalvoicedirect.com/signup/?task=submit&lang=en';
  //         $token = substr(md5(mt_rand() * time()), 0, 20);
  //         $i_password = substr(str_shuffle(md5(time())), 0, 5);
  //         $h323_password = substr(str_shuffle(md5(time())), 0, 10); //generate random password

  //         $product_id = '40281';
  //         $account_id = 0;
  //         $customer_id = $this->create_customer($nicename, $email);

  //         if ($customer_id != 0) {
  //           $account_id = $this->create_account($customer_id, $product_id, $email, $h323_password, $user_name, $i_password);
  //         }
  //         if (sizeof($numbers) == 1) {
  //         } elseif (sizeof($numbers) == 2) {
  //         } else {
  //           $data = array(
  //             'Revision' => '2024-06-13 22090',
  //             'package' => $didInfo['package'],
  //             'pack' => '0',
  //             'firstname' => $_POST['fname'],
  //             'lastname' => $_POST['lname'],
  //             'email' => $_POST["email"],
  //             'country' => $_POST['country'],
  //             'state' => $_POST['state'],
  //             'city' => $_POST['city'],
  //             'zip' => $_POST['zip'],
  //             'address' => $_POST['address1'],
  //             'companyname' => $_POST['companyname'],
  //             'salutation' => '',
  //             'midinit' => '',
  //             'phone1' => '',
  //             'cont1' => '',
  //             'fax' => '',
  //             'phone2' => '',
  //             'cont2' => '',
  //             'i_time_zone' => '',
  //             'note' => '',
  //             'number' => $numbers[0],
  //             'alias[1]' => $numbers[1],
  //             'alias[2]' => $numbers[2],
  //             // 'did_api[package]' => 'DIDAPI::BulkVS',
  //             'did_api[package]' => 'DIDAPI::ThinQ',
  //             'did_api[city_id]' => '',
  //             'did_api[ratecenter]' => '',
  //             'did_api[country]' => '',
  //             'did_api[monthly]' => '',
  //             'did_api[state]' => '',
  //             'subscription_token' => $token,
  //             'cc_number' => $request->cardNumber,
  //             'cc_month' => $request->month,
  //             'cc_year' => $request->year,
  //             'cc_cvv' => $request->cardCVC,
  //             'cc_name' => $request->nameOnCard
  //           );
  //         }

  //         try {
  //         } catch (Exception $e) {
  //           Log::error($e);
  //         }


  //         \DB::table('user_phone_numbers')->insert(['user_id' => $id, 'numbers' => $didInfo['number'], 'package_id' => $didInfo['package'], 'PortoutPin' => $token, 'ReferenceID' => $token, 'TrunkGroup' => 'Primary', 'Lidb' => $_POST['companyname'], 'sms' => 1, 'mms' => 0, 'webhook' => 'default', 'i_customer' => $customer_id, 'i_account' => $account_id, 'i_login' => $user_name, 'i_password' => $i_password, 'i_email' => $email, 'h323_password' => $h323_password, 'i_product' => $product_id]);
  //         //uncomment it after testing,
  //         unset($_SESSION["SelectNumberInfo"]);
  //         unset($_SESSION["non_memberreferal_token"]);
  //         unset($_SESSION['referal_token']);
  //       }
  //       $message = "Please check your email for further instructions on how to complete your account setup. Click the activation link to activate you account.";
  //       return redirect()->route('thank-you', ['message' => $message, 'orderid' => $transId]);
  //     } else {
  //       $redirect_back = true;
  //       $reason = "Charge Credit Card ERROR :  Invalid response";
  //       goto redirect;
  //       // echo "Charge Credit Card ERROR :  Invalid response\n";
  //     }
  //   } else {
  //     $redirect_back = true;
  //     $reason = "Credit Card Not Accepted";
  //     goto redirect;
  //   }
  // }

  //PortaBilling Soap Api Telinta
  public function create_customer($customer_name, $customer_email)
  {
    ini_set("soap.wsdl_cache_enabled", "1");
    date_default_timezone_set('UTC');

    $service = 'Admin';
    $host = 'mybilling.telinta.com'; // your domain should be placed here, like mybilling.your-domain.com

    $login = 'aliraza'; #API user login
    $password = env('TELINTA_API_PASSWORD'); #API user token

    $soap_options = array(
      'trace' => 1,
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
      'user_agent' => 'PHP-SOAP-API-script-' . $login,
    );

    $session = new SoapClient("https://" . $host . "/wsdl/Session{$service}Service.wsdl", $soap_options); //initialize API client
    $session_id = $session->login(array('login' => $login, 'token' => $password))->session_id; //obtain session ID by logging in

    //add the auth_info header for further requests to be authorized right away
    $headers = NULL;
    $headers[] = new SoapVar("<session_id>$session_id</session_id>", XSD_ANYXML, "session_id", "https://" . $host . "/Porta/SOAP/Session");
    $auth_info = new SoapHeader("https://" . $host . "/Porta/SOAP/Session", "auth_info", $headers);

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
    catch (SoapFault $e) {
      Log . error("Error: " . $e->getMessage());
      return 0;
    }
  }

  public function create_account($i_customer, $i_product, $email, $h323_password, $login_r, $password)
  {
    ini_set("soap.wsdl_cache_enabled", "1");
    date_default_timezone_set('UTC');

    $service = 'Admin';
    $host = 'mybilling.telinta.com'; // your domain should be placed here, like mybilling.your-domain.com

    $login = 'aliraza'; #API user login
    $password = env('TELINTA_API_PASSWORD'); #API user token

    $soap_options = array(
      'trace' => 1,
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
      'user_agent' => 'IMPORT-ACC-WITH-FWD-PHP-SOAP-' . $login,
    );

    $session = new SoapClient("https://" . $host . "/wsdl/Session{$service}Service.wsdl", $soap_options); //initialize API client
    $session_id = $session->login(array('login' => $login, 'token' => $password))->session_id; //obtain session ID by logging in

    //add the auth_info header for further requests to be authorized right away
    $headers = NULL;
    $headers[] = new SoapVar("<session_id>$session_id</session_id>", XSD_ANYXML, "session_id", "https://" . $host . "/Porta/SOAP/Session");
    $auth_info = new SoapHeader("https://" . $host . "/Porta/SOAP/Session", "auth_info", $headers);

    $id = '020' . time(); //generate new account ID
    $date = date('Y-m-d'); //activation_date for new account

    #build request
    $AddAccRequest = array(
      'account_info' => array(
        'id' => $id,
        'email' => $email,
        'billing_model' => '1',
        'activation_date' => $date,
        'i_product' => $i_product,
        'h323_password' => $h323_password,
        'i_customer' => $i_customer

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
    catch (SoapFault $e) {
      Log::error("Error: " . $e->getMessage());
      return 0;
    }
  }


  public function do_post_request($url, $data, $optional_headers = null)
  {
    $params = array('http' => array(
      'method' => 'POST',
      'content' => $data
    ));
    if ($optional_headers != null) {
      $params['http']['header'] = $optional_headers;
    }
    $ctx = stream_context_create($params);

    $fp = @fopen($url, 'r', false, $ctx);

    if (!$fp) {
      throw new Exception("Problem with $url, $php_errormsg");
    }
    $response = '';
    while (!feof($fp)) {
      $response = $response . fgets($fp);
    }
    if ($response === false) {
      throw new Exception("Problem reading data from $url, $php_errormsg");
    }

    fclose($fp);
    return $response;
  }
  public function activate(Request $req)
  {
    if (isset($req->id) and isset($req->code)) {
      $id = $req->id;
      $code = $req->code;

      $status = \DB::table('wpgc_users')->where('ID', $id)->where('user_activation_key', $req->code)->update(['user_status' => 1]);
      // dd($status);
      if ($status == 1) {
        return redirect()->route('emailActivation', ['message' => 1]);
      } else {
        return redirect()->route('emailActivation', ['message' => 2]);
      }
    }
  }
  public function emailActivation(Request $req)
  {
    if ($req->message == 1) {
      return redirect()->route('emailActivationView', ['message' => 'You have activated your account successfully']);

      //return view('emailActivation');
    } else {
      return redirect()->route('emailActivationView', ['message' => 'SomeThing Went Wrong']);
    }
  }
}
