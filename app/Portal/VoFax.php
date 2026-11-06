<?php

namespace App\Portal;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Fax in/out for the Virtual Office pages, done the way the VO app does it, always as the signed-in tenant:
 *  - inbox: Voicemail/get_mailbox_message_list {mailbox:"INBOX"} on each of the tenant's mailboxes, keep x-portaum-message-type=fax
 *  - file:  Voicemail/get_mailbox_message_attachment {message_uid}
 *  - delete: Voicemail/delete_mailbox_messages {message_uids:[..]}
 *  - sent:  Account/get_xdr_list {i_account:fax<ic>, i_service:3, from_date, to_date, show_unsuccessful:1} + INBOX/Sent of that account
 *  - send:  Internal/send_fax {i_account:fax<ic>, destination:digits} + upload_file (".save" before the extension = keep in history)
 * Secrets (account passwords, voice sessions) are never cached, logged or returned.
 */
class VoFax extends VoData
{
    private array $umx = [];

    /** Voice-application login as one tenant account, using the same credential the VO app uses (h323_password). */
    private function umLogin(array $a): ?string
    {
        $ia = (int) $a['i_account'];
        if (isset($this->umx[$ia])) return $this->umx[$ia];
        $base = rtrim(config('greetings.um_rest'), '/');
        $dom = (string) ($a['um_domain'] ?? '') ?: (string) config('greetings.um_domain');
        foreach (array_unique(array_filter([(string) ($a['password'] ?? ''), (string) ($a['h323_password'] ?? '')])) as $pw) {
            $s = Http::asMultipart()->timeout(20)->connectTimeout(5)->post("$base/Session/login", [['name' => 'params', 'contents' => json_encode(['login' => (string) $a['id'], 'password' => $pw, 'domain' => $dom])]])->json();
            if (!empty($s['session_id'])) return $this->umx[$ia] = (string) $s['session_id'];
        }
        return null;
    }

    private function umPost(int $ia, string $method, array $params, bool $blob = false)
    {
        $a = $this->account($ia);
        $sid = $this->umLogin($a);
        if (!$sid) throw new \RuntimeException('voice login failed');
        $r = Http::asMultipart()->timeout(60)->connectTimeout(5)->post(rtrim(config('greetings.um_rest'), '/') . "/$method", [
            ['name' => 'auth_info', 'contents' => json_encode(['session_id' => $sid])], ['name' => 'params', 'contents' => json_encode((object) $params)]]);
        if ($blob) {
            $ct = (string) $r->header('Content-Type');
            if ($r->status() !== 200 || str_contains($ct, 'json') || strlen($r->body()) < 32) return null;
            return ['body' => $r->body(), 'type' => $ct];
        }
        $j = $r->json();
        if (!is_array($j)) throw new \RuntimeException('bad voice response');
        if (!empty($j['faultcode'])) throw new \RuntimeException('voice fault: ' . preg_replace('/[^\w.]/', '', (string) $j['faultcode']));
        return $j;
    }

    public function close(): void
    {
        $base = rtrim(config('greetings.um_rest'), '/');
        $sids = $this->umx;
        if ($sids) { try { Http::pool(function ($p) use ($sids, $base) { foreach ($sids as $ia => $sid) $p->as((string) $ia)->asMultipart()->timeout(5)->post("$base/Session/logout", [['name' => 'params', 'contents' => json_encode(['session_id' => $sid])]]); }); } catch (\Throwable $e) {} }
        $this->umx = [];
        parent::close();
    }

    public static function digits(string $s): string { return preg_replace('/\D/', '', $s); }

    /** "Name" <sip:5034367160@host> / 5034367160#... -> 5034367160 */
    public static function caller(string $from): string
    {
        if (preg_match('/#([0-9]+)/', $from, $m)) return $m[1];
        if (preg_match('/sip:\+?([0-9]{4,})@/i', $from, $m)) return $m[1];
        if (preg_match('/\+?([0-9]{7,})/', $from, $m)) return $m[1];
        return trim($from, "\" <>");
    }

