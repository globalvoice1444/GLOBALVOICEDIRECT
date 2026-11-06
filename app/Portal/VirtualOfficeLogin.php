<?php

namespace App\Portal;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * Fallback portal login with Virtual Office (billing self-care) credentials.
 * Only used after the normal wpgc_users check fails. Passwords are never logged or stored.
 * Accepts CUSTOMER-realm logins only (admins/resellers/SIP accounts are refused).
 */
class VirtualOfficeLogin
{
    const MAX_PER_MINUTE = 5;

    /** Which verification path succeeded (reported once in the 'login ok' line). */
    private static ?string $path = null;

    /** Billing session kept after a successful login (server-side Laravel session only; never logged or sent to the browser). */
    public static ?array $kept = null;
    private static ?string $jwt = null;

    public static ?string $lastStage = null;
    public static function diag(string $stage, array $ctx = []): void
    {
        if ($stage !== 'api' || !empty($ctx['fault']) || ($ctx['http'] ?? 200) >= 400) self::$lastStage = $stage . ($stage === 'api' ? ':' . ($ctx['method'] ?? '') . ':' . ($ctx['http'] ?? '') . ':' . ($ctx['fault'] ?? '') : '');
        if (in_array($stage, ['jwt_session_ok', 'admin_lookup_ok'], true)) self::$path = $stage . (isset($ctx['by']) ? ':' . $ctx['by'] : '');
    }

    /** @return int|null wpgc_users.ID of the linked portal user, or null (= normal "wrong credentials"). */
    public static function attempt(string $login, string $password, string $ip): ?int
    {
        $login = trim($login);
        self::diag('attempt', ['user_present' => $login !== '', 'pass_present' => $password !== '', 'user_has_at' => str_contains($login, '@'), 'user_len' => strlen($login)]);
        if ($login === '' || $password === '' || strlen($login) > 100 || strlen($password) > 200) { self::diag('input_rejected'); return null; }
        $key = 'vo-login:' . sha1(strtolower($login) . '|' . $ip);
        if (RateLimiter::tooManyAttempts($key, self::MAX_PER_MINUTE)) { self::diag('rate_limited'); return null; }
        RateLimiter::hit($key, 60);

        try {
            $cust = self::verifyWithBilling($login, $password);
        } catch (\Throwable $e) {
            Log::warning('gvd-vo login billing unreachable', ['error' => get_class($e)]);
            return null; // unreachable = normal failure
        }
        if (!$cust) { Log::notice('gvd-vo login rejected', ['stage' => self::$lastStage, 'user_len' => strlen($login), 'user_has_at' => str_contains($login, '@')]); self::diag('billing_rejected'); return null; }
        RateLimiter::clear($key);
        try {
            $uid = self::linkUser($cust);
        } catch (\Throwable $e) {
            Log::error('gvd-vo link failed', ['i_customer' => $cust['i_customer'], 'error' => get_class($e), 'message' => self::safeMessage($e)]);
            return null;
        }
        Log::info('gvd-vo login ok', ['i_customer' => $cust['i_customer'], 'user_id' => $uid, 'path' => self::$path ?? 'customer_login']);
        return $uid;
    }

    private static function api(string $method, array $params, ?string $session = null, ?array $authInfo = null, ?string $base = null, array $headers = []): array
    {
        $form = [];
        if ($params || !$authInfo) $form['params'] = json_encode((object) $params);
        if ($session) $form['auth_info'] = json_encode(['session_id' => $session]);
        if ($authInfo) $form['auth_info'] = json_encode($authInfo);
        $r = Http::asForm()->withHeaders($headers)->timeout(10)->connectTimeout(5)->post(rtrim($base ?? config('portal.billing_rest'), '/') . '/' . $method, $form);
        $j = $r->json();
        self::diag('api', ['method' => $method, 'http' => $r->status(), 'fault' => is_array($j) ? ($j['faultcode'] ?? null) : 'non-json',
            'keys' => is_array($j) ? implode(',', array_slice(array_keys($j), 0, 8)) : null]);
        if (!is_array($j)) throw new \RuntimeException('bad billing response');
        return $j;
    }

