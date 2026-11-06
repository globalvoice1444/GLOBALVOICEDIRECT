<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 2026-11-02 trk: shared open/click tracking for every GVD email type.
 *   types: drip, workflow, broadcast (admin) | ec, ec_seq, ec_auto (customer panel)
 *   Tracking::apply($html, $type, $sendId, $refId, $email, $ownerId) at send time -> rewrites links + adds pixel.
 *   Pixel: /t/o/{token}.gif   Click: /t/c/{token}  (token = HMAC-signed; click target looked up from email_track_links = allowlist of the email's own links, so no open redirect).
 * Opens are approximate (image blocking, Apple Mail Privacy Protection, security scanners). Machine-looking opens are flagged.
 */
class Tracking
{
    const TYPES = ['drip' => 'Drip step', 'workflow' => 'Workflow email', 'broadcast' => 'Broadcast', 'ec' => 'Customer campaign', 'ec_seq' => 'Customer sequence', 'ec_auto' => 'Customer automation'];

    private static function key(): string { return hash('sha256', 'gvd-trk|' . config('app.key'), true); }
    private static function b64(string $s): string { return rtrim(strtr(base64_encode($s), '+/', '-_'), '='); }

    public static function token(int $tid, int $link = 0): string
    {
        $p = $tid . '-' . $link;
        return self::b64($p) . '.' . substr(self::b64(hash_hmac('sha256', $p, self::key(), true)), 0, 22);
    }

    /** Returns [track_send_id, link_idx] or null. */
    public static function parse(string $tok): ?array
    {
        if (!preg_match('/^([A-Za-z0-9_-]{2,40})\.([A-Za-z0-9_-]{22})$/', $tok, $m)) return null;
        $p = base64_decode(strtr($m[1], '-_', '+/'), true);
        if ($p === false || !preg_match('/^(\d{1,12})-(\d{1,4})$/', $p, $q)) return null;
        if (!hash_equals(substr(self::b64(hash_hmac('sha256', $p, self::key(), true)), 0, 22), $m[2])) return null;
        return [(int) $q[1], (int) $q[2]];
    }

    /** Never throws: on any problem returns the original html untouched (sending must not break). */
    public static function apply(string $html, string $type, int $sendId, ?int $refId, string $email, ?int $ownerId = null): string
    {
        if (!config('tracking.enabled', true) || !isset(self::TYPES[$type]) || $sendId <= 0) return $html;
        try {
            DB::table('email_track_sends')->insertOrIgnore(['type' => $type, 'send_id' => $sendId, 'ref_id' => $refId, 'owner_id' => $ownerId,
                'email' => mb_substr(strtolower($email), 0, 190), 'sent_at' => now(), 'created_at' => now()]);
            $tid = (int) DB::table('email_track_sends')->where('type', $type)->where('send_id', $sendId)->value('id');
            if (!$tid) return $html;
            $base = rtrim((string) config('tracking.base_url', config('app.url')), '/');
            $links = []; $idx = 0;
            $out = preg_replace_callback('/(<a\b[^>]*?\bhref\s*=\s*)(["\'])(.*?)\2/is', function ($m) use (&$links, &$idx, $tid, $base) {
                $url = html_entity_decode($m[3], ENT_QUOTES | ENT_HTML5);
                if (!preg_match('#^https?://#i', $url) || stripos($url, 'unsubscribe') !== false || strlen($url) > 2000 || $idx >= 50) return $m[0];
                $i = ++$idx; $links[] = ['track_send_id' => $tid, 'idx' => $i, 'url' => $url];
                return $m[1] . $m[2] . e($base . '/t/c/' . self::token($tid, $i)) . $m[2];
            }, $html);
            if ($out === null) return $html;
            if ($links) DB::table('email_track_links')->insertOrIgnore($links);
            $px = '<img src="' . e($base . '/t/o/' . self::token($tid) . '.gif') . '" width="1" height="1" alt="" style="display:block;border:0;width:1px;height:1px">';
            return stripos($out, '</body>') !== false ? preg_replace('#</body>#i', $px . '</body>', $out, 1) : $out . $px;
        } catch (\Throwable $e) {
            Log::warning('tracking apply failed', ['type' => $type, 'send' => $sendId, 'err' => mb_substr($e->getMessage(), 0, 200)]);
            return $html;
        }
    }

    /** Heuristic: Apple MPP / proxies / scanners / prefetch. */
    public static function machine(string $ua, ?string $sentAt, string $event): bool
    {
        if ($ua === '' || preg_match('/bot|crawl|spider|preview|scan|barracuda|proofpoint|mimecast|symantec|messagelabs|ironport|fortinet|trendmicro|headless|python|curl|wget|go-http|java\//i', $ua)) return true;
        if ($event === 'open' && $ua === 'Mozilla/5.0') return true;                  // Apple Mail Privacy Protection proxy
        if ($sentAt && strtotime($sentAt) > time() - ($event === 'open' ? 2 : 10)) return true;    // fetched instantly after send = prefetch/scanner
        return false;
    }

    public static function record(int $tid, string $event, int $link, string $ip, string $ua): ?object
    {
        $s = DB::table('email_track_sends')->where('id', $tid)->first();
        if (!$s) return null;
        $ua = mb_substr($ua, 0, 255); $mach = self::machine($ua, $s->sent_at, $event) ? 1 : 0;
        $iph = substr(hash_hmac('sha256', $ip, self::key()), 0, 16);
        $w = ['track_send_id' => $tid, 'event' => $event, 'link_idx' => $link];
        $ex = DB::table('email_track_events')->where($w)->first();
        if ($ex) DB::table('email_track_events')->where('id', $ex->id)->update(['count' => DB::raw('count+1'), 'last_at' => now(), 'machine' => min((int) $ex->machine, $mach), 'ip_hash' => $iph, 'user_agent' => $ua]);
        else DB::table('email_track_events')->insertOrIgnore($w + ['type' => $s->type, 'send_id' => $s->send_id, 'ref_id' => $s->ref_id, 'ip_hash' => $iph, 'user_agent' => $ua, 'machine' => $mach, 'count' => 1, 'first_at' => now(), 'last_at' => now()]);
        // a click proves the email was opened (images may be blocked)
        if ($event === 'click' && !DB::table('email_track_events')->where('track_send_id', $tid)->where('event', 'open')->exists())
            DB::table('email_track_events')->insertOrIgnore(['track_send_id' => $tid, 'event' => 'open', 'link_idx' => 0, 'type' => $s->type, 'send_id' => $s->send_id, 'ref_id' => $s->ref_id, 'ip_hash' => $iph, 'user_agent' => $ua, 'machine' => $mach, 'count' => 1, 'first_at' => now(), 'last_at' => now()]);
        if ($s->type === 'ec') DB::table('ec_recipients')->where('id', $s->send_id)->whereNull('opened_at')->update(['opened_at' => now()]);
        return $s;
    }

    /** Aggregate per ref_id for a type. human = excludes machine-flagged events. */
    public static function summary(string $type, ?array $refIds = null, ?int $ownerId = null): array
    {
        $q = DB::table('email_track_sends as s')->leftJoin('email_track_events as o', fn ($j) => $j->on('o.track_send_id', '=', 's.id')->where('o.event', 'open'))
            ->leftJoin(DB::raw("(SELECT track_send_id, SUM(count) clicks, MIN(machine) m FROM email_track_events WHERE event='click' GROUP BY track_send_id) c"), 'c.track_send_id', '=', 's.id')
            ->where('s.type', $type);
        if ($refIds !== null) $q->whereIn('s.ref_id', $refIds ?: [0]);
        if ($ownerId !== null) $q->where('s.owner_id', $ownerId);
        $rows = $q->groupBy('s.ref_id')->selectRaw('s.ref_id, COUNT(*) tracked, SUM(o.id IS NOT NULL) uniq_opens, SUM(o.id IS NOT NULL AND o.machine=0) human_opens, COALESCE(SUM(o.count),0) opens, SUM(c.track_send_id IS NOT NULL) uniq_clicks, SUM(c.track_send_id IS NOT NULL AND c.m=0) human_clicks, COALESCE(SUM(c.clicks),0) clicks')->get();
        $out = [];
        foreach ($rows as $r) {
            $t = max(1, (int) $r->tracked);
            $out[(int) $r->ref_id] = ['tracked' => (int) $r->tracked, 'opens' => (int) $r->opens, 'unique_opens' => (int) $r->uniq_opens, 'human_opens' => (int) $r->human_opens,
                'clicks' => (int) $r->clicks, 'unique_clicks' => (int) $r->uniq_clicks, 'human_clicks' => (int) $r->human_clicks,
                'open_rate' => round($r->uniq_opens * 100 / $t, 1), 'ctr' => round($r->uniq_clicks * 100 / $t, 1), 'click_to_open' => $r->uniq_opens ? round($r->uniq_clicks * 100 / $r->uniq_opens, 1) : 0];
        }
        return $out;
    }

    /** Per-recipient rows. */
    public static function recipients(string $type, int $refId, int $limit = 500, ?int $ownerId = null, ?string $filter = null)
    {
        $q = DB::table('email_track_sends as s')->where('s.type', $type)->where('s.ref_id', $refId)
            ->leftJoin('email_track_events as o', fn ($j) => $j->on('o.track_send_id', '=', 's.id')->where('o.event', 'open'))
            ->leftJoin(DB::raw("(SELECT track_send_id, SUM(count) clicks, MIN(first_at) first_click, MAX(last_at) last_click, MIN(machine) m FROM email_track_events WHERE event='click' GROUP BY track_send_id) c"), 'c.track_send_id', '=', 's.id');
        if ($ownerId !== null) $q->where('s.owner_id', $ownerId);
        if ($filter === 'opened') $q->whereNotNull('o.id'); elseif ($filter === 'clicked') $q->whereNotNull('c.track_send_id'); elseif ($filter === 'unopened') $q->whereNull('o.id');
        return $q->orderByRaw('c.clicks IS NULL, o.id IS NULL')->orderByDesc('s.id')->limit($limit)
            ->get(['s.id', 's.send_id', 's.email', 's.sent_at', 'o.first_at as first_open', 'o.last_at as last_open', 'o.count as open_count', 'o.machine as open_machine',
                'c.clicks', 'c.first_click', 'c.last_click', 'c.m as click_machine']);
    }

    public static function linkStats(string $type, int $refId, ?int $ownerId = null)
    {
        $q = DB::table('email_track_events as e')->join('email_track_sends as s', 's.id', '=', 'e.track_send_id')->join('email_track_links as l', fn ($j) => $j->on('l.track_send_id', '=', 'e.track_send_id')->on('l.idx', '=', 'e.link_idx'))
            ->where('e.event', 'click')->where('s.type', $type)->where('s.ref_id', $refId);
        if ($ownerId !== null) $q->where('s.owner_id', $ownerId);
        return $q->groupBy('l.url')->selectRaw('l.url, COUNT(*) uniq, SUM(e.count) clicks')->orderByDesc('uniq')->limit(30)->get();
    }
}
