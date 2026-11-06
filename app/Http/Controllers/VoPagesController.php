<?php

namespace App\Http\Controllers;

use App\Fax\FaxInbox;
use App\Portal\BillingSession;
use App\Portal\PhoneNumbers;
use App\Portal\VoData;
use App\Portal\VoMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

/** Calls, Extensions, Conference rooms, Call block, Users, Extras, Dial out numbers. Tenant-scoped, admin-only writes. */
class VoPagesController extends Controller
{
    const NC = ['Cache-Control' => 'private, no-store, max-age=0', 'Pragma' => 'no-cache'];
    const TYPES = ['inbound' => 'inbound', 'outbound' => 'outbound', 'dial-out' => 'dial_out', 'recorded' => 'recorded_calls'];
    const STATUS = ['inbound' => ['' => 'Any', 'connected' => 'Connected', 'missed' => 'Missed', 'voicemail' => 'Voicemail', 'fax' => 'Fax'],
        'outbound' => ['' => 'Any', 'connected' => 'Connected', 'missed' => 'Unanswered'], 'dial-out' => ['' => 'Any', 'connected' => 'Connected', 'missed' => 'Unanswered'], 'recorded' => []];
    const RANGES = ['0' => 'Today', '-1' => 'Yesterday', '-7' => 'Last 7 days', '-30' => 'Past 30 days', '-60' => 'Past 60 days'];
    const TITLES = ['inbound' => 'Inbound calls', 'outbound' => 'Outbound calls', 'dial-out' => 'Dial out', 'recorded' => 'Recorded calls', 'conference-recordings' => 'Recorded conferences'];

    // ---------- common ----------
    private function svc(): VoData|\Symfony\Component\HttpFoundation\Response
    {
        if (\App\Portal\Demo::on()) return \App\Portal\Demo::readonly(); // sample-data login: never touches the billing API
        $ten = FaxInbox::tenantsForUser(session()->get('user_id'));
        $ic = BillingSession::storedCustomer();
        if ($ic !== null && !in_array($ic, $ten, true)) return response()->json(['error' => 'Not allowed for this account.'], 403, self::NC);
        $k = BillingSession::get();
        if (!$k) return response()->json(['error' => 'Your sign-in has expired. Please sign out and sign in again.', 'signin' => true], 401, self::NC);
        if (!in_array((int) $k['i_customer'], $ten, true)) return response()->json(['error' => 'Not allowed for this account.'], 403, self::NC);
        return new VoData($k);
    }

    private function page(string $view, callable $fn)
    {
        if (\App\Portal\Demo::on()) return response()->view($view, $this->demoPage($view), 200)->withHeaders(self::NC); // sample-data login only
        $v = $this->svc();
        if (!$v instanceof VoData) {
            $err = json_decode($v->getContent(), true)['error'] ?? 'Not available';
            return response()->view($view, ['error' => $err, 'admin' => false, 'd' => []], $v->getStatusCode() === 403 ? 403 : 200)->withHeaders(self::NC);
        }
        try { $d = $fn($v); $err = null; }
        catch (\Throwable $e) {
            $d = []; $err = 'This page could not be loaded right now. Please try again.';
            Log::warning('gvd-vo page failed', ['view' => $view, 'i_customer' => $v->ic, 'error' => get_class($e), 'm' => substr(preg_replace('/[^\w .:]/', '', $e->getMessage()), 0, 80)]);
        } finally { $v->close(); }
        return response()->view($view, ['error' => $err, 'admin' => $v->isAdmin(), 'd' => $d])->withHeaders(self::NC);
    }

    private function admin(): VoData|\Symfony\Component\HttpFoundation\Response
    {
        $v = $this->svc(); if (!$v instanceof VoData) return $v;
        if (!$v->isAdmin()) return response()->json(['error' => 'Only the company administrator can do this.'], 403, self::NC);
        $key = 'vo-write:' . session()->get('user_id');
        if (RateLimiter::tooManyAttempts($key, 30)) return response()->json(['error' => 'Too many changes. Wait a minute.'], 429, self::NC);
        RateLimiter::hit($key, 60);
        return $v;
    }

    private function ok(VoData $v, string $what) { Log::info('gvd-vo change', ['i_customer' => $v->ic, 'user_id' => session()->get('user_id'), 'what' => $what]); return response()->json(['ok' => true], 200, self::NC); }
    private function fail(VoData $v, \Throwable $e, string $what)
    {
        Log::warning('gvd-vo change failed', ['i_customer' => $v->ic, 'what' => $what, 'error' => get_class($e), 'm' => substr(preg_replace('/[^\w .:]/', '', $e->getMessage()), 0, 80)]);
        return response()->json(['error' => $e instanceof \DomainException ? 'Not allowed.' : 'The change could not be saved.'], $e instanceof \DomainException ? 403 : 502, self::NC);
    }
    private static function et(?string $utc): string
    {
        if (!$utc) return '';
        try { return (new \DateTimeImmutable($utc, new \DateTimeZone('UTC')))->setTimezone(new \DateTimeZone('America/New_York'))->format('M j, Y g:i A'); } catch (\Throwable $e) { return $utc; }
    }
    private static function dur($s): string { $s = (int) round((float) $s); return sprintf('%d:%02d', intdiv($s, 60), $s % 60); }

    /** Fixture data for the sample-data login (App\Portal\Demo); views without fixtures show an empty, friendly state. */
    private function demoPage(string $view): array
    {
        $r = request(); $D = \App\Portal\Demo::class;
        if ($view === 'users.vo-extensions') return ['error' => null, 'admin' => true, 'd' => $D::extensions()];
        if ($view === 'users.vo-users') return ['error' => null, 'admin' => true, 'd' => $D::users()];
        if ($view === 'users.vo-calls') {
            $slug = (string) $r->route('slug');
            if ($slug === 'conference-recordings' || !isset(self::TITLES[$slug])) return ['error' => 'No recordings yet.', 'admin' => true, 'd' => []];
            $rows = $D::callRows($slug);
            $range = array_key_exists((string) $r->query('range', '-7'), self::RANGES) ? (string) $r->query('range', '-7') : '-7';
            return ['error' => null, 'admin' => true, 'd' => ['slug' => $slug, 'title' => self::TITLES[$slug], 'rows' => $rows, 'total' => count($rows), 'page' => 1, 'per' => 50,
                'range' => $range, 'ranges' => self::RANGES, 'statuses' => self::STATUS[$slug] ?? [], 'q' => ['status' => '', 'from' => '', 'to' => '', 'ext' => '']]];
        }
        if ($view === 'users.vo-conferences') return ['error' => null, 'admin' => true, 'd' => $D::conferences()];
        if ($view === 'users.vo-dialout') return ['error' => null, 'admin' => true, 'd' => $D::dialOut()];
        if ($view === 'users.vo-webrtc') return ['error' => null, 'admin' => true, 'd' => $D::webrtcExts()];
        if ($view === 'users.vo-callblock') return ['error' => null, 'admin' => true, 'd' => $D::callBlock()];
        if ($view === 'users.vo-extras') {
            $on = []; foreach ($D::extras() as $x) $on[$x[0]] = $x[2];
            $rows = array_map(fn ($x) => ['code' => $x[0], 'name' => self::EXTRA_NAMES[$x[0]] ?? $x[0], 'desc' => self::EXTRA_DESC[$x[0]] ?? '', 'fee' => (float) $x[1],
                'on' => $x[2], 'qualified' => true, 'changeable' => true, 'lock' => self::extraLock($x[0], $on)], $D::extras());
            return ['error' => null, 'admin' => true, 'd' => ['extras' => $rows, 'found' => true]];
        }
        return ['error' => 'Nothing to show here yet.', 'admin' => false, 'd' => []];
    }

    // ---------- Calls ----------
    private function xdrParams(string $slug, Request $r): array
    {
        $tz = new \DateTimeZone('America/New_York'); $utc = new \DateTimeZone('UTC');
        $range = array_key_exists((string) $r->query('range', '-7'), self::RANGES) ? (string) $r->query('range', '-7') : '-7';
        $today = new \DateTimeImmutable('today', $tz);
        $from = $today->modify(($range === '0' ? '+0' : $range) . ' days');
        $to = $range === '-1' ? $today : new \DateTimeImmutable('now', $tz);
        $p = ['type' => self::TYPES[$slug], 'i_service' => 3, 'show_unsuccessful' => 1,
            'from_date' => $from->setTimezone($utc)->format('Y-m-d H:i:s'), 'to_date' => $to->setTimezone($utc)->format('Y-m-d H:i:s')];
        $st = (string) $r->query('status', '');
        if ($st !== '' && isset(self::STATUS[$slug][$st])) $p['status'] = $st;
        foreach (['from' => 'CLI', 'to' => 'CLD', 'ext' => 'account_id'] as $q => $f) { $x = trim((string) $r->query($q, '')); if ($x !== '') $p[$f] = mb_substr($x, 0, 40); }
        if ($slug === 'recorded') $p['call_recording'] = 1;
        return [$p, $range];
    }