    /** Open tenant accounts (with voice login) that can hold faxes, guarded lines excluded. Detailed records stay in memory only. */
    private function mailboxAccounts(): array
    {
        $list = [];
        foreach ($this->call('Account/get_account_list', ['i_customer' => $this->ic, 'limit' => 500, 'bill_status' => 'O'])['account_list'] ?? [] as $a) {
            if ((int) ($a['i_customer'] ?? 0) !== $this->ic || self::never($a)) continue;
            $list[(int) $a['i_account']] = $a;
        }
        if ($fa = $this->faxAccount()) $list[$fa['i_account']] = $fa;
        $info = [];
        foreach (array_chunk(array_keys($list), 12) as $chunk)
            $info += $this->many(array_combine($chunk, array_map(fn ($ia) => ['Account/get_account_info', ['i_account' => $ia, 'detailed_info' => 1]], $chunk)));
        $out = [];
        foreach ($info as $ia => $r) {
            $a = $r['account_info'] ?? null;
            if (!$a || (int) ($a['i_customer'] ?? 0) !== $this->ic || self::never($a)) continue;
            $out[(int) $ia] = $a;
        }
        return $out;
    }

    /** Pool: log in to every mailbox, then list one mailbox folder on each. Returns ia => messages[]. */
    private function listAll(array $accts, string $folder): array
    {
        $base = rtrim(config('greetings.um_rest'), '/');
        // VO logs in with the account's web password; some accounts (fax, newer lines) only have the SIP one.
        foreach (['password', 'h323_password'] as $fld) {
            $all = array_filter($accts, fn ($a) => !isset($this->umx[(int) $a['i_account']]) && (string) ($a[$fld] ?? '') !== '');
            foreach (array_chunk($all, 12, true) as $todo) {
            $res = Http::pool(function ($p) use ($todo, $base, $fld) {
                foreach ($todo as $ia => $a)
                    $p->as((string) $ia)->asMultipart()->timeout(20)->connectTimeout(5)->post("$base/Session/login", [['name' => 'params', 'contents' => json_encode(['login' => (string) $a['id'], 'password' => (string) $a[$fld], 'domain' => (string) ($a['um_domain'] ?? '') ?: (string) config('greetings.um_domain')])]]);
            });
            foreach ($todo as $ia => $a) { $x = $res[(string) $ia] ?? null; $sid = $x instanceof \Illuminate\Http\Client\Response ? ($x->json()['session_id'] ?? null) : null; if ($sid) $this->umx[(int) $ia] = (string) $sid; }
            }
        }
        $ok = array_filter($accts, fn ($a) => isset($this->umx[(int) $a['i_account']]));
        $out = array_fill_keys(array_map('intval', array_keys(array_diff_key($accts, $ok))), null);
        foreach (array_chunk($ok, 12, true) as $chunk) {
            $res = Http::pool(function ($p) use ($chunk, $base, $folder) {
                foreach ($chunk as $ia => $a) $p->as((string) $ia)->asMultipart()->timeout(25)->connectTimeout(5)->post("$base/Voicemail/get_mailbox_message_list", [
                    ['name' => 'auth_info', 'contents' => json_encode(['session_id' => $this->umx[(int) $ia]])], ['name' => 'params', 'contents' => json_encode(['mailbox' => $folder])]]);
            });
            foreach ($chunk as $ia => $a) { $x = $res[(string) $ia] ?? null; $j = $x instanceof \Illuminate\Http\Client\Response ? $x->json() : null; $out[(int) $ia] = is_array($j) && empty($j['faultcode']) ? ($j['messages'] ?? []) : null; }
        }
        return $out;
    }

    private static function did(array $a): string
    {
        $id = (string) ($a['id'] ?? '');
        if (preg_match('/^1?(\d{10})$/', self::digits($id), $m) && self::digits($id) === $id) return $m[1];
        return $id;
    }

