<?php

namespace App\Support;

use App\Portal\Demo;
use App\Support\CustomerEmail as CE;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


/**
 * 2026-11-02 AI Connect (MCP). Customers create API keys (stored only as SHA-256 hashes) that let an AI assistant
 * use their own GVD tools. Every write needs confirm=true; without it a preview is returned. Every call is logged.
 */
class AiConnect
{
    const TZ = 'America/New_York';
    const WRITE_TOOLS = ['add_contact', 'send_email', 'enroll_in_sequence', 'place_call', 'ai_call'];

    // ---------------- visibility / switches ----------------
    public static function visibleTo(?int $uid): bool
    {
        if (config('ai_connect.enabled')) return true;
        if (session()->get('admin_id')) return true;
        return $uid !== null && Demo::isDemoUser($uid);
    }

    /** Key auth works only while the platform flag is on, except the demo account (so we can test before launch). */
    public static function platformOpenFor(int $uid): bool
    {
        return (bool) config('ai_connect.enabled') || Demo::isDemoUser($uid);
    }

    public static function userEnabled(int $uid): bool
    {
        $v = DB::table('ai_settings')->where('user_id', $uid)->value('enabled');
        return $v === null ? true : (bool) $v;
    }

    public static function setUserEnabled(int $uid, bool $on): void
    {
        DB::table('ai_settings')->updateOrInsert(['user_id' => $uid], ['enabled' => $on ? 1 : 0, 'updated_at' => now(), 'created_at' => now()]);
    }

    // ---------------- keys ----------------
    public static function hash(string $plain): string { return hash('sha256', $plain); }

    /** Returns [id, plaintext]. Plaintext is shown once and never stored. */
    public static function createKey(int $uid, string $name, array $scopes, int $limit): array
    {
        $plain = 'gvd_live_' . Str::random(40);
        $scopes = array_values(array_intersect(array_keys(config('ai_connect.scopes')), $scopes));
        $id = DB::table('ai_keys')->insertGetId(['user_id' => $uid, 'name' => mb_substr(trim($name) ?: 'AI assistant', 0, 80), 'prefix' => substr($plain, 0, 13),
            'hash' => self::hash($plain), 'scopes' => json_encode($scopes), 'daily_limit' => max(1, min($limit, (int) config('ai_connect.max_daily_limit'))),
            'created_at' => now(), 'updated_at' => now()]);
        return [$id, $plain];
    }

    public static function findKey(string $plain): ?object
    {
        if (!preg_match('/^gvd_live_[A-Za-z0-9]{40}$/', $plain)) return null;
        $k = DB::table('ai_keys')->where('hash', self::hash($plain))->whereNull('revoked_at')->first();
        return $k ?: null;
    }

    public static function scopes(object $k): array { return json_decode((string) $k->scopes, true) ?: []; }

    public static function usedToday(int $keyId): int
    {
        return DB::table('ai_activity')->where('key_id', $keyId)->where('method', 'tools/call')->where('created_at', '>=', now(self::TZ)->startOfDay()->utc())->count();
    }

    public static function log(int $uid, ?int $keyId, string $method, ?string $tool, ?string $mode, string $status, ?string $summary, ?string $ip): void
    {
        try {
            DB::table('ai_activity')->insert(['user_id' => $uid, 'key_id' => $keyId, 'method' => mb_substr($method, 0, 40), 'tool' => $tool ? mb_substr($tool, 0, 40) : null,
                'mode' => $mode, 'status' => $status, 'summary' => $summary ? mb_substr($summary, 0, 255) : null, 'ip' => $ip, 'created_at' => now()]);
        } catch (\Throwable $e) {}
    }


    // ---------------- calling line (2026-11-02 mcp2) ----------------
    const NEVER_DIAL = ['5613635538']; // fax-forwarded number: never dialed by AI Connect

    public static function fmt(string $n): string { $d = substr(preg_replace('/\D/', '', $n), -10); return strlen($d) === 10 ? sprintf('(%s) %s-%s', substr($d, 0, 3), substr($d, 3, 3), substr($d, 6)) : $n; }

