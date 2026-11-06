<?php

namespace App\Http\Controllers;

use App\Portal\Demo;
use App\Support\CustomerEmail as CE;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

/** 2026-11-02 Customer-panel Email Campaigns (rebuild). Every query is scoped to session user_id. Sample login: read-only demo data. */
class EmailCampaignController extends Controller
{
    const NC = ['Cache-Control' => 'private, no-store, max-age=0', 'Pragma' => 'no-cache'];

    private function uid(): int { $u = (int) session()->get('user_id'); abort_unless($u > 0, 403); return $u; }
    private function j($d, int $s = 200) { return response()->json($d, $s, self::NC); }
    private function err(string $m, int $s = 422) { return $this->j(['error' => $m], $s); }
    private function limit(string $k, int $max, int $sec = 60): bool { $k = "ec:$k:" . $this->uid(); if (RateLimiter::tooManyAttempts($k, $max)) return false; RateLimiter::hit($k, $sec); return true; }
    private function camp(int $id) { return DB::table('ec_campaigns')->where('id', $id)->where('user_id', $this->uid())->first(); }

    public static function starters(): array
    {
        $btn = fn ($t) => '<p style="margin:22px 0"><a href="https://" style="display:inline-block;background:#1E2A36;color:#ffffff;text-decoration:none;font-weight:bold;padding:12px 22px;border-radius:8px">' . $t . '</a></p>';
        return [
            ['key' => 'announce', 'name' => 'Announcement', 'subject' => 'News from {{my_business}}', 'html' => '<h2>Big news, {{first_name}}!</h2><p>We have something new at {{my_business}} we think you will love.</p><p>Here is what is changing and why it matters for {{business_name}}.</p>' . $btn('Learn more') . '<p>Thanks,<br>The {{my_business}} team</p>'],
            ['key' => 'offer', 'name' => 'Special offer', 'subject' => 'A special offer for you, {{first_name}}', 'html' => '<h2>Save this week only</h2><p>Hi {{first_name}},</p><p>As a thank-you, here is an exclusive offer from {{my_business}}.</p><p style="font-size:22px;font-weight:bold;color:#FF5000">20% off your next order</p>' . $btn('Claim my offer') . '<p>Offer ends Friday.</p>'],
            ['key' => 'news', 'name' => 'Newsletter', 'subject' => 'This month at {{my_business}}', 'html' => '<h2>This month at {{my_business}}</h2><p>Hi {{first_name}}, here are a few updates.</p><h3>1. What is new</h3><p>Write a short update here.</p><h3>2. Tip of the month</h3><p>Share something useful.</p><h3>3. Coming up</h3><p>Let readers know what is next.</p>' . $btn('Visit our website')],
            ['key' => 'thanks', 'name' => 'Thank you / follow-up', 'subject' => 'Thank you, {{first_name}}', 'html' => '<p>Hi {{first_name}},</p><p>Thank you for choosing {{my_business}}. It was a pleasure working with {{business_name}}.</p><p>If you have a minute, we would love a quick review.</p>' . $btn('Leave a review') . '<p>Just reply to this email if you need anything.</p>'],
        ];
    }

    // ---------------- page ----------------
    public function index()
    {
        $uid = $this->uid();
        if (Demo::on()) return response()->view('users.gvd-email-campaign', ['d' => self::demoData()])->withHeaders(self::NC);
        $snd = CE::sender($uid);
        $camps = DB::table('ec_campaigns')->where('user_id', $uid)->orderByDesc('id')->limit(200)->get()->map(function ($c) { $a = (array) $c; $a['stats'] = CE::stats($c->id); return $a; })->all();
        $tags = [];
        foreach (DB::table('ec_contacts')->where('user_id', $uid)->whereNotNull('tags')->pluck('tags') as $t) foreach (explode(',', $t) as $x) $tags[$x] = ($tags[$x] ?? 0) + 1;
        ksort($tags);
        $d = ['demo' => false, 'sender' => $snd, 'campaigns' => $camps, 'tags' => $tags,
            'counts' => DB::table('ec_contacts')->where('user_id', $uid)->selectRaw("COUNT(*) total, SUM(status='active') active, SUM(status<>'active') inactive")->first(),
            'suppressed' => DB::table('ec_suppressions')->where('user_id', $uid)->count(),
            'templates' => DB::table('ec_templates')->where('user_id', $uid)->orderByDesc('id')->limit(100)->get(['id', 'name', 'body_html'])->all(),
            'smtp' => ($x = DB::table('ec_smtp')->where('user_id', $uid)->first(['provider', 'host', 'port', 'encryption', 'username', 'from_name', 'from_email', 'verified_at', 'last_error'])) ? (array) $x : null, 'presets' => CE::PRESETS,
            'starters' => self::starters(), 'limit' => CE::limitFor($uid), 'today' => CE::sentToday($uid), 'blocked' => CE::blocked($uid), 'live' => (bool) config('ec.sending'),
            'maxMb' => (int) (config('ec.max_csv_bytes') / 1048576), 'maxRows' => (int) config('ec.max_csv_rows')];
        return response()->view('users.gvd-email-campaign', ['d' => $d])->withHeaders(self::NC);
    }

