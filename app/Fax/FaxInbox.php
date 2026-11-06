<?php

namespace App\Fax;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Inbound fax inbox: Resend inbound email -> Svix-verified -> parsed -> tenant-mapped -> encrypted PDF.
 * Tenant = billing i_customer, taken from user_phone_numbers (user_id -> i_customer, numbers = DIDs).
 * Never logs secrets.
 */
class FaxInbox
{
    // ---------- keys ----------
    private static function k(string $name): string
    {
        $v = (string) config("fax.$name");
        if ($v === '') throw new \RuntimeException("fax.$name not configured");
        $raw = base64_decode($v, true);
        return ($raw !== false && strlen($raw) === 32) ? $raw : hash('sha256', $v, true);
    }

    public static function encrypt(string $plain): string
    {
        $iv = random_bytes(12); $tag = '';
        $body = openssl_encrypt($plain, 'aes-256-gcm', self::k('enc_key'), OPENSSL_RAW_DATA, $iv, $tag);
        return 'GVDF1' . $iv . $tag . $body;
    }

    public static function decrypt(string $blob): string
    {
        if (substr($blob, 0, 5) !== 'GVDF1') throw new \RuntimeException('bad fax file');
        $out = openssl_decrypt(substr($blob, 33), 'aes-256-gcm', self::k('enc_key'), OPENSSL_RAW_DATA, substr($blob, 5, 12), substr($blob, 17, 16));
        if ($out === false) throw new \RuntimeException('fax decrypt failed');
        return $out;
    }

    // ---------- per-tenant address ----------
    public static function token(int $iCustomer): string
    {
        return substr(hash_hmac('sha256', (string) $iCustomer, self::k('address_key')), 0, 12);
    }

    public static function address(int $iCustomer): string
    {
        return 'fax-' . $iCustomer . '-' . self::token($iCustomer) . '@' . config('fax.inbound_domain');
    }

    /** i_customer from any recipient fax-<i_customer>-<token>@..., token checked in constant time. */
    public static function tenantFromAddresses(array $addrs): ?int
    {
        foreach ($addrs as $a) {
            if (preg_match('/fax-(\d+)-([0-9a-f]{12})@/i', (string) $a, $m)
                && hash_equals(self::token((int) $m[1]), strtolower($m[2]))) return (int) $m[1];
        }
        return null;
    }

    // ---------- Svix (Resend webhooks) ----------
    public static function verifySvix(string $raw, ?string $id, ?string $ts, ?string $sigs, ?string $secret = null, ?int $now = null): bool
    {
        $secret = $secret ?? (string) config('fax.webhook_secret');
        if ($secret === '' || !$id || !$ts || !$sigs || !ctype_digit($ts)) return false;
        if (abs(($now ?? time()) - (int) $ts) > 300) return false;
        $key = base64_decode(preg_replace('/^whsec_/', '', $secret), true);
        if ($key === false) return false;
        $expected = base64_encode(hash_hmac('sha256', "$id.$ts.$raw", $key, true));
        foreach (explode(' ', $sigs) as $s) {
            $parts = explode(',', $s, 2);
            if (($parts[0] ?? '') === 'v1' && hash_equals($expected, $parts[1] ?? '')) return true;
        }
        return false;
    }

    // ---------- parsing ----------
    public static function digits(string $s): string { return preg_replace('/\D/', '', $s); }

    /** Mail gateways sometimes URL-encode the local part ('%2B1503...' for '+1503...'). */
    public static function decodeAddr(string $a): string
    {
        return preg_replace_callback('/%([0-9A-Fa-f]{2})/', fn ($m) => chr(hexdec($m[1])), $a);
    }

    public static function didFromTo(array $to): ?string
    {
        foreach (array_map([self::class, 'decodeAddr'], array_map('strval', $to)) as $t) if (preg_match('/\+?(\d{7,15})@um\.globalvoicedirect\.com/i', (string) $t, $m)) return $m[1];
        return null;
    }

    public static function callerFromFrom(string $from): ?string
    {
        $from = self::decodeAddr($from);
        if (!preg_match('/(\+?[\w.-]+)@um\.globalvoicedirect\.com/i', $from, $m)) return null;
        $c = $m[1];
        return preg_match('/^\+?\d{7,15}$/', $c) ? '+' . ltrim($c, '+') : $c;
    }