    /** Detailed records for given tenant accounts (pooled), guarded lines and other tenants dropped. */
    private function infoFor(array $ias): array
    {
        $info = [];
        foreach (array_chunk(array_values(array_unique($ias)), 12) as $chunk)
            $info += $this->many(array_combine($chunk, array_map(fn ($ia) => ['Account/get_account_info', ['i_account' => $ia, 'detailed_info' => 1]], $chunk)));
        $out = [];
        foreach ($info as $ia => $r) { $a = $r['account_info'] ?? null; if ($a && (int) ($a['i_customer'] ?? 0) === $this->ic && !self::never($a)) $out[(int) $ia] = $a; }
        return $out;
    }

    /** Fax mailboxes of this tenant (i_account ids only, no secrets): kept 30 days, refreshed by a full scan at most once a day. */
    private function faxBoxKey(): string { return "gvd-fax:boxes:{$this->ic}"; }
    public function faxBoxes(): ?array { $c = Cache::get($this->faxBoxKey()); return is_array($c) ? $c : null; }
    public function needsRescan(): bool { $c = $this->faxBoxes(); return !$c || (time() - (int) $c['at']) > 86400; }

    /** Full scan of every mailbox; remembers which ones hold faxes (plus the fax account). */
    public function rescan(): array
    {
        if (!Cache::add("gvd-fax:scanlock:{$this->ic}", 1, 600)) return $this->faxBoxes()['ias'] ?? [];
        try {
            $accts = $this->mailboxAccounts();
            $lists = $this->listAll($accts, 'INBOX');
            $ias = [];
            foreach ($lists as $ia => $msgs) foreach ((array) $msgs as $m) if (strtolower((string) ($m['x-portaum-message-type'] ?? '')) === 'fax') { $ias[] = (int) $ia; break; }
            foreach ($accts as $ia => $a) if (preg_match('/^fax/i', (string) ($a['id'] ?? ''))) $ias[] = (int) $ia;
            $old = $this->faxBoxes()['ias'] ?? [];
            $ias = array_values(array_unique(array_merge($ias, $old)));
            Cache::put($this->faxBoxKey(), ['ias' => $ias, 'at' => time()], 30 * 86400);
            Cache::put("gvd-fax:in:{$this->ic}", $this->rows($accts, $lists), 180);
            return $ias;
        } finally { Cache::forget("gvd-fax:scanlock:{$this->ic}"); }
    }

    private function rows(array $accts, array $lists): array
    {
        $rows = []; $failed = 0;
        foreach ($lists as $ia => $msgs) {
            if ($msgs === null) { $failed++; continue; }
            foreach ($msgs as $m) {
                if (strtolower((string) ($m['x-portaum-message-type'] ?? '')) !== 'fax') continue;
                $flags = is_array($m['flags'] ?? null) ? implode(' ', $m['flags']) : (string) ($m['flags'] ?? '');
                $rows[] = ['ia' => (int) $ia, 'uid' => (string) $m['message_uid'], 'from' => self::caller((string) ($m['from'] ?? '')),
                    'to' => self::did($accts[$ia]), 'date' => (string) ($m['delivery_date'] ?? ''), 'seen' => str_contains($flags, 'Seen'),
                    'pages' => isset($m['x-portaum-fax-pages']) ? (int) $m['x-portaum-fax-pages'] : (isset($m['x-fax-pages']) ? (int) $m['x-fax-pages'] : null)];
            }
        }
        usort($rows, fn ($a, $b) => strtotime($b['date'] ?: '0') <=> strtotime($a['date'] ?: '0'));
        return ['rows' => $rows, 'mailboxes' => count($accts), 'unreadable' => $failed];
    }

    /** Incoming faxes, newest first. Checks only the known fax mailboxes (full scan only the very first time). Cached 3 min. */
    public function inbox(bool $fresh = false): array
    {
        $ck = "gvd-fax:in:{$this->ic}";
        if (!$fresh && is_array($c = Cache::get($ck))) return $c;
        $boxes = $this->faxBoxes();
        if (!$boxes) { $this->rescan(); if (is_array($c = Cache::get($ck))) return $c; $boxes = $this->faxBoxes() ?? ['ias' => []]; }
        $ias = $boxes['ias'];
        $accts = $this->infoFor($ias);
        $out = $this->rows($accts, $this->listAll($accts, 'INBOX'));
        Cache::put($ck, $out, 180);
        return $out;
    }

