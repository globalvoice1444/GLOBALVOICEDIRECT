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
    'en' => '<img alt="" src="https://globalvoicedirect.com/wp-content/uploads/2015/05/title-image.jpg"><br>Your account number is: $number;<br>Account login: $cust_login<br>Account password:$cust_password<br>Access page: $cust_interface<br><br>Link to our Android App <a href="https://play.google.com/store/apps/details?id=net.mobyx.android&amp;hl=en" target="" rel="">https://play.google.com/store/apps/details?id=net.mobyx.android&amp;hl=en</a><br><br>Link to our IOS App&nbsp; <a href="https://itunes.apple.com/us/app/mobyx/id924542684?mt=8" target="" rel="">https://itunes.apple.com/us/app/mobyx/id924542684?mt=8</a><br><br><br><br>',
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
    'en' => '<img alt="" src="https://globalvoicedirect.com/wp-content/uploads/2015/05/title-image.jpg"><br><br>Please, follow the link $link to complete.<br><br><br>Link to our Android App&nbsp;<a href="https://play.google.com/store/apps/details?id=net.mobyx.android&amp;hl=en" target="" rel="">https://play.google.com/store/apps/details?id=net.mobyx.android&amp;hl=en</a><br><br>Link to our IOS App&nbsp;&nbsp;<a href="https://itunes.apple.com/us/app/mobyx/id924542684?mt=8" target="" rel="">https://itunes.apple.com/us/app/mobyx/id924542684?mt=8</a><br><br><br><br><br><br>',
  ),
  'error_notification_to' => 'support@globalvoicedirect.com',
  'error_notification_from' => '',
  'error_notification_subj' => 'an error occured during signup',
  'error_notification_message' => '$e',
);
?>