    public static function timeFromFilename(string $fn): ?\DateTimeImmutable
    {
        $utc = new \DateTimeZone('UTC');
        if (preg_match('/fax_message_(\d{4})-?(\d{2})-?(\d{2})[T_ -]?(\d{2})[:.-]?(\d{2})[:.-]?(\d{2})/i', $fn, $m))
            return new \DateTimeImmutable("$m[1]-$m[2]-$m[3] $m[4]:$m[5]:$m[6]", $utc);
        if (preg_match('/fax_message_(\d{10,13})/', $fn, $m))
            return (new \DateTimeImmutable('@' . (strlen($m[1]) === 13 ? intdiv((int) $m[1], 1000) : (int) $m[1])))->setTimezone($utc);
        return null;
    }

    public static function pdfPages(string $pdf): ?int
    {
        $n = preg_match_all('/\/Type\s*\/Page(?![s\w])/', $pdf);
        return $n > 0 ? $n : null;
    }

    // ---------- DID -> tenant (from the app's own DB) ----------
    /** Last 10 digits of every number in a user_phone_numbers.numbers value (JSON array, plain, or "(999) 999-9999"). */
    public static function numbersOf(?string $v): array
    {
        $list = json_decode((string) $v, true);
        if (!is_array($list)) $list = [(string) $v];
        $out = [];
        foreach ($list as $n) { $d = self::digits((string) $n); if (strlen($d) >= 10) $out[] = substr($d, -10); }
        return $out;
    }

    public static function tenantForDid(string $did): ?int
    {
        $d = self::digits($did);
        if (strlen($d) < 10) return null;
        $ten = substr($d, -10);
        $rows = DB::table('user_phone_numbers')->whereNotNull('i_customer')->where('i_customer', '>', 0)
            ->where('numbers', 'like', '%' . substr($ten, -4) . '%')->get(['i_customer', 'numbers']);
        $hits = [];
        foreach ($rows as $r) if (in_array($ten, self::numbersOf($r->numbers), true)) $hits[(int) $r->i_customer] = true;
        return count($hits) === 1 ? array_key_first($hits) : null; // ambiguous -> quarantine
    }

    /** i_customer values the logged-in portal user may see. */
    public static function tenantsForUser($userId): array
    {
        if (!$userId) return [];
        return DB::table('user_phone_numbers')->where('user_id', $userId)->whereNotNull('i_customer')
            ->where('i_customer', '>', 0)->distinct()->pluck('i_customer')->map(fn ($v) => (int) $v)->all();
    }

    // ---------- storage ----------
    public static function path(object $rec): string
    {
        $dir = $rec->status === 'quarantined' ? '_quarantine' : (string) $rec->i_customer;
        return config('fax.disk_path') . "/$dir/{$rec->uuid}.pdf.enc";
    }

    /**
     * @param array{emailId:string,from:string,to:array,receivedFor:array,subject:string,attachments:array<array{filename:string,content:string}>} $e
     */
    public static function ingest(array $e): array
    {
        $log = fn ($r, $extra = []) => Log::info('gvd-fax ingest', ['email_id' => $e['emailId'], 'result' => $r] + $extra);
        if (!preg_match('/@um\.globalvoicedirect\.com>?$/i', trim(self::decodeAddr($e['from'])))) { $log('rejected', ['reason' => 'sender']); return ['result' => 'rejected', 'reason' => 'not a fax sender']; }
        if (!preg_match('/^fax message$/i', trim($e['subject']))) { $log('rejected', ['reason' => 'subject']); return ['result' => 'rejected', 'reason' => 'not a fax subject']; }
        $pdf = null;
        foreach ($e['attachments'] as $a) if (preg_match('/^fax_message_.*\.pdf$/i', $a['filename']) && substr($a['content'], 0, 5) === '%PDF-') { $pdf = $a; break; }
        if (!$pdf) { $log('rejected', ['reason' => 'attachment']); return ['result' => 'rejected', 'reason' => 'no fax PDF']; }

        $sha = hash('sha256', $pdf['content']);
        $dup = DB::table('gvd_faxes')->where('sha256', $sha)->orWhere('email_id', $e['emailId'])->first();
        if ($dup) { $log('duplicate', ['id' => $dup->uuid]); return ['result' => 'duplicate', 'id' => $dup->uuid]; }

        $did = self::didFromTo($e['to']) ?? '';
        $byToken = self::tenantFromAddresses(array_merge($e['receivedFor'], $e['to']));
        $byDid = $did !== '' ? self::tenantForDid($did) : null;
        $status = 'stored'; $reason = null;
        if ($did === '') { $status = 'quarantined'; $reason = 'no DID in To'; }
        elseif ($byToken === null) { $status = 'quarantined'; $reason = 'no valid tenant address'; }
        // A valid HMAC token is the proof of tenancy. The DID only vetoes: if the portal links
        // this DID to a DIFFERENT customer, quarantine. Unlinked DIDs (no portal row yet) are accepted.
        elseif ($byDid !== null && $byDid !== $byToken) { $status = 'quarantined'; $reason = 'DID/tenant mismatch'; }

        $at = self::timeFromFilename($pdf['filename']) ?? new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $rec = (object) [
            'uuid' => (string) Str::uuid(), 'i_customer' => $status === 'stored' ? $byToken : null,
            'did' => $did, 'caller' => self::callerFromFrom($e['from']) ?? 'unknown',
            'received_at' => $at->format('Y-m-d H:i:s'), 'filename' => $pdf['filename'],
            'pages' => self::pdfPages($pdf['content']), 'size' => strlen($pdf['content']),
            'sha256' => $sha, 'email_id' => $e['emailId'], 'status' => $status, 'reason' => $reason,
        ];
        $file = self::path($rec);
        if (!is_dir(dirname($file))) mkdir(dirname($file), 0700, true);
        file_put_contents($file, self::encrypt($pdf['content']), LOCK_EX);
        @chmod($file, 0600);
        DB::table('gvd_faxes')->insert((array) $rec + ['created_at' => now(), 'updated_at' => now()]);
        $log($status, ['id' => $rec->uuid, 'reason' => $reason, 'i_customer' => $rec->i_customer]);
        return ['result' => $status, 'id' => $rec->uuid, 'reason' => $reason];
    }

