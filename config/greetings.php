<?php
return [
    // Voice-application REST used by the Virtual Office portal for greetings (auto attendant + voicemail).
    'um_rest' => env('GREETINGS_UM_REST', 'https://mybilling.globalvoicedirect.com:8092/um-rest'),
    'um_domain' => env('GREETINGS_UM_DOMAIN', 'um.globalvoicedirect.com'),
    'max_upload_kb' => (int) env('GREETINGS_MAX_UPLOAD_KB', 8192),
    // Never touched by this page (billing account ids / numbers).
    'never_accounts' => [52902139],
    'never_lines' => ['5613635538', '3635538'],
];
