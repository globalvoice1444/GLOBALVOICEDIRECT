<?php


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;







/*



|--------------------------------------------------------------------------



| Web Routes



|--------------------------------------------------------------------------



|



| Here is where you can register web routes for your application. These



| routes are loaded by the RouteServiceProvider within a group which



| contains the "web" middleware group. Now create something great!



|



*/
Route::get('create-customer', 'TestController@get_customer')->name('create-account');

Route::get('clear-cache', function () {



     \Artisan::call('route:clear');



    \Artisan::call('cache:clear');



       \Artisan::call('view:clear');



        dd(\Artisan::call('config:cache'));



   // return view('Pages.main');



});



Route::get('/home', function () {



    $item = DB::select('SELECT pro.*,pr.* from products pro LEFT JOIN productad_on pr ON pro.ProductID = pr.product_id where ProductLive = 0 order by ProductUpdateDate LIMIT 8');



    return view('index',['item' => $item]);



})->name('main');

Route::get('homepage', function(){

    $item = DB::select('SELECT pro.*,pr.* from products pro LEFT JOIN productad_on pr ON pro.ProductID = pr.product_id where ProductLive = 0 order by ProductUpdateDate LIMIT 8');

    return view('newhomepage',['item' => $item]);

})->name('homepage');

Route::get('404',function (){
    return view('errors.pagenotfound');
});
Route::get('Error',function(){
    return view('errors.general_error');
});
Route::get('tempUrl',function(){
    $tempvar = 0;
    return 80/$tempvar;
});



Route::get('/howitworks', function () {



    return view('howitwork');



});



Route::get('affiliate-referrals', 'RegisterController@AffiliateReferral')->name('affiliate-referrals');



Route::get('/affiliate', 'RegisterController@AffiliateIndex')->name('affiliate');



Route::post('affiliate-link', 'RegisterController@getAffiliateLink')->name('affiliate-link');



Route::post('affiliate/verify-code', 'RegisterController@AffiliateVerifyCode')->name('verify-code');



Route::post('affiliate-code-verify', 'RegisterController@AffiliateCodeVerify')->name('affiliate-code-verify');



Route::post('get-affiliate-link', 'RegisterController@getnonMemberAffiliateLink')->name('get-affiliate-link');



Route::post('aff-withdraw', 'RegisterController@AffiliateWithdraw')->name('aff-withdraw');



//coupon routes



Route::post('/coupon','CouponsController@store')->name('coupon.store');

Route::delete('/coupon','CouponsController@destroy')->name('coupon.destroy');





//end coupon routes



Route::post('affiliate-dashboard', 'RegisterController@AffiliateDashboard')->name('affiliate-dashboard');

Route::get('affiliate-dashboard','RegisterController@AffiliateDashboardIndex')->name('affiliate-dashboard');







Route::get('/single-product', 'HomeController@singleProduct');



Route::get('search', 'HomeController@search')->name('search');



//webhook URL for bulk sms webhook to forward message to ghl
Route::post('/bulkvs-webhook-url','BulkvsController@bulkvsWebook');






// Admin Routes Starts



Route::get('adminlogin','AdminController@index');



Route::get('adminforgotpasswod',function () {



    return view('adminforgotview');



});


Route::post('requestforresetpassword', 'AdminController@requestforresetpassword');



Route::get('setadminnewpassword','AdminController@adminsetnewpassword');



Route::post('actionresetnewadminpassword','AdminController@actionresetnewpasswordadmin');



Route::post('/admin/login','AdminController@login');