    // ---------------- contacts ----------------
    public function contacts(Request $r)
    {
        if (Demo::on()) { $c = self::demoContacts(); return $this->j(['rows' => $c, 'total' => count($c), 'page' => 1, 'pages' => 1]); }
        $uid = $this->uid();
        $q = DB::table('ec_contacts')->where('user_id', $uid);
        if ($s = trim((string) $r->query('q'))) $q->where(fn ($w) => $w->where('email', 'like', '%' . addcslashes($s, '%_\\') . '%')->orWhere('first_name', 'like', '%' . addcslashes($s, '%_\\') . '%')->orWhere('last_name', 'like', '%' . addcslashes($s, '%_\\') . '%')->orWhere('business_name', 'like', '%' . addcslashes($s, '%_\\') . '%'));
        if ($t = trim((string) $r->query('tag'))) $q->whereRaw('FIND_IN_SET(?, tags)', [$t]);
        if (in_array($st = (string) $r->query('status'), ['active', 'unsubscribed', 'bounced', 'complained'], true)) $q->where('status', $st);
        $total = (clone $q)->count(); $per = 50; $page = max(1, (int) $r->query('page', 1));
        $rows = $q->orderByDesc('id')->forPage($page, $per)->get(['id', 'email', 'first_name', 'last_name', 'business_name', 'tags', 'status', 'created_at']);
        return $this->j(['rows' => $rows, 'total' => $total, 'page' => $page, 'pages' => max(1, (int) ceil($total / $per))]);
    }

    public function contactSave(Request $r)
    {
        if (Demo::on()) return Demo::readonly();
        if (!$this->limit('c', 120)) return $this->err('Too many changes. Wait a minute.', 429);
        $uid = $this->uid();
        $email = CE::normEmail((string) $r->input('email'));
        if (!$email) return $this->err('Enter a valid email address.');
        $row = ['email' => $email, 'first_name' => mb_substr(trim(strip_tags((string) $r->input('first_name'))), 0, 100) ?: null, 'last_name' => mb_substr(trim(strip_tags((string) $r->input('last_name'))), 0, 100) ?: null,
            'business_name' => mb_substr(trim(strip_tags((string) $r->input('business_name'))), 0, 200) ?: null, 'tags' => CE::normTags((string) $r->input('tags')), 'updated_at' => now()];
        $id = (int) $r->input('id');
        if ($id) {
            $own = DB::table('ec_contacts')->where('id', $id)->where('user_id', $uid)->first();
            if (!$own) return $this->err('Contact not found.', 404);
            if (DB::table('ec_contacts')->where('user_id', $uid)->where('email', $email)->where('id', '<>', $id)->exists()) return $this->err('Another contact already uses that email.');
            DB::table('ec_contacts')->where('id', $id)->where('user_id', $uid)->update($row);
        } else {
            if (DB::table('ec_contacts')->where('user_id', $uid)->count() >= config('ec.max_contacts')) return $this->err('You have reached the contact limit. Contact support to raise it.');
            if (DB::table('ec_contacts')->where('user_id', $uid)->where('email', $email)->exists()) return $this->err('That email is already in your contacts.');
            $id = DB::table('ec_contacts')->insertGetId($row + ['user_id' => $uid, 'status' => CE::isSuppressed($uid, $email) ? 'unsubscribed' : 'active', 'source' => 'manual', 'created_at' => now()]);
            \App\Support\CustomerSequences::fire($uid, 'contact_added', [$id]);
            if ($row['tags']) \App\Support\CustomerSequences::fire($uid, 'tag_added', [$id], $row['tags']);
        }
        return $this->j(['ok' => true, 'id' => $id]);
    }

    public function contactBulk(Request $r)
    {
        if (Demo::on()) return Demo::readonly();
        $uid = $this->uid();
        $ids = array_slice(array_filter(array_map('intval', (array) $r->input('ids'))), 0, 5000);
        if (!$ids) return $this->err('Select at least one contact.');
        $q = fn () => DB::table('ec_contacts')->where('user_id', $uid)->whereIn('id', $ids);
        switch ((string) $r->input('action')) {
            case 'delete': $n = $q()->delete(); break;
            case 'tag':
                $tag = CE::normTags((string) $r->input('tag')); if (!$tag) return $this->err('Enter a tag.');
                $n = 0; foreach ($q()->get(['id', 'tags']) as $c) { $n += DB::table('ec_contacts')->where('id', $c->id)->where('user_id', $uid)->update(['tags' => CE::normTags(($c->tags ? $c->tags . ',' : '') . $tag), 'updated_at' => now()]); } \App\Support\CustomerSequences::fire($uid, 'tag_added', $ids, $tag); break;
            case 'untag':
                $tag = CE::normTags((string) $r->input('tag'));
                $n = 0; foreach ($q()->get(['id', 'tags']) as $c) { $n += DB::table('ec_contacts')->where('id', $c->id)->where('user_id', $uid)->update(['tags' => CE::normTags(array_diff(explode(',', (string) $c->tags), [$tag])), 'updated_at' => now()]); } break;
            case 'unsubscribe':
                $n = 0; foreach ($q()->pluck('email') as $e) { DB::table('ec_suppressions')->insertOrIgnore(['user_id' => $uid, 'email' => $e, 'reason' => 'manual', 'created_at' => now(), 'updated_at' => now()]); $n++; }
                $q()->update(['status' => 'unsubscribed', 'updated_at' => now()]); break;
            default: return $this->err('Unknown action.');
        }
        return $this->j(['ok' => true, 'n' => $n]);
    }