    public function forget(): void { Cache::forget("gvd-fax:in:{$this->ic}"); Cache::forget("gvd-fax:out:{$this->ic}"); }

    /** A message the tenant really has (from the cached/live list) — tenant scope check for file/delete. */
    public function ownsFax(int $ia, string $uid, string $kind = 'in'): bool
    {
        $rows = $kind === 'in' ? $this->inbox()['rows'] : $this->sent()['rows'];
        foreach ($rows as $r) if ((int) $r['ia'] === $ia && (string) $r['uid'] === $uid) return true;
        return false;
    }

    public function file(int $ia, string $uid): ?array
    {
        $f = $this->umPost($ia, 'Voicemail/get_mailbox_message_attachment', ['message_uid' => $uid], true);
        if (!$f) return null;
        $ct = strtolower($f['type']);
        if (preg_match('/boundary="?([^";]+)"?/i', $f['type'], $m)) {
            foreach (explode('--' . $m[1], $f['body']) as $part) {
                [$h, $b] = array_pad(preg_split("/\r?\n\r?\n/", $part, 2), 2, '');
                if (strlen($b) > 32 && !preg_match('/application\/json/i', $h)) { $f['body'] = rtrim($b, "\r\n"); $ct = preg_match('/content-type:\s*([^\r\n;]+)/i', $h, $m3) ? strtolower(trim($m3[1])) : ''; break; }
            }
        }
        if (str_starts_with($f['body'], '%PDF')) $ct = 'application/pdf';
        elseif (str_starts_with($f['body'], "II*\0") || str_starts_with($f['body'], "MM\0*")) $ct = 'image/tiff';
        return ['body' => $f['body'], 'type' => $ct ?: 'application/octet-stream', 'ext' => match (true) { str_contains($ct, 'pdf') => 'pdf', str_contains($ct, 'tif') => 'tif', default => 'bin' }];
    }

    public function delete(int $ia, string $uid): void
    {
        $this->umPost($ia, 'Voicemail/delete_mailbox_messages', ['message_uids' => [$uid]]);
        $this->forget();
    }

    /** The tenant's send-fax account, as the VO app finds it: id "fax<i_customer>". */
    public function faxAccount(): ?array
    {
        $ck = "gvd-fax:acct:{$this->ic}";
        if (is_array($c = Cache::get($ck))) return $c ?: null;
        $r = $this->findFaxAccount();
        Cache::put($ck, $r ?? [], 86400);
        return $r;
    }

    private function findFaxAccount(): ?array
    {
        $want = 'fax' . $this->ic;
        foreach ($this->call('Account/get_account_list', ['i_customer' => $this->ic, 'limit' => 500])['account_list'] ?? [] as $a)
            if ((string) ($a['id'] ?? '') === $want && (int) ($a['i_customer'] ?? 0) === $this->ic && !self::never($a))
                return ['i_account' => (int) $a['i_account'], 'id' => $want, 'cli' => (string) ($a['batch_name'] ?? '')];
        return null;
    }