    // ---------- signed 5-minute links ----------
    public static function sign(int $iCustomer, string $uuid, int $ttl = 300, ?int $now = null): array
    {
        $exp = ($now ?? time()) + $ttl;
        return ['exp' => $exp, 'sig' => hash_hmac('sha256', "$iCustomer.$uuid.$exp", self::k('url_key'))];
    }

    public static function checkSig(int $iCustomer, string $uuid, string $exp, string $sig, ?int $now = null): bool
    {
        if (!ctype_digit($exp) || (int) $exp < ($now ?? time()) || !preg_match('/^[0-9a-f]{64}$/', $sig)) return false;
        return hash_equals(hash_hmac('sha256', "$iCustomer.$uuid.$exp", self::k('url_key')), $sig);
    }

    // ---------- retention ----------
    public static function purge(?int $days = null): array
    {
        $days = $days ?? (int) config('fax.retention_days', 90);
        $cutoff = now()->subDays($days);
        $deleted = 0;
        DB::table('gvd_faxes')->where('received_at', '<', $cutoff)->orderBy('id')->chunkById(200, function ($rows) use (&$deleted) {
            foreach ($rows as $r) { @unlink(self::path($r)); DB::table('gvd_faxes')->where('id', $r->id)->delete(); $deleted++; }
        });
        DB::table('gvd_fax_audit')->where('created_at', '<', now()->subDays(max($days, 365)))->delete();
        Log::info('gvd-fax retention', ['days' => $days, 'deleted' => $deleted]);
        return ['deleted' => $deleted];
    }

    // ---------- Resend inbound API ----------
    public static function fetchResendEmail(string $emailId): array
    {
        $key = (string) config('fax.resend_api_key');
        if ($key === '') throw new \RuntimeException('RESEND_API_KEY not set');
        $id = rawurlencode($emailId);
        $em = Http::withToken($key)->timeout(20)->get("https://api.resend.com/emails/receiving/$id")->throw()->json();
        $al = Http::withToken($key)->timeout(20)->get("https://api.resend.com/emails/receiving/$id/attachments")->throw()->json();
        $atts = [];
        foreach ($al['data'] ?? [] as $a) {
            if (!preg_match('/\.pdf$/i', $a['filename'] ?? '') || ($a['size'] ?? 0) > 25 * 1024 * 1024 || empty($a['download_url'])) continue;
            $r = Http::timeout(60)->get($a['download_url']);
            if ($r->ok()) $atts[] = ['filename' => (string) $a['filename'], 'content' => $r->body()];
        }
        $arr = fn ($v) => is_array($v) ? array_map('strval', $v) : ($v ? [(string) $v] : []);
        $h = $em['headers'] ?? [];
        return ['emailId' => $emailId, 'from' => (string) ($h['from'] ?? $em['from'] ?? ''),
            'to' => array_merge($arr($em['to'] ?? null), $arr($h['to'] ?? null)), 'receivedFor' => $arr($em['received_for'] ?? null),
            'subject' => (string) ($em['subject'] ?? ''), 'attachments' => $atts];
    }
}