    /** Step 1 of import: upload, sniff headers, guess mapping. Parsed rows are cached 30 min, keyed per user. */
    public function importPreview(Request $r)
    {
        if (Demo::on()) return Demo::readonly();
        if (!$this->limit('imp', 20, 600)) return $this->err('Too many uploads. Try again in a few minutes.', 429);
        $f = $r->file('file');
        if (!$f || !$f->isValid()) return $this->err('Choose a CSV file.');
        if ($f->getSize() > config('ec.max_csv_bytes')) return $this->err('That file is too large. The limit is ' . (int) (config('ec.max_csv_bytes') / 1048576) . ' MB.');
        if (!in_array(strtolower($f->getClientOriginalExtension()), ['csv', 'txt'], true)) return $this->err('Upload a .csv file (in Excel or Google Sheets: File > Save as / Download > CSV).');
        $text = file_get_contents($f->getRealPath());
        $text = preg_replace('/^\xEF\xBB\xBF/', '', (string) $text);
        if (!mb_check_encoding($text, 'UTF-8')) $text = mb_convert_encoding($text, 'UTF-8', 'Windows-1252');
        $first = strtok($text, "\n");
        $delim = substr_count($first, ';') > substr_count($first, ',') ? ';' : (substr_count($first, "\t") > substr_count($first, ',') ? "\t" : ',');
        $fh = fopen('php://temp', 'r+'); fwrite($fh, $text); rewind($fh);
        $rows = []; while (($c = fgetcsv($fh, 0, $delim)) !== false) { if ($c === [null]) continue; $rows[] = array_map(fn ($x) => trim((string) $x), $c); if (count($rows) > config('ec.max_csv_rows') + 1) break; }
        fclose($fh);
        if (!$rows) return $this->err('That file is empty.');
        $tooMany = count($rows) > config('ec.max_csv_rows') + 1;
        // header detection: first row has no email address in it
        $hasHeader = !collect($rows[0])->contains(fn ($x) => (bool) CE::normEmail($x));
        $cols = max(array_map('count', array_slice($rows, 0, 50)));
        $hdr = $hasHeader ? $rows[0] : array_map(fn ($i) => 'Column ' . ($i + 1), range(0, $cols - 1));
        $data = $hasHeader ? array_slice($rows, 1) : $rows;
        $guess = [];
        foreach ($hdr as $i => $h) {
            $k = strtolower(preg_replace('/[^a-z]/i', '', $h));
            $g = match (true) { str_contains($k, 'email') || $k === 'mail' => 'email', in_array($k, ['first', 'firstname', 'fname', 'givenname', 'name'], true) => 'first_name',
                in_array($k, ['last', 'lastname', 'lname', 'surname', 'familyname'], true) => 'last_name', str_contains($k, 'business') || str_contains($k, 'company') || $k === 'organization' => 'business_name',
                str_contains($k, 'tag') || str_contains($k, 'group') || str_contains($k, 'list') => 'tags', default => '' };
            if ($g === '' && !$hasHeader && isset($data[0][$i]) && CE::normEmail($data[0][$i])) $g = 'email';
            if ($g !== '' && in_array($g, $guess, true)) $g = '';
            $guess[$i] = $g;
        }
        $tok = bin2hex(random_bytes(16));
        Cache::put("ec-imp:{$this->uid()}:$tok", array_slice($data, 0, config('ec.max_csv_rows')), 1800);
        return $this->j(['ok' => true, 'token' => $tok, 'headers' => $hdr, 'guess' => $guess, 'sample' => array_slice($data, 0, 5), 'rows' => min(count($data), config('ec.max_csv_rows')),
            'header' => $hasHeader, 'truncated' => $tooMany]);
    }

