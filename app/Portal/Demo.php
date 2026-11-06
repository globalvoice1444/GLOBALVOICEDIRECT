<?php
namespace App\Portal;

/**
 * Sample-data mode (2026-11-01) for ONE local login used to record how-to videos.
 * Scoped strictly to config('gvd_demo.user_id'); every other user gets false from on() and is untouched.
 * No billing API calls are made for this user; all values below are fictional (555-01xx numbers, example.com).
 * Remove: see config/gvd_demo.php.
 */
class Demo
{
    const READONLY = 'This sample account is read-only, so nothing was changed or sent.';
    const MAIN = '15550100100'; const FAX = '15550100150';

    public static function isDemoUser($uid = null): bool
    {
        $id = (int) config('gvd_demo.user_id', 0);
        if ($id <= 0 || !config('gvd_demo.enabled', false)) return false;
        $uid = $uid ?? session()->get('user_id');
        return $uid !== null && (int) $uid === $id;
    }
    public static function on(): bool { return self::isDemoUser(); }

    public static function ph(string $n): string { $d = substr(preg_replace('/\D/', '', $n), -10); return sprintf('(%s) %s-%s', substr($d, 0, 3), substr($d, 3, 3), substr($d, 6)); }
    private static function t(string $rel): \DateTimeImmutable { return new \DateTimeImmutable($rel, new \DateTimeZone('America/New_York')); }
    private static function et(string $rel): string { return self::t($rel)->format('M j, Y g:i A'); }

    public static function readonly(int $status = 422)
    {
        return response()->json(['ok' => false, 'error' => self::READONLY, 'message' => self::READONLY], $status, ['Cache-Control' => 'private, no-store']);
    }

