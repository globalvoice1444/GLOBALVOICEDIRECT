<?php
// Audio Campaign (2026-11-01). Calling stays OFF unless AUDIO_CAMPAIGN_SENDING=true. Secrets only from .env, never sent to the browser or logged.
return [
    'sending' => filter_var(env('AUDIO_CAMPAIGN_SENDING', false), FILTER_VALIDATE_BOOLEAN),
    'callback_url' => env('AUDIO_CAMPAIGN_CALLBACK_URL', 'https://mybilling.globalvoicedirect.com:8901/cgi/web/receive.pl'),
    'ivr_number' => env('AUDIO_CAMPAIGN_IVR_NUMBER', ''),   // account ID of the auto-attendant that plays the recording
    'max_numbers' => 100,
    'throttle_seconds' => 2,
    'max_upload_kb' => 10240,
    // Uploaded recordings are converted once to 8 kHz mono mu-law .au (the format play_prompt accepts) with a free static ffmpeg.
    'ffmpeg' => env('AUDIO_CAMPAIGN_FFMPEG', '/home/gvdcpanel/bin/ffmpeg'),
    'audio_base_url' => env('AUDIO_CAMPAIGN_AUDIO_URL', rtrim(env('APP_URL', 'https://app.globalvoicedirect.com'), '/') . '/ac-audio'),
    // 'ivr' = web callback to the shared auto-attendant (current path); 'callcontrol' = play_prompt with the recording URL (needs provider confirmation).
    'delivery' => env('AUDIO_CAMPAIGN_DELIVERY', 'ivr'),
];