    public function importCommit(Request $r)
    {
        if (Demo::on()) return Demo::readonly();
        $uid = $this->uid();
        $data = Cache::get("ec-imp:$uid:" . preg_replace('/[^a-f0-9]/', '', (string) $r->input('token')));
        if (!is_array($data)) return $this->err('This upload expired. Please upload the file again.');
        $map = (array) $r->input('map'); $col = [];
        foreach ($map as $i => $f) if (in_array($f, ['email', 'first_name', 'last_name', 'business_name', 'tags'], true) && !isset($col[$f])) $col[$f] = (int) $i;
        if (!isset($col['email'])) return $this->err('Pick which column holds the email address.');
        if (!$r->boolean('consent')) return $this->err('Please confirm these contacts agreed to hear from you.');
        $extraTag = CE::normTags((string) $r->input('tag'));
        $update = $r->boolean('update');
        $have = DB::table('ec_contacts')->where('user_id', $uid)->pluck('id', 'email')->all();
        $sup = DB::table('ec_suppressions')->where('user_id', $uid)->pluck('email')->flip()->all();
        $room = config('ec.max_contacts') - count($have);
        $st = ['added' => 0, 'updated' => 0, 'invalid' => 0, 'duplicates' => 0, 'suppressed' => 0, 'over_limit' => 0, 'invalid_rows' => []]; $seen = []; $ins = [];
        foreach ($data as $n => $row) {
            $g = fn ($f) => isset($col[$f]) ? mb_substr(trim(strip_tags((string) ($row[$col[$f]] ?? ''))), 0, $f === 'business_name' ? 200 : 100) : '';
            $e = CE::normEmail($g('email'));
            if (!$e) { $st['invalid']++; if (count($st['invalid_rows']) < 10) $st['invalid_rows'][] = ['row' => $n + 1, 'value' => mb_substr($g('email'), 0, 60)]; continue; }
            if (isset($seen[$e])) { $st['duplicates']++; continue; } $seen[$e] = 1;
            $tags = CE::normTags(trim($g('tags') . ',' . $extraTag, ','));
            $rec = ['first_name' => $g('first_name') ?: null, 'last_name' => $g('last_name') ?: null, 'business_name' => $g('business_name') ?: null];
            if (isset($have[$e])) {
                if ($update) {
                    $cur = DB::table('ec_contacts')->where('id', $have[$e])->where('user_id', $uid)->first(['tags']);
                    DB::table('ec_contacts')->where('id', $have[$e])->where('user_id', $uid)->update(array_filter($rec) + ['tags' => CE::normTags(trim(($cur->tags ?? '') . ',' . $tags, ',')), 'updated_at' => now()]);
                    $st['updated']++;
                } else $st['duplicates']++;
                continue;
            }
            if ($room <= 0) { $st['over_limit']++; continue; }
            if (isset($sup[$e])) $st['suppressed']++;
            $ins[] = $rec + ['user_id' => $uid, 'email' => $e, 'tags' => $tags, 'status' => isset($sup[$e]) ? 'unsubscribed' : 'active', 'source' => 'csv', 'created_at' => now(), 'updated_at' => now()];
            $room--; $st['added']++;
        }
        foreach (array_chunk($ins, 500) as $ch) DB::table('ec_contacts')->insertOrIgnore($ch);
        if ($ins) { $new = DB::table('ec_contacts')->where('user_id', $uid)->whereIn('email', array_column($ins, 'email'))->where('source', 'csv')->get(['id', 'tags']);
            \App\Support\CustomerSequences::fire($uid, 'contact_added', $new->pluck('id')->all());
            foreach ($new->groupBy('tags') as $tg => $grp) if ($tg) \App\Support\CustomerSequences::fire($uid, 'tag_added', $grp->pluck('id')->all(), (string) $tg); }
        Cache::forget("ec-imp:$uid:" . $r->input('token'));
        return $this->j(['ok' => true] + $st);
    }

    public function export()
    {
        $uid = $this->uid();
        $rows = Demo::on() ? collect(self::demoContacts())->map(fn ($x) => (object) $x) : DB::table('ec_contacts')->where('user_id', $uid)->orderBy('id')->get();
        $out = fopen('php://temp', 'r+'); fputcsv($out, ['email', 'first_name', 'last_name', 'business_name', 'tags', 'status']);
        foreach ($rows as $c) fputcsv($out, array_map(fn ($v) => preg_match('/^[=+\-@]/', (string) $v) ? "'" . $v : $v, [$c->email, $c->first_name, $c->last_name, $c->business_name, $c->tags, $c->status]));
        rewind($out);
        return response(stream_get_contents($out), 200, self::NC + ['Content-Type' => 'text/csv; charset=UTF-8', 'Content-Disposition' => 'attachment; filename="contacts.csv"']);
    }

    public function sampleCsv()
    {
        return response("email,first_name,last_name,business_name,tags\nmaria@sample-bakery.com,Maria,Lopez,Sample Bakery,customers\njames@sample-plumbing.com,James,Lee,Lee Plumbing,\"leads,vip\"\n", 200,
            ['Content-Type' => 'text/csv; charset=UTF-8', 'Content-Disposition' => 'attachment; filename="contacts-sample.csv"']);
    }

    // ---------------- campaigns ----------------
    private function audience(Request $r): array
    {
        $tags = array_values(array_filter(array_map(fn ($t) => CE::normTags($t), (array) $r->input('tags'))));
        return $tags ? ['tags' => $tags] : ['all' => true];
    }