Route::group(['middleware'=>['adminauth']],function(){
    // 2026-11-02 Automations > Drip Sequences
    Route::get('admin/automations/customer-email','AdminCustomerEmailController@index'); // 2026-11-02 cec
    Route::post('admin/automations/customer-email','AdminCustomerEmailController@update');
    Route::get('admin/automations/drip','AdminDripController@index');
    Route::post('admin/automations/drip/sequence','AdminDripController@sequenceCreate');
    Route::post('admin/automations/drip/sequence/{id}/status','AdminDripController@sequenceStatus')->where('id','[0-9]+');
    Route::post('admin/automations/drip/sequence/{id}/save','AdminDripController@sequenceSave')->where('id','[0-9]+');
    Route::post('admin/automations/drip/sequence/{id}/reorder','AdminDripController@reorder')->where('id','[0-9]+');
    Route::post('admin/automations/drip/sequence/{id}/enroll','AdminDripController@enrollSegment')->where('id','[0-9]+');
    Route::post('admin/automations/drip/step','AdminDripController@stepSave');
    Route::post('admin/automations/drip/step/{id}/duplicate','AdminDripController@stepDuplicate')->where('id','[0-9]+');
    Route::post('admin/automations/drip/step/{id}/delete','AdminDripController@stepDelete')->where('id','[0-9]+');
    Route::post('admin/automations/drip/step/{id}/status','AdminDripController@stepStatus')->where('id','[0-9]+');
    Route::post('admin/automations/drip/step/{id}/test','AdminDripController@test')->where('id','[0-9]+')->middleware('throttle:10,1');
    Route::get('admin/automations/drip/step/{id}/preview','AdminDripController@preview')->where('id','[0-9]+');
    // 2026-11-02 Automations > Broadcasts
    Route::get('admin/automations/broadcasts','AdminBroadcastController@index');
    Route::get("admin/automations/tracking", "TrackingController@admin"); // trk
    Route::post('admin/automations/broadcasts','AdminBroadcastController@create');
    Route::post('admin/automations/broadcasts/{id}/save','AdminBroadcastController@save')->where('id','[0-9]+');
    Route::post('admin/automations/broadcasts/{id}/audience','AdminBroadcastController@audience')->where('id','[0-9]+');
    Route::post('admin/automations/broadcasts/{id}/status','AdminBroadcastController@status')->where('id','[0-9]+');
    Route::post('admin/automations/broadcasts/{id}/test','AdminBroadcastController@test')->where('id','[0-9]+')->middleware('throttle:10,1');
    Route::get('admin/automations/broadcasts/{id}/preview','AdminBroadcastController@preview')->where('id','[0-9]+');
    Route::get('admin/automations/workflows','AdminWorkflowController@index');
    Route::post('admin/automations/workflows','AdminWorkflowController@create');
    Route::get('admin/automations/workflows/{id}','AdminWorkflowController@show')->where('id','[0-9]+');
    Route::post('admin/automations/workflows/{id}/save','AdminWorkflowController@save')->where('id','[0-9]+');
    Route::post('admin/automations/workflows/{id}/status','AdminWorkflowController@status')->where('id','[0-9]+');
    Route::post('admin/automations/workflows/{id}/delete','AdminWorkflowController@delete')->where('id','[0-9]+');
    Route::post('admin/automations/workflows/{id}/test','AdminWorkflowController@test')->where('id','[0-9]+')->middleware('throttle:5,1');
    Route::get('admin/alerts','AdminAlertsController@index');
    Route::post('admin/alerts/read','AdminAlertsController@markRead');
    Route::get('admin/script-audio/{id}','ScriptAudioController@admin')->where('id','[0-9]+');
    Route::get('admin/forms/listings','AdminListingsController@index');
    Route::post('admin/forms/listings/status','AdminListingsController@status');
    Route::post('admin/forms/listings/export','AdminListingsController@export');
    Route::get('admin/forms/listings/file/{name}','AdminListingsController@file')->where('name','bing-bulk-[A-Za-z0-9_-]+\.csv');
    Route::get('admin/forms/listings/{id}','AdminListingsController@show')->where('id','[0-9]+');
    Route::get('admin/forms/listings/{id}/guide','AdminListingsController@guide')->where('id','[0-9]+');
    Route::get('admin/forms/{kind}','AdminBizFormsController@index')->where('kind','marketing|credit');
    Route::get('admin/forms/credit/legacy/{id}','AdminBizFormsController@legacyShow')->where('id','[0-9]+');
    Route::get('admin/forms/credit/legacy/{id}/file/{field}','AdminBizFormsController@legacyFile')->where(['id'=>'[0-9]+','field'=>'upload']);
    Route::post('admin/forms/credit/legacy/{id}/reveal','AdminBizFormsController@legacyReveal')->where('id','[0-9]+');
    Route::get('admin/forms/{kind}/{id}','AdminBizFormsController@show')->where(['kind'=>'marketing|credit','id'=>'[0-9]+']);
    Route::post('admin/forms/{kind}/{id}/status','AdminBizFormsController@status')->where(['kind'=>'marketing|credit','id'=>'[0-9]+']);



    Route::get('/admin/categories','AdminController@categories');



    Route::get('admin/logout','AdminController@logout');



    Route::post('admin/act_updatecategory','AdminController@updateCategories');



    Route::post('admin/act_addcategory','AdminController@addCategory');



    Route::get('admin/addon','AdminController@addonProducts');



    Route::get('admin/act_removeAddonproduct','AdminController@removeAddon');



    Route::post('admin/get_updateAddonProducts','AdminController@getUpdateAddon');



    Route::post('admin/update_addon','AdminController@updateAddon');



    Route::get('admin/products','AdminController@products');



    Route::post('action/act_addProduct', 'AdminController@addProduct');



    Route::post('admin/get_updateProduct','AdminController@getUpdateProduct');



    Route::post('admin/act_updateProduct','AdminController@actUpdataProduct');



    Route::get('admin/act_removeProduct', 'AdminController@removeProduct');



    Route::get('admin/act_backToLiveProduct','AdminController@backToLive');



    Route::post('admin/act_addonsProduct','AdminController@productAddonAdd');



    Route::get('admin/orders','AdminController@orders');



    Route::get('admin/orderslip','AdminController@generateSlip');



    Route::post('admin/get_orderDetailss','AdminController@getOrderDetails');



    Route::get('admin/updateOrderStatus','AdminController@updateOrderStatus');



    Route::post('admin/assigntrackno','AdminController@assignTrackNumber');



    Route::get('admin/Payments','AdminController@payments');

    Route::get('admin/paymentgateways','AdminController@paymentsGateways');

    Route::post('/admin/payment-gateway/activate-only/{id}','AdminController@paymentgatewayactivateOnly');
    

    Route::get('email_for_payment/{id}','AdminController@emailforpayment');



    Route::get('admin/m_form','AdminController@getMarkrtingDetail');



    Route::post('admin/get_MarketInfo','AdminController@getMarketInfo');


    Route::get('admin/add_credit_form','AdminController@addcreditform');

    Route::get('admin/dis_credit_detail/{id}','AdminController@discreditdetail');


    Route::get('admin/scriptRecording','AdminController@scriptRecording');



    Route::post('admin/get_scriptrec','AdminController@getScriptRec');



    Route::get('admin/creditappInfo','AdminController@getCreditInfo');



    Route::get('admin/dis_all_detail','AdminController@getAllDetail');



    Route::get('admin/fax_management','AdminController@faxMan');

    Route::get('admin/sms_management','AdminController@smsManagement');

    Route::post('admin/act_addhttp','AdminController@addhttpSMS');

    Route::post('admin/act_newfaxacc','AdminController@newFaxAcc');

    //GoHighLevel
    Route::get('admin/ghl-workflow','GoHighLevelController@workflow');

    Route::post('admin/act_newexistingfaxacc','AdminController@newExistingFaxAcc');

    Route::post('admin/getUserDetailFax','AdminController@getUserDetailFax');



    Route::post('admin/getDid','AdminController@getDid');



    Route::post('admin/updatefaxacc','AdminController@updateFaxAcc');



    Route::post('admin/upd_RegDidfaxacc','AdminController@updateRegDidFaxAcc');



    Route::get('admin/sendemail','AdminController@sendEmail');



    Route::post('admin/sendEmailtoclients','AdminController@sendMailToClient');



    //Route::get('admin/usermanagement','AdminController@userManagement');

    Route::get('/admin/usermanagement', 'AdminController@userManagement')->name('admin.usermanagement');

    Route::get('/admin/usermanagement/active-users', 'AdminController@activeUsers')->name('admin.users.active');    
    Route::get('/admin/usermanagement/deactive-users', 'AdminController@deActiveUsers')->name('admin.users.deactive');  
    Route::get('/admin/usermanagement/email-users', 'AdminController@emailActiveUsers')->name('admin.users.email'); 
    Route::get('/admin/usermanagement/password-users', 'AdminController@passwordUsers')->name('admin.users.password');  
    Route::get('/admin/usermanagement/legal-users', 'AdminController@legalUsers')->name('admin.users.legal');   
    Route::get('/admin/usermanagement/phone-users', 'AdminController@phoneUsers')->name('admin.users.phone');   
    Route::get('/admin/usermanagement/failure-membership', 'AdminController@failureMembership')->name('admin.users.failure');   
    Route::get('/admin/usermanagement/affiliates', 'AdminController@affiliates')->name('admin.users.affiliates');   
    Route::get('/admin/usermanagement/affiliate-withdraw', 'AdminController@affiliateWithdraw')->name('admin.users.affiliate.withdraw');    
    Route::get('/admin/usermanagement/member-withdraw', 'AdminController@memberWithdraw')->name('admin.users.member.withdraw');


    Route::post('admin/affiliate-referrals','AdminController@getAffilateReferrals');


    Route::post('admin/withdraw/approved', 'AdminController@approvRequest')->name('withdraw-approved');


    Route::get('admin/affiliate/withdraw/approved/{id}', 'AdminController@AfiliateWithdrawRequestAction')->name('affiliate-request-approved');



    Route::get('admin/act_removeUser/{uid}','AdminController@removeUser');



    Route::get('admin/act_activateUser/{uid}','AdminController@activateUser');



    Route::get('admin/act_updateEmailActivate','AdminController@activateEmail');



    Route::post('admin/changepassword','AdminController@changePass');



    Route::post('/action/addNewUser', 'AdminController@addNewUser');



    Route::get('/admin/tospdf/{id}', 'AdminController@tosPdf');



    Route::get('/admin/approvescriptrecording', 'AdminController@approvescriptrecording');



    Route::get('/admin/removescriptrecording', 'AdminController@removescriptrecording');



    Route::get('/admin/affiliates', 'AdminController@affiliateProgram');



    Route::get('/admin/affiliate-users/{id}', 'AdminController@getAffiliateUsers');



    Route::get('/admin/deactivate-affiliate/{id}', 'AdminController@deactivateAffiliate');



    Route::get('admin/coupon/show','CouponsController@show')->name('admin.coupon.show');

    Route::get('admin/coupon/get','CouponsController@get')->name('admin.coupon.get');

    Route::post('admin/coupon/new','CouponsController@add')->name('admin.coupon.store');

    Route::post('admin/coupon/update/{id}','CouponsController@update')->name('admin.coupon.update');

    Route::get('admin/coupon/delete/{id}','CouponsController@destroy')->name('admin.coupon.delete');

    Route::post('admin/coupon/deactivate/{id}','CouponsController@deactivate')->name('admin.coupon.deactivate');



});



























