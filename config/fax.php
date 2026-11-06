<?php
// Global Voice Direct - inbound fax settings. Secrets come from .env only.
return [
    'webhook_secret' => env('RESEND_WEBHOOK_SECRET'),
    'resend_api_key' => env('RESEND_API_KEY'),
    'enc_key'        => env('FAX_ENC_KEY'),      // base64 of 32 random bytes (AES-256-GCM)
    'url_key'        => env('FAX_URL_KEY'),      // signs 5-minute view links
    'address_key'    => env('FAX_ADDRESS_KEY'),  // per-tenant address token
    'inbound_domain' => env('FAX_INBOUND_DOMAIN', 'iawhaagale.resend.app'),
    'retention_days' => (int) env('FAX_RETENTION_DAYS', 90),
    'disk_path'      => storage_path('app/private/faxes'), // never under public/
];
