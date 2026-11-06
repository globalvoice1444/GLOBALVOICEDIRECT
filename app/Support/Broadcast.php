<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/** 2026-11-02 One-off broadcasts. Reuses Drip render/deliver/unsubscribe. Sends only when DRIP_SENDING=true AND status SCHEDULED/SENDING. */
class Broadcast
{
    const STATUSES = ['DRAFT', 'SCHEDULED', 'SENDING', 'PAUSED', 'DONE'];
    const TZ = 'America/New_York';

    public static function invalidEmail(string $e): bool
    {
        if (!filter_var($e, FILTER_VALIDATE_EMAIL)) return true;
        return (bool) preg_match('/@(example\.(com|org|net)|test\.com|mailinator\.com|localhost)$|^(test|demo)[0-9._+-]*@/i', $e);
    }

    /** Existing customers: verified accounts, valid email, not demo, not suppressed. Returns rows keyed by email. */
    public static function customerAudience(): array
    {
        $biz = DB::table('addon_business_listing')->where('businessname', '<>', '')->orderBy('id')->pluck('businessname', 'userID')->all();
        foreach (DB::table('gvd_business_listings')->where('business_name', '<>', '')->pluck('business_name', 'user_id') as $k => $v) $biz[$k] = $v;
        $fn = DB::table('wpgc_usermeta')->where('meta_key', 'first_name')->where('meta_value', '<>', '')->pluck('meta_value', 'user_id')->all();
        $sup = DB::table('email_suppressions')->pluck('email')->flip();
        $out = [];
        foreach (DB::table('wpgc_users')->where('user_status', 1)->whereNotIn('user_login', Workflow::DEMO_LOGINS)->orderBy('ID')
            ->get(['ID', 'user_email', 'display_name', 'user_nicename']) as $u) {
            $e = strtolower(trim((string) $u->user_email));
            if (self::invalidEmail($e) || isset($sup[$e]) || isset($out[$e])) continue;
            $out[$e] = ['user_id' => $u->ID, 'email' => $e, 'first_name' => mb_substr(trim($fn[$u->ID] ?? '') ?: Drip::firstName($u->display_name ?: $u->user_nicename), 0, 100),
                'business_name' => mb_substr(trim((string) ($biz[$u->ID] ?? '')), 0, 200) ?: null, 'extra' => null, 'source' => 'customers'];
        }
        return $out;
    }

    /** Parse CSV text with header (email, first_name, business_name, extra). Returns [rows keyed by email, stats]. */
    public static function parseCsv(string $text): array
    {
        $lines = preg_split('/\r\n|\r|\n/', trim(preg_replace('/^\xEF\xBB\xBF/', '', $text)));
        $hdr = array_map(fn($h) => strtolower(trim(str_replace(' ', '_', $h))), str_getcsv(array_shift($lines) ?: ''));
        $ix = array_flip($hdr); $st = ['rows' => 0, 'invalid' => 0, 'dupes' => 0, 'suppressed' => 0];
        if (!isset($ix['email'])) return [[], $st + ['error' => 'CSV needs an "email" column header.']];
        $sup = DB::table('email_suppressions')->pluck('email')->flip(); $out = [];
        foreach (array_slice($lines, 0, 50000) as $ln) {
            if (trim($ln) === '') continue; $c = str_getcsv($ln); $st['rows']++;
            $g = fn($k) => isset($ix[$k]) ? trim((string) ($c[$ix[$k]] ?? '')) : '';
            $e = strtolower($g('email'));
            if (self::invalidEmail($e)) { $st['invalid']++; continue; }
            if (isset($sup[$e])) { $st['suppressed']++; continue; }
            if (isset($out[$e])) { $st['dupes']++; continue; }
            $out[$e] = ['user_id' => null, 'email' => $e, 'first_name' => mb_substr($g('first_name'), 0, 100) ?: null, 'business_name' => mb_substr($g('business_name'), 0, 200) ?: null,
                'extra' => mb_substr($g('extra'), 0, 255) ?: null, 'source' => 'csv'];
        }
        return [$out, $st];
    }

    public static function addRecipients(int $id, array $rows): int
    {
        $n = 0; $now = now();
        foreach (array_chunk(array_values($rows), 500) as $chunk)
            $n += DB::table('broadcast_recipients')->insertOrIgnore(array_map(fn($r) => $r + ['broadcast_id' => $id, 'status' => 'pending', 'created_at' => $now, 'updated_at' => $now], $chunk));
        return $n;
    }

