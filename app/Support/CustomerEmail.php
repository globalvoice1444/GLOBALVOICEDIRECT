<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

/**
 * 2026-11-02 Customer email campaigns engine (customer panel). Separate from admin Broadcast/Drip.
 * Sends only when config('ec.sending') AND campaign SCHEDULED/SENDING AND sender not blocked. Throttled per customer per ET day.
 */
class CustomerEmail
{
    const TZ = 'America/New_York';
    const TAGS = ['{{first_name}}', '{{last_name}}', '{{business_name}}', '{{my_business}}'];

    // ---------------- sanitizing ----------------
    const ALLOWED = ['p', 'br', 'b', 'strong', 'i', 'em', 'u', 's', 'a', 'h1', 'h2', 'h3', 'h4', 'ul', 'ol', 'li', 'img', 'span', 'div', 'blockquote', 'hr',
        'table', 'tbody', 'thead', 'tr', 'td', 'th', 'center', 'font'];
    const CSS_PROPS = ['color', 'background-color', 'background', 'text-align', 'font-size', 'font-weight', 'font-style', 'font-family', 'text-decoration', 'line-height',
        'padding', 'padding-top', 'padding-bottom', 'padding-left', 'padding-right', 'margin', 'margin-top', 'margin-bottom', 'margin-left', 'margin-right',
        'width', 'max-width', 'height', 'border', 'border-radius', 'border-top', 'border-bottom', 'display', 'vertical-align'];

    /** Strict allowlist sanitizer. No scripts, no <style>, no event handlers, only http(s)/mailto links, only https/data-free images. */
    public static function sanitize(string $html): string
    {
        $html = mb_substr($html, 0, 200000);
        if (trim($html) === '') return '';
        $doc = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"><div id="ecroot">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET);
        libxml_clear_errors();
        $root = $doc->getElementById('ecroot') ?: $doc->documentElement;
        self::clean($root);
        $out = '';
        foreach (iterator_to_array($root->childNodes) as $c) $out .= $doc->saveHTML($c);
        return trim($out);
    }

