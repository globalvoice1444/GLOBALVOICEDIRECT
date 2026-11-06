<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/** Thin wrapper over the provider already used by the app (config/bulkVs.php). Only called when gvd_sms.live is true. */
class SmsGateway
{
    public static function send(string $from, string $to, string $text): bool
    {
        if (!config('gvd_sms.live')) return false;
        $u = (string) config('bulkVs.username'); $p = (string) config('bulkVs.password');
        if ($u === '' || $p === '') return false;
        try {
            $r = Http::withBasicAuth($u, $p)->timeout(20)->post('https://portal.bulkvs.com/api/v1.0/messageSend', ['From' => $from, 'To' => [$to], 'Message' => $text]);
            $j = $r->json();
            return $r->successful() && (($j['Results'][0]['Status'] ?? '') === 'SUCCESS');
        } catch (\Throwable $e) { Log::warning('gvd-sms send failed', ['e' => get_class($e)]); return false; }
    }
}
