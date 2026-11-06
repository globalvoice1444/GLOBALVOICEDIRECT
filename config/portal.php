<?php
return [
    // Billing REST endpoint used to verify Virtual Office (customer self-care) logins.
    'billing_rest' => env('VO_BILLING_REST', 'https://mybilling.telinta.com/rest'),
    // Account-level Virtual Office logins (what the :8092 portal falls back to): VO/get_login_token on :8445.
    'vo_account_rest' => env('VO_ACCOUNT_REST', 'https://mybilling.globalvoicedirect.com:8445/rest'),
    // The :8092 portal sends its API calls to its own origin.
    'vo_portal_rest' => env('VO_PORTAL_REST', 'https://mybilling.globalvoicedirect.com:8092/rest'),
    'vo_login_enabled' => (bool) env('VO_LOGIN_ENABLED', true),
];