    /** Render via Drip shell (header, footer, unsubscribe). */
    public static function render(object $b, object $r): array
    {
        $first = trim((string) ($r->first_name ?? '')) ?: $b->first_name_fallback;
        $biz = trim((string) ($r->business_name ?? '')) ?: $b->business_fallback;
        $map = ['{{business_name}}' => e($biz), '{{extra}}' => e((string) ($r->extra ?? ''))];
        $step = (object) ['subject' => strtr($b->subject, ['{{business_name}}' => $biz, '{{extra}}' => (string) ($r->extra ?? '')]),
            'preview' => strtr((string) $b->preview, $map), 'body_html' => strtr((string) $b->body_html, $map)];
        return Drip::render($step, (object) ['email' => $r->email, 'first_name' => $first, 'user_id' => $r->user_id ?? null]);
    }

    public static function sentToday(int $id): int
    {
        $start = now(self::TZ)->startOfDay()->utc();
        return DB::table('broadcast_recipients')->where('broadcast_id', $id)->whereIn('status', ['sent', 'sending'])->where('updated_at', '>=', $start)->count();
    }

    /** Called from drip:run every 5 min. */
    public static function run(): array
    {
        $out = ['bc_sent' => 0, 'bc_failed' => 0, 'bc_skipped' => 0, 'bc_capped' => 0];
        if (!config('drip.enabled')) return $out;
        DB::table('broadcasts')->where('status', 'SCHEDULED')->where('start_at', '<=', now())->update(['status' => 'SENDING', 'updated_at' => now()]);
        $budget = Drip::sendBudget();   // 2026-11-05 hourly cap + quota pause (shared with drip/workflow)
        if ($budget <= 0) return $out + ['bc_paused_or_capped' => true];
        $capFrom = now()->subHours(config('drip.min_hours_between'));
        foreach (DB::table('broadcasts')->where('status', 'SENDING')->orderBy('id')->get() as $b) {
            if (isset($b->send_from_hour) && $b->send_from_hour !== null && now(self::TZ)->hour < (int) $b->send_from_hour) continue;   // daily window start (ET)
            $left = min($budget, $b->daily_cap - self::sentToday($b->id));
            if ($left <= 0) continue;
            $recs = DB::table('broadcast_recipients')->where('broadcast_id', $b->id)->where('status', 'pending')->orderBy('id')->limit($left * 3)->get();
            if ($recs->isEmpty()) {
                if (!DB::table('broadcast_recipients')->where('broadcast_id', $b->id)->where('status', 'sending')->exists())
                    DB::table('broadcasts')->where('id', $b->id)->update(['status' => 'DONE', 'finished_at' => now(), 'updated_at' => now()]);
                continue;
            }
            foreach ($recs as $r) {
                if ($left <= 0 || $budget <= 0) break;
                if (DB::table('broadcasts')->where('id', $b->id)->value('status') !== 'SENDING') break;   // paused mid-batch
                $skip = fn($why) => DB::table('broadcast_recipients')->where('id', $r->id)->update(['status' => 'skipped', 'reason' => $why, 'updated_at' => now()]);
                if (Drip::suppressed($r->email)) { $skip('unsubscribed'); $out['bc_skipped']++; continue; }
                if (Workflow::recentlyEmailed($r->email, $capFrom) || DB::table('broadcast_recipients')->where('email', $r->email)->where('status', 'sent')->where('sent_at', '>=', $capFrom)->exists()) { $out['bc_capped']++; continue; }
                if (!DB::table('broadcast_recipients')->where('id', $r->id)->where('status', 'pending')->update(['status' => 'sending', 'updated_at' => now()])) continue;
                try {
                    [$subj, $html, $unsub] = self::render($b, $r);
                    $html = Tracking::apply($html, "broadcast", (int) $r->id, (int) $b->id, $r->email); // trk (future sends only)
                    Drip::deliver($r->email, $subj, $html, $unsub);
                    DB::table('broadcast_recipients')->where('id', $r->id)->update(['status' => 'sent', 'sent_at' => now(), 'updated_at' => now()]);
                    $out['bc_sent']++;
                } catch (\Throwable $e) {
                    if (Drip::isQuotaError($e)) {   // 2026-11-05 host quota: put back to pending, stop everything until pause ends
                        DB::table('broadcast_recipients')->where('id', $r->id)->update(['status' => 'pending', 'reason' => null, 'updated_at' => now()]);
                        $out['bc_quota_paused'] = true; return $out;
                    }
                    DB::table('broadcast_recipients')->where('id', $r->id)->update(['status' => 'failed', 'reason' => mb_substr($e->getMessage(), 0, 250), 'updated_at' => now()]);
                    Log::warning('broadcast send failed', ['broadcast' => $b->id, 'rec' => $r->id, 'err' => mb_substr($e->getMessage(), 0, 200)]);
                    $out['bc_failed']++;
                }
                $left--; $budget--;
            }
            if ($budget <= 0) break;
        }
        return $out;
    }
}