    /**
     * Same order as the Virtual Office portal (:8092):
     *  1. Session/login {login,password} (customer self-care login);
     *  2. if that fails: POST :8445/rest/VO/get_login_token auth_info={login,password} -> {jwt},
     *     then Session/login {login: jwt.login, password:"JWT", token: jwt} (account-level VO login).
     * Only the customer and account realms are accepted; the account is mapped to its i_customer.
     */
    public static function verifyWithBilling(string $login, string $password): ?array
    {
        // Speed-up 2026-11-01y: logins known to use the account (JWT) path skip the customer Session/login (~0.7s).
        $pk = 'vo-path:' . sha1(strtolower($login));
        if (\Illuminate\Support\Facades\Cache::get($pk) === 'jwt') {
            $r = self::verifyJwt($login, $password);
            if ($r) return $r;
        }
        $s = self::api('Session/login', ['login' => $login, 'password' => $password]);
        if (!empty($s['session_id'])) { \Illuminate\Support\Facades\Cache::forget($pk); return self::readSession($s, $login); }
        $r = self::verifyJwt($login, $password);
        if ($r) \Illuminate\Support\Facades\Cache::put($pk, 'jwt', now()->addDays(30));
        return $r;
    }

    private static function verifyJwt(string $login, string $password): ?array
    {
        $t = self::api('VO/get_login_token', [], null, ['login' => $login, 'password' => $password], config('portal.vo_account_rest'));
        $jwt = (string) ($t['jwt'] ?? '');
        if ($jwt === '') return null;
        self::$jwt = $jwt;
        // The JWT was issued by the billing system over TLS in direct response to these credentials,
        // so its claims identify the account (the VO username can be an alias of the account login).
        $claims = self::jwtClaims($jwt);
        $jl = (string) ($claims['login'] ?? '');
        $jia = (int) ($claims['i_account'] ?? 0);
        self::diag('vo_token', ['claim_keys' => implode(',', array_keys($claims)), 'login_len' => strlen($jl), 'has_i_account' => $jia > 0,
            'typed_equals_claim' => strcasecmp($jl, $login) === 0]);
        if ($jl === '' && $jia <= 0) { self::diag('jwt_no_identity'); return null; }
        // a) exactly like the portal: same-origin (:8092) Session/login with the JWT.
        foreach ([config('portal.vo_portal_rest'), config('portal.billing_rest')] as $base) {
            try {
                $s = self::api('Session/login', ['login' => $jl, 'password' => 'JWT', 'token' => $jwt], null, null, $base);
                if (!empty($s['session_id'])) {
                    self::diag('jwt_session_ok', ['host' => parse_url($base, PHP_URL_HOST) . ':' . (parse_url($base, PHP_URL_PORT) ?: 443)]);
                    $r = self::readSession($s, $login, $base);
                    if ($r) return $r;
                }
            } catch (\Throwable $e) { self::diag('jwt_session_error', ['error' => get_class($e)]); }
        }
        return null; // no admin fallback: the JWT session is the only accepted path
    }

    /** Two parameterless session calls in parallel (same form/headers as api()). Failed/non-JSON responses come back as []. */
    private static function apiPair(string $sid, ?string $base, array $hdr, string $m1, string $m2): array
    {
        $url = rtrim($base ?? config('portal.billing_rest'), '/') . '/';
        $form = ['params' => '{}', 'auth_info' => json_encode(['session_id' => $sid])];
        $rs = \Illuminate\Support\Facades\Http::pool(fn ($p) => [
            $p->asForm()->withHeaders($hdr)->timeout(10)->connectTimeout(5)->post($url . $m1, $form),
            $p->asForm()->withHeaders($hdr)->timeout(10)->connectTimeout(5)->post($url . $m2, $form),
        ]);
        $out = [];
        foreach ([$m1, $m2] as $i => $m) {
            $j = ($rs[$i] instanceof \Illuminate\Http\Client\Response) ? $rs[$i]->json() : null;
            self::diag('api', ['method' => $m, 'http' => ($rs[$i] instanceof \Illuminate\Http\Client\Response) ? $rs[$i]->status() : 0, 'fault' => is_array($j) ? ($j['faultcode'] ?? null) : 'non-json']);
            $out[] = is_array($j) ? $j : [];
        }
        return $out;
    }

