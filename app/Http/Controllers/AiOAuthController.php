<?php

namespace App\Http\Controllers;

use App\Support\AiConnect;
use App\Support\AiOAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * 2026-11-02 AI Connect OAuth 2.1 authorization server for the MCP endpoint (/mcp).
 * Metadata (RFC 9728 + RFC 8414), dynamic client registration (RFC 7591), authorization code + PKCE S256 only,
 * refresh token rotation, revocation (RFC 7009). Everything returns 404 unless AI_CONNECT_OAUTH_ENABLED=true.
 */
class AiOAuthController extends Controller
{
    const H = ['Cache-Control' => 'no-store', 'Pragma' => 'no-cache', 'Access-Control-Allow-Origin' => '*', 'Access-Control-Allow-Headers' => 'Authorization, Content-Type, MCP-Protocol-Version', 'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS'];

    private function off() { return response()->json(['error' => 'not_found'], 404, ['Cache-Control' => 'no-store']); }
    private function j($d, int $s = 200) { return response()->json($d, $s, self::H, JSON_UNESCAPED_SLASHES); }
    private function oerr(string $e, string $desc, int $s = 400) { return $this->j(['error' => $e, 'error_description' => $desc], $s); }

    public function preflight() { return AiOAuth::on() ? response('', 204, self::H) : $this->off(); }

    public function protectedResource()
    {
        if (!AiOAuth::on()) return $this->off();
        return $this->j(['resource' => AiOAuth::resource(), 'authorization_servers' => [AiOAuth::base()],
            'scopes_supported' => array_keys(config('ai_connect.scopes')), 'bearer_methods_supported' => ['header'],
            'resource_name' => 'Global Voice Direct', 'resource_documentation' => AiOAuth::base() . '/user/ai-connect']);
    }

    public function authServer()
    {
        if (!AiOAuth::on()) return $this->off();
        $b = AiOAuth::base();
        return $this->j(['issuer' => $b, 'authorization_endpoint' => $b . '/oauth/authorize', 'token_endpoint' => $b . '/oauth/token',
            'registration_endpoint' => $b . '/oauth/register', 'revocation_endpoint' => $b . '/oauth/revoke',
            'scopes_supported' => array_keys(config('ai_connect.scopes')), 'response_types_supported' => ['code'], 'response_modes_supported' => ['query'],
            'grant_types_supported' => ['authorization_code', 'refresh_token'], 'code_challenge_methods_supported' => ['S256'],
            'token_endpoint_auth_methods_supported' => ['none', 'client_secret_post', 'client_secret_basic'],
            'revocation_endpoint_auth_methods_supported' => ['none', 'client_secret_post', 'client_secret_basic'],
            'service_documentation' => $b . '/user/ai-connect']);
    }

    /** RFC 7591 dynamic client registration (open, rate limited). */
    public function register(Request $r)
    {
        if (!AiOAuth::on()) return $this->off();
        $rk = 'oauth-reg:' . $r->ip();
        if (RateLimiter::tooManyAttempts($rk, 20)) return $this->oerr('slow_down', 'Too many registrations. Try again later.', 429);
        RateLimiter::hit($rk, 3600);
        $m = json_decode($r->getContent(), true); if (!is_array($m)) $m = $r->all();
        $uris = $m['redirect_uris'] ?? null;
        if (!is_array($uris) || !$uris || count($uris) > 10) return $this->oerr('invalid_redirect_uri', 'redirect_uris must list 1 to 10 URLs.');
        foreach ($uris as $u) if (!is_string($u) || !AiOAuth::validRedirect($u)) return $this->oerr('invalid_redirect_uri', 'Redirect URLs must use https (http is allowed only for localhost).');
        $gt = $m['grant_types'] ?? ['authorization_code', 'refresh_token'];
        if (!is_array($gt) || array_diff($gt, ['authorization_code', 'refresh_token'])) return $this->oerr('invalid_client_metadata', 'Only authorization_code and refresh_token grants are supported.');
        $rt = $m['response_types'] ?? ['code'];
        if (!is_array($rt) || array_diff($rt, ['code'])) return $this->oerr('invalid_client_metadata', 'Only the code response type is supported.');
        $am = (string) ($m['token_endpoint_auth_method'] ?? 'none');
        if (!in_array($am, ['none', 'client_secret_post', 'client_secret_basic'], true)) return $this->oerr('invalid_client_metadata', 'Unsupported token_endpoint_auth_method.');
        $name = trim(strip_tags((string) ($m['client_name'] ?? ''))); $name = mb_substr(preg_replace('/[\x00-\x1F\x7F]/u', '', $name) ?: 'AI assistant', 0, 120);
        $cid = 'gvdc_' . Str::random(32); $secret = $am === 'none' ? null : 'gvdcs_' . Str::random(48);
        DB::table('ai_oauth_clients')->insert(['client_id' => $cid, 'secret_hash' => $secret ? AiOAuth::h($secret) : null, 'name' => $name,
            'redirect_uris' => json_encode(array_values($uris), JSON_UNESCAPED_SLASHES), 'auth_method' => $am, 'ip' => $r->ip(), 'created_at' => now(), 'updated_at' => now()]);
        $out = ['client_id' => $cid, 'client_id_issued_at' => time(), 'client_name' => $name, 'redirect_uris' => array_values($uris),
            'grant_types' => ['authorization_code', 'refresh_token'], 'response_types' => ['code'], 'token_endpoint_auth_method' => $am];
        if ($secret) { $out['client_secret'] = $secret; $out['client_secret_expires_at'] = 0; }
        return $this->j($out, 201);
    }