// Admin Routes Ends















Route::post('/recentlyaddedmemeber', 'VisitorController@recentlyaddedmemeber');







Auth::routes();
// The built-in /login form never worked with wpgc_users; send everyone to the real portal login.
Route::get('login', function () { return redirect('/user-login', 301); })->name('login');
Route::post('login', function () { return redirect('/user-login', 303); });







Route::get('/home1', 'HomeController@index')->name('home');



Route::get('/feature', function(){



    return view('feature');



});



Route::get('/forgotpassword', function(){



    return view('forgotPassword');



})->name('forgotpassword');







Route::get('/contact-us', function(){



    return view('contact');



})->name('contact-us');



Route::get('/chose-pakage', function(){



    return view('chose_pakage');



});



Route::get('/portin-request-form', function(){



    return view('portin-form');



});



Route::get('/download-app', function(){



    return view('download-app');



});



Route::get('/phone-number', function(){



    return view('phone_number');



});



Route::get('user-extension', function(){



    return view('user_extensions');



});



Route::get('call-handling', function(){



    return view('call_handling');



});



Route::get('admin-feature', function(){



    return view('feature_admin');



});



Route::get('phone-feature', function(){



    return view('feature_phone');



});



Route::get('our-offices', function(){



    return view('ourOffices');



});



