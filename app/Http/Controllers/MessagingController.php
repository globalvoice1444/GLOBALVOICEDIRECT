<?php

namespace App\Http\Controllers;

use App\Fax\FaxInbox;
use App\Portal\BillingSession;
use App\Portal\PhoneNumbers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

/** Downloadable Phone Scripts + SMS Campaign. Tenant-scoped (user_id + i_customer). No external sends unless config gvd_sms.live. */
class MessagingController extends Controller
{
    const NC = ['Cache-Control' => 'private, no-store, max-age=0', 'Pragma' => 'no-cache'];
    const OPT_OUT = ' Reply STOP to opt out';

    const SCRIPTS = [
        'main' => ['Main greeting / auto-attendant', 'Business hours', 'Thank you for calling {business}. {departments_menu} To reach an extension directly, dial it at any time, or stay on the line for the next available representative.'],
        'after' => ['After-hours', 'Evenings & weekends', "You've reached {business}. Our office is open {hours}. Please leave a message and we'll return your call the next business day."],
        'holiday' => ['Holiday hours', 'Holidays', 'Thank you for calling {business}. Our office is closed for the holiday and will reopen on the next business day. Our regular hours are {hours}. Please leave a message and we will call you back.'],
        'hold' => ['On-hold message', 'Callers on hold', 'Thanks for holding. Your call is important to us at {business}. A representative will be with you shortly.'],
        'voicemail' => ['Voicemail greeting', 'Extension voicemail', "Hi, you've reached {business}. We're away from our desks right now. Please leave your name, number and a short message, and we'll call you back."],
        'busy' => ['Call-forwarding / busy', 'All agents busy', 'All of our representatives at {business} are currently helping other callers. Please stay on the line, or press 1 to leave a message and we will call you back shortly.'],
    ];

    /** Template library lives in config/gvd_phone_scripts.php (admin editor can extend later); falls back to SCRIPTS. */
    public static function library(): array
    {
        $s = config('gvd_phone_scripts.scripts');
        return is_array($s) && $s ? $s : self::SCRIPTS;
    }

    // ---------- context ----------
    private function ctx(): array
    {
        $uid = (int) session()->get('user_id');
        if (\App\Portal\Demo::on()) return ['uid' => $uid, 'ic' => null, 'admin' => true, 'numbers' => \App\Portal\Demo::smsNumbers()]; // sample-data login only
        $k = BillingSession::get(); $ic = null; $admin = false; $nums = [];
        if ($k && in_array((int) $k['i_customer'], FaxInbox::tenantsForUser($uid), true)) {
            $ic = (int) $k['i_customer'];
            try {
                $p = new PhoneNumbers($k); $admin = $p->isAdmin();
                $rows = Cache::remember("gvd-numbers:rows:$ic", 300, fn () => $p->all());
                foreach ($rows as $r) if ($r['kind'] !== 'system' && $r['status'] === 'Active' && preg_match('/^1?\d{10}$/', $r['id'])) $nums[] = ['id' => $r['id'], 'display' => $r['display']];
            } catch (\Throwable $e) { Log::warning('gvd-msg numbers failed', ['e' => get_class($e)]); }
        }
        return ['uid' => $uid, 'ic' => $ic, 'admin' => $admin, 'numbers' => $nums];
    }

    private function bizName(int $uid): string
    {
        try { $u = DB::table('wpgc_users')->where('ID', $uid)->first(); return (string) ($u->display_name ?? ''); } catch (\Throwable $e) { return ''; }
    }

    // ---------- Phone scripts ----------
    public function scripts()
    {
        $c = $this->ctx();
        $si = DB::table('script_info')->where('user_id', $c['uid'])->orderByDesc('id')->first(['id', 'active', 'audio_file_path', 'created_at', 'updated_at']);
        return response()->view('users.gvd-phone-scripts', ['admin' => $c['admin'], 'error' => null,
            'd' => ['scripts' => self::library(), 'business' => $this->bizName($c['uid']), 'hasScriptRec' => $si]])->withHeaders(self::NC);
    }