    /** Sent faxes of the last 30 days (VO "Manage send fax"), with the stored copy when one was kept. Cached 3 min. */
    public function sent(bool $fresh = false): array
    {
        $ck = "gvd-fax:out:{$this->ic}";
        if (!$fresh && is_array($c = Cache::get($ck))) return $c;
        $fa = $this->faxAccount();
        if (!$fa) { $out = ['fax' => null, 'rows' => []]; Cache::put($ck, $out, 180); return $out; }
        $ia = $fa['i_account'];
        $fmt = fn ($t) => gmdate('Y-m-d H:i:s', $t);
        $x = $this->call('Account/get_xdr_list', ['i_account' => $ia, 'i_service' => 3, 'from_date' => $fmt(time() - 30 * 86400), 'to_date' => $fmt(time()), 'show_unsuccessful' => 1, 'limit' => 500])['xdr_list'] ?? [];
        $acct = $this->account($ia);
        $sent = $this->listAll([$ia => $acct], 'INBOX/Sent')[$ia] ?? [];
        $byConf = []; foreach ($sent ?? [] as $m) if (!empty($m['x-email2fax-h323-conf-id'])) $byConf[(string) $m['x-email2fax-h323-conf-id']] = (string) $m['message_uid'];
        // one row per call id: the dial leg (has a destination) wins over the upload leg (blank destination)
        $byCall = [];
        foreach ($x as $r) {
            $cid = (string) ($r['h323_conf_id'] ?? '') ?: ('x' . ($r['i_xdr'] ?? count($byCall)));
            if (!isset($byCall[$cid]) || ((string) ($byCall[$cid]['CLD'] ?? '') === '' && (string) ($r['CLD'] ?? '') !== '')) $byCall[$cid] = $r;
        }
        $rows = [];
        foreach ($byCall as $cid => $r) {
            if ((string) ($r['CLD'] ?? '') === '') continue; // upload only, never dialed
            $failed = !empty($r['failed']);
            $reason = trim((string) ($r['disconnect_reason'] ?? ''));
            $ct = strtotime((string) ($r['connect_time'] ?? '') . ' UTC'); $dt = strtotime((string) ($r['disconnect_time'] ?? '') . ' UTC');
            $rows[] = ['ia' => $ia, 'uid' => $byConf[(string) $cid] ?? null, 'from' => (string) ($r['CLI'] ?? ''), 'to' => (string) ($r['CLD'] ?? ''),
                'date' => (string) ($r['bill_time'] ?? $r['connect_time'] ?? ''), 'status' => $failed ? 'Failed' . ($reason !== '' ? ' – ' . $reason : '') : 'Sent',
                'failed' => $failed, 'secs' => ($ct && $dt && $dt >= $ct) ? $dt - $ct : null];
        }
        usort($rows, fn ($a, $b) => strtotime($b['date'] ?: '0') <=> strtotime($a['date'] ?: '0'));
        $out = ['fax' => ['i_account' => $ia], 'rows' => $rows];
        Cache::put($ck, $out, 180);
        return $out;
    }

    /** Internal/send_fax exactly as the VO app posts it (billing session, multipart, upload_file). $dry: payload only, nothing sent. */
    public function send(string $destination, string $path, string $name, string $mime, bool $save, bool $dry): array
    {
        $fa = $this->faxAccount();
        if (!$fa) throw new \DomainException('no fax account');
        $dest = self::digits($destination);
        $fname = $save ? preg_replace('/(\.[^.]*)?$/', '.save$1', $name, 1) : $name;
        $params = ['i_account' => $fa['i_account'], 'destination' => $dest];
        $desc = ['method' => 'Internal/send_fax', 'params' => $params, 'upload_file' => ['filename' => $fname, 'type' => $mime, 'bytes' => filesize($path)]];
        if ($dry) return ['dry' => true] + $desc;
        $k = $this->k; $hdr = [];
        if (($k['csrf_token'] ?? '') !== '') { $hdr['CSRF-Token'] = $k['csrf_token']; $hdr['X-CSRF-Token'] = $k['csrf_token']; }
        if (($k['access_token'] ?? '') !== '') $hdr['Authorization'] = 'Bearer ' . $k['access_token'];
        $r = Http::asMultipart()->withHeaders($hdr)->timeout(120)->connectTimeout(5)->post(rtrim($k['base'], '/') . '/Internal/send_fax', [
            ['name' => 'params', 'contents' => json_encode($params)],
            ['name' => 'auth_info', 'contents' => json_encode(['session_id' => $k['session_id']])],
            ['name' => 'upload_file', 'contents' => fopen($path, 'r'), 'filename' => $fname, 'headers' => ['Content-Type' => $mime]],
        ]);
        $j = $r->json();
        if (!is_array($j) || !empty($j['faultcode']) || $r->status() >= 400) throw new \RuntimeException('send fault ' . preg_replace('/[^\w.]/', '', (string) ($j['faultcode'] ?? $r->status())));
        $this->forget();
        return ['dry' => false, 'ok' => true];
    }
}
