<?php
return [
    // Only these billing customers (i_customer) may use the browser phone.
    'tenants' => array_values(array_filter(array_map('intval', explode(',', env('WEBPHONE_TENANTS', '325037'))))),
    // Lines that must never register from the browser (billing account ids / numbers).
    'never_accounts' => array_values(array_filter(array_map('intval', explode(',', env('WEBPHONE_NEVER_ACCOUNTS', '52902139'))))),
    'never_lines' => array_values(array_filter(explode(',', env('WEBPHONE_NEVER_LINES', '5613635538,3635538')))),
    'wss' => env('WEBPHONE_WSS', 'wss://rtcsip.mybilling.net/wss'),
    'sip_domain' => env('WEBPHONE_SIP_DOMAIN', '208.89.105.229'),
    'stun' => env('WEBPHONE_STUN', 'stun:stun.l.google.com:19302'),
];