    /** The 4 sections from the form, as download items. */
    private static function sections(Request $r): array
    {
        $out = [];
        foreach (['sec_main' => 'Main greeting', 'sec_after' => 'After-hours greeting', 'sec_hold' => 'Message on hold script', 'sec_pron' => 'Special pronunciation words'] as $f => $h) {
            $t = mb_substr(trim((string) $r->input($f)), 0, 4000);
            if ($t !== '') $out[] = [$h, $t];
        }
        return $out;
    }

    public static function fill(string $tpl, string $biz, string $hours, string $deps): string
    {
        $menu = '';
        foreach (array_filter(array_map('trim', preg_split('/[,;\n]+/', $deps))) as $i => $d) {
            if (preg_match('/^(.*?)\s*[–-]\s*(\d)\b/u', $d, $m)) $menu .= "For {$m[1]}, press {$m[2]}. ";
            else $menu .= "For $d, press " . ($i + 1) . '. ';
        }
        return trim(strtr($tpl, ['{business}' => $biz ?: 'our office', '{hours}' => $hours ?: 'during regular business hours', '{departments_menu}' => trim($menu), '{departments}' => $deps]));
    }

    public function scriptDownload(Request $r)
    {
        $key = (string) $r->input('key', 'main'); $fmt = (string) $r->input('format', 'txt');
        $biz = mb_substr(trim((string) $r->input('business')), 0, 120);
        $hours = mb_substr(trim((string) $r->input('hours')), 0, 160);
        $deps = mb_substr(trim((string) $r->input('departments')), 0, 400);
        $text = mb_substr(trim((string) $r->input('text')), 0, 4000);
        $items = [];
        $ind = (string) $r->input('industry', '');
        if ($key === 'all') { foreach (self::library() as $s) { if ($ind === '' || ($s[3] ?? 'General') === $ind) $items[] = [$s[0], self::fill($s[2], $biz, $hours, $deps)]; } }
        else { $all = self::library(); if (!isset($all[$key])) abort(404); $sec = self::sections($r); if ($sec) $items = $sec; else $items[] = [$all[$key][0], $text !== '' ? $text : self::fill($all[$key][2], $biz, $hours, $deps)]; }
        $title = ($biz ?: 'Phone') . ' – ' . ($key === 'all' ? (($ind !== '' ? $ind . ' ' : '') . 'phone scripts') : (!empty($sec) ? 'Phone scripts' : $items[0][0]));
        $base = preg_replace('/[^A-Za-z0-9]+/', '-', strtolower(($biz ?: 'phone') . '-' . ($key === 'all' ? 'scripts' : $key))) ;
        $base = trim($base, '-');
        switch ($fmt) {
            case 'pdf': $body = ScriptDoc::pdf($title, $items); $mime = 'application/pdf'; $ext = 'pdf'; break;
            case 'docx': $body = ScriptDoc::docx($title, $items); $mime = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'; $ext = 'docx'; break;
            default: $body = ScriptDoc::txt($title, $items); $mime = 'text/plain; charset=UTF-8'; $ext = 'txt';
        }
        return response($body, 200, self::NC + ['Content-Type' => $mime, 'Content-Disposition' => "attachment; filename=\"$base.$ext\""]);
    }

    // ---------- SMS campaign ----------
    private function lists(int $uid): array
    {
        $g = DB::table('compaign_group')->where('user_id', $uid)->where('deleted', 0)->orderBy('group_name')->get(['id', 'group_name']);
        $cnt = DB::table('sms_contact')->where('user_id', $uid)->where('deleted', 0)->select('group_id', DB::raw('count(*) n'))->groupBy('group_id')->pluck('n', 'group_id');
        return $g->map(fn ($x) => ['id' => (int) $x->id, 'name' => (string) $x->group_name, 'count' => (int) ($cnt[$x->id] ?? 0)])->all();
    }