    private static function readSession(array $s, string $login, ?string $base = null): ?array
    {
        $sid = $s['session_id'];
        // Sessions opened with enable_csrf_protection need the returned csrf_token on every call.
        $hdr = [];
        if (!empty($s['csrf_token'])) { $hdr['CSRF-Token'] = (string) $s['csrf_token']; $hdr['X-CSRF-Token'] = (string) $s['csrf_token']; } // portal uses 'CSRF-Token'
        if (!empty($s['access_token'])) $hdr['Authorization'] = 'Bearer ' . $s['access_token'];
        self::diag('session_headers', ['csrf' => isset($hdr['CSRF-Token']), 'bearer' => isset($hdr['Authorization'])]);
        $api = fn ($m, $p) => self::api($m, $p, $sid, null, $base, $hdr);
        $keep = false;
        try {
            $claims = self::jwtClaims((string) ($s['access_token'] ?? ''));
            $realm = strtolower((string) ($claims['realm'] ?? ''));
            self::diag('session', ['realm' => $realm ?: null, 'claim_i_customer' => isset($claims['i_customer']), 'claim_i_account' => isset($claims['i_account']), 'mfa' => $s['mfa_enabled'] ?? null]);
            $numbers = [];
            if ($realm === 'account' || $realm === 'accounts') {
                [$ra, $rc] = self::apiPair($sid, $base, $hdr, 'Account/get_account_info', 'Customer/get_customer_info');
                $acc = $ra['account_info'] ?? null;
                $ic = (int) ($acc['i_customer'] ?? 0);
                if ($ic <= 0 || (isset($claims['i_account']) && (int) $claims['i_account'] !== (int) ($acc['i_account'] ?? 0))) { self::diag('account_info_mismatch', ['got' => $ic > 0]); return null; }
                if (($acc['blocked'] ?? 'N') === 'Y') { self::diag('account_blocked'); return null; }
                if ($n = self::did((string) ($acc['id'] ?? ''))) $numbers[] = $n;
                $cinfo = $rc['customer_info'] ?? [];
                $name = (string) ($cinfo['companyname'] ?? $cinfo['name'] ?? $acc['companyname'] ?? '');
                $email = strtolower((string) ($cinfo['email'] ?? $acc['email'] ?? ''));
            } elseif ($realm === 'customer' || $realm === '') {
                // Admin/reseller/other realms are refused. A token without a realm must still resolve
                // the caller's own customer record via the customer-only call below.
                $info = $api('Customer/get_customer_info', [])['customer_info'] ?? null;
                $ic = (int) ($info['i_customer'] ?? 0);
                if ($ic <= 0 || (isset($claims['i_customer']) && (int) $claims['i_customer'] !== $ic)) { self::diag('customer_info_mismatch', ['got' => $ic > 0]); return null; }
                if (($info['blocked'] ?? 'N') === 'Y') { self::diag('customer_blocked'); return null; }
                $acc = $api('Account/get_account_list', ['i_customer' => $ic, 'limit' => 1000]);
                foreach ($acc['account_list'] ?? [] as $a) if ($n = self::did((string) ($a['id'] ?? ''))) $numbers[] = $n;
                $name = (string) ($info['companyname'] ?? $info['name'] ?? '');
                $email = strtolower((string) ($info['email'] ?? ''));
            } else {
                self::diag('realm_refused', ['realm' => $realm]);
                return null;
            }
            self::$kept = ['base' => $base ?? config('portal.billing_rest'), 'session_id' => (string) $sid, 'csrf_token' => (string) ($s['csrf_token'] ?? ''),
                'access_token' => (string) ($s['access_token'] ?? ''), 'refresh_token' => (string) ($s['refresh_token'] ?? ''), 'jwt' => (string) (self::$jwt ?? ''),
                'jwt_login' => (string) ($claims['login'] ?? ''), 'realm' => $realm, 'i_customer' => $ic, 'at' => time()];
            $keep = true;
            return ['i_customer' => $ic, 'name' => $name, 'email' => $email, 'login' => $login, 'numbers' => array_values(array_unique($numbers))];
        } finally {
            if (!$keep) { try { $api('Session/logout', ['session_id' => $sid]); } catch (\Throwable $e) { /* ignore */ } }
        }
    }