    /** GET /oauth/authorize: validate, make the customer sign in with their normal login, then show consent. */
    public function authorizeForm(Request $r)
    {
        if (!AiOAuth::on()) return $this->off();
        $c = AiOAuth::client((string) $r->query('client_id', ''));
        $ru = (string) $r->query('redirect_uri', '');
        if (!$c) return $this->page('This app is not registered with Global Voice Direct.', 400);
        $uris = AiOAuth::redirects($c);
        if ($ru === '' && count($uris) === 1) $ru = $uris[0];
        if (!in_array($ru, $uris, true)) return $this->page('This app sent an address we don\'t recognize, so we stopped here to keep your account safe.', 400);
        $st = (string) $r->query('state', '');
        $back = fn (string $e, string $d) => redirect()->away($this->withQuery($ru, array_filter(['error' => $e, 'error_description' => $d, 'state' => $st], 'strlen')));
        if ($r->query('response_type') !== 'code') return $back('unsupported_response_type', 'Only response_type=code is supported.');
        $ch = (string) $r->query('code_challenge', '');
        if ($r->query('code_challenge_method') !== 'S256' || !preg_match('/^[A-Za-z0-9_-]{43,128}$/', $ch)) return $back('invalid_request', 'PKCE with code_challenge_method=S256 is required.');
        $res = $r->query('resource'); $res = is_string($res) ? $res : null;
        if (!AiOAuth::resourceOk($res)) return $back('invalid_target', 'Unknown resource.');

        $uid = (int) session()->get('user_id');
        if (!session()->get('user_login') || $uid <= 0) {
            session()->put('oauth_return', '/oauth/authorize?' . http_build_query($r->query()));
            return redirect('/user-login?' . http_build_query(['message' => 'Sign in to connect ' . $c->name . ' to your Global Voice Direct account.']));
        }
        if (!AiConnect::platformOpenFor($uid)) return $this->page('AI Connect is not available on your account yet.', 403);

        $scopes = AiOAuth::parseScopes((string) $r->query('scope', ''));
        $nonce = Str::random(40);
        session()->put('oauth_req.' . $nonce, ['cid' => $c->client_id, 'ru' => $ru, 'st' => $st, 'ch' => $ch, 'res' => $res, 'sc' => $scopes, 'at' => time()]);
        $labels = config('ai_connect.scopes'); $groups = self::groups();
        return response()->view('users.gvd-oauth-consent', ['app' => $c->name, 'host' => parse_url($ru, PHP_URL_HOST), 'nonce' => $nonce,
            'scopes' => $scopes, 'labels' => $labels, 'groups' => $groups, 'email' => (string) session()->get('user_email'),
            'enabled' => AiConnect::userEnabled($uid)])->withHeaders(['Cache-Control' => 'no-store', 'X-Frame-Options' => 'DENY', 'Content-Security-Policy' => "frame-ancestors 'none'"]);
    }