    // ---------- Fax ----------
    private const FAX_IN = [
        [9101, '15550100131', '-2 hours', false, 3, 'Northwind Supply', 'Purchase Order #PO-20417'],
        [9102, '15550100132', '-1 day 10:15', true, 1, 'Bluebird Printing', 'Proof approval form'],
        [9103, '15550100133', '-2 days 14:40', true, 2, 'Summit Logistics', 'Delivery confirmation'],
        [9104, '15550100134', '-4 days 09:05', true, 4, 'Harbor Insurance Group', 'Policy renewal summary'],
        [9105, '15550100135', '-6 days 16:30', true, 1, 'Maple Street Dental', 'Patient referral (sample)'],
    ];
    private const FAX_OUT = [
        [9201, '15550100141', '-3 hours', 'Delivered', false, 42, 'Signed service agreement'],
        [9202, '15550100142', '-1 day 11:20', 'Delivered', false, 65, 'Invoice INV-1042'],
        [9203, '15550100143', '-3 days 15:10', 'Busy, retry later', true, 0, 'Quote request'],
        [9204, '15550100144', '-5 days 10:00', 'Delivered', false, 38, 'W-9 form (sample)'],
    ];
    public static function faxInbox(): array
    {
        return array_map(fn ($f) => ['ia' => 1, 'uid' => $f[0], 'from' => self::ph($f[1]), 'to' => self::ph(self::FAX), 'date' => self::et($f[2]), 'ts' => self::t($f[2])->getTimestamp(), 'seen' => $f[3], 'pages' => $f[4]], self::FAX_IN);
    }
    public static function faxSent(): array
    {
        return array_map(fn ($f) => ['ia' => 1, 'uid' => $f[0], 'from' => self::ph(self::FAX), 'to' => self::ph($f[1]), 'date' => self::et($f[2]), 'status' => $f[3], 'failed' => $f[4], 'dur' => $f[5] ? sprintf('%d:%02d', intdiv($f[5], 60), $f[5] % 60) : '-'], self::FAX_OUT);
    }
    public static function recentFax()
    {
        return collect(self::FAX_IN)->take(5)->map(fn ($f) => (object) ['caller' => $f[1], 'did' => self::FAX, 'received_at' => self::t($f[2])->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s'), 'pages' => $f[4]]);
    }
    /** Sample fax document (generated PDF, fictional content). */
    public static function faxFile(int $uid, bool $download)
    {
        $f = null; foreach (array_merge(self::FAX_IN, self::FAX_OUT) as $x) if ($x[0] === $uid) $f = $x;
        if (!$f) abort(404);
        $in = $uid < 9200;
        $lines = [$f[6], '', ($in ? 'From: ' . $f[5] : 'To: ' . self::ph($f[1])), 'Fax: ' . self::ph($in ? $f[1] : self::FAX), 'To: Demo Business LLC  ' . self::ph(self::FAX), 'Date: ' . self::et($f[2]), '',
            'This is a sample document for illustration only.', 'All names, numbers and amounts are fictional.', '', 'Item                     Qty     Amount', '-------------------------------------', 'Office supplies           12    $184.00', 'Printer toner              3    $129.50', 'Shipping                   1     $18.00', '-------------------------------------', 'Total                            $331.50'];
        $body = self::pdf($lines);
        return response($body, 200, ['Content-Type' => 'application/pdf', 'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff',
            'Content-Disposition' => ($download ? 'attachment' : 'inline') . '; filename="fax-' . $uid . '.pdf"']);
    }
    private static function pdf(array $lines): string
    {
        $esc = fn ($s) => str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $s);
        $s = "BT /F1 20 Tf 72 720 Td (" . $esc(array_shift($lines)) . ") Tj ET\nBT /F2 11 Tf 72 690 Td 15 TL\n";
        foreach ($lines as $l) $s .= "(" . $esc($l) . ") Tj T*\n";
        $s .= "ET\n0.12 0.16 0.21 RG 2 w 72 740 m 540 740 l S\n";
        $objs = ['<< /Type /Catalog /Pages 2 0 R >>', '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 4 0 R /F2 5 0 R >> >> /Contents 6 0 R >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>', '<< /Type /Font /Subtype /Type1 /BaseFont /Courier >>',
            "<< /Length " . strlen($s) . " >>\nstream\n$s\nendstream"];
        $out = "%PDF-1.4\n"; $off = [];
        foreach ($objs as $i => $o) { $off[] = strlen($out); $out .= ($i + 1) . " 0 obj\n$o\nendobj\n"; }
        $x = strlen($out); $out .= "xref\n0 " . (count($objs) + 1) . "\n0000000000 65535 f \n";
        foreach ($off as $o) $out .= sprintf("%010d 00000 n \n", $o);
        return $out . "trailer\n<< /Size " . (count($objs) + 1) . " /Root 1 0 R >>\nstartxref\n$x\n%%EOF";
    }

    // ---------- Calls ----------
    private const CALLS = [ // [rel time, from, to, ext, secs, status]
        ['inbound', '-25 minutes', '15550100161', self::MAIN, '101', 312, 'Connected'],
        ['inbound', '-2 hours', '15550100162', self::MAIN, '102', 0, 'Missed'],
        ['inbound', '-5 hours', '15550100163', self::MAIN, '103', 95, 'Voicemail'],
        ['inbound', '-1 day 13:05', '15550100165', self::MAIN, '104', 128, 'Connected'],
        ['inbound', '-1 day 09:42', '15550100164', self::MAIN, '101', 486, 'Connected'],
        ['inbound', '-2 days 10:30', '15550100131', self::FAX, '', 64, 'Fax'],
        ['inbound', '-2 days 15:55', '15550100166', self::MAIN, '102', 241, 'Connected'],
        ['inbound', '-3 days 11:12', '15550100167', self::MAIN, '101', 0, 'Missed'],
        ['inbound', '-4 days 16:20', '15550100168', self::MAIN, '103', 733, 'Connected'],
        ['inbound', '-6 days 08:58', '15550100169', self::MAIN, '104', 57, 'Connected'],
        ['outbound', '-1 hour', '101', '15550100171', '101', 198, 'Connected'],
        ['outbound', '-1 day 10:40', '102', '15550100172', '102', 402, 'Connected'],
        ['outbound', '-2 days 14:15', '103', '15550100173', '103', 0, 'Unanswered'],
        ['outbound', '-3 days 09:30', '101', '15550100174', '101', 615, 'Connected'],
        ['outbound', '-5 days 12:00', '104', '15550100175', '104', 87, 'Connected'],
    ];
    public static function callRows(string $slug): array
    {
        $type = $slug === 'outbound' ? 'outbound' : 'inbound';
        $rows = [];
        foreach (self::CALLS as $i => $c) {
            if ($slug === 'dial-out' || ($slug !== 'recorded' && $c[0] !== $type) || ($slug === 'recorded' && !in_array($i, [0, 3, 8, 13], true))) continue;
            $ph = fn ($n) => strlen($n) > 5 ? self::ph($n) : $n;
            $rows[] = ['id' => 7000 + $i, 'time' => self::et($c[1]), 'from' => $ph($c[2]), 'to' => $ph($c[3]), 'called' => $c[0] === 'inbound' ? self::ph($c[3]) : '', 'ext' => $c[4],
                'dur' => sprintf('%d:%02d', intdiv($c[5], 60), $c[5] % 60), 'status' => $c[6]];
        }
        return $rows;
    }

    // ---------- Extensions / users / numbers ----------
    private const EXT = [['101', 'Front Desk', 'Always on', 'Off'], ['102', 'Sales', 'Ring, then voicemail', 'On'], ['103', 'Support', 'Ring, then voicemail', 'On'], ['104', 'Accounting', 'Forward to mobile', 'Off'], ['105', 'Conference Room', 'Always on', 'Off']];
    public static function extensions(): array
    {
        $rows = [];
        foreach (self::EXT as $i => $e) $rows[] = ['i_account' => 800001 + $i, 'ext' => $e[0], 'name' => $e[1], 'type' => 'Account', 'account' => 'demo' . $e[0], 'group' => 'employee',
            'sip' => $i === 4 ? '0' : '1', 'forward' => $e[2], 'recording' => $e[3], 'rec' => []];
        $rows[] = ['i_account' => 0, 'ext' => '200', 'name' => 'Main Menu', 'type' => 'Auto attendant', 'account' => '', 'group' => 'other', 'sip' => '', 'forward' => '', 'recording' => '', 'rec' => []];
        return ['rows' => $rows, 'hunt' => [['id' => '300', 'name' => 'Sales & Support', 'sequence' => 'Simultaneous', 'members' => 2], ['id' => '301', 'name' => 'Everyone', 'sequence' => 'Order', 'members' => 5]]];
    }
    public static function users(): array
    {
        return ['users' => [
            ['i_account' => 800001, 'login' => env('TELINTA_SIGNUP_LOGIN'), 'first' => 'Jordan', 'last' => 'Avery', 'email' => 'owner@example.com', 'status' => 'Active'],
            ['i_account' => 800002, 'login' => env('TELINTA_SIGNUP_LOGIN'), 'first' => 'Taylor', 'last' => 'Morgan', 'email' => 'sales@example.com', 'status' => 'Active'],
            ['i_account' => 800003, 'login' => env('TELINTA_SIGNUP_LOGIN'), 'first' => 'Casey', 'last' => 'Lee', 'email' => 'support@example.com', 'status' => 'Active'],
        ]];
    }
    public static function numbers(): array
    {
        $cli = fn ($t, $m) => ['text' => $t, 'mode' => $m];
        return [
            ['i_account' => 800001, 'id' => self::MAIN, 'display' => self::ph(self::MAIN), 'cli' => $cli('Company default', 'DEFAULT'), 'kind' => 'vo', 'custom' => false, 'status' => 'Active', 'plan' => 'Business Unlimited', 'label' => 'Main line'],
            ['i_account' => 800002, 'id' => '15550100102', 'display' => self::ph('15550100102'), 'cli' => $cli('This number', 'VODIDNUMBER'), 'kind' => 'vo', 'custom' => false, 'status' => 'Active', 'plan' => 'Business Unlimited', 'label' => 'Sales direct'],
            ['i_account' => 800006, 'id' => self::FAX, 'display' => self::ph(self::FAX), 'cli' => $cli('Company default', 'DEFAULT'), 'kind' => 'vo', 'custom' => false, 'status' => 'Active', 'plan' => 'Virtual Fax', 'label' => 'Fax line'],
        ];
    }
    /** Per-number settings for the side panel (fictional). */
    public static function numberDetails(): array
    {
        return [800001 => ['route' => 'ROOT', 'ext' => '', 'email' => 'notify', 'fax' => 'pdf', 'emailSet' => true],
            800002 => ['route' => 'TRANSFER', 'ext' => '102', 'email' => 'fwd_as_attachment', 'fax' => 'pdf', 'emailSet' => true],
            800006 => ['route' => 'TRANSFER', 'ext' => '101', 'email' => 'forward', 'fax' => 'pdf', 'emailSet' => true]];
    }

    // ---------- Conference rooms / call block / extras ----------
    public static function conferences(): array
    {
        $r = fn ($id, $n, $max, $rec, $wait, $pin) => ['i_conf' => $id, 'hasPin' => $pin, 'hasModPin' => $pin, 'name' => $n, 'max_participants' => $max, 'max_duration' => 120,
            'wait_for_moderator' => $wait, 'close_after_participants_exit' => 'Y', 'announce_name' => 'Y', 'announce_join_leave' => 'Y', 'announce_number_participants' => 'Y', 'record' => $rec];
        return ['ext' => '105', 'rooms' => [$r(9301, 'Weekly Team Meeting', 10, 'Y', 'Y', true), $r(9302, 'Sales Huddle', 6, 'N', 'N', true), $r(9303, 'Client Calls', 15, 'Y', 'Y', true), $r(9304, 'Open Room', 25, 'N', 'N', false)]];
    }
    public static function callBlock(): array
    {
        $t = [['i_account' => 800000, 'id' => 'company', 'label' => 'Company (all numbers)', 'guarded' => false],
            ['i_account' => 800001, 'id' => self::MAIN, 'label' => self::ph(self::MAIN), 'guarded' => false],
            ['i_account' => 800002, 'id' => '15550100102', 'label' => self::ph('15550100102'), 'guarded' => false]];
        $all = [800000, 800001, 800002];
        $n = fn ($num, $label, $on) => ['number' => $num, 'label' => $label, 'on' => $on];
        return ['targets' => $t, 'lists' => [
            'blocked' => [$n('15550100181', self::ph('15550100181') . ' (spam caller)', $all), $n('15550100182', self::ph('15550100182') . ' (robocall)', $all), $n('15550100183', self::ph('15550100183'), [800001])],
            'areas' => [$n('1555%', 'Area code 555 (example)', [800001, 800002])],
            'allowed' => [$n('15550100161', self::ph('15550100161') . ' (Northwind Supply)', $all), $n('15550100164', self::ph('15550100164') . ' (Harbor Insurance Group)', $all)]]];
    }
    /** Extras in display order: [code, monthly fee, on]. Names/descriptions come from the controller's own tables. */
    public static function extras(): array
    {
        return [['V2T', 4.95, true], ['CREC', 9.95, true], ['BLST', 0, true], ['EML2FAX', 0, true], ['ANI', 4.95, false], ['RNG', 0, true], ['WEBRTC', 0, true], ['INS', 2.95, false], ['CNF', 0, true]];
    }

    // ---------- Greetings ----------
    public static function greetings(): array
    {
        $ext = fn ($e) => 'Ext ' . $e[0] . ' - ' . $e[1];
        $a = fn ($q) => url('/user/greetings/audio?' . $q);
        $rows = [
            ['key' => 'menu:9401', 'kind' => 'company', 'name' => 'Company main greeting', 'i_menu' => 9401, 'audio' => $a('t=menu&id=9401'), 'custom' => true, 'schedule' => 'Mon-Fri 9:00 AM - 5:00 PM', 'period' => '',
                'noInput' => 'Transfer to Ext 101 - Front Desk', 'deletable' => false, 'alternates' => [
                    ['key' => 'menu:9403', 'name' => 'Holiday greeting', 'i_menu' => 9403, 'audio' => $a('t=menu&id=9403'), 'schedule' => 'On company holidays', 'period' => '', 'deletable' => true, 'active' => false]]],
            ['key' => 'menu:9402', 'kind' => 'company', 'name' => 'After-hours greeting', 'i_menu' => 9402, 'audio' => $a('t=menu&id=9402'), 'custom' => true, 'schedule' => 'Outside business hours', 'period' => '',
                'noInput' => 'Go to voicemail', 'deletable' => false, 'alternates' => []],
        ];
        foreach (array_slice(self::EXT, 0, 4) as $i => $e) {
            $ia = 800001 + $i;
            $rows[] = ['key' => "vm:$ia:standard", 'kind' => 'extension', 'name' => $ext($e), 'i_account' => $ia, 'audio' => $a("t=vm&id=$ia&p=standard"), 'schedule' => 'Always', 'noInput' => null, 'deletable' => false,
                'alternates' => [['key' => "vm:$ia:extended", 'name' => 'Extended absence', 'audio' => $a("t=vm&id=$ia&p=extended"), 'schedule' => 'When extended absence is turned on', 'deletable' => false, 'active' => false, 'extended' => true]]];
        }
        $cards = [
            ['title' => 'Main', 'key' => 'menu:9401', 'i_menu' => 9401, 'audio' => $a('t=menu&id=9401'), 'custom' => true, 'sync' => true,
                'hours' => ['label' => 'Mon-Fri 9:00 AM - 5:00 PM', 'periods' => ['Mon-Fri 9:00 AM - 5:00 PM']], 'noInput' => ['action' => 'Transfer to extension', 'target' => 'Ext 101 - Front Desk']],
            ['title' => 'After Hours', 'key' => 'menu:9402', 'i_menu' => 9402, 'audio' => $a('t=menu&id=9402'), 'custom' => true, 'sync' => true, 'hours' => null, 'noInput' => ['action' => 'Go to voicemail', 'target' => 'Ext 101 - Front Desk']],
        ];
        $h = (int) self::t('now')->format('G'); $wd = (int) self::t('now')->format('N');
        $now = ($wd <= 5 && $h >= 9 && $h < 17) ? 'Main greeting (Mon-Fri 9:00 AM - 5:00 PM)' : 'After-hours greeting (Outside business hours)';
        return ['error' => null, 'rows' => $rows, 'cards' => $cards, 'admin' => true, 'nowText' => $now, 'notes' => [],
            'holidays' => [['id' => 1, 'name' => 'New Year\'s Day', 'day' => '2027-01-01'], ['id' => 2, 'name' => 'Independence Day', 'day' => '2027-07-04'], ['id' => 3, 'name' => 'Thanksgiving', 'day' => '2026-11-26']]];
    }
    /** Short, soft two-note chime (generated WAV) so the sample greeting players work; no real recording is used. */
    public static function chimeWav(): string
    {
        $rate = 8000; $pcm = '';
        foreach ([[523.25, 0.45], [659.25, 0.45], [783.99, 0.9]] as [$f, $d]) {
            $n = (int) ($rate * $d);
            for ($i = 0; $i < $n; $i++) { $env = min(1, $i / 200) * exp(-3 * $i / $n); $pcm .= pack('v', (int) (9000 * $env * sin(2 * M_PI * $f * $i / $rate)) & 0xFFFF); }
        }
        return 'RIFF' . pack('V', 36 + strlen($pcm)) . 'WAVEfmt ' . pack('VvvVVvv', 16, 1, 1, $rate, $rate * 2, 2, 16) . 'data' . pack('V', strlen($pcm)) . $pcm;
    }
    public static function smsNumbers(): array { return [['id' => self::MAIN, 'display' => self::ph(self::MAIN)]]; }

    // ---------- Dashboard / profile ----------
    public static function stats(): array
    {
        $recent = array_map(fn ($r) => ['time' => $r['time'], 'from' => $r['from'], 'status' => $r['status'], 'dur' => $r['dur']], array_slice(self::callRows('inbound'), 0, 5));
        return ['ok' => true, 'calls_in' => count(self::callRows('inbound')), 'calls_out' => count(self::callRows('outbound')), 'extensions' => 5, 'numbers' => 3, 'recent' => $recent];
    }
    public static function profileSections(): array
    {
        return [
            'Business' => ['Company' => 'Demo Business LLC', 'Customer name' => 'Demo Business LLC', 'First name' => 'Jordan', 'Last name' => 'Avery'],
            'Contact' => ['Contact' => 'Jordan Avery', 'Email' => 'demo@example.com', 'Phone' => self::ph(self::MAIN), 'Alternate phone' => self::ph('15550100102'), 'Fax' => self::ph(self::FAX)],
            'Address' => ['Address line 1' => '100 Example Avenue', 'Address line 2' => 'Suite 200', 'City' => 'Springfield', 'State' => 'NJ', 'ZIP code' => '00000', 'Country' => 'US'],
            'Account' => ['Account ID' => '000000', 'Currency' => 'USD', 'Balance' => '0.00', 'Billing period' => 'Monthly', 'Customer since' => '2025-01-15', 'Status' => 'Open'],
        ];
    }
    // ---------- Email campaign (sample) ----------
    private const EMAIL_GROUPS = [[9301, 'Spring Newsletter'], [9302, 'VIP Customers'], [9303, 'Webinar Invitees']];
    private const EMAIL_CONTACTS = [
        ['Ava Thompson', 'ava.thompson@example.com', 9301], ['Liam Carter', 'liam.carter@example.com', 9301], ['Sophia Nguyen', 'sophia.nguyen@example.com', 9302],
        ['Noah Patel', 'noah.patel@example.com', 9302], ['Mia Robinson', 'mia.robinson@example.com', 9301], ['Ethan Brooks', 'ethan.brooks@example.com', 9303],
        ['Olivia Diaz', 'olivia.diaz@example.com', 9302], ['Lucas Green', 'lucas.green@example.com', 9303], ['Emma Wallace', 'emma.wallace@example.com', 9301],
        ['James Foster', 'james.foster@example.com', 9303],
    ];
    public static function emailGroups()
    {
        return collect(self::EMAIL_GROUPS)->map(fn ($g) => (object) ['id' => $g[0], 'group_name' => $g[1], 'user_id' => (int) config('gvd_demo.user_id'), 'deleted' => 0]);
    }
    public static function emailGroup($id) { return self::emailGroups()->firstWhere('id', (int) $id); }
    public static function emailContacts()
    {
        $i = 0;
        return collect(self::EMAIL_CONTACTS)->map(function ($c) use (&$i) { $i++; return (object) ['id' => 9400 + $i, 'contact_name' => $c[0], 'contact_address' => $c[1], 'group_id' => $c[2], 'deleted' => 0, 'created_at' => self::t("-$i days")->format('Y-m-d H:i:s')]; });
    }

    // ---------- Audio campaign (sample) ----------
    public static function audioLines(): array { return [self::ph('15550100100'), self::ph('15550100102'), self::ph('15550100150')]; }
    private const AUDIO_CAMPS = [
        [9501, 'Holiday hours reminder', 'done', '-6 days', ['placed' => 11, 'failed' => 1]],
        [9502, 'Appointment confirmations', 'running', '-1 hours', ['placed' => 5, 'calling' => 1, 'waiting' => 4]],
        [9503, 'New service announcement', 'ready', '-1 day', ['waiting' => 8]],
    ];
    public static function audioIndex(?int $show): array
    {
        $camps = collect(self::AUDIO_CAMPS)->map(fn ($c) => (object) ['id' => $c[0], 'name' => $c[1], 'status' => $c[2], 'recording_id' => 9601, 'caller_account' => self::audioLines()[0], 'created_at' => self::t($c[3])->format('Y-m-d H:i:s')]);
        $counts = collect(self::AUDIO_CAMPS)->mapWithKeys(fn ($c) => [$c[0] => $c[4]]);
        $recs = collect([(object) ['id' => 9601, 'name' => 'Holiday-hours-message.mp3', 'size' => 182000, 'created_at' => self::t('-7 days')->format('Y-m-d H:i:s')],
                         (object) ['id' => 9602, 'name' => 'Appointment-reminder.wav', 'size' => 240000, 'created_at' => self::t('-2 days')->format('Y-m-d H:i:s')]]);
        $s = $show ? $camps->firstWhere('id', $show) : null;
        if ($show && !$s) abort(404);
        $targets = collect();
        if ($s) { $k = 0; foreach ($counts[$s->id] as $st => $n) for ($j = 0; $j < $n; $j++) { $k++; $targets->push((object) ['number' => self::ph(sprintf('1555010%04d', 200 + $k)), 'status' => $st,
            'attempted_at' => in_array($st, ['placed', 'failed', 'calling'], true) ? self::t($s->created_at)->modify('+' . ($k * 2) . ' seconds')->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s') : null]); } }
        return ['camps' => $camps, 'counts' => $counts, 'recs' => $recs, 'accounts' => self::audioLines(), 'billing' => true, 'show' => $s, 'demoTargets' => $targets,
            'sending' => true, 'max' => (int) config('audio_campaign.max_numbers'), 'off' => ''];
    }

    // ---------- Dial out numbers / WebRTC / balance (2026-11-01, sample only) ----------
    public static function dialOut(): array
    {
        $d = [self::MAIN, '15550100102'];
        $r = fn ($ia, $num, $nick, $show, $on) => ['i_account' => $ia, 'number' => $num, 'nickname' => $nick, 'show' => $show, 'showLabel' => self::ph($show), 'active' => $on, 'guarded' => false];
        return ['rows' => [$r(800201, '15550100191', 'Owner mobile', self::MAIN, true), $r(800202, '15550100192', 'Sales cell', '15550100102', true), $r(800203, '15550100193', 'Home office', self::MAIN, false)],
            'dids' => array_map(fn ($x) => ['value' => $x, 'label' => self::ph($x)], $d)];
    }
    public static function webrtcExts(): array
    {
        return ['exts' => array_map(fn ($e) => ['key' => 'demo' . $e[0], 'ext' => $e[0], 'label' => 'Ext ' . $e[0] . ' - ' . $e[1]], array_slice(self::EXT, 0, 4))];
    }
    public static function balance(): array { return ['balance' => 42.5, 'currency' => 'USD']; }
}