Route::get('about', function(){



    return view('about');



});



Route::view('welcome-video', 'welcome-video'); // public welcome video page 2026-11-02vid

Route::get('tos', function(){
    return view('tos');
});


Route::get('privacy-policy', function(){
    return view('privacy-policy');
});

Route::get('refund-policy', function(){
    return view('refund-policy');
});








Route::post('contactUs','HomeController@contactUs')->name('contactUs');



/*package-pricing view*/



Route::get('package-pricing', function(){



    return view('package_pricing');



});



Route::get('number', 'HomeController@memberShipCardReview')->name('membershipcardview');


Route::post('number/searchlocalareacode', 'HomeController@searchlocalareacode')->name('searchlocalareacode');

 
    //Route::get('number/getData', 'HomeController@getAllAvailableNumbersbyLocalcode')->name('getData');
 
    Route::get('number/getData', 'HomeController@getData')->name('getData');




Route::get('number/getRateCenter', 'HomeController@getRateCenter')->name('getRateCenter');



Route::post('/number/maintainSession','HomeController@maintainSession')->name('maintainSession');

//Go High Level Webhook URL
// 2026-11-06 GHL webhook retired: Route::get('/ghl-webhook','GoHighLevelController@webhookurl')->name('ghlwebhook');







Route::get('credit-application', function(){



    return view('credit-application');



});



// Route::get('credit-apps', function(){



//     return view('credit-app.index');



// })->name('credit-apps');
Route::get('credit-apps', function () {

    $payment_gateway = DB::table('payment_gateways')
        ->where('is_active', 1)  // active = 1
        ->value('gateway_name');
    $payment_gateway = $payment_gateway ? strtolower(trim($payment_gateway)) : $payment_gateway;

    //$payment_gateway = 'stripe';

    return view('credit-app.index', compact('payment_gateway'));

})->name('credit-apps');



Route::get('thank-you', function(){



    return view('credit-app.thankyou');



})->name('thank-you');



//signupaction



Route::post('signupaction','RegisterController@signupaction');



Route::get('activate','RegisterController@activate');



Route::get('email-activation','RegisterController@emailActivation')->name('emailActivation');



Route::get('emailActivationView', function(){



return view('emailActivation');



})->name('emailActivationView');



/*login*/



Route::get('/user-login', 'RegisterController@userLoginPage')->name('user-login');
Route::get('/', 'RegisterController@userLoginPage')->name('user-login');



Route::post('loginAction','RegisterController@loginAction');



Route::post('/user/updateLogin','ProfileController@updateLoginInfo');



//



Route::post('resetPassword','RegisterController@resetPassword');



/*set new password view*/



Route::get('setNewPassword','RegisterController@setNewPassword')->name('setNewPassword');



Route::post('act_resetnewPassword','RegisterController@act_resetnewPassword');







/*products listing*/



Route::get('store','storeController@store')->name('store');



//Route::get('store','storeController@store')->name('store');
Route::get('cart/cart','storeController@cart')->name('cart');
Route::post('cart/postcart', 'storeController@postCart');
Route::get('cartItems','storeController@cartItems')->name('cartItems');
Route::post('updateqty','storeController@updateqty')->name('updateqty');
Route::any('checkout','storeController@checkout')->name('checkout');
Route::get('delcart','storeController@delcart')->name('delcart');
Route::post('/cart/checkout/submitOrder','storeController@submitOrder')->name('submitOrder');
Route::get('action/activate', 'UserController@activateUserByEmail');

/*   USER CONTROLLER ROUTES */