    public function audienceCount(Request $r)
    {
        if (Demo::on()) return $this->j(['count' => $this->audience($r)['tags'] ?? null ? 18 : 42]);
        return $this->j(['count' => CE::audienceQuery($this->uid(), $this->audience($r))->count()]);
    }

    public function save(Request $r)
    {
        if (Demo::on()) return Demo::readonly();
        if (!$this->limit('s', 60)) return $this->err('Too many saves. Wait a minute.', 429);
        $uid = $this->uid();
        $name = mb_substr(trim(strip_tags((string) $r->input('name'))), 0, 150);
        if ($name === '') return $this->err('Give your campaign a name.');
        $row = ['name' => $name, 'subject' => mb_substr(trim(strip_tags((string) $r->input('subject'))), 0, 200) ?: null, 'preview' => mb_substr(trim(strip_tags((string) $r->input('preview'))), 0, 200) ?: null,
            'body_html' => CE::sanitize((string) $r->input('body_html')), 'audience' => json_encode($this->audience($r)), 'updated_at' => now()];
        $id = (int) $r->input('id');
        if ($id) {
            $c = $this->camp($id); if (!$c) return $this->err('Campaign not found.', 404);
            if (!in_array($c->status, ['DRAFT', 'SCHEDULED'], true)) return $this->err('This campaign has started sending, so it can no longer be edited. Duplicate it instead.');
            DB::table('ec_campaigns')->where('id', $id)->where('user_id', $uid)->update($row);
        } else $id = DB::table('ec_campaigns')->insertGetId($row + ['user_id' => $uid, 'status' => 'DRAFT', 'created_at' => now()]);
        if ($r->filled('template_name')) DB::table('ec_templates')->insert(['user_id' => $uid, 'name' => mb_substr(trim(strip_tags((string) $r->input('template_name'))), 0, 150), 'body_html' => $row['body_html'], 'created_at' => now(), 'updated_at' => now()]);
        return $this->j(['ok' => true, 'id' => $id, 'body_html' => $row['body_html']]);
    }

    private function ready(object $c): ?string
    {
        if (!$c->subject) return 'Add a subject line.';
        if (trim(strip_tags((string) $c->body_html, '<img>')) === '') return 'Write your email first.';
        return null;
    }

    /** Test to self: always and only to the account's own login email. */
    public function test(Request $r)
    {
        if (Demo::on()) return Demo::readonly();
        $uid = $this->uid(); $c = $this->camp((int) $r->input('id'));
        if (!$c) return $this->err('Save the campaign first.', 404);
        if ($w = $this->ready($c)) return $this->err($w);
        if ($b = CE::blocked($uid)) return $this->err($b);
        if (!CE::smtp($uid)) return $this->j(['error' => 'Connect your email first. Campaigns are sent from your own mailbox. Open Sending email to connect it.', 'connect' => true], 422);
        if (!$this->limit('t', 10, 3600)) return $this->err('You can send up to 10 test emails an hour.', 429);
        $snd = CE::sender($uid);
        if (!filter_var($snd['email'], FILTER_VALIDATE_EMAIL)) return $this->err('Your account has no valid email for tests. Update your profile.');
        $sample = DB::table('ec_contacts')->where('user_id', $uid)->where('status', 'active')->whereNotNull('first_name')->orderBy('id')->first() ?: (object) ['first_name' => 'Maria', 'last_name' => 'Lopez', 'business_name' => 'Sample Bakery'];
        $sample = (object) ['email' => $snd['email'], 'first_name' => $sample->first_name, 'last_name' => $sample->last_name, 'business_name' => $sample->business_name];
        try { [$s, $h, $u] = CE::render($c, $sample, $snd); CE::deliver($snd, $snd['email'], '[Test] ' . $s, $h, $u); }
        catch (\Throwable $e) { \Log::warning('ec test failed', ['e' => mb_substr($e->getMessage(), 0, 200)]); return $this->err('The test email could not be sent. Please try again.'); }
        return $this->j(['ok' => true, 'to' => $snd['email']]);
    }

    public function preview(Request $r)
    {
        $uid = $this->uid(); $snd = Demo::on() ? ['uid' => $uid, 'email' => 'owner@example.com', 'business' => 'Demo Business LLC', 'address' => '100 Main St, Sample City, NY 10001'] : CE::sender($uid);
        $c = (object) ['subject' => (string) $r->input('subject'), 'preview' => (string) $r->input('preview'), 'body_html' => (string) $r->input('body_html')];
        $sample = (object) ['email' => 'maria@sample-bakery.com', 'first_name' => 'Maria', 'last_name' => 'Lopez', 'business_name' => 'Sample Bakery'];
        [$s, $h] = CE::render($c, $sample, $snd);
        $box = Demo::on() ? (object) ['from_name' => 'Demo Business LLC', 'from_email' => 'owner@example.com'] : CE::smtp($uid);
        return $this->j(['subject' => $s, 'html' => $h, 'from' => $box ? (($box->from_name ?: $snd['business']) . ' <' . $box->from_email . '>') : 'Not connected yet: connect your email', 'reply_to' => $box->from_email ?? '—']);
    }

