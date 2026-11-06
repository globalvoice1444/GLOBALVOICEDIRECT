<?php defined('SIGNUP') or die('Restricted access'); 
$config=array (
  'server_url' => 'mybilling.globalvoicedirect.com',
  'references' => 
  array (
    'account_sci' => 'https://mybilling.globalvoicedirect.com:8082/',
    'customer_sci' => 'https://mybilling.globalvoicedirect.com:8082/',
  ),
  'submit_note' => '',
  'login' => getenv('TELINTA_SIGNUP_LOGIN'),
  'password' => getenv('TELINTA_SIGNUP_PASSWORD'),
  'service' => 'Admin',
  'env' => '119',
  'valid_email_domains' => '',
);
$packages = array(
array (
  'virtoffice' => true,
  'virtoffice_desc' => '3',
  'qrcode' => true,
  'subscriber' => 
  array (
    'id_source' => 'DID_API',
    'billing_model' => 1,
    'i_account_balance_control_type' => 3,
    'opening_balance_a' => 0.0,
    'acl_a' => 155,
    'batch_name' => '270239-vo-did',
    'ecommerce_enabled' => 'N',
    'follow_me_enabled' => 'N',
    'currency' => 'USD',
    'i_parent' => 270239,
    'blocked_a' => 'N',
    'customer_class' => 3786,
    'i_balance_control_type' => 1,
    'opening_balance_c' => 0.0,
    'acl_c' => 136,
    'blocked_c' => 'N',
    'i_billing_period' => 5,
  ),
  'config' => 
  array (
    'didapi_countries' => 
    array (
      0 => 'CA',
      1 => 'US',
    ),
    'offset' => '0',
    'limit' => '50',
  ),
  'did_mask' => 
  array (
    'patern' => 
    array (
      'direct' => '',
      'back' => '',
    ),
    'replace' => 
    array (
      'direct' => '',
      'back' => '',
    ),
  ),
  'template_account' => 
  array (
    'currency' => 'USD',
    'id' => '18773085803',
  ),
  'description' => 
  array (
    'en' => 'SMB standard phone plan Virtual Office Plan',
  ),
  'amount' => '24.95',
  'i_product' => '23822',
),
array (
  'virtoffice' => true,
  'virtoffice_desc' => '2',
  'qrcode' => true,
  'subscriber' => 
  array (
    'id_source' => 'DID_API',
    'billing_model' => 1,
    'i_account_balance_control_type' => 3,
    'opening_balance_a' => 0.0,
    'acl_a' => 155,
    'batch_name' => '270239-vo-did',
    'ecommerce_enabled' => 'N',
    'follow_me_enabled' => 'N',
    'currency' => 'USD',
    'i_parent' => 270239,
    'blocked_a' => 'N',
    'customer_class' => 3786,
    'i_balance_control_type' => 1,
    'opening_balance_c' => 0.0,
    'acl_c' => 136,
    'blocked_c' => 'N',
    'i_billing_period' => 5,
  ),
  'config' => 
  array (
    'didapi_countries' => 
    array (
      0 => 'CA',
      1 => 'US',
    ),
    'offset' => '0',
    'limit' => '50',
  ),
  'did_mask' => 
  array (
    'patern' => 
    array (
      'direct' => '',
      'back' => '',
    ),
    'replace' => 
    array (
      'direct' => '',
      'back' => '',
    ),
  ),
  'template_account' => 
  array (
    'currency' => 'USD',
    'id' => '18773085803',
  ),
  'description' => 
  array (
    'en' => 'SMALL BUSINESS PREMIUM ',
  ),
  'amount' => '49.95',
  'i_product' => '10692',
),
array (
  'virtoffice' => true,
  'virtoffice_desc' => '4',
  'qrcode' => true,
  'subscriber' => 
  array (
    'id_source' => 'DID_API',
    'billing_model' => 1,
    'i_account_balance_control_type' => 3,
    'opening_balance_a' => 0.0,
    'acl_a' => 155,
    'batch_name' => '270239-vo-did',
    'ecommerce_enabled' => 'N',
    'follow_me_enabled' => 'N',
    'currency' => 'USD',
    'i_parent' => 270239,
    'blocked_a' => 'N',
    'customer_class' => 3786,
    'i_balance_control_type' => 1,
    'opening_balance_c' => 0.0,
    'acl_c' => 136,
    'blocked_c' => 'N',
    'i_billing_period' => 5,
  ),
  'config' => 
  array (
    'didapi_countries' => 
    array (
      0 => 'CA',
      1 => 'US',
    ),
    'offset' => '0',
    'limit' => '50',
  ),
  'did_mask' => 
  array (
    'patern' => 
    array (
      'direct' => '',
      'back' => '',
    ),
    'replace' => 
    array (
      'direct' => '',
      'back' => '',
    ),
  ),
  'template_account' => 
  array (
    'currency' => 'USD',
    'id' => '18773085803',
  ),
  'description' => 
  array (
    'en' => 'Enterprise Virtual Office Plan',
  ),
  'amount' => '84.95',
  'i_product' => '10689',
),
array (
  'virtoffice' => true,
  'virtoffice_desc' => '6',
  'qrcode' => true,
  'subscriber' => 
  array (
    'id_source' => 'DID_API',
    'billing_model' => 1,
    'i_account_balance_control_type' => 3,
    'opening_balance_a' => 0.0,
    'acl_a' => 155,
    'batch_name' => '270239-vo-did',
    'ecommerce_enabled' => 'N',
    'follow_me_enabled' => 'N',
    'currency' => 'USD',
    'i_parent' => 270239,
    'blocked_a' => 'N',
    'customer_class' => 3786,
    'i_balance_control_type' => 1,
    'opening_balance_c' => 0.0,
    'acl_c' => 136,
    'blocked_c' => 'N',
    'i_billing_period' => 5,
  ),
  'config' => 
  array (
    'didapi_countries' => 
    array (
      0 => 'US',
    ),
    'offset' => '0',
    'limit' => '50',
  ),
  'did_mask' => 
  array (
    'patern' => 
    array (
      'direct' => '',
      'back' => '',
    ),
    'replace' => 
    array (
      'direct' => '',
      'back' => '',
    ),
  ),
  'template_account' => 
  array (
    'currency' => 'USD',
    'id' => '18773085803',
  ),
  'description' => 
  array (
    'en' => 'Business Sip Trunking, Call Center',
  ),
  'amount' => '19.95',
  'i_product' => '10576',
)
);
?>