Route::group(['middleware' => ['authentication']], function () {
    //User Order
    Route::get('/user/order', 'UserController@orderDetail');
    Route::get('/user/view_order_detail/{id}', 'UserController@viewOrderDetail')->name('view_order_detail');
    Route::get('/user/unpaid_order/{id}', 'UserController@unpaidOrder')->name('unpaid_order');
    Route::post('/user/submit_unpaid_payment','UserController@submit_unpaid_payment');

    Route::get('/user/fax_mod', fn () => redirect('/user/fax/send'))->name('fax_mod'); // old fax sender retired
    Route::get('/user/referral_program', 'RegisterController@referral_program')->name('referral_program');
    Route::get('/user/referral_dashboard', 'RegisterController@referral_dashboard')->name('referral_dashboard');
    Route::get('/user/withdraw-bonus', 'RegisterController@withdrawBonusRequest')->name('withdraw_bonus_request');
    Route::post('/user/withdraw-reward', 'RegisterController@withdrawRewardRequest')->name('withdraw_reward_request');
    Route::get('payment','UserController@payment');
    Route::get('/user/billPay','UserController@billPay')->name('user/billPay');
    Route::post('act_submitPayment','UserController@act_submitPayment');
    // merged 2026-10-31: old customer credit form now lives at /user/credit-application (UserController@credit_app kept, unused)
    Route::permanentRedirect('user/credit_app', '/user/credit-application');
    Route::post('act_submitcreditinfo', 'UserController@act_submitcreditinfo')->name('act_submitcreditinfo');
    // 2026-10-31: old marketing form retired (301 to /user/marketing-form, defined at end of file); UserPanelController@marketingForm and marketing_info kept untouched
    Route::post('/actions/act_submitMarForm', 'UserPanelController@act_submitMarForm');

    Route::get('/additional/credit_app','UserPanelController@additional_credit_app');
    Route::post('/additional/actother_submitMartForm','UserPanelController@act_another_submitMarForm');

    Route::get('user/unpaid_order/{id}','UserController@unpaid_order');

    Route::get('/user/script_recording', 'UserController@scriptRecording');
    Route::get('addPort','UserPanelController@addPort');
    Route::post('act_addport','UserPanelController@act_addport');
    Route::get('listingbusinessname','UserPanelController@listingbusinessname');
    Route::post('saveScriptInfo', 'UserController@saveScriptInfo')->name('saveScriptInfo');
    Route::post('act_Listingbusinessname','UserPanelController@act_Listingbusinessname');
    Route::get('/user/howtovideo','UserPanelController@howtovideo');
    Route::get('/user/profile','VoProfileController@index'); // VO profile 2026-11-01 (old: RegisterController@profile, kept)
    //Route::get('profile','RegisterController@profile');
    // 2026-11-02 cec: legacy email campaign pages/endpoints retired (no owner checks, sent only to first contact). New: /user/email-campaigns (EmailCampaignController). Old controller methods kept, unrouted.
    Route::get('ot_editor_sendEmail', fn () => redirect('/user/email-campaigns'))->name('ot_editor_sendEmailView');
    Route::post('act_ot_sendEmail', fn () => redirect('/user/email-campaigns'));
    Route::get('send_email', fn () => redirect('/user/email-campaigns'))->name('send_email');
    Route::any('sendEmailTemp', fn () => response('This page has moved to /user/email-campaigns', 410))->name('sendEmailTemp');
    Route::any('saveEmailTemp', fn () => response('This page has moved to /user/email-campaigns', 410))->name('saveEmailTemp');
    Route::any('saveAndSendEmailTemp', fn () => response('This page has moved to /user/email-campaigns', 410))->name('saveAndSendEmailTemplate');
    Route::any('getTemplates', fn () => response('', 410))->name('getTemplates');
    Route::get('emailCampaign', fn () => redirect('/user/email-campaigns'))->name('emailCampaign');
    Route::post('act_add_email_contacts', fn () => redirect('/user/email-campaigns?tab=contacts'))->name('act_add_email_contacts');
    Route::get('/user/audioCampaign','UserPanelController@audioCampaign')->name('audioCampaign');
    Route::get('sampleCsv', function(){
        $pathToFile="public/sendEmail.csv";
        return response()->download($pathToFile);
        //return view('tos');

    });

    Route::get('sampleCsvContacts', function(){
        $pathToFile="public/contactCsv.csv";
        return response()->download($pathToFile);
        //return view('tos');

    });
    Route::get('sampleontactandcontentCsv', function(){
        $pathToFile="public/contactandcontentCsv.csv";
        return response()->download($pathToFile);
        //return view('tos');
    });

    Route::any('check','UserPanelController@check')->name('check');
    Route::get('active_calls','UserPanelController@active_calls');
    Route::get('user/fax','VoFaxController@inbox')->name('user.fax');
    Route::get('user/fax/list','VoFaxController@list');
    Route::get('user/fax/live/{ia}/{uid}','VoFaxController@file')->where(['ia'=>'[0-9]+','uid'=>'[0-9]+']);
    Route::post('user/fax/delete','VoFaxController@delete');
    Route::get('user/fax/send','VoFaxController@sendPage');
    Route::post('user/fax/send','VoFaxController@send');
    Route::get('user/fax/sent','VoFaxController@sent');
    Route::get('user/phone','PhoneController@index')->name('user.phone');
    Route::post('user/phone/credentials','PhoneController@credentials')->name('user.phone.credentials');
    Route::get('user/greetings','GreetingsController@index')->name('user.greetings');
    Route::get('user/greetings/audio','GreetingsController@audio');
    Route::post('user/greetings/replace','GreetingsController@replace');
    Route::post('user/greetings/restore','GreetingsController@restore');
    Route::post('user/greetings/delete','GreetingsController@delete');
    Route::post('user/greetings/alternate','GreetingsController@alternate');
    Route::post('user/greetings/schedule','GreetingsController@schedule');
    Route::post('user/greetings/holidays','GreetingsController@holidaysSave');
    Route::get('user/phone-numbers','PhoneNumbersController@index')->name('user.phone-numbers');
    Route::get('user/phone-numbers/detail','PhoneNumbersController@detail');
    Route::post('user/phone-numbers/save','PhoneNumbersController@save');
    Route::get('user/calls/recording','VoPagesController@recording');
    Route::post('user/calls/recording/delete','VoPagesController@recordingDelete');
    Route::get('user/calls/conference-recording','VoPagesController@confRecording');
    Route::get('user/calls/{slug}','VoPagesController@calls')->where('slug','inbound|outbound|dial-out|recorded|conference-recordings');
    Route::get('user/extensions','VoPagesController@extensions');
    Route::get('user/extensions/detail','VoPagesController@extDetail');
    Route::post('user/extensions/secret','VoPagesController@extSecret');
    Route::post('user/extensions/save','VoPagesController@extSave');
    Route::get('user/conference-rooms','VoPagesController@conferences');
    Route::post('user/conference-rooms/pins','VoPagesController@confPins');
    Route::post('user/conference-rooms/save','VoPagesController@confSave');
    Route::get('user/call-block','VoPagesController@callBlock');
    Route::post('user/call-block/save','VoPagesController@callBlockSave');
    Route::get('user/users','VoPagesController@users');
    Route::post('user/users/secret','VoPagesController@userSecret');
    Route::post('user/users/save','VoPagesController@userSave');
    Route::get('user/extras','VoPagesController@extras');
    Route::post('user/extras/toggle','VoPagesController@extrasToggle');
    Route::get('user/dial-out-numbers','VoPagesController@dialOutNumbers');
    Route::post('user/dial-out-numbers/save','VoPagesController@dialOutSave')->middleware('throttle:30,1'); // 2026-11-01 dw
    Route::get('user/webrtc','VoPagesController@webrtc'); // 2026-11-01 dw
    Route::get('user/mobile-app','VoPagesController@mobileApp'); // 2026-11-01 ma
    Route::post('user/mobile-app/creds','VoPagesController@mobileCreds'); // 2026-11-01 qr
    Route::post('user/mobile-app/email','VoPagesController@mobileEmail'); // 2026-11-01 qr
    Route::get('user/phone/balance','VoPagesController@balance')->middleware('throttle:60,1'); // 2026-11-01 dw
    Route::get('user/home','VoHomeController@index'); // VO customer dashboard 2026-10-31
    Route::get('user/home/stats','VoHomeController@stats'); // read-only dashboard counts 2026-11-01
    Route::get('user/phone-scripts/audio/{id}','ScriptAudioController@user')->where('id','[0-9]+');
    // Audio Campaign 2026-11-01 (hidden from the menu; calls only when AUDIO_CAMPAIGN_SENDING=true)
    Route::get('user/audio-campaigns','AudioCampaignController@index');
    Route::get('user/audio-campaigns/{id}/status','AudioCampaignController@status')->where('id','[0-9]+')->middleware('throttle:60,1');
    Route::get('user/audio-campaigns/recording/{id}','AudioCampaignController@recording')->where('id','[0-9]+');
    Route::post('user/audio-campaigns','AudioCampaignController@store')->middleware('throttle:10,1');
    Route::post('user/audio-campaigns/{id}/start','AudioCampaignController@start')->where('id','[0-9]+')->middleware('throttle:10,1');
    Route::post('user/audio-campaigns/{id}/pause','AudioCampaignController@pause')->where('id','[0-9]+')->middleware('throttle:20,1');
    Route::post('user/audio-campaigns/{id}/delete','AudioCampaignController@destroy')->where('id','[0-9]+')->middleware('throttle:20,1');
    Route::get('user/phone-scripts','MessagingController@scripts');
    Route::post('user/phone-scripts/download','MessagingController@scriptDownload');
    Route::get('user/business-listings','BusinessListingsController@index');
    Route::post('user/business-listings','BusinessListingsController@save');
    Route::get('user/business-listings/{id}/guide','BusinessListingsController@guide')->where('id','[0-9]+');
    Route::get('user/marketing-form','BizFormsController@marketing');
    Route::post('user/marketing-form','BizFormsController@marketingSave');
    Route::get('user/credit-application','BizFormsController@credit');
    Route::post('user/credit-application','BizFormsController@creditSave');
    Route::get('user/sms-campaigns','MessagingController@sms');
    Route::post('user/sms-campaigns/save','MessagingController@smsSave');
    Route::post('user/sms-campaigns/delete','MessagingController@smsDelete');
    Route::post('user/sms-campaigns/send','MessagingController@smsSend');
    Route::get('user/fax/{uuid}/file','FaxController@file')->where('uuid','[0-9a-fA-F-]{36}');
    Route::post('getaudionumbers','UserPanelController@getaudionumbers');
    Route::get('smscampaign','UserPanelController@smscampaign')->name('smscampaign');
    Route::get('viewSmsDetail','UserPanelController@viewSmsDetail')->name('viewSmsDetail');
    Route::post('getSmsSentDet','UserPanelController@getSmsSentDet');
    //delete sms Contact
    Route::post('deleteContact','UserPanelController@deleteContact');
    // Delete Email Contact
    Route::post('deleteEmailContact','UserPanelController@deleteEmailContact');
    Route::post('removeSmsText/{id}','UserPanelController@removeSmsText');
    Route::post('deleteSmsContact','UserPanelController@deleteSmsContact');
    Route::get('buy_credit','UserPanelController@buy_credit')->name('buy_credit');
    Route::post('smsCreditAction','UserPanelController@smsCreditAction');
    Route::post('smscampaign/sendsms','UserController@smsCampaignSendSms');
    Route::post('contactAction','UserController@contactAction');
    Route::get('users/connect/beta','UserPanelController@phone_plans')->name('phone_plans');
    Route::get('audiosubscriber','UserPanelController@audiosubscriber')->name('audiosubscriber');
    Route::post('saveAudioBroadcastNumber','UserPanelController@saveAudioBroadcastNumber')->name('saveAudioBroadcastNumber');
    Route::get('audiosubscriberDelete','UserPanelController@audiosubscriberDelete');
    Route::any('calls_history','UserPanelController@calls_history')->name('calls_history');
    Route::any('later_calls_campaign','UserPanelController@later_calls_campaign')->name('later_calls_campaign');
    Route::post('submitcallschedule','UserPanelController@submitcallschedule')->name('submitcallschedule');
    Route::post('act_getDids','UserPanelController@act_getDids')->name('act_getDids');



    Route::post('act_newfaxacc','UserPanelController@act_newfaxacc')->name('act_newfaxacc');







    Route::post('act_updateCreditLineInfo','ProfileController@updateCreditInfo')->name('updateCLInfo');







    Route::post('uploadvfacofficetypeeditor','UserController@uploadvfacofficetypeeditor')->name('uploadvfacofficetypeeditor');



    Route::post('coverpagecreatefax','UserController@coverpagecreatefax')->name('coverpagecreatefax');



    Route::post('uploadownfilevfax','UserController@uploadownfilevfax')->name('uploadownfilevfax');



    Route::post('uploadImageUri','UserController@uploadownfilevfaxCam')->name('uploadownfilevfaxCam');



    Route::get('removefaxdocument','UserController@removefaxdocument')->name('removefaxdocument');



    Route::get('removeallfaxdocument','UserController@removeallfaxdocument')->name('removeallfaxdocument');



    Route::post('uploadvfaxall','UserController@uploadvfaxall')->name('uploadvfaxall');



    Route::get('viewfaxattachment','UserController@viewfaxattachment')->name('viewfaxattachment');



    Route::get('deleteselectedfax','UserController@deleteselectedfax')->name('deleteselectedfax');



    Route::post('uploadquickfax','UserController@uploadquickfax')->name('uploadquickfax');



    Route::get('samplecover','UserController@samplecover')->name('samplecover');



    Route::post('updatescriptinfo','UserController@updatescriptinfo')->name('updatescriptinfo');



    /*Route::any('active_calls','UserPanelController@active_callsView')->name('active_callsView');*/







    Route::post('/user/updateBillingAddress', 'ProfileController@updateBillingAddress');



    Route::post('/user/updateaccount', 'ProfileController@updateCreditCardInfo');



    // User logout



    Route::get('/user/logout', 'RegisterController@logout');



    Route::get('/user/cronemail', 'UserController@cronSentEmail');



    });



    // Route::get('sendtestemail', 'TestController@sendemail');
    Route::get('sendbasicemail','MailController@basic_email');

 
