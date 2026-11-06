<?php

namespace App\Portal;

use Illuminate\Support\Facades\Cache;

/**
 * Which optional menu entries have data for the signed-in tenant (Dial out, Recorded conferences, Dial out numbers).
 * Computed after the response is sent and cached 10 minutes; until known, those entries stay hidden.
 */
class VoMenu
{
    public static function flags(): array
    {
        $ic = BillingSession::storedCustomer();
        if (!$ic) return [];
        $key = "gvd-vo:menu:$ic";
        $f = Cache::get($key);
        if (is_array($f)) return $f;
        if (!app()->bound('gvd-vo-menu-scheduled')) {
            app()->instance('gvd-vo-menu-scheduled', true);
            $k = BillingSession::get();
            if ($k) app()->terminating(function () use ($k, $key) {
                try {
                    $v = new VoData($k);
                    $tz = new \DateTimeZone('UTC');
                    $r = $v->many([
                        'acc' => ['Account/get_account_list', ['i_customer' => $v->ic, 'limit' => 500, 'bill_status' => 'O']],
                        'dial' => ['VO/get_customer_xdrs', ['type' => 'dial_out', 'i_service' => 3, 'show_unsuccessful' => 1, 'limit' => 1, 'offset' => 0,
                            'from_date' => (new \DateTimeImmutable('-60 days', $tz))->format('Y-m-d H:i:s'), 'to_date' => (new \DateTimeImmutable('now', $tz))->format('Y-m-d H:i:s')]],
                        'conf' => ['LS_Conference/get_room_recordings', ['secure' => 1, 'limit' => 1, 'offset' => 0]],
                    ]);
                    if ($r['acc'] === null) return;
                    $pinless = false; foreach ($r['acc']['account_list'] ?? [] as $a) if (preg_match('/^a/i', (string) ($a['id'] ?? ''))) $pinless = true;
                    Cache::put($key, ['dialOutNumbers' => $pinless, 'dialOut' => $pinless || count($r['dial']['xdr_list'] ?? []) > 0,
                        'confRecordings' => count($r['conf']['recordings_list'] ?? []) > 0], 600);
                } catch (\Throwable $e) {}
            });
        }
        return [];
    }
}