    private static function row(array $x): array
    {
        $st = (string) ($x['status'] ?? '');
        return ['id' => (int) ($x['i_xdr'] ?? 0), 'time' => self::et($x['bill_time'] ?? $x['connect_time'] ?? null), 'from' => (string) ($x['CLI'] ?? ''),
            'to' => (string) ($x['CLD'] ?? ''), 'called' => (string) ($x['incoming_CLD'] ?? ''), 'ext' => (string) ($x['account_id'] ?? ''),
            'dur' => self::dur($x['charged_quantity'] ?? 0), 'status' => ucfirst(str_replace('_', ' ', preg_replace('/^_vo_/', '', $st)))];
    }

    public function calls(Request $req, string $slug)
    {
        if ($slug === 'conference-recordings') return $this->confRecordings($req);
        if (!isset(self::TYPES[$slug])) abort(404);
        $csv = $req->query('csv') === '1';
        if ($csv) {
            $v = $this->svc(); if (!$v instanceof VoData) return $v;
            [$p] = $this->xdrParams($slug, $req);
            try { $l = $v->call('VO/get_customer_xdrs', $p + ['limit' => 100000, 'offset' => 0])['xdr_list'] ?? []; }
            catch (\Throwable $e) { return response('The list could not be loaded.', 502, self::NC); }
            $out = fopen('php://temp', 'w+'); fputcsv($out, ['Time (ET)', 'From', 'To', 'Number called', 'Extension', 'Duration', 'Status']);
            foreach ($l as $x) { $r = self::row($x); fputcsv($out, [$r['time'], $r['from'], $r['to'], $r['called'], $r['ext'], $r['dur'], $r['status']]); }
            rewind($out);
            return response(stream_get_contents($out), 200, self::NC + ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="' . $slug . '-calls.csv"']);
        }
        return $this->page('users.vo-calls', function (VoData $v) use ($req, $slug) {
            [$p, $range] = $this->xdrParams($slug, $req);
            $per = 50; $pg = max(1, (int) $req->query('page', 1));
            $r = $v->call('VO/get_customer_xdrs', $p + ['limit' => $per, 'offset' => ($pg - 1) * $per]);
            return ['slug' => $slug, 'title' => self::TITLES[$slug], 'rows' => array_map([self::class, 'row'], $r['xdr_list'] ?? []), 'total' => (int) ($r['total'] ?? 0),
                'page' => $pg, 'per' => $per, 'range' => $range, 'ranges' => self::RANGES, 'statuses' => self::STATUS[$slug],
                'q' => ['status' => (string) $req->query('status', ''), 'from' => (string) $req->query('from', ''), 'to' => (string) $req->query('to', ''), 'ext' => (string) $req->query('ext', '')]];
        });
    }

    /** GET /user/calls/recording?id=i_xdr[&dl=1] */
    public function recording(Request $req)
    {
        $v = $this->svc(); if (!$v instanceof VoData) return response('', $v->getStatusCode(), self::NC);
        $id = (int) $req->query('id'); if ($id <= 0) return response('', 404, self::NC);
        $ck = "gvd-vo:rec:{$v->ic}:$id";
        $f = Cache::get($ck);
        if (!is_array($f)) {
            try { $f = $v->blob('CDR/get_call_recording', ['i_xdr' => $id]); } catch (\Throwable $e) { $f = null; }
            finally { $v->close(); }
            if (!$f) return response('', 404, self::NC);
            if (strlen($f['body']) < 30 * 1024 * 1024) Cache::put($ck, $f, 900);
        }
        $body = $f['body']; $len = strlen($body);
        $type = str_starts_with($f['type'], 'audio/') ? $f['type'] : (str_starts_with($body, 'RIFF') ? 'audio/wav' : 'audio/mpeg');
        $ext = $type === 'audio/wav' ? 'wav' : 'mp3';
        $h = ['Content-Type' => $type, 'Accept-Ranges' => 'bytes', 'Cache-Control' => 'private, max-age=900', 'X-Content-Type-Options' => 'nosniff',
            'Content-Disposition' => ($req->query('dl') === '1' ? 'attachment' : 'inline') . '; filename="recording-' . $id . '.' . $ext . '"'];
        $st = 200; $start = 0; $end = $len - 1;
        if (preg_match('/^bytes=(\d*)-(\d*)$/', (string) $req->header('Range'), $m) && ($m[1] !== '' || $m[2] !== '')) {
            if ($m[1] === '') { $start = max(0, $len - (int) $m[2]); }
            else { $start = (int) $m[1]; if ($m[2] !== '') $end = min($end, (int) $m[2]); }
            if ($start > $end || $start >= $len) return response('', 416, ['Content-Range' => "bytes */$len"] + $h);
            $st = 206; $h['Content-Range'] = "bytes $start-$end/$len";
        }
        $out = $st === 206 ? substr($body, $start, $end - $start + 1) : $body;
        $h['Content-Length'] = (string) strlen($out);
        while (ob_get_level() > 0) ob_end_clean();
        return response($out, $st, $h);
    }

    /** POST /user/calls/recording/delete {ids[]} — admin */
    public function recordingDelete(Request $req)
    {
        $v = $this->admin(); if (!$v instanceof VoData) return $v;
        $ids = array_values(array_filter(array_map('intval', (array) $req->input('ids', [])))); $ids = array_slice($ids, 0, 200);
        if (!$ids) return response()->json(['error' => 'Nothing selected.'], 422, self::NC);
        try { $v->call('CDR/batch_delete_call_recording', ['xdr_list' => array_map(fn ($i) => ['id' => $i], $ids)]); foreach ($ids as $i) Cache::forget("gvd-vo:rec:{$v->ic}:$i"); return $this->ok($v, 'delete recordings x' . count($ids)); }
        catch (\Throwable $e) { return $this->fail($v, $e, 'delete recordings'); }
    }

    private function confRecordings(Request $req)
    {
        return $this->page('users.vo-calls', function (VoData $v) use ($req) {
            $per = 50; $pg = max(1, (int) $req->query('page', 1));
            $r = $v->call('LS_Conference/get_room_recordings', ['secure' => 1, 'limit' => $per, 'offset' => ($pg - 1) * $per]);
            $rows = [];
            foreach ($r['recordings_list'] ?? [] as $i => $x) $rows[] = ['id' => ($pg - 1) * $per + $i, 'room' => (string) ($x['room_name'] ?? ''), 'time' => self::et($x['timestamp'] ?? null),
                'size' => round(((int) ($x['size'] ?? 0)) / 1048576, 1) . ' MB', 'file' => (string) ($x['filename'] ?? '')];
            return ['slug' => 'conference-recordings', 'title' => self::TITLES['conference-recordings'], 'rows' => $rows, 'total' => (int) ($r['total'] ?? count($rows)), 'page' => $pg, 'per' => $per];
        });
    }

    /** GET /user/calls/conference-recording?n=index : streamed through our server from the recording's own link. */
    public function confRecording(Request $req)
    {
        $v = $this->svc(); if (!$v instanceof VoData) return response('', $v->getStatusCode(), self::NC);
        $n = max(0, (int) $req->query('n'));
        try {
            $x = $v->call('LS_Conference/get_room_recordings', ['secure' => 1, 'limit' => 1, 'offset' => $n])['recordings_list'][0] ?? null;
            $url = (string) ($x['url_to_download'] ?? '');
            $billingHost = parse_url($v->k['base'], PHP_URL_HOST);
            $h = parse_url($url, PHP_URL_HOST);
            if (!$x || !$url || !($h === $billingHost || filter_var($h, FILTER_VALIDATE_IP))) return response('', 404, self::NC);
            $r = \Illuminate\Support\Facades\Http::withOptions(['verify' => false])->timeout(120)->get($url);
            if (!$r->ok()) return response('', 404, self::NC);
            return response($r->body(), 200, self::NC + ['Content-Type' => 'audio/mpeg', 'Content-Disposition' => 'attachment; filename="' . preg_replace('/[^\w.-]/', '_', (string) ($x['filename'] ?? 'conference.mp3')) . '"']);
        } catch (\Throwable $e) { return response('', 502, self::NC); }
    }

    // ---------- Extensions ----------
    private function extRows(VoData $v): array
    {
        return Cache::remember("gvd-vo:ext:{$v->ic}", 300, function () use ($v) {
            $exts = $v->call('Customer/get_extensions_list', ['i_customer' => $v->ic])['extensions_list'] ?? [];
            $acc = []; foreach ($v->accounts(['forward_mode', 'call_recording'], true) as $a) $acc[(int) $a['i_account']] = $a;
            $rows = [];
            foreach ($exts as $e) {
                $ia = (int) ($e['i_account'] ?? 0); $a = $acc[$ia] ?? [];
                $fw = VoData::sf($a, 'forward_mode'); $rec = VoData::sf($a, 'call_recording');
                $rows[] = ['i_account' => $ia, 'ext' => (string) $e['id'], 'name' => (string) ($e['name'] ?? ''), 'type' => (string) ($e['type'] ?? ''),
                    'account' => (string) ($e['account_id'] ?? ''), 'group' => preg_match('/^020' . $v->ic . '\d+$/', (string) ($e['account_id'] ?? '')) ? 'employee' : 'other',
                    'sip' => (string) ($a['sip_status'] ?? ''), 'forward' => $fw['effective'] ?? '', 'recording' => $rec['effective'] ?? '', 'rec' => $rec['attr'] ?? []];
            }
            $hg = [];
            foreach ($v->call('Customer/get_huntgroup_list', ['i_customer' => $v->ic])['huntgroup_list'] ?? [] as $g)
                $hg[] = ['id' => (string) ($g['hg_number'] ?? ''), 'name' => (string) ($g['name'] ?? ''), 'sequence' => (string) ($g['hunt_sequence'] ?? ''), 'members' => count($g['assigned_extensions'] ?? [])];
            return ['rows' => $rows, 'hunt' => $hg];
        });
    }

    public function extensions()
    {
        return $this->page('users.vo-extensions', fn (VoData $v) => $this->extRows($v));
    }

    private function extAllowed(VoData $v, int $ia): array
    {
        foreach ($this->extRows($v)['rows'] as $r) if ($r['i_account'] === $ia && $r['type'] === 'Account') return $r;
        throw new \DomainException('not an extension');
    }

    /** GET /user/extensions/detail?id=[&part=vm] — phone/forwarding part (parallel, cached 5 min); voicemail part separately. */
    public function extDetail(Request $req)
    {
        $v = $this->svc(); if (!$v instanceof VoData) return $v;
        $ia = (int) $req->query('id'); $part = $req->query('part') === 'vm' ? 'vm' : 'main';
        try {
            $row = $this->extAllowed($v, $ia);
            $ck = "gvd-vo:extd:{$v->ic}:$ia:$part";
            if ($req->boolean('fresh')) Cache::forget($ck);
            $out = Cache::remember($ck, 300, function () use ($v, $ia, $row, $part) {
                if ($part === 'vm') {
                    $vm = $v->umAs($ia, 'Voicemail/get_vm_settings', [])['vm_settings'] ?? null;
                    if (is_array($vm)) $vm = array_intersect_key($vm, array_flip(['ext_email', 'ext_email_action', 'fax_file', 'greetings', 'auto_play', 'ext_email_vm_fmt']));
                    return ['vm' => $vm];
                }
                $j = $v->many(['sip' => ['Account/get_sip_status', ['i_account' => $ia]], 'fm' => ['Account/get_account_followme', ['i_account' => $ia]],
                    'info' => ['Account/get_account_info', ['i_account' => $ia]]]);
                if ($j['fm'] === null && $j['info'] === null) throw new \RuntimeException('billing unavailable');
                $info = $j['info']['account_info'] ?? []; $sip = $j['sip']['sip_info'] ?? $j['sip'] ?? []; $fm = $j['fm'] ?? [];
                $nums = [];
                foreach ($fm['followme_numbers'] ?? [] as $n) $nums[] = ['id' => (int) ($n['i_follow_me_number'] ?? 0), 'number' => (string) ($n['redirect_number'] ?? ''), 'name' => (string) ($n['name'] ?? ''),
                    'timeout' => (int) ($n['timeout'] ?? 0), 'active' => ($n['active'] ?? 'Y') !== 'N'];
                return ['ext' => $row['ext'], 'name' => $row['name'], 'login' => (string) ($info['id'] ?? $row['account']), 'domain' => (string) ($info['sip_domain'] ?? $info['domain'] ?? ''),
                    'sipStatus' => is_array($sip) ? array_intersect_key($sip, array_flip(['sip_status', 'user_agent', 'contact', 'expires', 'registered'])) : null,
                    'forward' => $row['forward'], 'sequence' => (string) ($fm['followme_info']['sequence'] ?? ''), 'numbers' => $nums,
                    'recording' => ['on' => $row['recording'] === 'Y', 'attr' => $row['rec']]];
            });
            return response()->json($out, 200, self::NC);
        } catch (\DomainException $e) { return response()->json(['error' => 'Not found'], 404, self::NC); }
        catch (\Throwable $e) { return response()->json(['error' => 'Could not load this extension right now.'], 502, self::NC); }
        finally { $v->close(); }
    }

    /** POST /user/extensions/secret {id} — admin only, SIP password, never logged. */
    public function extSecret(Request $req)
    {
        $v = $this->admin(); if (!$v instanceof VoData) return $v;
        try { $ia = (int) $req->input('id'); $this->extAllowed($v, $ia); return response()->json(['value' => $v->secretOf($ia, 'h323_password')], 200, self::NC); }
        catch (\Throwable $e) { return response()->json(['error' => 'Not available'], 404, self::NC); }
    }

    /** POST /user/extensions/save {id, section, ...} — admin only */
    public function extSave(Request $req)
    {
        $v = $this->admin(); if (!$v instanceof VoData) return $v;
        $ia = (int) $req->input('id'); $sec = (string) $req->input('section');
        try {
            $row = $this->extAllowed($v, $ia);
            $acct = $v->own($ia);
            if ($sec === 'voicemail') {
                $vs = [];
                $act = (string) $req->input('ext_email_action', '');
                if (in_array($act, ['none', 'forward', 'notify', 'copy', 'fwd_as_attachment'], true)) $vs['ext_email_action'] = $act;
                $fax = (string) $req->input('fax_file', '');
                if (in_array($fax, ['multi_png', 'multi_tiff', 'pdf', 'tiff'], true)) $vs['fax_file'] = $fax;
                if ($req->has('ext_email')) { $em = trim((string) $req->input('ext_email')); if ($em !== '' && !filter_var($em, FILTER_VALIDATE_EMAIL)) return response()->json(['error' => 'Enter a valid email address.'], 422, self::NC); $vs['ext_email'] = $em; }
                if ($vs) $v->umAs($ia, 'Voicemail/set_vm_settings', ['vm_settings' => $vs]);
            } elseif ($sec === 'forwarding') {
                if (PhoneNumbers::forwardingGuarded($acct)) throw new \DomainException('guarded');
                $mode = (string) $req->input('mode', '');
                if ($mode !== '' && in_array($mode, ['N', 'F', 'C', 'Y', 'U'], true) && $mode !== $row['forward'])
                    $v->call('Account/update_account', ['account_info' => ['i_account' => $ia, 'service_features' => [['name' => 'forward_mode', 'flag_value' => $mode]]]]);
                $seq = (string) $req->input('sequence', '');
                $fm = $v->call('Account/get_account_followme', ['i_account' => $ia]);
                $ifm = (int) ($fm['followme_info']['i_follow_me'] ?? 0);
                if ($seq !== '' && in_array($seq, ['Order', 'Random', 'Simultaneous'], true) && $seq !== ($fm['followme_info']['sequence'] ?? ''))
                    $v->call('Account/update_account_followme', ['followme_info' => ['i_account' => $ia, 'sequence' => $seq]]);
                if ($req->has('numbers')) {
                    $want = json_decode((string) $req->input('numbers'), true);
                    if (!is_array($want) || count($want) > 10) return response()->json(['error' => 'Up to 10 numbers.'], 422, self::NC);
                    $have = []; foreach ($fm['followme_numbers'] ?? [] as $n) $have[(int) $n['i_follow_me_number']] = $n;
                    $keep = [];
                    foreach ($want as $w) {
                        $num = preg_replace('/[^\d+*#]/', '', (string) ($w['number'] ?? ''));
                        if ($num === '') continue;
                        $info = ['redirect_number' => $num, 'timeout' => max(5, min(120, (int) ($w['timeout'] ?? 20))), 'active' => !empty($w['active']) ? 'Y' : 'N', 'name' => mb_substr((string) ($w['name'] ?? ''), 0, 40)];
                        $id = (int) ($w['id'] ?? 0);
                        if ($id && isset($have[$id])) { $keep[$id] = 1; $v->call('Account/update_followme_number', ['number_info' => $info + ['i_follow_me_number' => $id]]); }
                        else $v->call('Account/add_followme_number', ['number_info' => $info + ['i_follow_me' => $ifm, 'i_account' => $ia, 'keep_original_cld' => 'Y']]);
                    }
                    foreach ($have as $id => $_) if (!isset($keep[$id])) $v->call('Account/delete_followme_number', ['i_follow_me_number' => $id]);
                }
            } elseif ($sec === 'recording') {
                $on = $req->boolean('on');
                $b = fn ($k) => $req->boolean($k);
                $sf = ['name' => 'call_recording', 'flag_value' => $on ? 'Y' : 'N', 'attributes' => [
                    ['name' => 'call_recording_out', 'values' => [$b('out') ? '1' : '0']], ['name' => 'call_recording_in', 'values' => [$b('in') ? '2' : '0']],
                    ['name' => 'call_recording_redirect', 'values' => [$b('redirect') ? '4' : '0']], ['name' => 'on_demand', 'values' => [$b('on_demand') ? 'Y' : 'N']],
                    ['name' => 'mail_recording', 'values' => [$b('mail') ? 'Y' : 'N']], ['name' => 'audible_notification', 'values' => [$b('audible') ? 'Y' : 'N']]]];
                $v->call('Account/update_account', ['account_info' => ['i_account' => $ia, 'service_features' => [$sf]]]);
            } else return response()->json(['error' => 'Unknown section'], 422, self::NC);
            Cache::forget("gvd-vo:ext:{$v->ic}"); Cache::forget("gvd-vo:extd:{$v->ic}:$ia:main"); Cache::forget("gvd-vo:extd:{$v->ic}:$ia:vm");
            return $this->ok($v, "extension {$row['ext']} $sec");
        } catch (\Throwable $e) { return $this->fail($v, $e, "extension $sec"); }
        finally { $v->close(); }
    }

    // ---------- Conference rooms ----------
    private function confAccount(VoData $v): int
    {
        foreach ($this->extRows($v)['rows'] as $e) if ($e['account'] === "{$v->ic}-vo-conf") return $e['i_account'];
        throw new \RuntimeException('no conference account');
    }
    const CONF_FIELDS = ['name', 'max_participants', 'max_duration', 'wait_for_moderator', 'close_after_participants_exit', 'announce_name', 'announce_join_leave', 'announce_number_participants', 'record'];

    public function conferences()
    {
        return $this->page('users.vo-conferences', function (VoData $v) {
            $ia = $this->confAccount($v);
            $rooms = [];
            foreach ($v->umAs($ia, 'Conference/get_conf_list', [])['conf_list'] ?? [] as $c) {
                $r = ['i_conf' => (int) ($c['i_conf'] ?? 0), 'hasPin' => !empty($c['pin']), 'hasModPin' => !empty($c['moderator_pin'])];
                foreach (self::CONF_FIELDS as $f) $r[$f] = $c[$f] ?? null;
                $rooms[] = $r;
            }
            $ext = null; foreach ($this->extRows($v)['rows'] as $e) if ($e['i_account'] === $ia) $ext = $e['ext'];
            return ['rooms' => $rooms, 'ext' => $ext];
        });
    }

    /** POST /user/conference-rooms/pins {i_conf} — admin only, never logged */
    public function confPins(Request $req)
    {
        $v = $this->admin(); if (!$v instanceof VoData) return $v;
        try {
            $ia = $this->confAccount($v); $id = (int) $req->input('i_conf');
            foreach ($v->umAs($ia, 'Conference/get_conf_list', [])['conf_list'] ?? [] as $c)
                if ((int) ($c['i_conf'] ?? 0) === $id) return response()->json(['pin' => (string) ($c['pin'] ?? ''), 'moderator_pin' => (string) ($c['moderator_pin'] ?? '')], 200, self::NC);
            return response()->json(['error' => 'Not found'], 404, self::NC);
        } catch (\Throwable $e) { return response()->json(['error' => 'Not available'], 502, self::NC); }
        finally { $v->close(); }
    }

    /** POST /user/conference-rooms/save {i_conf?, fields…} or {i_conf, delete:1} — admin only */
    public function confSave(Request $req)
    {
        $v = $this->admin(); if (!$v instanceof VoData) return $v;
        try {
            $ia = $this->confAccount($v); $id = (int) $req->input('i_conf');
            if ($req->boolean('delete')) { if (!$id) return response()->json(['error' => 'Unknown room'], 422, self::NC); $v->umAs($ia, 'Conference/del_conf', ['i_conf' => $id]); return $this->ok($v, "conference delete $id"); }
            $info = [];
            $name = trim((string) $req->input('name', '')); if ($name === '' || mb_strlen($name) > 64) return response()->json(['error' => 'Enter a room name.'], 422, self::NC);
            $info['name'] = $name;
            foreach (['max_participants', 'max_duration'] as $f) if ($req->filled($f)) $info[$f] = max(0, (int) $req->input($f));
            foreach (['wait_for_moderator', 'close_after_participants_exit', 'announce_name', 'announce_join_leave', 'announce_number_participants', 'record'] as $f) if ($req->has($f)) $info[$f] = $req->boolean($f) ? 'Y' : 'N';
            foreach (['pin', 'moderator_pin'] as $f) if ($req->filled($f)) { $p = (string) $req->input($f); if (!preg_match('/^\d{3,12}$/', $p)) return response()->json(['error' => 'PINs must be 3–12 digits.'], 422, self::NC); $info[$f] = $p; }
            if ($id) $v->umAs($ia, 'Conference/update_conf', ['conf_info' => $info + ['i_conf' => $id]]);
            else $v->umAs($ia, 'Conference/create_conf', ['conf_info' => $info]);
            return $this->ok($v, $id ? "conference update $id" : 'conference create');
        } catch (\Throwable $e) { return $this->fail($v, $e, 'conference save'); }
        finally { $v->close(); }
    }

    // ---------- Call block ----------
    /** Lines a block can apply to: the VO base record and every active VO number (guarded lines flagged). */
    private function blockTargets(VoData $v): array
    {
        $t = [];
        foreach ($v->accounts([], true) as $a) {
            $id = (string) $a['id']; $batch = (string) ($a['batch_name'] ?? '');
            if ($id !== "{$v->ic}-vo-did" && !str_starts_with($batch, "{$v->ic}-vo-did")) continue;
            if ($id !== "{$v->ic}-vo-did" && !preg_match('/^1?\d{10}$/', $id) && !preg_match('/^\d{7}$/', $id)) continue;
            $t[(int) $a['i_account']] = ['i_account' => (int) $a['i_account'], 'id' => $id, 'label' => $id === "{$v->ic}-vo-did" ? 'Company (all VO numbers)' : PhoneNumbers::fmt($id),
                'guarded' => PhoneNumbers::forwardingGuarded($a)];
        }
        return $t;
    }

    private function blockState(VoData $v, array $targets): array
    {
        $jobs = [];
        foreach ($targets as $ia => $_) { $jobs["c$ia"] = ['Account/get_cp_condition_list', ['i_account' => $ia]]; $jobs["r$ia"] = ['Account/get_cp_rule_list', ['i_account' => $ia]]; }
        $res = $v->many($jobs); $st = [];
        foreach ($targets as $ia => $_) $st[$ia] = ['conds' => $res["c$ia"]['cp_condition_list'] ?? [], 'rules' => $res["r$ia"]['cp_rule_list'] ?? [], 'ok' => $res["c$ia"] !== null];
        return $st;
    }

    private static function kind(string $condName): ?string { return preg_match('/^Block/i', $condName) ? 'Block' : (preg_match('/^Allow/i', $condName) ? 'Allow' : null); }

    public function callBlock()
    {
        return $this->page('users.vo-callblock', function (VoData $v) {
            $targets = $this->blockTargets($v); $st = $this->blockState($v, $targets);
            $lists = ['blocked' => [], 'areas' => [], 'allowed' => []];
            foreach ($st as $ia => $s) foreach ($s['conds'] as $c) {
                if (($c['type'] ?? '') !== 'FromNumber' || !($k = self::kind((string) ($c['name'] ?? '')))) continue;
                foreach ($c['numbers'] ?? [] as $n) {
                    $num = (string) ($n['number'] ?? ''); if ($num === '') continue;
                    $sec = $k === 'Allow' ? 'allowed' : (str_ends_with($num, '%') ? 'areas' : 'blocked');
                    $lists[$sec][$num] = $lists[$sec][$num] ?? ['number' => $num, 'label' => str_ends_with($num, '%') ? 'Area code ' . rtrim($num, '%') : PhoneNumbers::fmt($num), 'on' => []];
                    $lists[$sec][$num]['on'][] = $ia;
                }
            }
            foreach ($lists as &$l) $l = array_values($l); unset($l);
            return ['lists' => $lists, 'targets' => array_values($targets)];
        });
    }

    /** POST /user/call-block/save {section: blocked|areas|allowed, number, action: add|remove, targets[]} — admin only */
    public function callBlockSave(Request $req)
    {
        $v = $this->admin(); if (!$v instanceof VoData) return $v;
        $sec = (string) $req->input('section'); $act = (string) $req->input('action');
        if (!in_array($sec, ['blocked', 'areas', 'allowed'], true) || !in_array($act, ['add', 'remove'], true)) return response()->json(['error' => 'Unknown request'], 422, self::NC);
        $raw = preg_replace('/[^\d]/', '', (string) $req->input('number'));
        if ($sec === 'areas') { if (!preg_match('/^1?\d{3}$/', $raw)) return response()->json(['error' => 'Enter a 3-digit area code.'], 422, self::NC); $num = (strlen($raw) === 3 ? '1' . $raw : $raw) . '%'; }
        else { if (strlen($raw) === 10) $raw = '1' . $raw; if (!preg_match('/^\d{8,15}$/', $raw)) return response()->json(['error' => 'Enter the full number, e.g. 1 973 865 9541.'], 422, self::NC); $num = $raw; }
        $kind = $sec === 'allowed' ? 'Allow' : 'Block';
        try {
            $all = $this->blockTargets($v);
            $picked = array_values(array_unique(array_map('intval', (array) $req->input('targets', []))));
            if (!$picked) $picked = array_keys(array_filter($all, fn ($t) => !$t['guarded'])); // default: every VO number except the guarded lines
            $picked = array_values(array_filter($picked, fn ($ia) => isset($all[$ia])));
            if (!$picked) return response()->json(['error' => 'Choose at least one number.'], 422, self::NC);
            $st = $this->blockState($v, array_intersect_key($all, array_flip($picked)));
            $done = 0;
            foreach ($picked as $ia) {
                $conds = array_values(array_filter($st[$ia]['conds'], fn ($c) => ($c['type'] ?? '') === 'FromNumber' && self::kind((string) ($c['name'] ?? '')) === $kind));
                if ($act === 'remove') {
                    foreach ($conds as $c) {
                        $nums = array_values(array_filter($c['numbers'] ?? [], fn ($n) => ($n['number'] ?? '') !== $num));
                        if (count($nums) !== count($c['numbers'] ?? [])) { $v->call('Account/update_cp_condition', ['cp_condition_info' => ['i_cp_condition' => (int) $c['i_cp_condition'], 'numbers' => $nums]]); $done++; }
                    }
                    continue;
                }
                $has = null; foreach ($conds as $c) foreach ($c['numbers'] ?? [] as $n) if (($n['number'] ?? '') === $num) $has = $c;
                $target = $has;
                if (!$target) {
                    foreach ($conds as $c) { $len = 4 + array_sum(array_map(fn ($n) => strlen((string) $n['number']) + 4, $c['numbers'] ?? [])); if ($len + strlen($num) + 4 < 250) { $target = $c; break; } }
                    $entry = ['number' => $num, 'negated' => 'N', 'onnet' => 'N'];
                    if ($target) { $v->call('Account/update_cp_condition', ['cp_condition_info' => ['i_cp_condition' => (int) $target['i_cp_condition'], 'numbers' => array_merge($target['numbers'] ?? [], [$entry])]]); }
                    else {
                        $name = $kind . (count($conds) ? ' ' . (count($conds) + 1) : '');
                        $r = $v->call('Account/add_cp_condition', ['cp_condition_info' => ['i_account' => $ia, 'name' => $name, 'type' => 'FromNumber', 'numbers' => [$entry]]]);
                        $target = ['i_cp_condition' => (int) ($r['i_cp_condition'] ?? 0)];
                        if (!$target['i_cp_condition']) throw new \RuntimeException('condition not created');
                    }
                    $done++;
                }
                $ruled = false; foreach ($st[$ia]['rules'] as $r) if ((int) ($r['from_number_i_cp_condition'] ?? 0) === (int) $target['i_cp_condition']) $ruled = true;
                if (!$ruled) $v->call('Account/add_cp_rule', ['cp_rule_info' => ['i_account' => $ia, 'from_number_i_cp_condition' => (int) $target['i_cp_condition'], 'action' => $kind === 'Allow' ? 4 : 0,
                    'is_active' => 'Y', 'order_number' => $kind === 'Allow' ? 1 : count($st[$ia]['rules']) + 1, 'to_number_i_cp_condition' => null, 'time_window_i_cp_condition' => null]]);
            }
            return $this->ok($v, "call block $act $sec on " . count($picked) . " lines ($done changed)");
        } catch (\Throwable $e) { return $this->fail($v, $e, "call block $act"); }
    }

    // ---------- Users ----------
    private function userRows(VoData $v): array
    {
        $out = [];
        foreach ($v->accounts() as $a) {
            $id = (string) $a['id']; if (!str_starts_with($id, 'vo-user-')) continue;
            $bs = (string) ($a['bill_status'] ?? '');
            $out[] = ['i_account' => (int) $a['i_account'], 'login' => preg_replace('/^vo-user-/', '', preg_replace('/-old\d+$/', '', $id)), 'id' => $id,
                'first' => (string) ($a['firstname'] ?? ''), 'last' => (string) ($a['lastname'] ?? ''), 'email' => (string) ($a['email'] ?? ''),
                'status' => ($a['blocked'] ?? 'N') === 'Y' ? 'Blocked' : ($bs === 'C' ? 'Closed' : 'Active'), 'product' => (string) ($a['product_name'] ?? '')];
        }
        return $out;
    }

    public function users() { return $this->page('users.vo-users', fn (VoData $v) => ['users' => $this->userRows($v)]); }

    /** POST /user/users/secret {id} — admin only: the user's sign-in password, never logged. */
    public function userSecret(Request $req)
    {
        $v = $this->admin(); if (!$v instanceof VoData) return $v;
        $ia = (int) $req->input('id');
        try { foreach ($this->userRows($v) as $u) if ($u['i_account'] === $ia) return response()->json(['value' => $v->secretOf($ia, 'password')], 200, self::NC); return response()->json(['error' => 'Not found'], 404, self::NC); }
        catch (\Throwable $e) { return response()->json(['error' => 'Not available'], 404, self::NC); }
    }

    /** POST /user/users/save {id?, first, last, email, password? (new users)} — admin only */
    public function userSave(Request $req)
    {
        $v = $this->admin(); if (!$v instanceof VoData) return $v;
        $first = trim(mb_substr((string) $req->input('first'), 0, 40)); $last = trim(mb_substr((string) $req->input('last'), 0, 40)); $email = strtolower(trim((string) $req->input('email')));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return response()->json(['error' => 'Enter a valid email address.'], 422, self::NC);
        $ia = (int) $req->input('id');
        try {
            $users = $this->userRows($v);
            if ($ia) {
                $u = null; foreach ($users as $x) if ($x['i_account'] === $ia) $u = $x;
                if (!$u) return response()->json(['error' => 'Not found'], 404, self::NC);
                $v->call('Account/update_account', ['account_info' => ['i_account' => $ia, 'firstname' => $first, 'lastname' => $last, 'email' => $email]]);
                return $this->ok($v, "user update $ia");
            }
            $pw = (string) $req->input('password');
            if (strlen($pw) < 8) return response()->json(['error' => 'Choose a password of at least 8 characters.'], 422, self::NC);
            foreach ($users as $x) if ($x['status'] === 'Active' && strcasecmp($x['login'], $email) === 0) return response()->json(['error' => 'That user already exists.'], 422, self::NC);
            // New users copy the plan settings of an existing active user of this customer.
            $tpl = null; foreach ($users as $x) if ($x['status'] === 'Active') { $tpl = $v->own($x['i_account']); break; }
            if (!$tpl) return response()->json(['error' => 'Users can\'t be added here yet for this account.'], 422, self::NC);
            $info = ['i_customer' => $v->ic, 'id' => 'vo-user-' . $email, 'email' => $email, 'firstname' => $first, 'lastname' => $last, 'password' => $pw, 'opening_balance' => 0];
            foreach (['i_product', 'batch_name', 'billing_model', 'iso_4217', 'i_role', 'i_account_role', 'i_time_zone', 'i_lang'] as $f) if (isset($tpl[$f])) $info[$f] = $tpl[$f];
            $v->call('Account/add_account', ['account_info' => $info]);
            return $this->ok($v, 'user add');
        } catch (\Throwable $e) { return $this->fail($v, $e, 'user save'); }
    }

    // ---------- Extras (same list, state and locks as Virtual Office "Manage extras") ----------
    const EXTRA_ORDER = ['V2T', 'CREC', 'BLST', 'EML2FAX', 'ANI', 'RNG', 'WEBRTC', 'INS', 'CNF'];
    const EXTRA_NAMES = ['V2T' => 'Voicemail Transcription', 'CREC' => 'Call Recording', 'BLST' => 'Simultaneous Ringing', 'EML2FAX' => 'Email To Fax', 'ANI' => 'Dial Out',
        'RNG' => 'Ring In/Out', 'WEBRTC' => 'WebRTC', 'INS' => 'Individual Number Settings', 'CNF' => 'Conference'];
    const EXTRA_DESC = [
        'V2T' => 'Voicemail Transcription converts your voicemails into text messages. Once enabled for a customer, you can enable it for an extension in General Info section.',
        'CREC' => 'Call Recording allows you to record calls. Once this Extra is added, you can enable Call Recording per extension on the Extensions screen.',
        'BLST' => 'If you have more than one call forwarding number on an extension, simultaneous ringing will ring them simultaneously to reduce wait times for your callers.',
        'EML2FAX' => 'Allows you to define a receiving number and upload PDF to send it as a fax.',
        'ANI' => 'Allows you to create accounts for outgoing calls via IVR application and select caller ID seen to the callee from the list of DID numbers.',
        'RNG' => 'Allows to register extensions on SIP devices or soft-phones to receive incoming calls over the Internet, outgoing calls can be available with forced caller ID from the list of DID numbers.',
        'WEBRTC' => "Allows to register extensions on the WebRTC dialer. Works if 'Ring In/Out' is enabled.",
        'INS' => 'Allows to configure different greetings, periods and actions per DID number.',
        'CNF' => 'Enables the use of conferencing services.'];
    const VO_NUM_KEYS = ['AG', 'MG', 'PIN', 'WZ', 'EDB', 'EDE', 'EEB', 'EEE', 'BOE', 'EOE'];

    /** Parse the customer's "virtoffice" custom field exactly like VO does ("Y:AG=1:...:CREC:RNG"). */
    private static function voParse(string $v): array
    {
        $o = ['enable' => false, 'nums' => [], 'extras' => []];
        foreach (explode(':', $v) as $t) {
            if ($t === '') continue;
            [$k, $x] = array_pad(explode('=', $t, 2), 2, null);
            if ($k === 'Y' || $k === 'N') $o['enable'] = $k === 'Y';
            elseif (in_array($k, self::VO_NUM_KEYS, true)) $o['nums'][$k] = (int) $x;
            else $o['extras'][$k] = true;
        }
        return $o;
    }
    private static function voSerialize(array $o): string
    {
        $n = []; foreach ($o['nums'] as $k => $x) $n[] = $k . '=' . $x;
        return ($o['enable'] ? 'Y' : 'N') . ':' . implode(':', $n) . ':' . implode(':', array_keys(array_filter($o['extras'])));
    }
    /** VO's canChange(): '' when the switch is shown, otherwise the reason it is locked. */
    private static function extraLock(string $code, array $on): string
    {
        if ($code === 'WEBRTC' && empty($on['WEBRTC']) && empty($on['RNG'])) return 'Cannot enable WebRTC when Ring In/Out is disabled';
        if ($code === 'RNG' && !empty($on['RNG']) && !empty($on['WEBRTC'])) return 'Cannot disable Ring In/Out when WebRTC is enabled';
        return '';
    }
    private static function eo(array $x): int { $i = array_search($x['name'] ?? '', self::EXTRA_ORDER, true); return $i === false ? 99 : $i; }
    private function extrasState(VoData $v): array
    {
        $av = json_decode($v->call('VO/available_extras', [])['json_string'] ?? '[]', true) ?: [];
        $plan = json_decode($v->call('VO/get_my_plan', [])['json_string'] ?? '{}', true) ?: [];
        $q = $plan['data']['virtoffice']['qualified_extras'] ?? [];
        $cfs = $v->call('Customer/get_custom_fields_values', ['i_customer' => $v->ic])['custom_fields_values'] ?? [];
        $cf = null; foreach ($cfs as $c) if (($c['name'] ?? '') === 'virtoffice') { $cf = $c; break; }
        $vo = self::voParse((string) ($cf['text_value'] ?? $cf['db_value'] ?? ''));
        usort($av, fn ($a, $b) => self::eo($a) <=> self::eo($b));
        return [$av, $q, $cf, $vo];
    }

    public function extras()
    {
        return $this->page('users.vo-extras', function (VoData $v) {
            [$av, $q, $cf, $vo] = $this->extrasState($v);
            $rows = [];
            foreach ($av as $x) {
                $c = (string) $x['name']; $on = !empty($vo['extras'][$c]);
                $qual = !$q || in_array($c, $q, true);
                $rows[] = ['code' => $c, 'name' => self::EXTRA_NAMES[$c] ?? $c, 'desc' => self::EXTRA_DESC[$c] ?? '', 'fee' => (float) ($x['monthly_fee'] ?? 0),
                    'on' => $on, 'qualified' => $qual, 'changeable' => $qual && !empty($x['i_subscription']), 'lock' => self::extraLock($c, $vo['extras'])];
            }
            return ['extras' => $rows, 'found' => $cf !== null];
        });
    }

    /** Turn one extra on/off the way VO does: rewrite the customer's "virtoffice" custom field. dry=1 returns the payload without saving. */
    public function extrasToggle(Request $r)
    {
        $v = $this->admin(); if (!$v instanceof VoData) return $v;
        try {
            $code = (string) $r->input('code'); $want = $r->boolean('on');
            [$av, $q, $cf, $vo] = $this->extrasState($v);
            $x = null; foreach ($av as $a) if ((string) $a['name'] === $code) { $x = $a; break; }
            if (!$x || !$cf) throw new \DomainException('unknown extra');
            if (($q && !in_array($code, $q, true)) || empty($x['i_subscription'])) throw new \DomainException('not changeable');
            if (self::extraLock($code, $vo['extras']) !== '') throw new \DomainException('locked');
            if (!empty($vo['extras'][$code]) === $want) return response()->json(['ok' => true, 'unchanged' => true], 200, self::NC);
            $vo['extras'][$code] = $want;
            $new = self::voSerialize($vo);
            $payload = ['i_customer' => $v->ic, 'custom_fields_values' => [array_filter(['i_custom_field' => $cf['i_custom_field'] ?? null, 'name' => 'virtoffice', 'text_value' => $new, 'db_value' => $new], fn ($z) => $z !== null)]];
            if ($r->boolean('dry')) return response()->json(['ok' => true, 'dry' => true, 'method' => 'Customer/update_custom_fields_values', 'payload' => $payload], 200, self::NC);
            $v->call('Customer/update_custom_fields_values', $payload);
            return $this->ok($v, 'extra ' . $code . ($want ? ' on' : ' off'));
        } catch (\Throwable $e) { return $this->fail($v, $e, 'extra toggle'); }
        finally { $v->close(); }
    }

    // ---------- Dial out numbers (VO "Dial out numbers": pinless accounts "a<number>") ----------
    /** DIDs the caller ID can be shown as (VO: accounts in batch "<ic>-vo-did*", value = account id). */
    private function dialDids(array $accts, int $ic): array
    {
        $o = [];
        foreach ($accts as $a) { $id = (string) ($a['id'] ?? ''); if ($id !== "$ic-vo-did" && str_starts_with((string) ($a['batch_name'] ?? ''), "$ic-vo-did")) $o[] = ['value' => $id, 'label' => PhoneNumbers::fmt($id)]; }
        return $o;
    }
    private function dialRows(array $accts): array
    {
        $rows = [];
        foreach ($accts as $a) {
            $id = (string) ($a['id'] ?? ''); if (!preg_match('/^a/i', $id)) continue;
            $cli = VoData::sf($a, 'cli');
            $show = (string) ($cli['attr']['centrex'] ?? ($cli['attr']['display_number'] ?? ''));
            $rows[] = ['i_account' => (int) $a['i_account'], 'number' => substr($id, 1), 'nickname' => (string) ($a['cont1'] ?? ''), 'show' => $show,
                'showLabel' => $show !== '' ? PhoneNumbers::fmt($show) : '', 'active' => ($a['blocked'] ?? 'N') !== 'Y', 'guarded' => PhoneNumbers::forwardingGuarded($a)];
        }
        usort($rows, fn ($x, $y) => strcmp($x['number'], $y['number']));
        return $rows;
    }
    public function dialOutNumbers()
    {
        return $this->page('users.vo-dialout', function (VoData $v) {
            $acc = $v->accounts(['cli'], true);
            return ['rows' => $this->dialRows($acc), 'dids' => $this->dialDids($acc, $v->ic)];
        });
    }

    /** POST /user/dial-out-numbers/save {action: add|update|delete|toggle, i_account, number, nickname, show, active} — admin only */
    public function dialOutSave(Request $req)
    {
        $v = $this->admin(); if (!$v instanceof VoData) return $v;
        $act = (string) $req->input('action');
        if (!in_array($act, ['add', 'update', 'delete', 'toggle'], true)) return response()->json(['error' => 'Unknown request'], 422, self::NC);
        try {
            $acc = $v->accounts(['cli'], true);
            $dids = array_column($this->dialDids($acc, $v->ic), 'value');
            $mine = null; $ia = (int) $req->input('i_account');
            if ($act !== 'add') {
                foreach ($acc as $a) if ((int) $a['i_account'] === $ia && preg_match('/^a/i', (string) $a['id'])) $mine = $a;
                if (!$mine) return response()->json(['error' => 'That dial out number was not found on your account.'], 404, self::NC);
                if (PhoneNumbers::forwardingGuarded($mine)) return response()->json(['error' => 'This number is protected and cannot be changed here.'], 403, self::NC);
            }
            if ($act === 'delete') { $v->call('Account/delete_account', ['i_account' => $ia]); return $this->ok($v, "dial out delete $ia"); }
            if ($act === 'toggle') {
                $v->call('Account/update_account', ['account_info' => ['i_account' => $ia, 'blocked' => $req->boolean('active') ? 'N' : 'Y']]);
                return $this->ok($v, "dial out toggle $ia");
            }
            $num = preg_replace('/\D/', '', (string) $req->input('number'));
            if (!preg_match('/^\d{7,14}$/', $num)) return response()->json(['error' => 'Enter the number you call from (7 to 14 digits).'], 422, self::NC);
            $nick = trim(strip_tags((string) $req->input('nickname')));
            if ($nick === '' || mb_strlen($nick) > 40) return response()->json(['error' => 'Enter a nickname (up to 40 characters).'], 422, self::NC);
            $show = (string) $req->input('show');
            if (!in_array($show, $dids, true)) return response()->json(['error' => 'Choose which of your numbers to show.'], 422, self::NC);
            foreach (config('greetings.never_lines', []) as $g) if ($g !== '' && str_contains($num, preg_replace('/\D/', '', $g))) return response()->json(['error' => 'This number is protected and cannot be used here.'], 403, self::NC);
            foreach ($acc as $a) if (strcasecmp((string) $a['id'], "a$num") === 0 && (int) $a['i_account'] !== $ia) return response()->json(['error' => 'That number is already a dial out number.'], 422, self::NC);
            $sf = [['name' => 'cli', 'flag_value' => 'Y', 'attributes' => [['name' => 'centrex', 'values' => [$show]], ['name' => 'display_number', 'values' => [$show]]]]];
            if ($act === 'update') {
                $v->call('Account/update_account', ['account_info' => ['i_account' => $ia, 'id' => "a$num", 'cont1' => $nick, 'blocked' => $req->boolean('active') ? 'N' : 'Y', 'service_features' => $sf]]);
                return $this->ok($v, "dial out update $ia");
            }
            // add: the same record VO creates (plan product, customer currency/time zone/language/email, description DIALOUT, debit model)
            $plan = json_decode($v->call('VO/get_my_plan', [])['json_string'] ?? '{}', true) ?: [];
            $ip = (int) ($plan['i_product'] ?? ($plan['data']['i_product'] ?? 0));
            if ($ip <= 0) return response()->json(['error' => 'Your plan could not be read, so the number was not added. Please contact support.'], 502, self::NC);
            $c = $v->call('Customer/get_customer_info', ['i_customer' => $v->ic])['customer_info'] ?? [];
            $info = ['id' => "a$num", 'i_product' => $ip, 'h323_password' => substr(bin2hex(random_bytes(8)), 0, 12), 'i_customer' => $v->ic, 'iso_4217' => $c['iso_4217'] ?? null,
                'i_time_zone' => $c['i_time_zone'] ?? null, 'i_lang' => $c['i_lang'] ?? null, 'email' => $c['email'] ?? null, 'description' => 'DIALOUT', 'billing_model' => 1, 'cont1' => $nick,
                'blocked' => $req->boolean('active', true) ? 'N' : 'Y', 'service_features' => $sf];
            $r = $v->call('Account/add_account', ['account_info' => array_filter($info, fn ($x) => $x !== null)]);
            Cache::forget("gvd-vo:menu:{$v->ic}");
            return $this->ok($v, 'dial out add ' . (int) ($r['i_account'] ?? 0));
        } catch (\Throwable $e) { return $this->fail($v, $e, "dial out $act"); }
    }

    // ---------- WebRTC (which extension the browser phone uses) ----------
    /** Extensions VO offers on its WebRTC page: account-bound extensions whose "default_action" flag has bit 1 set. */
    public function webrtc()
    {
        return $this->page('users.vo-webrtc', function (VoData $v) {
            $r = $v->many(['ext' => ['Customer/get_extensions_list', ['i_customer' => $v->ic]],
                'acc' => ['Account/get_account_list', ['i_customer' => $v->ic, 'limit' => 500, 'bill_status' => 'O', 'get_service_features' => ['default_action']]]]);
            if ($r['ext'] === null || $r['acc'] === null) throw new \RuntimeException('webrtc lists');
            $acc = []; foreach ($r['acc']['account_list'] ?? [] as $a) if ((int) ($a['i_customer'] ?? 0) === $v->ic) $acc[(int) $a['i_account']] = $a;
            $exts = [];
            foreach ($r['ext']['extensions_list'] ?? [] as $x) {
                $ia = (int) ($x['i_account'] ?? 0); if ($ia <= 0 || !isset($acc[$ia])) continue;
                $da = VoData::sf($acc[$ia], 'default_action');
                if (!$da || ((int) $da['flag'] & 1) === 0) continue;
                $exts[] = ['key' => (string) $ia, 'ext' => (string) ($x['id'] ?? ''), 'label' => 'Ext ' . ($x['id'] ?? '') . (($x['name'] ?? '') !== '' ? ' - ' . $x['name'] : '')];
            }
            usort($exts, fn ($a, $b) => strnatcmp($a['ext'], $b['ext']));
            return ['exts' => $exts];
        });
    }

    /** GET /user/phone/balance: the customer's balance exactly as billing returns it. */
    public function balance()
    {
        if (\App\Portal\Demo::on()) return response()->json(\App\Portal\Demo::balance(), 200, self::NC);
        $v = $this->svc(); if (!$v instanceof VoData) return $v;
        try {
            $c = $v->call('Customer/get_customer_info', ['i_customer' => $v->ic])['customer_info'] ?? [];
            return response()->json(['balance' => $c['balance'] ?? null, 'currency' => $c['iso_4217'] ?? null], 200, self::NC);
        } catch (\Throwable $e) { return $this->fail($v, $e, 'balance'); } finally { $v->close(); }
    }

    // ---------- Mobile app sign-in (2026-11-01 qr; mirrors the VO extension "Ring In/Out" panel) ----------
    const MA_DEF = ['ios' => 'https://apps.apple.com/us/app/mobyx/id924542684', 'android' => 'https://play.google.com/store/apps/details?id=net.mobyx.android'];
    const MA_NEED_RNG = 'Turn on Ring In/Out to use the mobile app';
    private static function maUrl($u): string { $u = trim((string) $u); return preg_match('#^https://[^\s"<>]+$#i', $u) ? $u : ''; }
    private static function maEnc(string $s): string { return strtr(rawurlencode($s), ['%21' => '!', '%2A' => '*', '%27' => "'", '%28' => '(', '%29' => ')']); } // = JS encodeURIComponent

    /** Plan config + extension eligibility (RNG extra on, account-bound extension, default_action bit 1 = VO rules). */
    private function maState(VoData $v): array
    {
        $j = $v->many(['plan' => ['VO/get_my_plan', []], 'ext' => ['Customer/get_extensions_list', ['i_customer' => $v->ic]],
            'acc' => ['Account/get_account_list', ['i_customer' => $v->ic, 'limit' => 500, 'bill_status' => 'O', 'get_service_features' => ['default_action']]],
            'cf' => ['Customer/get_custom_fields_values', ['i_customer' => $v->ic]]]);
        if ($j['plan'] === null || $j['ext'] === null || $j['acc'] === null) throw new \RuntimeException('mobile lists');
        $plan = json_decode((string) ($j['plan']['json_string'] ?? ($j['plan']['data']['json_string'] ?? '{}')), true) ?: [];
        $cfg = $plan['config'] ?? ($plan['data']['config'] ?? []); if (!is_array($cfg)) $cfg = [];
        if (Cache::add("gvd-ma:cfglog:{$v->ic}", 1, 86400))
            Log::info('gvd-mobile config', ['i_customer' => $v->ic, 'keys' => array_keys($cfg), 'mobyx_suffix' => (string) ($cfg['mobyx_suffix'] ?? ''),
                'urls' => array_map('strval', array_intersect_key($cfg, array_flip(['mobyx_android_url', 'mobyx_ios_url', 'download_desktop_dialer_win', 'download_desktop_dialer_mac', 'download_desktop_dialer_lin'])))]);
        $vo = ['extras' => []];
        foreach ($j['cf']['custom_fields_values'] ?? [] as $c) if (($c['name'] ?? '') === 'virtoffice') { $vo = self::voParse((string) ($c['text_value'] ?? $c['db_value'] ?? '')); break; }
        $rng = !empty($vo['extras']['RNG']);
        $acc = []; foreach ($j['acc']['account_list'] ?? [] as $a) if ((int) ($a['i_customer'] ?? 0) === $v->ic) $acc[(int) $a['i_account']] = $a;
        $exts = [];
        foreach ($j['ext']['extensions_list'] ?? [] as $x) {
            $ia = (int) ($x['i_account'] ?? 0); if ($ia <= 0 || !isset($acc[$ia])) continue;
            if (PhoneNumbers::forwardingGuarded($acc[$ia])) continue; // never offer the guarded fax-forward line
            $da = VoData::sf($acc[$ia], 'default_action'); $bit = $da && ((int) $da['flag'] & 1) !== 0;
            $why = !$rng ? self::MA_NEED_RNG : (!$bit ? 'Ring In/Out is not turned on for this extension' : '');
            $exts[] = ['id' => $ia, 'ext' => (string) ($x['id'] ?? ''), 'label' => 'Ext ' . ($x['id'] ?? '') . (($x['name'] ?? '') !== '' ? ' - ' . $x['name'] : ''), 'ok' => $why === '', 'why' => $why];
        }
        usort($exts, fn ($a, $b) => strnatcmp($a['ext'], $b['ext']));
        return ['cfg' => $cfg, 'rng' => $rng, 'exts' => $exts];
    }
    private static function maView(array $cfg, bool $rng, array $exts): array
    {
        return ['ios' => self::maUrl($cfg['mobyx_ios_url'] ?? '') ?: self::MA_DEF['ios'], 'android' => self::maUrl($cfg['mobyx_android_url'] ?? '') ?: self::MA_DEF['android'],
            'win' => self::maUrl($cfg['download_desktop_dialer_win'] ?? ''), 'mac' => self::maUrl($cfg['download_desktop_dialer_mac'] ?? ''), 'lin' => self::maUrl($cfg['download_desktop_dialer_lin'] ?? ''),
            'rng' => $rng, 'exts' => $exts, 'signin' => true];
    }
    private function maEligible(VoData $v, int $ia): array
    {
        $st = $this->maState($v);
        foreach ($st['exts'] as $e) if ($e['id'] === $ia) { if (!$e['ok']) throw new \DomainException('not eligible'); return [$e, $st['cfg']]; }
        throw new \DomainException('not an extension');
    }

    /** GET /user/mobile-app: download badges for everyone; per-extension sign-in for the account admin. */
    public function mobileApp()
    {
        if (\App\Portal\Demo::on()) {
            $ex = array_map(fn ($e, $i) => ['id' => 900101 + $i, 'ext' => $e['ext'], 'label' => $e['label'], 'ok' => true, 'why' => ''], \App\Portal\Demo::webrtcExts()['exts'], array_keys(\App\Portal\Demo::webrtcExts()['exts']));
            $ex[] = ['id' => 900199, 'ext' => '105', 'label' => 'Ext 105 - Conference Room', 'ok' => false, 'why' => 'Ring In/Out is not turned on for this extension'];
            return response()->view('users.vo-mobile-app', ['error' => null, 'admin' => true, 'demo' => true, 'd' => self::maView([], true, $ex)], 200)->withHeaders(self::NC);
        }
        $v = $this->svc();
        if (!$v instanceof VoData) return response()->view('users.vo-mobile-app', ['error' => null, 'admin' => false, 'demo' => false, 'd' => self::maView([], false, []) + ['signin' => false]], 200)->withHeaders(self::NC);
        try {
            $admin = $v->isAdmin();
            if (!$admin) $d = self::maView([], false, []);
            else { $st = $this->maState($v); $d = self::maView($st['cfg'], $st['rng'], $st['exts']); }
            $err = null;
        } catch (\Throwable $e) {
            $d = self::maView([], false, []); $d['signin'] = false; $err = 'Sign-in details could not be loaded right now. Please try again.';
            Log::warning('gvd-vo page failed', ['view' => 'mobile-app', 'i_customer' => $v->ic, 'error' => get_class($e)]);
        } finally { $v->close(); }
        return response()->view('users.vo-mobile-app', ['error' => $err, 'admin' => $admin ?? false, 'demo' => false, 'd' => $d])->withHeaders(self::NC);
    }

    /** POST /user/mobile-app/creds {id} — admin only. Credentials + QR text for one eligible extension. Never logged or cached. */
    public function mobileCreds(Request $req)
    {
        if (\App\Portal\Demo::on()) {
            $n = preg_replace('/\D/', '', (string) $req->input('id')) ?: '0';
            return response()->json(['rows' => [['Mobile app login', 'demo-' . $n, false], ['Mobile app password', 'Sample-Pass-123', true], ['SIP login', '1190300' . substr($n, -3), false], ['SIP password', 'Sample-Sip-456', true]],
                'qr' => 'csc:1190300' . substr($n, -3) . ':Sample-Sip-456@demo.example', 'demo' => true], 200, self::NC);
        }
        $v = $this->admin(); if (!$v instanceof VoData) return $v;
        try {
            $ia = (int) $req->input('id');
            [, $cfg] = $this->maEligible($v, $ia);
            $j = $v->many(['info' => ['Account/get_account_info', ['i_account' => $ia, 'detailed_info' => 1]], 'cf' => ['Account/get_custom_fields_values', ['i_account' => $ia]]]);
            $a = $j['info']['account_info'] ?? null;
            if (!$a || (int) ($a['i_customer'] ?? 0) !== $v->ic) throw new \DomainException('not yours');
            $ov = null;
            foreach ($j['cf']['custom_fields_values'] ?? [] as $c) if (($c['name'] ?? '') === 'vo_override_sipinfo') {
                $t = trim((string) ($c['text_value'] ?? $c['db_value'] ?? '')); if ($t !== '') { $x = json_decode($t, true); if (is_array($x) && $x) $ov = $x; } break; }
            $rows = []; $qr = null;
            if ($ov) {
                foreach ($ov as $k => $val) {
                    if (strtoupper((string) $k) === 'QR') { $qr = is_scalar($val) ? (string) $val : null; continue; }
                    if (strtoupper((string) $k) === 'EMAIL' || !is_scalar($val)) continue;
                    $s = (string) $val; $sec = str_starts_with($s, "\u{1f441}") || preg_match('/pass/i', (string) $k);
                    $rows[] = [ucfirst(str_replace('_', ' ', (string) $k)), ltrim($s, "\u{1f441}"), (bool) $sec];
                }
            } else {
                if (!empty($cfg['mobyx']) || !empty($cfg['dialer'])) { $rows[] = ['Mobile app login', (string) ($a['login'] ?? ''), false]; $rows[] = ['Mobile app password', (string) ($a['password'] ?? ''), true]; }
                if (!empty($cfg['sip_domain'])) $rows[] = ['SIP domain', (string) $cfg['sip_domain'], false];
                $rows[] = ['SIP login', (string) ($a['id'] ?? ''), false]; $rows[] = ['SIP password', (string) ($a['h323_password'] ?? ''), true];
                $suf = (string) ($cfg['mobyx_suffix'] ?? '');
                if ($suf !== '' && ($a['id'] ?? '') !== '') $qr = 'csc:' . $a['id'] . ':' . self::maEnc((string) ($a['h323_password'] ?? '')) . '@' . $suf;
            }
            return response()->json(['rows' => $rows, 'qr' => $qr], 200, self::NC);
        } catch (\DomainException $e) { return response()->json(['error' => 'This extension cannot use the mobile app yet.'], 403, self::NC); }
        catch (\Throwable $e) { return response()->json(['error' => 'Sign-in details could not be loaded right now.'], 502, self::NC); }
        finally { $v->close(); }
    }

    /** POST /user/mobile-app/email {id, confirm=1} — admin only. Billing emails the setup QR to the extension's user. */
    public function mobileEmail(Request $req)
    {
        if (\App\Portal\Demo::on()) return \App\Portal\Demo::readonly();
        $v = $this->admin(); if (!$v instanceof VoData) return $v;
        if ($req->input('confirm') !== '1') { $v->close(); return response()->json(['error' => 'Please confirm first.'], 422, self::NC); }
        try {
            $ia = (int) $req->input('id');
            [$e] = $this->maEligible($v, $ia);
            $v->call('Internal/get_mobyx_qr_code', ['i_account' => $ia, 'send_by_mail' => 1]);
            return $this->ok($v, 'mobile setup email ext ' . $e['ext']);
        } catch (\Throwable $e) { return $this->fail($v, $e, 'mobile setup email'); }
        finally { $v->close(); }
    }
}

