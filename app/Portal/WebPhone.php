<?php

namespace App\Portal;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

/**
 * Browser phone credentials, read with the billing session that was opened when the customer signed in
 * to this portal (one set of credentials). The session lives only in the server-side Laravel session,
 * encrypted. Nothing here is logged or sent to the browser except the extensions' own SIP logins.
 */
class WebPhone
{
    public static function enabledFor(array $tenants): array
    {
        return array_values(array_intersect($tenants, config('webphone.tenants', [])));
    }

    private static function call(string $base, string $method, array $params, ?array $k = null): array
    {
        $form = ['params' => json_encode((object) $params)];
        $hdr = [];
        if ($k) {
            $form['auth_info'] = json_encode(['session_id' => $k['session_id']]);
            if ($k['csrf_token'] !== '') { $hdr['CSRF-Token'] = $k['csrf_token']; $hdr['X-CSRF-Token'] = $k['csrf_token']; }
            if ($k['access_token'] !== '') $hdr['Authorization'] = 'Bearer ' . $k['access_token'];
        }
        $j = Http::asForm()->withHeaders($hdr)->timeout(15)->connectTimeout(5)->post(rtrim($base, '/') . '/' . $method, $form)->json();
        if (!is_array($j)) throw new \RuntimeException('bad billing response');
        return $j;
    }

    private static function load(): ?array
    {
        $v = session()->get('gvd_billing');
        if (!is_string($v) || $v === '') return null;
        try { $k = json_decode(Crypt::decryptString($v), true); } catch (\Throwable $e) { return null; }
        return is_array($k) && !empty($k['session_id']) ? $k : null;
    }

    private static function save(array $k): void
    {
        session()->put('gvd_billing', Crypt::encryptString(json_encode($k)));
    }

    private static function probe(array $k): bool
    {
        $m = in_array($k['realm'], ['account', 'accounts'], true) ? 'Account/get_account_info' : 'Customer/get_customer_info';
        $r = self::call($k['base'], $m, [], $k);
        return empty($r['faultcode']) && (isset($r['customer_info']) || isset($r['account_info']));
    }

    /** Valid session: as stored, refreshed, or re-opened with the stored sign-in token. Null = sign in again. */
    private static function live(array $k): ?array
    {
        try { if (self::probe($k)) return $k; } catch (\Throwable $e) { /* try refresh */ }
        if ($k['refresh_token'] !== '') {
            try {
                $r = self::call($k['base'], 'Session/refresh_access_token', ['refresh_token' => $k['refresh_token']]);
                if (!empty($r['access_token'])) {
                    $n = array_merge($k, ['access_token' => (string) $r['access_token'], 'session_id' => (string) ($r['session_id'] ?? $k['session_id']),
                        'refresh_token' => (string) ($r['refresh_token'] ?? $k['refresh_token']), 'at' => time()]);
                    if (self::probe($n)) return $n;
                }
            } catch (\Throwable $e) { /* try jwt */ }
        }
        if ($k['jwt'] !== '' && $k['jwt_login'] !== '') {
            try {
                $s = self::call($k['base'], 'Session/login', ['login' => $k['jwt_login'], 'password' => 'JWT', 'token' => $k['jwt']]);
                if (!empty($s['session_id'])) {
                    $n = array_merge($k, ['session_id' => (string) $s['session_id'], 'csrf_token' => (string) ($s['csrf_token'] ?? ''),
                        'access_token' => (string) ($s['access_token'] ?? ''), 'refresh_token' => (string) ($s['refresh_token'] ?? ''), 'at' => time()]);
                    if (self::probe($n)) return $n;
                }
            } catch (\Throwable $e) { /* expired */ }
        }
        return null;
    }

    /** fax / VO user / DID / retired accounts are never phones. */
    private static function notPhone(array $a): bool
    {
        $id = strtolower((string) ($a['id'] ?? '') . ' ' . (string) ($a['login'] ?? ''));
        foreach (['fax', 'vo-user', 'vo-did', '-did', 'did', '-old'] as $w) if (str_contains($id, $w)) return true;
        return VirtualOfficeLogin::did((string) ($a['id'] ?? '')) !== null;
    }