    /** POST /oauth/authorize: Allow or Deny (CSRF protected, signed-in session). */
    public function authorizeDecide(Request $r)
    {
        if (!AiOAuth::on()) return $this->off();
        $uid = (int) session()->get('user_id');
        if (!session()->get('user_login') || $uid <= 0) return redirect('/user-login');
        $nonce = (string) $r->input('nonce', '');
        $q = preg_match('/^[A-Za-z0-9]{40}$/', $nonce) ? session()->pull('oauth_req.' . $nonce) : null;
        if (!$q || time() - (int) $q['at'] > 900) return $this->page('This request expired. Go back to your AI assistant and connect again.', 400);
        $c = AiOAuth::client($q['cid']);
        if (!$c || !in_array($q['ru'], AiOAuth::redirects($c), true)) return $this->page('This app is no longer registered.', 400);
        $p = array_filter(['state' => $q['st']], 'strlen');
        if ($r->input('decision') !== 'allow') {
            AiConnect::log($uid, null, 'oauth', null, null, 'denied', 'declined ' . $c->name, $r->ip());
            return redirect()->away($this->withQuery($q['ru'], ['error' => 'access_denied', 'error_description' => 'The account owner declined.'] + $p));
        }
        if (!AiConnect::platformOpenFor($uid)) return $this->page('AI Connect is not available on your account yet.', 403);
        $scopes = array_values(array_intersect($q['sc'], (array) $r->input('scopes', [])));
        if (!$scopes) return $this->page('Choose at least one permission. Go back to your AI assistant and connect again.', 422);
        $keyId = AiOAuth::grantKey($uid, $c, $scopes);
        $code = AiOAuth::issueCode($c, $uid, $keyId, $q['ru'], $q['ch'], $q['res']);
        AiConnect::log($uid, $keyId, 'oauth', null, null, 'ok', 'approved ' . $c->name . ' (' . implode(', ', $scopes) . ')', $r->ip());
        return redirect()->away($this->withQuery($q['ru'], ['code' => $code] + $p));
    }

    /** POST /oauth/token */
    public function token(Request $r)
    {
        if (!AiOAuth::on()) return $this->off();
        $rk = 'oauth-tok:' . $r->ip();
        if (RateLimiter::tooManyAttempts($rk, 60)) return $this->oerr('slow_down', 'Too many requests.', 429);
        RateLimiter::hit($rk, 60);
        [$c, $err] = $this->clientAuth($r); if ($err) return $err;
        $gt = (string) $r->input('grant_type', '');

        if ($gt === 'authorization_code') {
            $code = (string) $r->input('code', '');
            if (!preg_match('/^gvdac_[A-Za-z0-9]{48}$/', $code)) return $this->oerr('invalid_grant', 'Invalid code.');
            return DB::transaction(function () use ($r, $c, $code) {
                $row = DB::table('ai_oauth_codes')->where('code_hash', AiOAuth::h($code))->lockForUpdate()->first();
                if (!$row || $row->client_id !== $c->client_id) return $this->oerr('invalid_grant', 'Invalid code.');
                if ($row->used_at) { AiOAuth::revokeGrant((int) $row->user_id, (int) $row->key_id); return $this->oerr('invalid_grant', 'Code already used.'); }
                DB::table('ai_oauth_codes')->where('id', $row->id)->update(['used_at' => now(), 'updated_at' => now()]);
                if (now()->gt(\Carbon\Carbon::parse($row->expires_at))) return $this->oerr('invalid_grant', 'Code expired.');
                if ((string) $r->input('redirect_uri', $row->redirect_uri) !== $row->redirect_uri) return $this->oerr('invalid_grant', 'redirect_uri mismatch.');
                $v = (string) $r->input('code_verifier', '');
                if (!preg_match('/^[A-Za-z0-9._~-]{43,128}$/', $v)) return $this->oerr('invalid_grant', 'code_verifier is required.');
                $calc = rtrim(strtr(base64_encode(hash('sha256', $v, true)), '+/', '-_'), '=');
                if (!hash_equals($row->challenge, $calc)) return $this->oerr('invalid_grant', 'PKCE verification failed.');
                $res = $r->input('resource'); if (is_string($res) && !AiOAuth::resourceOk($res)) return $this->oerr('invalid_target', 'Unknown resource.');
                if (!DB::table('ai_keys')->where('id', $row->key_id)->whereNull('revoked_at')->exists()) return $this->oerr('invalid_grant', 'Access was revoked.');
                return $this->j(AiOAuth::issueTokens($c->client_id, (int) $row->user_id, (int) $row->key_id));
            });
        }

        if ($gt === 'refresh_token') {
            $rt = (string) $r->input('refresh_token', '');
            if (!preg_match('/^gvdrt_[A-Za-z0-9]{48}$/', $rt)) return $this->oerr('invalid_grant', 'Invalid refresh token.');
            return DB::transaction(function () use ($c, $rt) {
                $t = DB::table('ai_oauth_tokens')->where('refresh_hash', AiOAuth::h($rt))->lockForUpdate()->first();
                if (!$t || $t->client_id !== $c->client_id) return $this->oerr('invalid_grant', 'Invalid refresh token.');
                if ($t->revoked_at) { AiOAuth::revokeGrant((int) $t->user_id, (int) $t->key_id); return $this->oerr('invalid_grant', 'Refresh token already used or revoked.'); } // reuse detection
                if (now()->gt(\Carbon\Carbon::parse($t->refresh_expires_at))) return $this->oerr('invalid_grant', 'Refresh token expired.');
                if (!DB::table('ai_keys')->where('id', $t->key_id)->whereNull('revoked_at')->exists()) return $this->oerr('invalid_grant', 'Access was revoked.');
                DB::table('ai_oauth_tokens')->where('id', $t->id)->update(['revoked_at' => now(), 'updated_at' => now()]); // rotate
                return $this->j(AiOAuth::issueTokens($c->client_id, (int) $t->user_id, (int) $t->key_id));
            });
        }
        return $this->oerr('unsupported_grant_type', 'Use authorization_code or refresh_token.');
    }