    /** US 10-digit DID from an account id, or null (extensions like 0203250370 are skipped). */
    public static function did(string $id): ?string
    {
        $d = preg_replace('/\D/', '', $id);
        if (strlen($d) === 11 && $d[0] === '1') $d = substr($d, 1);
        return preg_match('/^[2-9]\d{2}[2-9]\d{6}$/', $d) ? $d : null;
    }

    /** Exception message without bound values, SQL text or connection details. */
    public static function safeMessage(\Throwable $e): string
    {
        $m = $e->getMessage();
        $m = preg_replace('/\s*\((Connection|SQL):.*$/s', '', $m);
        return mb_substr($m, 0, 300);
    }

    public static function jwtClaims(string $jwt): array
    {
        $p = explode('.', $jwt);
        if (count($p) !== 3) return [];
        $j = json_decode(base64_decode(strtr($p[1], '-_', '+/') . str_repeat('=', (4 - strlen($p[1]) % 4) % 4)), true);
        return is_array($j) ? $j : [];
    }

    /** Find or create the shadow portal user for this i_customer, and its user_phone_numbers link. */
    public static function linkUser(array $c): int
    {
        return DB::transaction(function () use ($c) {
            $ic = $c['i_customer'];
            $link = DB::table('gvd_portal_links')->where('i_customer', $ic)->lockForUpdate()->first();
            if ($link && DB::table('wpgc_users')->where('ID', $link->user_id)->exists()) {
                $uid = (int) $link->user_id;
                DB::table('gvd_portal_links')->where('id', $link->id)->update(['last_login_at' => now(), 'updated_at' => now()]);
            } else {
                $email = $c['email'];
                if ($email === '' || DB::table('wpgc_users')->where('user_email', $email)->exists()) $email = "vo{$ic}@users.globalvoicedirect.com";
                $name = $c['name'] !== '' ? Str::limit($c['name'], 190, '') : "Customer $ic";
                $uid = (int) DB::table('wpgc_users')->insertGetId([
                    'user_login' => "vo{$ic}", 'user_pass' => Hash::make(Str::random(64)), // unusable locally
                    'user_nicename' => $name, 'display_name' => $name, 'user_email' => $email,
                    'user_registered' => now(), 'user_activation_key' => '', 'user_status' => 1, 'active' => 0,
                    'referrer_token' => Str::random(32),
                ]);
                DB::table('gvd_portal_links')->insert(['i_customer' => $ic, 'user_id' => $uid, 'source' => 'virtual_office',
                    'last_login_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
            }
            // user_phone_numbers.numbers is varchar(200): spread the DIDs over as many rows as needed.
            $chunks = [];
            $cur = [];
            foreach ($c['numbers'] as $n) {
                if ($cur && strlen(json_encode(array_merge($cur, [$n]))) > 200) { $chunks[] = $cur; $cur = []; }
                $cur[] = $n;
            }
            $chunks[] = $cur; // at least one row (possibly '[]') so the portal user maps to the customer
            $rows = DB::table('user_phone_numbers')->where('user_id', $uid)->where('i_customer', $ic)->orderBy('id')->get(['id', 'numbers']);
            foreach ($chunks as $i => $chunk) {
                $nums = json_encode(array_values($chunk));
                if (isset($rows[$i])) {
                    if ($rows[$i]->numbers !== $nums) DB::table('user_phone_numbers')->where('id', $rows[$i]->id)->update(['numbers' => $nums, 'updated_at' => now()]);
                } else {
                    DB::table('user_phone_numbers')->insert(['user_id' => $uid, 'i_customer' => $ic, 'numbers' => $nums,
                        'i_name' => mb_substr($c['name'], 0, 190), 'i_email' => mb_substr($c['email'], 0, 190), 'created_at' => now(), 'updated_at' => now()]);
                }
            }
            for ($i = count($chunks); $i < count($rows); $i++) DB::table('user_phone_numbers')->where('id', $rows[$i]->id)->delete();
            return $uid;
        });
    }
}