    public function schedule(Request $r)
    {
        if (Demo::on()) return Demo::readonly();
        $uid = $this->uid(); $c = $this->camp((int) $r->input('id'));
        if (!$c) return $this->err('Campaign not found.', 404);
        if (!in_array($c->status, ['DRAFT', 'SCHEDULED'], true)) return $this->err('This campaign was already sent.');
        if ($w = $this->ready($c)) return $this->err($w);
        if ($b = CE::blocked($uid)) return $this->err($b);
        if (!CE::smtp($uid)) return $this->j(['error' => 'Connect your email first. Campaigns are sent from your own mailbox. Open Sending email to connect it.', 'connect' => true], 422);
        if ($r->input('confirm') !== 'SEND') return $this->err('Please confirm before sending.');
        $n = CE::audienceQuery($uid, json_decode((string) $c->audience, true) ?: ['all' => true])->count();
        if (!$n) return $this->err('No active contacts match this audience.');
        $at = now();
        if (trim((string) $r->input('at')) !== '') {
            try { $at = \Carbon\Carbon::parse((string) $r->input('at'), CE::TZ)->utc(); } catch (\Throwable $e) { return $this->err('That date is not valid.'); }
            if ($at->lt(now()->subMinutes(2))) return $this->err('Pick a time in the future.');
            if ($at->gt(now()->addDays(90))) return $this->err('Schedule up to 90 days ahead.');
        }
        DB::table('ec_campaigns')->where('id', $c->id)->where('user_id', $uid)->update(['status' => 'SCHEDULED', 'scheduled_at' => $at, 'pause_reason' => null, 'updated_at' => now()]);
        $lim = CE::limitFor($uid);
        return $this->j(['ok' => true, 'recipients' => $n, 'days' => (int) ceil($n / max(1, $lim)), 'limit' => $lim, 'live' => (bool) config('ec.sending')]);
    }

    public function action(Request $r)
    {
        if (Demo::on()) return Demo::readonly();
        $uid = $this->uid(); $c = $this->camp((int) $r->input('id'));
        if (!$c) return $this->err('Campaign not found.', 404);
        $u = fn ($x) => DB::table('ec_campaigns')->where('id', $c->id)->where('user_id', $uid)->update($x + ['updated_at' => now()]);
        switch ((string) $r->input('action')) {
            case 'pause': if (!in_array($c->status, ['SCHEDULED', 'SENDING'], true)) return $this->err('Nothing to pause.'); $u(['status' => 'PAUSED', 'pause_reason' => 'Paused by you.']); break;
            case 'resume':
                if ($c->status !== 'PAUSED') return $this->err('This campaign is not paused.');
                if ($b = CE::blocked($uid)) return $this->err($b);
                if (!CE::smtp($uid)) return $this->j(['error' => 'Connect your email first. Campaigns are sent from your own mailbox. Open Sending email to connect it.', 'connect' => true], 422);
                $u(['status' => $c->started_at ? 'SENDING' : 'SCHEDULED', 'pause_reason' => null, 'scheduled_at' => $c->scheduled_at ?: now()]); break;
            case 'unschedule': if ($c->status !== 'SCHEDULED') return $this->err('Not scheduled.'); $u(['status' => 'DRAFT', 'scheduled_at' => null]); break;
            case 'cancel':
                if (!in_array($c->status, ['SCHEDULED', 'SENDING', 'PAUSED'], true)) return $this->err('Nothing to cancel.');
                DB::table('ec_recipients')->where('campaign_id', $c->id)->where('status', 'pending')->update(['status' => 'skipped', 'reason' => 'cancelled', 'updated_at' => now()]);
                $u(['status' => $c->started_at ? 'DONE' : 'DRAFT', 'finished_at' => $c->started_at ? now() : null, 'pause_reason' => $c->started_at ? 'Cancelled by you.' : null]); break;
            case 'duplicate':
                $id = DB::table('ec_campaigns')->insertGetId(['user_id' => $uid, 'name' => mb_substr('Copy of ' . $c->name, 0, 150), 'subject' => $c->subject, 'preview' => $c->preview, 'body_html' => $c->body_html, 'audience' => $c->audience, 'status' => 'DRAFT', 'created_at' => now(), 'updated_at' => now()]);
                return $this->j(['ok' => true, 'id' => $id]);
            case 'delete':
                if (in_array($c->status, ['SENDING'], true)) return $this->err('Pause or cancel the campaign before deleting it.');
                DB::table('ec_recipients')->where('campaign_id', $c->id)->delete(); DB::table('ec_campaigns')->where('id', $c->id)->where('user_id', $uid)->delete(); break;
            default: return $this->err('Unknown action.');
        }
        return $this->j(['ok' => true]);
    }

