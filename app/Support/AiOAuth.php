<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * 2026-11-02 AI Connect OAuth 2.1 (MCP authorization spec). Each approved app gets its own hidden ai_keys row
 * (prefix "oauth"), so tokens carry exactly the same customer, scopes, daily limit, logging and kill switch as API keys.
 * Only SHA-256 hashes of codes, tokens and client secrets are stored. Off unless AI_CONNECT_OAUTH_ENABLED=true.
 */
class AiOAuth
{
    const KEY_PREFIX = 'oauth';

    public static function on(): bool { return (bool) config('ai_connect.oauth_enabled'); }
    public static function base(): string { return rtrim((string) config('app.url'), '/') ?: 'https://app.globalvoicedirect.com'; }
    public static function resource(): string { return self::base() . '/mcp'; }
    public static function h(string $s): string { return hash('sha256', $s); }

    public static function client(string $cid): ?object
    {
        if (!preg_match('/^gvdc_[A-Za-z0-9]{32}$/', $cid)) return null;
        return DB::table('ai_oauth_clients')->where('client_id', $cid)->first() ?: null;
    }

    public static function redirects(object $c): array { return json_decode((string) $c->redirect_uris, true) ?: []; }

    /** https anywhere; http only for loopback (native/dev clients). No fragments. */
    public static function validRedirect(string $u): bool
    {
        if (strlen($u) > 500 || str_contains($u, '#')) return false;
        $p = parse_url($u); if (!$p || empty($p['scheme']) || empty($p['host'])) return false;
        if ($p['scheme'] === 'https') return true;
        return $p['scheme'] === 'http' && in_array($p['host'], ['localhost', '127.0.0.1', '[::1]'], true);
    }

    public static function resourceOk(?string $r): bool
    {
        if ($r === null || $r === '') return true;
        return in_array(rtrim($r, '/'), [self::resource(), self::base()], true);
    }

    public static function parseScopes(?string $s): array
    {
        $all = array_keys(config('ai_connect.scopes'));
        $want = preg_split('/\s+/', trim((string) $s), -1, PREG_SPLIT_NO_EMPTY);
        $want = array_values(array_intersect($all, $want));
        return $want ?: $all;
    }

    /** Creates the hidden key that represents this app's grant. */
    public static function grantKey(int $uid, object $client, array $scopes): int
    {
        return DB::table('ai_keys')->insertGetId(['user_id' => $uid, 'name' => mb_substr($client->name, 0, 80), 'prefix' => self::KEY_PREFIX,
            'hash' => self::h('oauth-grant:' . Str::random(64)), 'scopes' => json_encode(array_values($scopes)),
            'daily_limit' => (int) config('ai_connect.default_daily_limit'), 'created_at' => now(), 'updated_at' => now()]);
    }

    public static function issueCode(object $client, int $uid, int $keyId, string $redirect, string $challenge, ?string $resource): string
    {
        $code = 'gvdac_' . Str::random(48);
        DB::table('ai_oauth_codes')->insert(['code_hash' => self::h($code), 'client_id' => $client->client_id, 'user_id' => $uid, 'key_id' => $keyId,
            'redirect_uri' => $redirect, 'challenge' => $challenge, 'resource' => $resource ?: null,
            'expires_at' => now()->addMinutes(5), 'created_at' => now(), 'updated_at' => now()]);
        return $code;
    }

    /** Returns token response array. */
    public static function issueTokens(string $cid, int $uid, int $keyId): array
    {
        $a = 'gvdat_' . Str::random(48); $r = 'gvdrt_' . Str::random(48);
        $ttl = (int) config('ai_connect.oauth_access_ttl', 3600);
        DB::table('ai_oauth_tokens')->insert(['access_hash' => self::h($a), 'refresh_hash' => self::h($r), 'client_id' => $cid, 'user_id' => $uid, 'key_id' => $keyId,
            'access_expires_at' => now()->addSeconds($ttl), 'refresh_expires_at' => now()->addDays((int) config('ai_connect.oauth_refresh_days', 30)),
            'created_at' => now(), 'updated_at' => now()]);
        $k = DB::table('ai_keys')->where('id', $keyId)->first();
        return ['access_token' => $a, 'token_type' => 'Bearer', 'expires_in' => $ttl, 'refresh_token' => $r, 'scope' => implode(' ', AiConnect::scopes($k))];
    }

    /** Access token -> the grant's ai_keys row (same object McpController uses for API keys), or null. */
    public static function findAccess(string $tok): ?object
    {
        if (!self::on() || !preg_match('/^gvdat_[A-Za-z0-9]{48}$/', $tok)) return null;
        $t = DB::table('ai_oauth_tokens')->where('access_hash', self::h($tok))->whereNull('revoked_at')->where('access_expires_at', '>', now())->first();
        if (!$t) return null;
        $k = DB::table('ai_keys')->where('id', $t->key_id)->where('user_id', $t->user_id)->whereNull('revoked_at')->first();
        return $k ?: null;
    }

    /** Revoke a whole app grant (key + every token). */
    public static function revokeGrant(int $uid, int $keyId): bool
    {
        $n = DB::table('ai_keys')->where('user_id', $uid)->where('id', $keyId)->where('prefix', self::KEY_PREFIX)->whereNull('revoked_at')->update(['revoked_at' => now(), 'updated_at' => now()]);
        DB::table('ai_oauth_tokens')->where('user_id', $uid)->where('key_id', $keyId)->whereNull('revoked_at')->update(['revoked_at' => now(), 'updated_at' => now()]);
        return (bool) $n;
    }
}
