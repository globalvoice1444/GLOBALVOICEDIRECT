<?php

namespace App\Portal;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

/** The customer's own billing session kept (encrypted) in the Laravel session at sign-in. Never logged. */
class BillingSession
{
    public static function call(array $k, string $method, array $params = []): array
    {
        $hdr = [];
        if (($k['csrf_token'] ?? '') !== '') { $hdr['CSRF-Token'] = $k['csrf_token']; $hdr['X-CSRF-Token'] = $k['csrf_token']; }
        if (($k['access_token'] ?? '') !== '') $hdr['Authorization'] = 'Bearer ' . $k['access_token'];
        $j = Http::asForm()->withHeaders($hdr)->timeout(20)->connectTimeout(5)->post(rtrim($k['base'], '/') . '/' . $method,
            ['params' => json_encode((object) $params), 'auth_info' => json_encode(['session_id' => $k['session_id']])])->json();
        if (!is_array($j)) throw new \RuntimeException('bad billing response');
        return $j;
    }

    private static function probe(array $k): bool
    {
        $m = in_array($k['realm'], ['account', 'accounts'], true) ? 'Account/get_account_info' : 'Customer/get_customer_info';
        $r = self::call($k, $m);
        return empty($r['faultcode']) && (isset($r['customer_info']) || isset($r['account_info']));
    }

    /** i_customer of the stored session without contacting billing (for tenant checks first). */
    public static function storedCustomer(): ?int
    {
        $v = session()->get('gvd_billing');
        if (!is_string($v) || $v === '') return null;
        try { $k = json_decode(Crypt::decryptString($v), true); } catch (\Throwable $e) { return null; }
        return is_array($k) && !empty($k['i_customer']) ? (int) $k['i_customer'] : null;
    }

    /** Valid session (as stored, refreshed, or re-opened with the stored sign-in token), or null = sign in again. */
    public static function get(): ?array
    {
        $v = session()->get('gvd_billing');
        if (!is_string($v) || $v === '') return null;
        try { $k = json_decode(Crypt::decryptString($v), true); } catch (\Throwable $e) { return null; }
        if (!is_array($k) || empty($k['session_id'])) return null;
        $ok = null;
        try { if (self::probe($k)) $ok = $k; } catch (\Throwable $e) {}
        if (!$ok && ($k['refresh_token'] ?? '') !== '') {
            try {
                $r = Http::asForm()->timeout(15)->post(rtrim($k['base'], '/') . '/Session/refresh_access_token', ['params' => json_encode(['refresh_token' => $k['refresh_token']])])->json();
                if (!empty($r['access_token'])) {
                    $n = array_merge($k, ['access_token' => (string) $r['access_token'], 'session_id' => (string) ($r['session_id'] ?? $k['session_id']), 'refresh_token' => (string) ($r['refresh_token'] ?? $k['refresh_token'])]);
                    if (self::probe($n)) $ok = $n;
                }
            } catch (\Throwable $e) {}
        }
        if (!$ok && ($k['jwt'] ?? '') !== '' && ($k['jwt_login'] ?? '') !== '') {
            try {
                $s = Http::asForm()->timeout(15)->post(rtrim($k['base'], '/') . '/Session/login', ['params' => json_encode(['login' => $k['jwt_login'], 'password' => 'JWT', 'token' => $k['jwt']])])->json();
                if (!empty($s['session_id'])) {
                    $n = array_merge($k, ['session_id' => (string) $s['session_id'], 'csrf_token' => (string) ($s['csrf_token'] ?? ''), 'access_token' => (string) ($s['access_token'] ?? ''), 'refresh_token' => (string) ($s['refresh_token'] ?? '')]);
                    if (self::probe($n)) $ok = $n;
                }
            } catch (\Throwable $e) {}
        }
        if (!$ok) { session()->forget('gvd_billing'); return null; }
        session()->put('gvd_billing', Crypt::encryptString(json_encode($ok)));
        return $ok;
    }
}