    public function report(Request $r, $id)
    {
        if (Demo::on()) return $this->j(['stats' => ['total' => 42, 'sent' => 40, 'failed' => 1, 'skipped' => 1, 'pending' => 0, 'sending' => 0, 'opened' => 19], 'rows' => array_map(fn ($c) => ['email' => $c['email'], 'status' => 'sent', 'reason' => null, 'sent_at' => now()->subDay()->toDateTimeString(), 'opened_at' => null], array_slice(self::demoContacts(), 0, 8))]);
        $c = $this->camp((int) $id); if (!$c) return $this->err('Campaign not found.', 404);
        $q = DB::table('ec_recipients')->where('campaign_id', $c->id);
        if (in_array($st = (string) $r->query('status'), ['pending', 'sent', 'failed', 'skipped'], true)) $q->where('status', $st);
        // trk: clicks + per-recipient opens/clicks
        $q->from("ec_recipients as r")->leftJoin("email_track_sends as ts", fn ($j) => $j->on("ts.send_id", "=", "r.id")->where("ts.type", "ec"))
            ->leftJoin("email_track_events as o", fn ($j) => $j->on("o.track_send_id", "=", "ts.id")->where("o.event", "open"))
            ->leftJoin(DB::raw("(SELECT track_send_id, SUM(count) clicks, MIN(first_at) first_click FROM email_track_events WHERE event='click' GROUP BY track_send_id) k"), "k.track_send_id", "=", "ts.id");
        $st = CE::stats($c->id) + (\App\Support\Tracking::summary("ec", [$c->id], (int) $c->user_id)[$c->id] ?? ["unique_clicks" => 0, "clicks" => 0, "tracked" => 0, "unique_opens" => 0]);
        return $this->j(["stats" => $st, "rows" => $q->orderByDesc("r.updated_at")->limit(500)->get(["r.email", "r.status", "r.reason", "r.sent_at", "r.opened_at", "o.count as open_count", "o.last_at as last_open", "k.clicks", "k.first_click"])]);
    }

    public function templateDelete(Request $r)
    {
        if (Demo::on()) return Demo::readonly();
        DB::table('ec_templates')->where('id', (int) $r->input('id'))->where('user_id', $this->uid())->delete();
        return $this->j(['ok' => true]);
    }

    // ---------------- customer's own mailbox (SMTP) ----------------
    /** Save + verify: connects, logs in, and sends a test to the from address. Only saved when the test succeeds. Password is encrypted and never returned. */
    public function smtpSave(Request $r)
    {
        if (Demo::on()) return Demo::readonly();
        if (!$this->limit('smtp', 8, 600)) return $this->err('Too many attempts. Try again in 10 minutes.', 429);
        $uid = $this->uid();
        $prov = array_key_exists((string) $r->input('provider'), CE::PRESETS) ? (string) $r->input('provider') : 'other';
        $host = strtolower(trim((string) $r->input('host'))); $port = (int) $r->input('port'); $enc = $r->input('encryption') === 'ssl' ? 'ssl' : 'tls';
        $user = mb_substr(trim((string) $r->input('username')), 0, 190);
        $from = CE::normEmail((string) $r->input('from_email')); $name = mb_substr(trim(preg_replace('/[\r\n<>"]+/', ' ', strip_tags((string) $r->input('from_name')))), 0, 100);
        $pw = (string) $r->input('password');
        $old = DB::table('ec_smtp')->where('user_id', $uid)->first();
        if ($pw === '' && $old) $enc_pw = $old->password_enc; elseif ($pw === '') return $this->err('Enter the password (for Gmail, Yahoo and Microsoft use an app password).'); else $enc_pw = \Illuminate\Support\Facades\Crypt::encryptString(mb_substr($pw, 0, 300));
        if (!$from) return $this->err('Enter the email address you send from.');
        if ($user === '') return $this->err('Enter the username (usually your full email address).');
        if ($w = CE::hostOk($host, $port)) return $this->err($w);
        $box = (object) ['user_id' => $uid, 'provider' => $prov, 'host' => $host, 'port' => $port, 'encryption' => $enc, 'username' => $user, 'password_enc' => $enc_pw, 'from_name' => $name ?: CE::sender($uid)['business'], 'from_email' => $from];
        $snd = CE::sender($uid);
        try {
            $html = '<p>Your email is connected to Global Voice Direct email campaigns.</p><p>Campaigns will now be sent from <b>' . e($from) . '</b>.</p>';
            CE::deliver($snd, $from, 'Your email is connected', $html, CE::unsubscribeUrl($uid, $from), $box);
        } catch (\Throwable $e) {
            $m = $e->getMessage();
            $hint = preg_match('/535|534|auth|credential|password|Username/i', $m) ? 'The server rejected the username or password. For Gmail, Yahoo or Microsoft 365, create an app password and use it here (your normal password will not work with 2-step verification).'
                : (preg_match('/timed? ?out|Connection could not be established|refused/i', $m) ? 'Could not reach the mail server. Check the server name, port and security setting.' : 'The connection test failed. Check your settings and try again.');
            \Log::info('ec smtp verify failed', ['uid' => $uid, 'host' => $host, 'code' => preg_match('/\b([45]\d\d)\b/', $m, $mm) ? $mm[1] : get_class($e)]);
            if ($old) DB::table('ec_smtp')->where('user_id', $uid)->update(['last_error' => mb_substr($hint, 0, 255), 'updated_at' => now()]);
            return $this->err($hint);
        }
        $row = (array) $box; unset($row['user_id']);
        $row += ['daily_limit' => CE::PRESETS[$prov][4], 'verified_at' => now(), 'last_error' => null, 'updated_at' => now()];
        DB::table('ec_smtp')->updateOrInsert(['user_id' => $uid], $row + ['created_at' => $old->created_at ?? now()]);
        return $this->j(['ok' => true, 'from' => $from]);
    }

