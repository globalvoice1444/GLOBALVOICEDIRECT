<?php
// 2026-11-02 Customer email campaigns. EC_SENDING=false stops every customer campaign send (global kill switch).
return [
    'sending' => env('EC_SENDING', false),
    'daily_limit' => (int) env('EC_DAILY_LIMIT', 500),
    'new_sender_limit' => (int) env('EC_NEW_SENDER_LIMIT', 100),
    'new_sender_days' => 7,
    'spacing_seconds' => 3,
    'batch' => (int) env('EC_BATCH', 50),
    'max_fail_rate' => 0.10,      // pause a campaign when >10% of 20+ attempts fail
    'max_bounce_rate' => 0.05,    // pause sender when >5% of last 7 days' sends bounced (webhook)
    'max_complaint_rate' => 0.003,
    'max_csv_bytes' => 5 * 1024 * 1024,
    'max_csv_rows' => 20000,
    'max_contacts' => 25000,
    'webhook_secret' => env('EC_WEBHOOK_SECRET'),
];