    public static function line(int $uid): ?object
    {
        $l = DB::table('ai_call_lines')->where('user_id', $uid)->first();
        return $l ?: null;
    }

    /** Same rules as Audio Campaign: blocked, over credit limit, expired, not open. */
    public static function accountProblem(array $a): ?string
    {
        if (($a['blocked'] ?? 'N') === 'Y') return 'This line is blocked. Contact our support team.';
        if ((int) ($a['billing_model'] ?? 0) === 1 && isset($a['credit_limit']) && $a['credit_limit'] !== null && (float) ($a['balance'] ?? 0) >= (float) $a['credit_limit']) return 'Your account has reached its credit limit.';
        if (!empty($a['expiration_date']) && date('Y-m-d') >= substr((string) $a['expiration_date'], 0, 10)) return 'This line has expired. Contact our support team.';
        if (($a['bill_status'] ?? 'O') !== 'O') return 'This line is not active.';
        return null;
    }

    /** Web callback (server-to-server): rings the customer's line first, then the target. The URL carries the line password and is never logged. */
    public static function dial(object $line, string $to): array
    {
        $url = (string) config('audio_campaign.callback_url');
        if ($url === '') return [false, 'calling is not configured.'];
        try { $raw = \Illuminate\Support\Facades\Crypt::decryptString($line->secret_enc); } catch (\Throwable $e) { return [false, 'please link your line again.']; }
        // mcp6: new links store {"web": self-care password, "sip": service password}; the phone system's own form works with the web password. Old links hold the SIP one only.
        $j0 = json_decode($raw, true);
        $tries = is_array($j0) ? array_values(array_filter([$j0['sip'] ?? '', $j0['web'] ?? ''], fn ($v) => $v !== '')) : [$raw];
        $labels = is_array($j0) ? array_keys(array_filter(['service' => $j0['sip'] ?? '', 'web' => $j0['web'] ?? ''], fn ($v) => $v !== '')) : ['service'];
        $tries = is_array($j0) ? array_values(array_filter([$j0['sip'] ?? '', $j0['web'] ?? ''], fn ($v) => $v !== '')) : [$raw];
        $last = 'the phone system did not accept the call.';
        foreach ($tries as $ti => $secret) { // a wrong password is refused before anything is dialed, so the next one is only tried after a refusal
            try {
                $r = \Illuminate\Support\Facades\Http::timeout(30)->connectTimeout(5)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0 (GVD AI Connect)', 'X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json, text/javascript, */*; q=0.01',
                        'Origin' => self::origin($url), 'Referer' => self::origin($url) . '/'])
                    ->asForm()->post($url, ['Account' => $line->account, 'Password' => $secret, 'First_Phone_Number' => $line->account, 'Second_Phone_Number' => $to, 'cb_type' => 'extended']);
            } catch (\Throwable $e) { return [false, 'could not reach the phone system. Try again shortly.']; }
            $j = $r->successful() ? json_decode((string) $r->body(), true) : null;
            if (is_array($j) && (int) ($j['success'] ?? 0) === 1) return [true, '', $labels[$ti] ?? '?'];
            \Illuminate\Support\Facades\Log::info('ai-connect callback refused', ['password_type' => $labels[$ti] ?? '?']);
            $last = is_array($j) && !empty($j['error']) ? mb_substr(strip_tags((string) $j['error']), 0, 180) : 'the phone system did not accept the call (HTTP ' . $r->status() . ').';
        }
        return [false, $last . (is_array($j0) ? '' : ' Please unlink and link your line again.')];
    }

    /** Originate via Contabo voice gateway (Asterisk → AudioSocket → Grok). Never logs the gateway token. */
    public static function dialAi(object $line, string $to, string $instructions = '', string $voice = ''): array
    {
        $url = (string) config('ai_connect.voice_gateway_url');
        $token = (string) config('ai_connect.voice_gateway_token');
        if ($url === '' || $token === '') return [false, 'AI voice gateway is not configured.'];
        $payload = ['to' => $to, 'from' => preg_replace('/\D/', '', (string) $line->account)];
        if ($instructions !== '') $payload['instructions'] = mb_substr($instructions, 0, 4000);
        if ($voice !== '' && preg_match('/^[A-Za-z0-9_-]{1,32}$/', $voice)) $payload['voice'] = $voice;
        try {
            $r = \Illuminate\Support\Facades\Http::timeout(30)->connectTimeout(5)
                ->withToken($token)
                ->acceptJson()
                ->asJson()
                ->post($url . '/v1/calls', $payload);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('ai-connect voice gateway unreachable', ['err' => mb_substr($e->getMessage(), 0, 150)]);
            return [false, 'could not reach the AI voice gateway. Try again shortly.'];
        }
        $j = null;
        try { $j = $r->json(); } catch (\Throwable $e) { $j = null; }
        if ($r->status() === 202 && is_array($j) && !empty($j['ok'])) {
            $cid = (string) ($j['callId'] ?? '');
            return [true, 'AI agent is dialing +' . $to . ' now for a two-way conversation'
                . ($cid !== '' ? " (call id $cid)" : '') . '.', ['callId' => $cid]];
        }
        $err = is_array($j) ? (string) ($j['error'] ?? '') : '';
        if ($err === '' && is_array($j) && !empty($j['note'])) $err = (string) $j['note'];
        if ($err === '') $err = 'gateway HTTP ' . $r->status();
        // Trunk not ready is expected until Telinta allows this server.
        \Illuminate\Support\Facades\Log::info('ai-connect voice gateway refused', ['http' => $r->status()]);
        return [false, mb_substr(strip_tags($err), 0, 220)];
    }

    private static function origin(string $url): string { $u = parse_url($url); return ($u['scheme'] ?? 'https') . '://' . ($u['host'] ?? '') . (isset($u['port']) ? ':' . $u['port'] : ''); }

    // ---------------- tool catalog ----------------
    public static function tools(): array
    {
        $confirm = ['type' => 'boolean', 'description' => 'Must be true to actually do it. Leave out to get a preview first.'];
        return [
            ['name' => 'list_contacts', 'scope' => 'contacts:read', 'description' => 'List contacts in the customer\'s Global Voice Direct email list. Optional search and tag filter.',
                'inputSchema' => ['type' => 'object', 'properties' => ['search' => ['type' => 'string'], 'tag' => ['type' => 'string'], 'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100]]],
                'annotations' => ['readOnlyHint' => true]],
            ['name' => 'add_contact', 'scope' => 'contacts:read', 'description' => 'Add (or update) one contact. Returns a preview unless confirm=true.',
                'inputSchema' => ['type' => 'object', 'required' => ['email'], 'properties' => ['email' => ['type' => 'string'], 'first_name' => ['type' => 'string'], 'last_name' => ['type' => 'string'], 'business_name' => ['type' => 'string'], 'tags' => ['type' => 'string', 'description' => 'Comma-separated'], 'confirm' => $confirm]],
                'annotations' => ['readOnlyHint' => false, 'destructiveHint' => false]],
            ['name' => 'list_email_templates', 'scope' => 'campaigns:read', 'description' => 'List the customer\'s saved email templates (id and name).',
                'inputSchema' => ['type' => 'object', 'properties' => new \stdClass()], 'annotations' => ['readOnlyHint' => true]],
            ['name' => 'send_email', 'scope' => 'email:send', 'description' => 'Send one email from the customer\'s own connected mailbox. Daily limits, unsubscribe footer, unsubscribe list and open/click tracking all apply. Returns a preview unless confirm=true.',
                'inputSchema' => ['type' => 'object', 'required' => ['to', 'subject'], 'properties' => ['to' => ['type' => 'string'], 'subject' => ['type' => 'string'], 'body_html' => ['type' => 'string', 'description' => 'Simple HTML. Merge tags: {{first_name}} {{last_name}} {{business_name}} {{my_business}}'], 'template_id' => ['type' => 'integer', 'description' => 'Use a saved template instead of body_html'], 'confirm' => $confirm]],
                'annotations' => ['readOnlyHint' => false, 'destructiveHint' => false, 'openWorldHint' => true]],
            ['name' => 'enroll_in_sequence', 'scope' => 'sequences:run', 'description' => 'Add contacts (by email) to one of the customer\'s email sequences. Returns a preview unless confirm=true. Pass no sequence_id to list sequences.',
                'inputSchema' => ['type' => 'object', 'properties' => ['sequence_id' => ['type' => 'integer'], 'emails' => ['type' => 'array', 'items' => ['type' => 'string'], 'maxItems' => 200], 'confirm' => $confirm]],
                'annotations' => ['readOnlyHint' => false, 'destructiveHint' => false]],
            ['name' => 'get_campaign_report', 'scope' => 'reports:read', 'description' => 'Report for an email campaign: sent, failed, opened, clicks. Pass no campaign_id to list recent campaigns.',
                'inputSchema' => ['type' => 'object', 'properties' => ['campaign_id' => ['type' => 'integer']]], 'annotations' => ['readOnlyHint' => true]],
            ['name' => 'place_call', 'scope' => 'calls:place', 'description' => 'Ring the customer\'s line, then connect it to a number (click-to-call). Returns a preview unless confirm=true.',
                'inputSchema' => ['type' => 'object', 'required' => ['to'], 'properties' => ['to' => ['type' => 'string', 'description' => 'US number to call'], 'confirm' => $confirm]],
                'annotations' => ['readOnlyHint' => false, 'destructiveHint' => false, 'openWorldHint' => true]],
            ['name' => 'ai_call', 'scope' => 'calls:ai', 'description' => 'Have the Global Voice Direct AI agent (Grok) place an outbound call and hold a two-way voice conversation. Uses the Contabo Asterisk voice gateway, not click-to-call. Returns a preview unless confirm=true.',
                'inputSchema' => ['type' => 'object', 'required' => ['to'], 'properties' => [
                    'to' => ['type' => 'string', 'description' => 'US number the AI should call'],
                    'instructions' => ['type' => 'string', 'description' => 'Optional per-call instructions for the AI agent'],
                    'voice' => ['type' => 'string', 'description' => 'Optional Grok voice name (default from gateway)'],
                    'confirm' => $confirm,
                ]],
                'annotations' => ['readOnlyHint' => false, 'destructiveHint' => false, 'openWorldHint' => true]],
        ];
    }

    // ---------------- tool implementations ----------------
    /** Returns [isError(bool), text, summary-for-log, mode]. */
    public static function call(int $uid, string $tool, array $a): array
    {
        $confirm = ($a['confirm'] ?? false) === true || ($a['confirm'] ?? null) === 'true';
        $demo = Demo::isDemoUser($uid);
        switch ($tool) {
            case 'list_contacts':
                $q = DB::table('ec_contacts')->where('user_id', $uid);
                if ($s = trim((string) ($a['search'] ?? ''))) $q->where(fn ($w) => $w->where('email', 'like', '%' . addcslashes($s, '%_') . '%')->orWhere('first_name', 'like', '%' . addcslashes($s, '%_') . '%')->orWhere('last_name', 'like', '%' . addcslashes($s, '%_') . '%')->orWhere('business_name', 'like', '%' . addcslashes($s, '%_') . '%'));
                if ($t = CE::normTags($a['tag'] ?? '')) $q->whereRaw('FIND_IN_SET(?, tags)', [$t]);
                $total = (clone $q)->count();
                $rows = $q->orderByDesc('id')->limit(max(1, min(100, (int) ($a['limit'] ?? 25))))->get(['id', 'email', 'first_name', 'last_name', 'business_name', 'tags', 'status']);
                return [false, json_encode(['total' => $total, 'contacts' => $rows], JSON_UNESCAPED_SLASHES), "$total contacts", 'read'];

            case 'add_contact':
                $email = CE::normEmail((string) ($a['email'] ?? ''));
                if (!$email) return [true, 'That email address is not valid.', 'invalid email', 'preview'];
                $row = ['first_name' => mb_substr(trim((string) ($a['first_name'] ?? '')), 0, 80), 'last_name' => mb_substr(trim((string) ($a['last_name'] ?? '')), 0, 80),
                    'business_name' => mb_substr(trim((string) ($a['business_name'] ?? '')), 0, 120), 'tags' => CE::normTags($a['tags'] ?? '')];
                $exists = DB::table('ec_contacts')->where('user_id', $uid)->where('email', $email)->exists();
                if (!$confirm) return [false, 'Preview: will ' . ($exists ? 'update' : 'add') . " contact $email" . ($row['tags'] ? " with tags {$row['tags']}" : '') . '. Call again with confirm=true to save.', "preview $email", 'preview'];
                if ($demo) return [true, Demo::READONLY, 'demo read-only', 'confirm'];
                if (!$exists && DB::table('ec_contacts')->where('user_id', $uid)->count() >= (int) config('ec.max_contacts', 25000)) return [true, 'Your contact list is full (' . number_format((int) config('ec.max_contacts')) . ' contacts).', 'list full', 'confirm'];
                $id = self::upsertContact($uid, $email, $row);
                if (!$exists) CustomerSequences::fire($uid, 'contact_added', [$id]);
                return [false, ($exists ? 'Updated' : 'Added') . " contact $email (id $id).", ($exists ? 'updated ' : 'added ') . $email, 'confirm'];

            case 'list_email_templates':
                $t = DB::table('ec_templates')->where('user_id', $uid)->orderByDesc('id')->limit(100)->get(['id', 'name', 'updated_at']);
                return [false, json_encode(['templates' => $t], JSON_UNESCAPED_SLASHES), count($t) . ' templates', 'read'];

            case 'send_email':
                return self::sendEmail($uid, $a, $confirm, $demo);

            case 'enroll_in_sequence':
                $sid = (int) ($a['sequence_id'] ?? 0);
                if (!$sid) {
                    $s = DB::table('ec_sequences')->where('user_id', $uid)->orderByDesc('id')->get(['id', 'name', 'status']);
                    return [false, json_encode(['sequences' => $s]), 'listed sequences', 'read'];
                }
                $seq = DB::table('ec_sequences')->where('id', $sid)->where('user_id', $uid)->first();
                if (!$seq) return [true, 'Sequence not found.', "seq $sid not found", 'preview'];
                $emails = array_values(array_filter(array_map(fn ($e) => CE::normEmail((string) $e), array_slice((array) ($a['emails'] ?? []), 0, 200))));
                if (!$emails) return [true, 'Give one or more contact emails.', 'no emails', 'preview'];
                $ids = DB::table('ec_contacts')->where('user_id', $uid)->whereIn('email', $emails)->where('status', 'active')->pluck('id')->all();
                $missing = count($emails) - count($ids);
                $note = $seq->status === 'ACTIVE' ? '' : ' Note: this sequence is paused, so no emails go out until you turn it on.';
                if (!$confirm) return [false, "Preview: will add " . count($ids) . " contact(s) to \"{$seq->name}\"" . ($missing ? " ($missing not found or unsubscribed, skipped)" : '') . ".$note Call again with confirm=true.", "preview seq $sid x" . count($ids), 'preview'];
                if ($demo) return [true, Demo::READONLY, 'demo read-only', 'confirm'];
                $n = CustomerSequences::enroll($uid, $sid, $ids, 'ai_connect');
                return [false, "Added $n contact(s) to \"{$seq->name}\" (already-enrolled contacts are skipped).$note", "enrolled $n in seq $sid", 'confirm'];

            case 'get_campaign_report':
                $cid = (int) ($a['campaign_id'] ?? 0);
                if (!$cid) {
                    $c = DB::table('ec_campaigns')->where('user_id', $uid)->orderByDesc('id')->limit(25)->get(['id', 'name', 'status', 'scheduled_at', 'finished_at']);
                    return [false, json_encode(['campaigns' => $c]), 'listed campaigns', 'read'];
                }
                $c = DB::table('ec_campaigns')->where('id', $cid)->where('user_id', $uid)->first(['id', 'name', 'subject', 'status', 'started_at', 'finished_at']);
                if (!$c) return [true, 'Campaign not found.', "camp $cid not found", 'read'];
                $st = CE::stats($cid);
                $clicks = null;
                try { $ev = DB::table('email_track_events')->where('type', 'ec')->where('ref_id', $cid); $clicks = ['unique_clickers' => (clone $ev)->where('event', 'click')->distinct()->count('send_id'), 'human_opens' => (clone $ev)->where('event', 'open')->where('machine', 0)->distinct()->count('send_id')]; } catch (\Throwable $e) {}
                return [false, json_encode(['campaign' => $c, 'stats' => $st] + ($clicks !== null ? ['tracking' => $clicks] : []), JSON_UNESCAPED_SLASHES), "report $cid", 'read'];

            case 'place_call':
                $to = preg_replace('/\D/', '', (string) ($a['to'] ?? ''));
                if (strlen($to) === 10) $to = '1' . $to;
                if (!preg_match('/^1[2-9]\d{2}[2-9]\d{6}$/', $to)) return [true, 'Give a valid 10-digit US phone number.', 'invalid number', 'preview'];
                if (in_array(substr($to, -10), self::NEVER_DIAL, true)) return [true, 'That number can\'t be called through AI Connect.', 'protected number', 'preview'];
                $line = self::line($uid);
                $off = 'Calling through AI Connect is not switched on yet. We will let you know when it is ready.';
                $noLine = 'Link a line for AI calling first (AI Connect > Calling line).';
                if (!$confirm) return [false, "Preview: will ring your line " . ($line ? self::fmt($line->account) : '(none linked)') . " first, then connect it to +$to. "
                    . (!config('ai_connect.calls_enabled') ? $off : (!$line ? $noLine : 'Call again with confirm=true to place the call.')), 'preview call', 'preview'];
                if ($demo) return [true, Demo::READONLY, 'demo read-only', 'confirm'];
                if (!config('ai_connect.calls_enabled')) return [true, $off, 'calls off', 'confirm'];
                if (!$line) return [true, $noLine, 'no line', 'confirm'];
                $dr = self::dial($line, $to); [$ok, $why] = $dr; $pt = $dr[2] ?? null;
                return [!$ok, $ok ? "Calling now: your line " . self::fmt($line->account) . " will ring first, then connect to +$to." : "Call not placed: $why", ($ok ? 'call placed to +' : 'call failed to +') . $to . ($pt ? " (password type: $pt)" : ''), 'confirm'];

            case 'ai_call':
                $to = preg_replace('/\D/', '', (string) ($a['to'] ?? ''));
                if (strlen($to) === 10) $to = '1' . $to;
                if (!preg_match('/^1[2-9]\d{2}[2-9]\d{6}$/', $to)) return [true, 'Give a valid 10-digit US phone number.', 'invalid number', 'preview'];
                if (in_array(substr($to, -10), self::NEVER_DIAL, true)) return [true, 'That number can\'t be called through AI Connect.', 'protected number', 'preview'];
                $line = self::line($uid);
                $instructions = trim((string) ($a['instructions'] ?? ''));
                $voice = trim((string) ($a['voice'] ?? ''));
                $off = 'AI voice calling is not switched on yet. We will let you know when it is ready.';
                $noGw = 'The AI voice gateway is not configured on the server yet.';
                $noLine = 'Link a line for AI calling first (AI Connect > Calling line). Your line is used as the caller ID.';
                $gwUrl = (string) config('ai_connect.voice_gateway_url');
                $gwTok = (string) config('ai_connect.voice_gateway_token');
                $voiceOn = (bool) config('ai_connect.voice_calls_enabled');
                if (!$confirm) {
                    $preview = "Preview: the GVD AI agent will dial +$to"
                        . ($line ? ' showing caller ID ' . self::fmt($line->account) : ' (no calling line linked yet)')
                        . ' and hold a two-way voice conversation'
                        . ($instructions !== '' ? ' with your instructions' : '')
                        . '.';
                    if (!$voiceOn) $preview .= ' ' . $off;
                    elseif ($gwUrl === '' || $gwTok === '') $preview .= ' ' . $noGw;
                    elseif (!$line) $preview .= ' ' . $noLine;
                    else $preview .= ' Call again with confirm=true to place the AI call.';
                    return [false, $preview, 'preview ai_call', 'preview'];
                }
                if ($demo) return [true, Demo::READONLY, 'demo read-only', 'confirm'];
                if (!$voiceOn) return [true, $off, 'ai voice off', 'confirm'];
                if ($gwUrl === '' || $gwTok === '') return [true, $noGw, 'gateway unset', 'confirm'];
                if (!$line) return [true, $noLine, 'no line', 'confirm'];
                $dr = self::dialAi($line, $to, $instructions, $voice);
                $ok = (bool) ($dr[0] ?? false);
                $why = (string) ($dr[1] ?? 'unknown error');
                return [!$ok, $ok ? $why : ('AI call not placed: ' . $why), ($ok ? 'ai_call placed to +' : 'ai_call failed to +') . $to, 'confirm'];
        }
        return [true, 'Unknown tool.', 'unknown tool', null];
    }

    private static function upsertContact(int $uid, string $email, array $row): int
    {
        $ex = DB::table('ec_contacts')->where('user_id', $uid)->where('email', $email)->first(['id', 'tags']);
        if ($ex) {
            $upd = array_filter(['first_name' => $row['first_name'], 'last_name' => $row['last_name'], 'business_name' => $row['business_name']], fn ($v) => $v !== '');
            if ($row['tags']) $upd['tags'] = CE::normTags(array_merge(array_filter(explode(',', (string) $ex->tags)), explode(',', $row['tags'])));
            if ($upd) DB::table('ec_contacts')->where('id', $ex->id)->update($upd + ['updated_at' => now()]);
            return (int) $ex->id;
        }
        return (int) DB::table('ec_contacts')->insertGetId(['user_id' => $uid, 'email' => $email, 'first_name' => $row['first_name'], 'last_name' => $row['last_name'],
            'business_name' => $row['business_name'], 'tags' => $row['tags'], 'status' => 'active', 'source' => 'ai_connect', 'created_at' => now(), 'updated_at' => now()]);
    }

    private static function sendEmail(int $uid, array $a, bool $confirm, bool $demo): array
    {
        $to = CE::normEmail((string) ($a['to'] ?? ''));
        if (!$to) return [true, 'The "to" email address is not valid.', 'invalid to', 'preview'];
        $subject = mb_substr(trim(str_replace(["\r", "\n"], ' ', (string) ($a['subject'] ?? ''))), 0, 200);
        if ($subject === '') return [true, 'Add a subject.', 'no subject', 'preview'];
        $body = (string) ($a['body_html'] ?? '');
        if (!empty($a['template_id'])) {
            $tpl = DB::table('ec_templates')->where('id', (int) $a['template_id'])->where('user_id', $uid)->value('body_html');
            if ($tpl === null) return [true, 'Template not found.', 'template not found', 'preview'];
            $body = (string) $tpl;
        }
        if (strip_tags($body) === $body && trim($body) !== '') $body = '<p>' . nl2br(e($body)) . '</p>';
        $body = CE::sanitize($body);
        if (trim(strip_tags($body)) === '') return [true, 'Add a message (body_html) or a template_id.', 'no body', 'preview'];

        $ct = DB::table('ec_contacts')->where('user_id', $uid)->where('email', $to)->first();
        $smtp = CE::smtp($uid);
        $room = CE::limitFor($uid) - CE::sentToday($uid) - CustomerSequences::sentToday($uid);
        $problems = [];
        if (!config('ec.sending')) $problems[] = 'Email sending is paused on our side right now.';
        if ($demo) $problems[] = 'This is the sample account, so nothing is sent.';
        if (!$smtp) $problems[] = 'Connect your email first (Email campaign > Sending email). Emails go out from your own mailbox.';
        if ($why = CE::blocked($uid)) $problems[] = $why;
        if (CE::isSuppressed($uid, $to) || ($ct && $ct->status !== 'active')) $problems[] = "$to has unsubscribed or bounced, so it can't be emailed.";
        if ($room <= 0) $problems[] = 'You reached today\'s sending limit (' . CE::limitFor($uid) . '). It resets at midnight Eastern.';

        $snd = CE::sender($uid);
        $r = (object) ['email' => $to, 'first_name' => $ct->first_name ?? '', 'last_name' => $ct->last_name ?? '', 'business_name' => $ct->business_name ?? ''];
        [$subjR] = CE::render((object) ['subject' => $subject, 'body_html' => $body, 'preview' => ''], $r, $snd);
        if (!$confirm) {
            $txt = "Preview\nFrom: " . ($smtp ? ($smtp->from_name ?: $snd['business']) . ' <' . $smtp->from_email . '>' : '(no mailbox connected)') . "\nTo: $to\nSubject: $subjR\n"
                . "Message: " . mb_substr(trim(preg_replace('/\s+/', ' ', strip_tags($body))), 0, 400) . "\nAn unsubscribe link and your business address are added at the bottom.\n"
                . 'Sends left today: ' . max(0, $room) . "\n" . ($problems ? 'Cannot send yet: ' . implode(' ', $problems) : 'Call again with confirm=true to send.');
            return [false, $txt, "preview to $to", 'preview'];
        }
        if ($problems) return [true, 'Not sent. ' . implode(' ', $problems), 'blocked: ' . mb_substr($problems[0], 0, 120), 'confirm'];

        $cid = 0;
        try {
            $cid = DB::table('ec_campaigns')->insertGetId(['user_id' => $uid, 'name' => mb_substr('AI Connect: ' . $subject, 0, 190), 'subject' => $subject, 'preview' => '', 'body_html' => $body,
                'audience' => json_encode(['ai_connect' => true, 'to' => $to]), 'status' => 'SENDING', 'started_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
            $contactId = $ct ? (int) $ct->id : self::upsertContact($uid, $to, ['first_name' => '', 'last_name' => '', 'business_name' => '', 'tags' => 'ai-connect']);
            $rid = DB::table('ec_recipients')->insertGetId(['campaign_id' => $cid, 'contact_id' => $contactId, 'email' => $to, 'first_name' => $r->first_name, 'last_name' => $r->last_name,
                'business_name' => $r->business_name, 'status' => 'sending', 'created_at' => now(), 'updated_at' => now()]);
            $c = DB::table('ec_campaigns')->where('id', $cid)->first();
            [$s, $h, $u] = CE::render($c, DB::table('ec_recipients')->where('id', $rid)->first(), $snd, $rid);
            $h = Tracking::apply($h, 'ec', (int) $rid, $cid, $to, $uid);
            DB::table('ec_senders')->insertOrIgnore(['user_id' => $uid, 'created_at' => now(), 'updated_at' => now()]);
            DB::table('ec_senders')->where('user_id', $uid)->whereNull('first_sent_at')->update(['first_sent_at' => now(), 'updated_at' => now()]);
            CE::deliver($snd, $to, $s, $h, $u, $smtp);
            DB::table('ec_recipients')->where('id', $rid)->update(['status' => 'sent', 'sent_at' => now(), 'updated_at' => now()]);
            DB::table('ec_campaigns')->where('id', $cid)->update(['status' => 'DONE', 'finished_at' => now(), 'updated_at' => now()]);
            return [false, "Sent to $to from your mailbox. Track opens and clicks with get_campaign_report campaign_id=$cid.", "sent to $to (campaign $cid)", 'confirm'];
        } catch (\Throwable $e) {
            if ($cid) {
                DB::table('ec_recipients')->where('campaign_id', $cid)->update(['status' => 'failed', 'reason' => mb_substr($e->getMessage(), 0, 250), 'updated_at' => now()]);
                DB::table('ec_campaigns')->where('id', $cid)->update(['status' => 'DONE', 'finished_at' => now(), 'updated_at' => now()]);
            }
            \Illuminate\Support\Facades\Log::warning('ai-connect send failed', ['uid' => $uid, 'err' => mb_substr($e->getMessage(), 0, 150)]);
            return [true, 'Not sent: your mailbox refused the message. Check your email connection under Email campaign > Sending email.', 'send failed', 'confirm'];
        }
    }
}
