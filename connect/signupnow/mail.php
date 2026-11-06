<?php defined('SIGNUP') or die('Restricted access'); 
$mail = array (
  'phone_link' => 'https://play.google.com/store/apps/details?id=net.mobyx.android&hl=en',
  'cust_from' => 'support@globalvoicedirect.com',
  'cust_subj' => 
  array (
    'en' => 'Subscriber info',
  ),
  'cust_message' => 
  array (
    'en' => 'Your account number is: $number;<br>Account login: $acc_login<br>Account password:$acc_password<br>Access page: $acc_interface<br><br>Link to our Android App <a href="https://play.google.com/store/apps/details?id=net.mobyx.android&amp;hl=en" target="" rel="">https://play.google.com/store/apps/details?id=net.mobyx.android&amp;hl=en</a><br><br>Link to our IOS App&nbsp; <a href="https://itunes.apple.com/us/app/mobyx/id924542684?mt=8" target="" rel="">https://itunes.apple.com/us/app/mobyx/id924542684?mt=8</a><br><br><br><ol><li><img alt="" src="https://mybilling.globalvoicedirect.com:8082/sites/globalvoicedirect.com/files/globaltech.png"></li></ol>',
  ),
  'signup_notification_to' => 'support@globalvoicedirect.com',
  'signup_notification_from' => '',
  'signup_notification_subj' => 'New customer subscribed',
  'signup_notification_message' => 'New customer subscribed. Customer name: $cust_name Account ID: $number',
  'sms_validation_message' => 'Verification code:$code (session:$session)',
  'paypal_pending_subject' => 
  array (
    'en' => 'PayPal payment status check',
  ),
  'paypal_pending_message' => 
  array (
    'en' => 'Please, follow the link $link once PayPal transaction is complete (sometimes it takes a while to complete the transaction).',
  ),
  'email_confirm_subject' => 
  array (
    'en' => 'Signup confirmation',
  ),
  'email_confirm_message' => 
  array (
    'en' => 'Please, follow the link $link to complete.<br><br>Link to our Android App <a href="https://play.google.com/store/apps/details?id=net.mobyx.android&amp;hl=en" target="" rel="">https://play.google.com/store/apps/details?id=net.mobyx.android&amp;hl=en</a><br><br>Link to our IOS App&nbsp; <a href="https://itunes.apple.com/us/app/mobyx/id924542684?mt=8" target="" rel="">https://itunes.apple.com/us/app/mobyx/id924542684?mt=8</a><br><br><br>',
  ),
  'error_notification_to' => '',
  'error_notification_from' => '',
  'error_notification_subj' => 'an error occured during signup',
  'error_notification_message' => '$e',
);
?>