// 2026-10-31: old marketing form -> new marketing form (public, before login)
Route::permanentRedirect('marketing-form', '/user/marketing-form');

// 2026-10-31 VO redesign: legacy customer pages -> VO pages (legacy code kept). Audio Campaign and addPort untouched.
Route::permanentRedirect('smscampaign', '/user/sms-campaigns');
Route::permanentRedirect('user/script_recording', '/user/phone-scripts');
Route::permanentRedirect('additional/credit_app', '/user/credit-application');

// 2026-11-01 Audio Campaign: converted recording fetched by the billing platform (per-recording unguessable token; only while a campaign uses it).
Route::get('ac-audio/{tok}','AudioCampaignController@playAudio')->where('tok','[a-f0-9]{48}(\.au)?')->middleware('throttle:120,1');

// v4: fix old 404 link
Route::get("affiliate-referral", function(){ return redirect("/affiliate-referrals", 301); });

// 2026-11-02 Drip email unsubscribe (signed link; POST supports one-click List-Unsubscribe)
Route::get('email/unsubscribe','DripUnsubscribeController@show')->name('drip.unsubscribe')->middleware('throttle:60,1');
Route::post('email/unsubscribe','DripUnsubscribeController@store')->middleware('throttle:60,1');

// 2026-11-02 cec: Customer email campaigns (rebuild). All endpoints scoped to session user_id.
Route::group(['middleware' => ['authentication']], function () {
    Route::get('user/email-campaigns', 'EmailCampaignController@index');
    Route::get('user/email-campaigns/contacts', 'EmailCampaignController@contacts')->middleware('throttle:120,1');
    Route::get('user/email-campaigns/contacts/export', 'EmailCampaignController@export')->middleware('throttle:10,1');
    Route::get('user/email-campaigns/contacts/sample.csv', 'EmailCampaignController@sampleCsv');
    Route::post('user/email-campaigns/contacts/save', 'EmailCampaignController@contactSave');
    Route::post('user/email-campaigns/contacts/bulk', 'EmailCampaignController@contactBulk')->middleware('throttle:60,1');
    Route::post('user/email-campaigns/contacts/import/preview', 'EmailCampaignController@importPreview');
    Route::post('user/email-campaigns/contacts/import', 'EmailCampaignController@importCommit')->middleware('throttle:20,1');
    Route::post('user/email-campaigns/audience', 'EmailCampaignController@audienceCount')->middleware('throttle:120,1');
    Route::post('user/email-campaigns/preview', 'EmailCampaignController@preview')->middleware('throttle:120,1');
    Route::post('user/email-campaigns/campaign/save', 'EmailCampaignController@save');
    Route::post('user/email-campaigns/campaign/test', 'EmailCampaignController@test');
    Route::post('user/email-campaigns/campaign/schedule', 'EmailCampaignController@schedule')->middleware('throttle:20,1');
    Route::post('user/email-campaigns/campaign/action', 'EmailCampaignController@action')->middleware('throttle:60,1');
    Route::get('user/email-campaigns/campaign/{id}/report', 'EmailCampaignController@report')->where('id', '[0-9]+');
    Route::post('user/email-campaigns/templates/delete', 'EmailCampaignController@templateDelete');
    Route::post('user/email-campaigns/smtp/save', 'EmailCampaignController@smtpSave')->middleware('throttle:10,1');
    Route::post('user/email-campaigns/smtp/delete', 'EmailCampaignController@smtpDelete');
    Route::post('user/email-campaigns/seq/create', 'EcAutomationController@seqCreate')->middleware('throttle:30,1');
    Route::post('user/email-campaigns/seq/update', 'EcAutomationController@seqUpdate')->middleware('throttle:60,1');
    Route::post('user/email-campaigns/step/save', 'EcAutomationController@stepSave')->middleware('throttle:60,1');
    Route::post('user/email-campaigns/step/delete', 'EcAutomationController@stepDelete')->middleware('throttle:60,1');
    Route::post('user/email-campaigns/auto/save', 'EcAutomationController@autoSave')->middleware('throttle:30,1');
    Route::post('user/email-campaigns/auto/update', 'EcAutomationController@autoUpdate')->middleware('throttle:60,1');
    Route::get("user/email-campaigns/seq/{id}/report", "TrackingController@seqReport")->where("id", "[0-9]+"); // trk
});
Route::match(['get', 'post'], 'ec/unsubscribe', 'EcPublicController@unsubscribe')->name('ec.unsubscribe')->middleware('throttle:60,1');
Route::get('ec/o', 'EcPublicController@open')->name('ec.open')->middleware('throttle:600,1');

