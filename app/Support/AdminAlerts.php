<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/** Admin control-panel alerts (no email). Never throws. */
class AdminAlerts
{
    public static function log(string $type, string $severity, string $title, array $details = [], ?int $userId = null, ?int $iCustomer = null): ?int
    {
        try {
            if ($userId === null && session()->has('user_id')) $userId = (int) session()->get('user_id');
            if ($iCustomer === null) {
                try { $k = \App\Portal\BillingSession::get(); if ($k && !empty($k['i_customer'])) $iCustomer = (int) $k['i_customer']; } catch (\Throwable $e) {}
            }
            return DB::table('gvd_admin_alerts')->insertGetId([
                'type' => mb_substr($type, 0, 40), 'severity' => mb_substr($severity, 0, 20), 'title' => mb_substr($title, 0, 200),
                'details' => json_encode($details, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'i_customer' => $iCustomer, 'user_id' => $userId, 'ip' => request()->ip(),
                'created_at' => now(), 'updated_at' => now(),
            ]);
        } catch (\Throwable $e) { Log::warning('admin alert failed', ['e' => get_class($e)]); return null; }
    }

    public static function unread(?string $severity = null): int
    {
        try { $q = DB::table('gvd_admin_alerts')->whereNull('read_at'); if ($severity) $q->where('severity', $severity); return $q->count(); }
        catch (\Throwable $e) { return 0; }
    }
}