    private function q(array $c) { return DB::table('gvd_sms_campaigns')->where('user_id', $c['uid'])->where(fn ($q) => $c['ic'] === null ? $q->whereNull('i_customer') : $q->where('i_customer', $c['ic'])); }

    public function sms()
    {
        $c = $this->ctx();
        $camps = $this->q($c)->orderByDesc('id')->limit(100)->get()->map(fn ($x) => (array) $x)->all();
        return response()->view('users.gvd-sms-campaign', ['admin' => $c['admin'], 'error' => null, 'd' => [
            'campaigns' => $camps, 'lists' => $this->lists($c['uid']), 'numbers' => $c['numbers'],
            'live' => (bool) config('gvd_sms.live'), 'optOut' => trim(self::OPT_OUT)]])->withHeaders(self::NC);
    }

    public static function segments(string $s): array
    {
        $gsm = !preg_match('/[^\x0A\x0D\x20-\x7E£¥èéùìòÇØøÅåΔ_ΦΓΛΩΠΨΣΘΞÆæßÉ¤¡ÄÖÑÜ§¿äöñüà€]/u', $s);
        $n = mb_strlen($s); $one = $gsm ? 160 : 70; $multi = $gsm ? 153 : 67;
        return ['chars' => $n, 'segments' => $n <= $one ? 1 : (int) ceil($n / $multi), 'encoding' => $gsm ? 'GSM-7' : 'Unicode'];
    }

    private function validated(Request $r, array $c): array
    {
        $name = trim((string) $r->input('name')); $msg = trim((string) $r->input('message'));
        if ($name === '' || mb_strlen($name) > 120) throw new \DomainException('Give the campaign a name (up to 120 characters).');
        if ($msg === '' || mb_strlen($msg) > 1200) throw new \DomainException('Write a message (up to 1,200 characters).');
        $lid = (int) $r->input('list_id');
        if ($lid && !collect($this->lists($c['uid']))->firstWhere('id', $lid)) throw new \DomainException('Pick one of your contact lists.');
        $from = preg_replace('/\D/', '', (string) $r->input('sender'));
        if ($from !== '' && !in_array($from, array_column($c['numbers'], 'id'), true)) throw new \DomainException('Pick one of your own numbers as the sender.');
        $sched = null;
        if (trim((string) $r->input('schedule_at')) !== '') {
            try { $sched = \Carbon\Carbon::parse((string) $r->input('schedule_at'), 'America/New_York'); } catch (\Throwable $e) { throw new \DomainException('The schedule date is not valid.'); }
            if ($sched->isPast()) throw new \DomainException('The schedule time is in the past.');
        }
        $full = rtrim($msg) . self::OPT_OUT; $sg = self::segments($full);
        return ['name' => $name, 'message' => $msg, 'list_id' => $lid ?: null, 'sender' => $from ?: null,
            'scheduled_at' => $sched?->clone()->utc()->format('Y-m-d H:i:s'), 'segments' => $sg['segments'], 'encoding' => $sg['encoding']];
    }

    public function smsSave(Request $r)
    {
        $c = $this->ctx();
        $key = 'gvd-sms:' . $c['uid'];
        if (RateLimiter::tooManyAttempts($key, 30)) return response()->json(['error' => 'Too many changes. Wait a minute.'], 429, self::NC);
        RateLimiter::hit($key, 60);
        try { $v = $this->validated($r, $c); } catch (\DomainException $e) { return response()->json(['error' => $e->getMessage()], 422, self::NC); }
        $v['status'] = $v['scheduled_at'] ? 'scheduled' : 'draft';
        $v['updated_at'] = now();
        $id = (int) $r->input('id');
        if ($id) {
            $row = $this->q($c)->where('id', $id)->first();
            if (!$row) return response()->json(['error' => 'Campaign not found.'], 404, self::NC);
            if (in_array($row->status, ['sent', 'sending'], true)) return response()->json(['error' => 'A sent campaign can\'t be edited.'], 422, self::NC);
            $this->q($c)->where('id', $id)->update($v);
        } else {
            $id = DB::table('gvd_sms_campaigns')->insertGetId($v + ['user_id' => $c['uid'], 'i_customer' => $c['ic'], 'created_at' => now()]);
        }
        return response()->json(['ok' => true, 'id' => $id, 'status' => $v['status']], 200, self::NC);
    }