// 2026-11-02 trk: open pixel + signed click redirect (all GVD email types)
Route::get('t/o/{tok}.gif', 'TrackingController@open')->where('tok', '[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+')->middleware('throttle:600,1');
Route::get('t/c/{tok}', 'TrackingController@click')->where('tok', '[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+')->middleware('throttle:300,1');

// 2026-11-02 mcp: AI Connect (customer API keys) + MCP endpoint. Hidden until AI_CONNECT_ENABLED=true (admin + demo preview).
Route::group(['middleware' => ['authentication']], function () {
    Route::get('user/ai-connect', 'AiConnectController@index');
    Route::post('user/ai-connect/keys/create', 'AiConnectController@create')->middleware('throttle:10,1');
    Route::post('user/ai-connect/keys/revoke', 'AiConnectController@revoke')->middleware('throttle:30,1');
    Route::post('user/ai-connect/toggle', 'AiConnectController@toggle')->middleware('throttle:30,1');
    Route::post('user/ai-connect/line/link', 'AiConnectController@linkLine')->middleware('throttle:10,1');
    Route::post('user/ai-connect/line/unlink', 'AiConnectController@unlinkLine')->middleware('throttle:10,1');
});
// 2026-11-06 Voice bot. voice.provision 404s every route while AI_VOICE_PROVISIONING_ENABLED is off.
Route::middleware(['voice.provision', 'authentication'])->group(function () {
    Route::get('user/ai-connect/voice-bot', 'VoiceBotController@index');
    Route::post('user/ai-connect/voice-bot', 'VoiceBotController@provision')->middleware('throttle:10,1');
    Route::post('user/ai-connect/voice-bot/key/remove', 'VoiceBotController@removeKey')->middleware('throttle:10,1');
    Route::post('user/ai-connect/voice-bot/remove', 'VoiceBotController@remove')->middleware('throttle:10,1');
});
// 2026-11-02 oauth: OAuth 2.1 for AI Connect (404 unless AI_CONNECT_OAUTH_ENABLED=true)
Route::post('user/ai-connect/apps/revoke', 'AiConnectController@revokeApp')->middleware(['authentication', 'throttle:30,1']);
Route::get('oauth/authorize', 'AiOAuthController@authorizeForm')->middleware('throttle:60,1');
Route::post('oauth/authorize', 'AiOAuthController@authorizeDecide')->middleware('throttle:30,1');
Route::group(['middleware' => ['throttle:120,1'], 'excluded_middleware' => [\App\Http\Middleware\VerifyCsrfToken::class, \Illuminate\Session\Middleware\StartSession::class, \Illuminate\View\Middleware\ShareErrorsFromSession::class, \App\Http\Middleware\EncryptCookies::class, \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class]], function () {
    Route::get('.well-known/oauth-protected-resource', 'AiOAuthController@protectedResource');
    Route::get('.well-known/oauth-protected-resource/mcp', 'AiOAuthController@protectedResource');
    Route::get('.well-known/oauth-authorization-server', 'AiOAuthController@authServer');
    Route::get('.well-known/oauth-authorization-server/mcp', 'AiOAuthController@authServer');
    Route::get('.well-known/openid-configuration', 'AiOAuthController@authServer');
    Route::post('oauth/register', 'AiOAuthController@register');
    Route::post('oauth/token', 'AiOAuthController@token');
    Route::post('oauth/revoke', 'AiOAuthController@revoke');
    Route::options('oauth/{any}', 'AiOAuthController@preflight')->where('any', 'register|token|revoke');
});
Route::match(['get', 'post', 'delete'], 'mcp', 'McpController@handle')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class, \Illuminate\Session\Middleware\StartSession::class, \Illuminate\View\Middleware\ShareErrorsFromSession::class, \App\Http\Middleware\EncryptCookies::class, \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class]);
