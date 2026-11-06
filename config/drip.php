<?php
// 2026-11-02 Drip email automation. Global kill switch: nothing is sent to customers unless DRIP_SENDING=true
// AND the sequence is ACTIVE AND the step is ACTIVE.
return [
    'enabled' => env('DRIP_SENDING', false),
    'min_hours_between' => (int) env('DRIP_MIN_HOURS_BETWEEN', 20),   // per-user frequency cap across all sequences
    'max_late_days' => (int) env('DRIP_MAX_LATE_DAYS', 3),            // steps overdue longer than this are skipped, not burst-sent
    'batch' => (int) env('DRIP_BATCH', 15),                            // max emails per scheduler run (2026-11-05: 50 -> 15)
    'hourly_cap' => (int) env('DRIP_HOURLY_CAP', 90),                  // 2026-11-05 max successful sends per rolling hour, all mailers combined
    'quota_pause_minutes' => (int) env('DRIP_QUOTA_PAUSE_MINUTES', 60), // 2026-11-05 pause all mailers this long after an SMTP quota/rate-limit error
    'reply_to' => env('MAIL_REPLY_TO_ADDRESS', 'support@globalvoicedirect.com'),
    'app_url' => 'https://app.globalvoicedirect.com',
    'address' => '165 Halsey St, Suite 106',
    'phone' => '(877) 308-5803',
    'voice_note_url' => env('DRIP_VOICE_NOTE_URL', ''),   // optional pre-recorded welcome voice note (https)
];