    private static function clean(\DOMNode $n): void
    {
        foreach (iterator_to_array($n->childNodes) as $c) {
            if ($c instanceof \DOMComment || $c instanceof \DOMProcessingInstruction || $c instanceof \DOMCdataSection) { $n->removeChild($c); continue; }
            if (!($c instanceof \DOMElement)) continue;
            $tag = strtolower($c->nodeName);
            if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'button', 'textarea', 'select', 'svg', 'math', 'link', 'meta', 'base', 'head', 'title', 'noscript', 'template', 'video', 'audio'], true)) { $n->removeChild($c); continue; }
            if (!in_array($tag, self::ALLOWED, true)) { // unwrap unknown tag, keep its text/children
                self::clean($c);
                while ($c->firstChild) $n->insertBefore($c->firstChild, $c);
                $n->removeChild($c); continue;
            }
            foreach (iterator_to_array($c->attributes) as $a) {
                $name = strtolower($a->nodeName); $v = trim($a->nodeValue);
                $keep = false;
                if ($name === 'style') { $v = self::css($v); $keep = $v !== ''; }
                elseif ($name === 'href' && $tag === 'a') $keep = (bool) preg_match('#^(https?://|mailto:|\{\{unsubscribe_url\}\})#i', $v);
                elseif ($name === 'src' && $tag === 'img') $keep = (bool) preg_match('#^https://#i', $v);
                elseif (in_array($name, ['alt', 'title', 'width', 'height', 'align', 'colspan', 'rowspan', 'cellpadding', 'cellspacing', 'border', 'color'], true)) $keep = !preg_match('/[<>"]|javascript:/i', $v);
                $c->removeAttribute($a->nodeName);
                if ($keep) $c->setAttribute($name, $v);
            }
            if ($tag === 'a' && $c->hasAttribute('href')) { $c->setAttribute('target', '_blank'); $c->setAttribute('rel', 'noopener'); }
            self::clean($c);
        }
    }

    private static function css(string $s): string
    {
        if (preg_match('/expression|javascript:|url\s*\(|@import|behavior|-moz-binding|\\\\/i', $s)) return '';
        $out = [];
        foreach (explode(';', $s) as $d) {
            if (!str_contains($d, ':')) continue;
            [$p, $v] = array_map('trim', explode(':', $d, 2));
            $p = strtolower($p);
            if (in_array($p, self::CSS_PROPS, true) && $v !== '' && preg_match('/^[#\w\s.,%()\'"-]+$/u', $v) && strlen($v) < 120) $out[] = "$p:$v";
        }
        return implode(';', $out);
    }

    // ---------------- identity ----------------
    public static function sender(int $uid): array
    {
        $u = DB::table('wpgc_users')->where('ID', $uid)->first(['ID', 'user_email', 'display_name']);
        $biz = DB::table('gvd_business_listings')->where('user_id', $uid)->where('business_name', '<>', '')->value('business_name')
            ?: DB::table('addon_business_listing')->where('userID', $uid)->where('businessname', '<>', '')->orderByDesc('id')->value('businessname')
            ?: DB::table('user_shipping_address')->where('user_id', $uid)->where('company_name', '<>', '')->value('company_name')
            ?: ($u->display_name ?? '');
        $addr = DB::table('user_shipping_address')->where('user_id', $uid)->orderByDesc('id')->first(['address', 'city', 'state', 'zip']);
        $a = $addr ? trim(implode(', ', array_filter([trim((string) $addr->address), trim((string) $addr->city), trim(trim((string) $addr->state) . ' ' . trim((string) $addr->zip))]))) : '';
        $biz = trim(preg_replace('/[\r\n<>"]+/', ' ', (string) $biz));
        return ['uid' => $uid, 'email' => strtolower(trim((string) ($u->user_email ?? ''))), 'business' => mb_substr($biz ?: 'Your business', 0, 80), 'address' => mb_substr($a, 0, 200)];
    }

    public static function unsubscribeUrl(int $uid, string $email): string
    {
        return URL::signedRoute('ec.unsubscribe', ['u' => $uid, 'e' => strtolower(trim($email))]);
    }

    public static function openUrl(int $rid): string
    {
        return URL::signedRoute('ec.open', ['r' => $rid]);
    }

    // ---------------- render ----------------
    public static function mergeVars(object $r, array $snd): array
    {
        $first = trim((string) ($r->first_name ?? ''));
        return ['{{first_name}}' => $first !== '' ? $first : 'there', '{{last_name}}' => trim((string) ($r->last_name ?? '')),
            '{{business_name}}' => trim((string) ($r->business_name ?? '')) ?: 'your business', '{{my_business}}' => $snd['business']];
    }

    /** Returns [subject, html, unsubscribeUrl]. Body is re-sanitized at send time. */
    public static function render(object $c, object $r, array $snd, ?int $rid = null): array
    {
        $v = self::mergeVars($r, $snd);
        $ve = array_map(fn ($x) => e($x), $v);
        $unsub = self::unsubscribeUrl($snd['uid'], $r->email);
        $subject = mb_substr(str_replace(["\r", "\n"], ' ', strtr((string) $c->subject, $v)), 0, 200);
        $body = strtr(self::sanitize((string) $c->body_html), $ve + ['{{unsubscribe_url}}' => e($unsub)]);
        $pre = e(strtr((string) $c->preview, $v));
        $pixel = $rid ? '<img src="' . e(self::openUrl($rid)) . '" width="1" height="1" alt="" style="display:block;border:0;width:1px;height:1px">' : '';
        $html = '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head><body style="margin:0;background:#F4F6F9;font-family:Inter,Segoe UI,Arial,sans-serif;color:#0F1B26">'
            . '<div style="display:none;max-height:0;overflow:hidden">' . $pre . '</div>'
            . '<table width="100%" cellpadding="0" cellspacing="0" style="background:#F4F6F9;padding:24px 0"><tr><td align="center">'
            . '<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#fff;border-radius:12px;border:1px solid #E6EAF0">'
            . '<tr><td style="padding:28px;font-size:15px;line-height:1.6">' . $body . '</td></tr>'
            . '<tr><td style="padding:16px 28px;border-top:1px solid #E6EAF0;color:#64748B;font-size:12px;line-height:1.6">'
            . e($snd['business']) . ($snd['address'] !== '' ? ' &middot; ' . e($snd['address']) : '') . '<br>'
            . 'You received this email from ' . e($snd['business']) . '. <a href="' . e($unsub) . '" style="color:#64748B">Unsubscribe</a> from their emails.'
            . '</td></tr></table></td></tr></table>' . $pixel . '</body></html>';
        return [$subject, $html, $unsub];
    }

    const PRESETS = [
        'gmail' => ['Gmail / Google Workspace', 'smtp.gmail.com', 587, 'tls', 500],
        'outlook' => ['Outlook / Microsoft 365', 'smtp.office365.com', 587, 'tls', 300],
        'yahoo' => ['Yahoo Mail', 'smtp.mail.yahoo.com', 465, 'ssl', 500],
        'zoho' => ['Zoho Mail', 'smtp.zoho.com', 465, 'ssl', 500],
        'other' => ['Other (SMTP)', '', 587, 'tls', 500],
    ];

    /** Customer's connected mailbox, or null. Never returns the password. */
    public static function smtp(int $uid): ?object
    {
        $s = DB::table('ec_smtp')->where('user_id', $uid)->first();
        return $s && $s->verified_at ? $s : null;
    }

    /** Refuse internal/private hosts and odd ports (customer-supplied host). */
    public static function hostOk(string $host, int $port): ?string
    {
        if (!in_array($port, [25, 465, 587, 2525], true)) return 'Use port 465, 587, 2525 or 25.';
        if (!preg_match('/^[a-z0-9.-]+\.[a-z]{2,}$/i', $host)) return 'Enter a valid SMTP server name, like smtp.gmail.com.';
        $ips = @gethostbynamel($host) ?: [];
        if (!$ips) return 'We could not find that SMTP server.';
        foreach ($ips as $ip) if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) return 'That SMTP server address is not allowed.';
        return null;
    }

    public static function transport(object $s): \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport
    {
        $t = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport($s->host, (int) $s->port, $s->encryption === 'ssl');
        $t->setUsername($s->username); $t->setPassword(\Illuminate\Support\Facades\Crypt::decryptString($s->password_enc));
        $st = $t->getStream(); if ($st instanceof \Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream) $st->setTimeout(20);
        return $t;
    }

    /** Sends through the CUSTOMER'S OWN mailbox (per-customer mailer built at send time). Never uses GVD's mail server. */
    public static function deliver(array $snd, string $to, string $subject, string $html, string $unsub, ?object $smtp = null, $transport = null): void
    {
        $smtp = $smtp ?: self::smtp($snd['uid']);
        if (!$smtp) throw new \RuntimeException('no_mailbox');
        $m = (new \Symfony\Component\Mime\Email())->from(new \Symfony\Component\Mime\Address($smtp->from_email, $smtp->from_name ?: $snd['business']))
            ->to($to)->replyTo($smtp->from_email)->subject($subject)->html($html);
        $h = $m->getHeaders();
        $h->addTextHeader('List-Unsubscribe', '<' . $unsub . '>');
        $h->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
        (new \Symfony\Component\Mailer\Mailer($transport ?: self::transport($smtp)))->send($m);
    }

    // ---------------- contacts ----------------
    public static function normEmail(string $e): ?string
    {
        $e = strtolower(trim($e, " \t\n\r\0\x0B<>\"'"));
        if (strlen($e) > 190 || !filter_var($e, FILTER_VALIDATE_EMAIL)) return null;
        if (preg_match('/@(example\.(com|org|net)|localhost)$/', $e)) return null;
        return $e;
    }

    public static function normTags($tags): ?string
    {
        $a = is_array($tags) ? $tags : preg_split('/[,;|]+/', (string) $tags);
        $a = array_values(array_unique(array_filter(array_map(fn ($t) => mb_substr(trim(preg_replace('/[^\pL\pN _-]/u', '', mb_strtolower((string) $t))), 0, 40), $a))));
        return $a ? mb_substr(implode(',', array_slice($a, 0, 15)), 0, 500) : null;
    }

    public static function isSuppressed(int $uid, string $email): bool
    {
        return DB::table('ec_suppressions')->where('user_id', $uid)->where('email', $email)->exists();
    }

    /** Contacts matching audience ['all'=>true] or ['tags'=>[..]], active and not suppressed. */
    public static function audienceQuery(int $uid, array $aud)
    {
        $q = DB::table('ec_contacts as c')->where('c.user_id', $uid)->where('c.status', 'active')
            ->whereNotExists(fn ($s) => $s->from('ec_suppressions as s')->whereColumn('s.email', 'c.email')->where('s.user_id', $uid));
        $tags = array_filter((array) ($aud['tags'] ?? []));
        if (empty($aud['all']) && $tags) $q->where(function ($w) use ($tags) { foreach ($tags as $t) $w->orWhereRaw('FIND_IN_SET(?, c.tags)', [$t]); });
        return $q;
    }

    // ---------------- limits ----------------
    public static function limitFor(int $uid): int
    {
        $s = DB::table('ec_senders')->where('user_id', $uid)->first();
        if ($s && $s->daily_limit) return (int) $s->daily_limit;
        $box = DB::table('ec_smtp')->where('user_id', $uid)->value('daily_limit');
        $cap = $box ? min((int) $box, (int) config('ec.daily_limit', 500)) : (int) config('ec.daily_limit', 500);
        $new = !$s || !$s->first_sent_at || \Carbon\Carbon::parse($s->first_sent_at)->gt(now()->subDays(config('ec.new_sender_days', 7)));
        return $new ? min($cap, (int) config('ec.new_sender_limit', 100)) : $cap;
    }

    public static function sentToday(int $uid): int
    {
        $start = now(self::TZ)->startOfDay()->utc();
        return DB::table('ec_recipients as r')->join('ec_campaigns as c', 'c.id', '=', 'r.campaign_id')->where('c.user_id', $uid)
            ->whereIn('r.status', ['sent', 'sending', 'failed'])->where('r.updated_at', '>=', $start)->count();
    }

    public static function blocked(int $uid): ?string
    {
        $s = DB::table('ec_senders')->where('user_id', $uid)->first();
        return $s && $s->blocked ? ($s->blocked_reason ?: 'Sending is paused for this account.') : null;
    }

    public static function stats(int $cid): array
    {
        $r = DB::table('ec_recipients')->where('campaign_id', $cid)->selectRaw("COUNT(*) total, SUM(status='pending') pending, SUM(status='sending') sending, SUM(status='sent') sent, SUM(status='failed') failed, SUM(status='skipped') skipped, SUM(opened_at IS NOT NULL) opened")->first();
        return array_map('intval', (array) $r);
    }

    /** Snapshot audience into recipients (idempotent). */
    public static function snapshot(object $c): int
    {
        $aud = json_decode((string) $c->audience, true) ?: ['all' => true];
        $n = 0; $now = now();
        self::audienceQuery((int) $c->user_id, $aud)->orderBy('c.id')->select('c.id', 'c.email', 'c.first_name', 'c.last_name', 'c.business_name')
            ->chunk(500, function ($rows) use ($c, $now, &$n) {
                $n += DB::table('ec_recipients')->insertOrIgnore($rows->map(fn ($x) => ['campaign_id' => $c->id, 'contact_id' => $x->id, 'email' => $x->email, 'first_name' => $x->first_name,
                    'last_name' => $x->last_name, 'business_name' => $x->business_name, 'status' => 'pending', 'created_at' => $now, 'updated_at' => $now])->all());
            });
        return $n;
    }

    // ---------------- worker ----------------
    public static function run(): array
    {
        $out = ['started' => 0, 'sent' => 0, 'failed' => 0, 'skipped' => 0, 'paused' => 0, 'disabled' => !config('ec.sending')];
        if (!config('ec.sending')) return $out;
        $demo = (int) config('gvd_demo.user_id', 0);
        foreach (DB::table('ec_campaigns')->where('status', 'SCHEDULED')->where('scheduled_at', '<=', now())->limit(50)->get() as $c) {
            if (DB::table('ec_campaigns')->where('id', $c->id)->where('status', 'SCHEDULED')->update(['status' => 'SENDING', 'started_at' => now(), 'updated_at' => now()])) {
                self::snapshot($c); $out['started']++;
            }
        }
        foreach (DB::table('ec_campaigns')->where('status', 'SENDING')->orderBy('id')->limit(100)->get() as $c) {
            $uid = (int) $c->user_id;
            if ($uid === $demo) { self::pause($c->id, 'Sample account: sending disabled.'); $out['paused']++; continue; }
            if ($why = self::blocked($uid)) { self::pause($c->id, $why); $out['paused']++; continue; }
            $room = min((int) config('ec.batch', 50), self::limitFor($uid) - self::sentToday($uid) - CustomerSequences::sentToday($uid));
            if ($room <= 0) continue; // resumes next ET day
            $smtp = self::smtp($uid);
            if (!$smtp) { self::pause($c->id, 'Connect your email to send. Campaigns now go out from your own mailbox.'); $out['paused']++; continue; }
            $snd = self::sender($uid);
            try { $tr = self::transport($smtp); } catch (\Throwable $e) { self::pause($c->id, 'Your email connection needs attention. Reconnect it under Sending email.'); $out['paused']++; continue; }
            $first = true;
            $ids = DB::table('ec_recipients')->where('campaign_id', $c->id)->where('status', 'pending')->orderBy('id')->limit($room)->pluck('id');
            if ($ids->isEmpty()) {
                if (!DB::table('ec_recipients')->where('campaign_id', $c->id)->where('status', 'sending')->exists())
                    DB::table('ec_campaigns')->where('id', $c->id)->update(['status' => 'DONE', 'finished_at' => now(), 'updated_at' => now()]);
                continue;
            }
            DB::table('ec_senders')->insertOrIgnore(['user_id' => $uid, 'created_at' => now(), 'updated_at' => now()]);
            DB::table('ec_senders')->where('user_id', $uid)->whereNull('first_sent_at')->update(['first_sent_at' => now(), 'updated_at' => now()]);
            foreach ($ids as $rid) {
                // claim (pending -> sending) so overlap/crash never double-sends
                if (!DB::table('ec_recipients')->where('id', $rid)->where('status', 'pending')->update(['status' => 'sending', 'updated_at' => now()])) continue;
                $r = DB::table('ec_recipients')->where('id', $rid)->first();
                if (self::isSuppressed($uid, $r->email) || DB::table('ec_contacts')->where('user_id', $uid)->where('email', $r->email)->where('status', '<>', 'active')->exists()) {
                    DB::table('ec_recipients')->where('id', $rid)->update(['status' => 'skipped', 'reason' => 'unsubscribed', 'updated_at' => now()]); $out['skipped']++; continue;
                }
                try {
                    [$subj, $html, $unsub] = self::render($c, $r, $snd, $rid);
                    $html = Tracking::apply($html, "ec", (int) $rid, (int) $c->id, $r->email, $uid); // trk
                    if (!$first) sleep((int) config('ec.spacing_seconds', 3)); $first = false;
                    self::deliver($snd, $r->email, $subj, $html, $unsub, $smtp, $tr);
                    DB::table('ec_recipients')->where('id', $rid)->update(['status' => 'sent', 'sent_at' => now(), 'updated_at' => now()]); $out['sent']++;
                } catch (\Throwable $e) {
                    DB::table('ec_recipients')->where('id', $rid)->update(['status' => 'failed', 'reason' => mb_substr($e->getMessage(), 0, 250), 'updated_at' => now()]); $out['failed']++;
                    Log::warning('ec send failed', ['campaign' => $c->id, 'err' => mb_substr($e->getMessage(), 0, 200)]);
                }
            }
            $st = self::stats($c->id); $tries = $st['sent'] + $st['failed'];
            if ($tries >= 20 && $st['failed'] / $tries > config('ec.max_fail_rate', 0.1)) { self::pause($c->id, 'Paused automatically: too many failed sends. Check your list, then resume.'); $out['paused']++; }
        }
        return $out;
    }

    public static function pause(int $cid, string $why): void
    {
        DB::table('ec_campaigns')->where('id', $cid)->whereIn('status', ['SCHEDULED', 'SENDING'])->update(['status' => 'PAUSED', 'pause_reason' => mb_substr($why, 0, 255), 'updated_at' => now()]);
    }

    /** Bounce/complaint from provider webhook. Marks contact, then auto-blocks a sender whose 7-day rates are too high. */
    public static function feedback(string $email, string $type, ?int $uid = null): void
    {
        $email = strtolower(trim($email));
        $q = DB::table('ec_recipients as r')->join('ec_campaigns as c', 'c.id', '=', 'r.campaign_id')->where('r.email', $email)->where('r.status', 'sent')->where('r.sent_at', '>=', now()->subDays(7));
        if ($uid) $q->where('c.user_id', $uid);
        $row = $q->orderByDesc('r.sent_at')->first(['r.id', 'c.user_id', 'c.id as cid']);
        if (!$row) return;
        $uid = (int) $row->user_id; $st = $type === 'complaint' ? 'complained' : 'bounced';
        DB::table('ec_recipients')->where('id', $row->id)->update(['reason' => $st, 'updated_at' => now()]);
        DB::table('ec_contacts')->where('user_id', $uid)->where('email', $email)->update(['status' => $st, 'updated_at' => now()]);
        DB::table('ec_suppressions')->insertOrIgnore(['user_id' => $uid, 'email' => $email, 'reason' => $st, 'created_at' => now(), 'updated_at' => now()]);
        $since = now()->subDays(7);
        $base = DB::table('ec_recipients as r')->join('ec_campaigns as c', 'c.id', '=', 'r.campaign_id')->where('c.user_id', $uid)->where('r.sent_at', '>=', $since);
        $sent = (clone $base)->where('r.status', 'sent')->count();
        $b = (clone $base)->where('r.reason', 'bounced')->count(); $k = (clone $base)->where('r.reason', 'complained')->count();
        $why = null;
        if ($sent >= 50 && $b / $sent > config('ec.max_bounce_rate', 0.05)) $why = 'Paused automatically: bounce rate over 5%. Contact support to review your list.';
        if ($sent >= 50 && $k / $sent > config('ec.max_complaint_rate', 0.003)) $why = 'Paused automatically: spam complaints over 0.3%. Contact support.';
        if ($why) {
            DB::table('ec_senders')->insertOrIgnore(['user_id' => $uid, 'created_at' => now(), 'updated_at' => now()]);
            DB::table('ec_senders')->where('user_id', $uid)->update(['blocked' => 1, 'blocked_reason' => $why, 'updated_at' => now()]);
            DB::table('ec_campaigns')->where('user_id', $uid)->whereIn('status', ['SCHEDULED', 'SENDING'])->update(['status' => 'PAUSED', 'pause_reason' => $why, 'updated_at' => now()]);
        }
    }
}
