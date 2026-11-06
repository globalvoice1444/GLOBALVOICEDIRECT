<?php

namespace App\Http\Controllers;

use App\Support\CustomerEmail as CE;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** 2026-11-02 Public endpoints for customer email campaigns: signed unsubscribe (one-click), open pixel, provider bounce/complaint webhook. */
class EcPublicController extends Controller
{
    public function unsubscribe(Request $r)
    {
        if (!$r->hasValidSignature()) abort(403, 'This unsubscribe link is invalid.');
        $uid = (int) $r->query('u'); $email = strtolower((string) $r->query('e'));
        $biz = CE::sender($uid)['business'];
        if ($r->isMethod('post') && $uid > 0 && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            DB::table('ec_suppressions')->insertOrIgnore(['user_id' => $uid, 'email' => $email, 'reason' => 'unsubscribe', 'ip' => $r->ip(), 'created_at' => now(), 'updated_at' => now()]);
            DB::table('ec_contacts')->where('user_id', $uid)->where('email', $email)->update(['status' => 'unsubscribed', 'updated_at' => now()]);
            if ($r->input('List-Unsubscribe') === 'One-Click') return response('', 200);
        }
        $done = DB::table('ec_suppressions')->where('user_id', $uid)->where('email', $email)->exists();
        return response()->view('ec.unsubscribe', ['email' => $email, 'biz' => $biz, 'done' => $done, 'action' => $r->fullUrl()])->header('Cache-Control', 'no-store');
    }

    public function open(Request $r)
    {
        if ($r->hasValidSignature()) DB::table('ec_recipients')->where('id', (int) $r->query('r'))->whereNull('opened_at')->update(['opened_at' => now()]);
        return response(base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'), 200, ['Content-Type' => 'image/gif', 'Cache-Control' => 'no-store']);
    }

    /** Resend webhook (email.bounced / email.complained). Inactive until EC_WEBHOOK_SECRET is set and the webhook is added in Resend. */
    public function webhook(Request $r)
    {
        $sec = (string) config('ec.webhook_secret');
        if ($sec === '') return response()->json(['error' => 'not configured'], 503);
        $raw = $r->getContent();
        if (!self::svix($sec, $raw, (string) $r->header('svix-id'), (string) $r->header('svix-timestamp'), (string) $r->header('svix-signature'))) return response()->json(['error' => 'invalid signature'], 401);
        $j = json_decode($raw, true) ?: [];
        $type = (string) ($j['type'] ?? '');
        if (!in_array($type, ['email.bounced', 'email.complained'], true)) return response()->json(['ok' => true, 'ignored' => $type]);
        $to = (array) ($j['data']['to'] ?? []);
        $uid = null; foreach ((array) ($j['data']['headers'] ?? []) as $h) if (strtolower($h['name'] ?? '') === 'x-gvd-sender') $uid = (int) $h['value'];
        foreach ($to as $e) CE::feedback((string) $e, $type === 'email.complained' ? 'complaint' : 'bounce', $uid ?: null);
        return response()->json(['ok' => true]);
    }

    private static function svix(string $secret, string $body, string $id, string $ts, string $sig): bool
    {
        if ($id === '' || $ts === '' || $sig === '' || abs(time() - (int) $ts) > 300) return false;
        $key = base64_decode(str_starts_with($secret, 'whsec_') ? substr($secret, 6) : $secret);
        $exp = base64_encode(hash_hmac('sha256', "$id.$ts.$body", $key, true));
        foreach (explode(' ', $sig) as $p) { [$v, $s] = array_pad(explode(',', $p, 2), 2, ''); if ($v === 'v1' && hash_equals($exp, $s)) return true; }
        return false;
    }
}