    public function smtpDelete()
    {
        if (Demo::on()) return Demo::readonly();
        $uid = $this->uid();
        DB::table('ec_smtp')->where('user_id', $uid)->delete();
        DB::table('ec_campaigns')->where('user_id', $uid)->whereIn('status', ['SCHEDULED', 'SENDING'])->update(['status' => 'PAUSED', 'pause_reason' => 'Your email was disconnected. Connect it again to resume.', 'updated_at' => now()]);
        return $this->j(['ok' => true]);
    }

    // ---------------- demo (sample login only) ----------------
    public static function demoContacts(): array
    {
        $n = [['Maria', 'Lopez', 'Sample Bakery', 'customers,vip'], ['James', 'Lee', 'Lee Plumbing', 'customers'], ['Aisha', 'Khan', 'Bright Dental', 'leads'], ['Tom', 'Rivera', 'Rivera Auto', 'customers'],
            ['Emily', 'Chen', 'Chen Accounting', 'leads,newsletter'], ['Daniel', 'Brooks', 'Brooks Realty', 'newsletter'], ['Sofia', 'Patel', 'Patel Pharmacy', 'customers'], ['Chris', 'Adams', 'Adams Fitness', 'leads']];
        return array_map(fn ($x, $i) => ['id' => 9000 + $i, 'email' => strtolower($x[0]) . '@example.com', 'first_name' => $x[0], 'last_name' => $x[1], 'business_name' => $x[2], 'tags' => $x[3], 'status' => $i === 7 ? 'unsubscribed' : 'active', 'created_at' => now()->subDays(10 - $i)->toDateTimeString()], $n, array_keys($n));
    }

    public static function demoData(): array
    {
        $st = fn ($t, $s, $f, $k, $o, $p = 0) => ['total' => $t, 'sent' => $s, 'failed' => $f, 'skipped' => $k, 'pending' => $p, 'sending' => 0, 'opened' => $o];
        $tpl = self::starters();
        return ['demo' => true, 'sender' => ['uid' => 0, 'email' => 'owner@example.com', 'business' => 'Demo Business LLC', 'address' => '100 Main St, Sample City, NY 10001'],
            'campaigns' => [
                ['id' => 901, 'name' => 'November newsletter', 'subject' => 'This month at Demo Business LLC', 'preview' => 'Three quick updates', 'body_html' => $tpl[2]['html'], 'audience' => '{"tags":["newsletter"]}', 'status' => 'DONE', 'scheduled_at' => now()->subDays(3)->toDateTimeString(), 'started_at' => now()->subDays(3)->toDateTimeString(), 'pause_reason' => null, 'stats' => $st(42, 40, 1, 1, 19)],
                ['id' => 902, 'name' => 'Holiday offer', 'subject' => 'A special offer for you, {{first_name}}', 'preview' => '20% off this week', 'body_html' => $tpl[1]['html'], 'audience' => '{"all":true}', 'status' => 'SCHEDULED', 'scheduled_at' => now()->addDays(2)->setTime(14, 0)->utc()->toDateTimeString(), 'started_at' => null, 'pause_reason' => null, 'stats' => $st(0, 0, 0, 0, 0)],
                ['id' => 903, 'name' => 'Thank-you follow-up', 'subject' => 'Thank you, {{first_name}}', 'preview' => '', 'body_html' => $tpl[3]['html'], 'audience' => '{"tags":["customers"]}', 'status' => 'DRAFT', 'scheduled_at' => null, 'started_at' => null, 'pause_reason' => null, 'stats' => $st(0, 0, 0, 0, 0)],
            ],
            'tags' => ['customers' => 4, 'leads' => 3, 'newsletter' => 2, 'vip' => 1], 'counts' => (object) ['total' => 8, 'active' => 7, 'inactive' => 1], 'suppressed' => 1,
            'templates' => [], 'smtp' => ['provider' => 'gmail', 'host' => 'smtp.gmail.com', 'port' => 587, 'encryption' => 'tls', 'username' => 'owner@example.com', 'from_name' => 'Demo Business LLC', 'from_email' => 'owner@example.com', 'verified_at' => now()->subDays(5)->toDateTimeString(), 'last_error' => null], 'presets' => CE::PRESETS, 'starters' => $tpl, 'limit' => 500, 'today' => 0, 'blocked' => null, 'live' => true, 'maxMb' => 5, 'maxRows' => 20000];
    }
}