    public function smsDelete(Request $r)
    {
        $c = $this->ctx();
        $n = $this->q($c)->where('id', (int) $r->input('id'))->whereNotIn('status', ['sending'])->delete();
        return response()->json(['ok' => $n > 0], $n ? 200 : 404, self::NC);
    }

    /** Send (admin + confirm). dry=1 or config live=false => nothing leaves the server; returns what would be sent. */
    public function smsSend(Request $r)
    {
        if (\App\Portal\Demo::on()) return \App\Portal\Demo::readonly(); // sample-data login: never sends
        $c = $this->ctx();
        if (!$c['admin']) return response()->json(['error' => 'Only the company administrator can send campaigns.'], 403, self::NC);
        if ($r->input('confirm') !== 'SEND') return response()->json(['error' => 'Please confirm before sending.'], 422, self::NC);
        $row = $this->q($c)->where('id', (int) $r->input('id'))->first();
        if (!$row) return response()->json(['error' => 'Campaign not found.'], 404, self::NC);
        if (!$row->list_id || !$row->sender) return response()->json(['error' => 'Pick a contact list and a sender number first.'], 422, self::NC);
        if (!in_array($row->sender, array_column($c['numbers'], 'id'), true)) return response()->json(['error' => 'The sender number is no longer on your account.'], 422, self::NC);
        $contacts = DB::table('sms_contact')->where('user_id', $c['uid'])->where('group_id', $row->list_id)->where('deleted', 0)->get(['contact_name', 'contact_number']);
        $biz = $this->bizName($c['uid']); $out = [];
        foreach ($contacts as $ct) {
            $num = preg_replace('/\D/', '', (string) $ct->contact_number);
            if (strlen($num) === 10) $num = "1$num";
            if (!preg_match('/^1[2-9]\d{9}$/', $num)) continue;
            $parts = preg_split('/\s+/', trim((string) $ct->contact_name), 2);
            $out[] = ['to' => $num, 'text' => strtr($row->message, ['{first_name}' => $parts[0] ?? '', '{last_name}' => $parts[1] ?? '', '{business}' => $biz]) . self::OPT_OUT];
        }
        $dry = $r->boolean('dry') || !config('gvd_sms.live');
        if ($dry) {
            $this->q($c)->where('id', $row->id)->update(['last_dry_run_at' => now(), 'updated_at' => now()]);
            return response()->json(['ok' => true, 'dry' => true, 'live' => (bool) config('gvd_sms.live'), 'recipients' => count($out), 'skipped' => count($contacts) - count($out),
                'sample' => $out[0]['text'] ?? null, 'segments' => $row->segments], 200, self::NC);
        }
        // Live path (only when GVD_SMS_LIVE=true). Untested against the provider.
        $this->q($c)->where('id', $row->id)->update(['status' => 'sending', 'updated_at' => now()]);
        $sent = 0; $failed = 0;
        foreach ($out as $m) {
            $ok = SmsGateway::send($row->sender, $m['to'], $m['text']);
            DB::table('gvd_sms_messages')->insert(['campaign_id' => $row->id, 'to' => $m['to'], 'status' => $ok ? 'accepted' : 'failed', 'created_at' => now()]);
            $ok ? $sent++ : $failed++;
        }
        $this->q($c)->where('id', $row->id)->update(['status' => 'sent', 'sent_count' => $sent, 'failed_count' => $failed, 'sent_at' => now(), 'updated_at' => now()]);
        return response()->json(['ok' => true, 'dry' => false, 'sent' => $sent, 'failed' => $failed], 200, self::NC);
    }
}