    private static function blockedLine(array $a): bool
    {
        if (in_array((int) ($a['i_account'] ?? 0), config('webphone.never_accounts', []), true)) return true;
        foreach ([(string) ($a['id'] ?? ''), (string) ($a['login'] ?? '')] as $v) {
            $hay = preg_replace('/\D/', '', $v);
            foreach (config('webphone.never_lines', []) as $n) { $n = preg_replace('/\D/', '', $n); if ($n !== '' && str_contains($hay, $n)) return true; }
        }
        return false;
    }

    /** @param int[] $allowed allowlisted i_customer values of the signed-in portal user. @return array{status:int,body:array,stats:array} */
    public static function credentials(array $allowed): array
    {
        $again = ['status' => 401, 'body' => ['error' => 'Your sign-in has expired. Please sign out and sign in again to use the phone.', 'signin' => true], 'stats' => []];
        $k = self::load();
        if (!$k) return $again;
        if (!in_array((int) $k['i_customer'], $allowed, true)) return ['status' => 403, 'body' => ['error' => 'The browser phone is not enabled for your account.'], 'stats' => []];
        $k = self::live($k);
        if (!$k) { session()->forget('gvd_billing'); return $again; }
        self::save($k);
        $ic = (int) $k['i_customer'];
        $api = fn ($m, $p) => self::call($k['base'], $m, $p, $k);

        if (in_array($k['realm'], ['account', 'accounts'], true)) {
            $acc = $api('Account/get_account_info', [])['account_info'] ?? null;
            $accounts = $acc && (int) ($acc['i_customer'] ?? 0) === $ic ? [$acc] : [];
        } else {
            $accounts = $api('Account/get_account_list', ['i_customer' => $ic, 'limit' => 1000])['account_list'] ?? [];
        }
        $labels = [];
        try {
            foreach ($api('Customer/get_extensions_list', ['i_customer' => $ic])['extensions_list'] ?? [] as $x)
                if (!empty($x['i_account'])) $labels[(int) $x['i_account']] = ['ext' => (string) ($x['id'] ?? ''), 'name' => (string) ($x['name'] ?? '')];
        } catch (\Throwable $e) { /* labels are optional */ }

        $exts = []; $errors = []; $st = ['accounts' => count($accounts), 'labels' => count($labels), 'with_password' => 0, 'missing_password' => 0];
        // Only real PBX extensions (Customer/get_extensions_list) are ever offered; everything else is refused here.
        foreach ($accounts as $a) {
            if ((int) ($a['i_customer'] ?? $ic) !== $ic) continue;
            $ia = (int) ($a['i_account'] ?? 0);
            if ($ia <= 0 || !isset($labels[$ia]) || $labels[$ia]['ext'] === '') continue;   // not an extension
            if (self::blockedLine($a) || self::notPhone($a)) continue;
            if (($a['blocked'] ?? 'N') === 'Y' || in_array(($a['bill_status'] ?? 'O'), ['C', 'I'], true)) continue; // silently skipped
            $lab = $labels[$ia];
            $label = "Ext {$lab['ext']}" . ($lab['name'] !== '' ? " - {$lab['name']}" : '');
            $pw = (string) ($a['h323_password'] ?? '');
            if ($pw === '') {
                try { $pw = (string) (($api('Account/get_account_info', ['i_account' => $ia])['account_info'] ?? [])['h323_password'] ?? ''); } catch (\Throwable $e) { /* below */ }
            }
            if ($pw === '') { $st['missing_password']++; $errors[] = ['extension' => $label, 'error' => 'No phone password is available for this extension']; continue; }
            $st['with_password']++;
            $exts[] = ['key' => (string) $ia, 'label' => $label, 'ext' => $lab['ext'], 'plan' => (string) ($a['product_name'] ?? ''),
                'user' => (string) $a['id'], 'password' => $pw];
        }
        usort($exts, fn ($x, $y) => [$x['plan'], (int) $x['ext'], $x['label']] <=> [$y['plan'], (int) $y['ext'], $y['label']]);
        return ['status' => 200, 'stats' => $st, 'body' => ['wss' => config('webphone.wss'), 'domain' => config('webphone.sip_domain'),
            'iceServers' => [['urls' => config('webphone.stun')]], 'extensions' => $exts, 'errors' => $errors]];
    }
}