    /** POST /oauth/revoke (RFC 7009): always 200. Revokes the token pair it belongs to. */
    public function revoke(Request $r)
    {
        if (!AiOAuth::on()) return $this->off();
        [$c, $err] = $this->clientAuth($r); if ($err) return $err;
        $tok = (string) $r->input('token', ''); $h = AiOAuth::h($tok);
        DB::table('ai_oauth_tokens')->where('client_id', $c->client_id)->whereNull('revoked_at')
            ->where(fn ($q) => $q->where('access_hash', $h)->orWhere('refresh_hash', $h))->update(['revoked_at' => now(), 'updated_at' => now()]);
        return response('', 200, self::H);
    }

    /** Returns [client, null] or [null, error response]. Public clients send client_id only. */
    private function clientAuth(Request $r): array
    {
        $cid = (string) $r->input('client_id', ''); $sec = (string) $r->input('client_secret', '');
        $auth = (string) $r->header('Authorization', '');
        if (stripos($auth, 'Basic ') === 0) { $d = base64_decode(substr($auth, 6), true); if ($d !== false && str_contains($d, ':')) { [$a, $b] = explode(':', $d, 2); $cid = urldecode($a); $sec = urldecode($b); } }
        $c = AiOAuth::client($cid);
        if (!$c) return [null, $this->oerr('invalid_client', 'Unknown client.', 401)];
        if ($c->secret_hash && !hash_equals($c->secret_hash, AiOAuth::h($sec))) return [null, $this->oerr('invalid_client', 'Client authentication failed.', 401)];
        return [$c, null];
    }

    private function withQuery(string $u, array $p): string { return $u . (str_contains($u, '?') ? '&' : '?') . http_build_query($p); }

    private function page(string $msg, int $s)
    {
        return response()->view('users.gvd-oauth-consent', ['error' => $msg], $s)->withHeaders(['Cache-Control' => 'no-store', 'X-Frame-Options' => 'DENY']);
    }

    public static function groups(): array
    {
        return ['contacts:read' => ['Contacts', 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8M22 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8'],
            'campaigns:read' => ['Email templates', 'M4 4h16v16H4zM4 9h16M9 20V9'],
            'email:send' => ['Email', 'M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2zM22 6l-10 7L2 6'],
            'sequences:run' => ['Sequences', 'M3 12h4l3-9 4 18 3-9h4'],
            'reports:read' => ['Reports', 'M3 3v18h18M7 15l4-4 3 3 5-6'],
            'calls:place' => ['Calls', 'M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z']];
    }
}
