<?php defined('SIGNUP') or die('Restricted access'); 
$mail = array (
  'phone_link' => '',
  'cust_from' => '',
  'cust_subj' => 
  array (
    'en' => 'Subscriber info',
  ),
  'cust_message' => 
  array (
    'en' => 'Your account number is: $number;<br>Account login: $cust_login<br>Account password:$cust_password<br>Access page: $cust_interface',
  ),
  'signup_notification_to' => '',
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
    'en' => 'Please, follow the link $link to complete.',
  ),
  'error_notification_to' => '',
  'error_notification_from' => '',
  'error_notification_subj' => 'an error occured during signup',
  'error_notification_message' => '$e',
);
?>