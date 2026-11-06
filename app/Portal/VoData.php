<?php

namespace App\Portal;

use Illuminate\Support\Facades\Http;

/**
 * Read/write helpers for the Virtual Office pages, always with the signed-in customer's own billing session.
 * Every value shown comes from the API; nothing about the tenant is hard-coded. Secrets are never logged.
 */
class VoData extends PhoneNumbers
{
    /** Billing call that throws on a fault. */
    public function call(string $m, array $p = []): array
    {
        $r = $this->b($m, $p);
        if (!empty($r['faultcode'])) throw new \RuntimeException('fault ' . preg_replace('/[^\w.]/', '', (string) $r['faultcode']));
        return $r;
    }

    /** Billing call whose answer is a file (recordings). Null when the API answers with JSON (no file). */
    public function blob(string $m, array $p): ?array
    {
        $k = $this->k; $hdr = [];
        if (($k['csrf_token'] ?? '') !== '') { $hdr['CSRF-Token'] = $k['csrf_token']; $hdr['X-CSRF-Token'] = $k['csrf_token']; }
        if (($k['access_token'] ?? '') !== '') $hdr['Authorization'] = 'Bearer ' . $k['access_token'];
        $r = Http::asForm()->withHeaders($hdr)->timeout(60)->connectTimeout(5)
            ->post(rtrim($k['base'], '/') . '/' . $m, ['params' => json_encode((object) $p), 'auth_info' => json_encode(['session_id' => $k['session_id']])]);
        $ct = (string) $r->header('Content-Type');
        if ($r->status() !== 200 || str_contains($ct, 'json') || strlen($r->body()) < 32) return null;
        $body = $r->body();
        // multipart answers: keep the binary part only
        if (preg_match('/boundary="?([^";]+)"?/i', $ct, $m2)) {
            foreach (explode('--' . $m2[1], $body) as $part) {
                [$h, $b] = array_pad(preg_split("/\r?\n\r?\n/", $part, 2), 2, '');
                if ($b !== '' && !preg_match('/content-type:\s*application\/json/i', $h) && strlen($b) > 32) { $body = rtrim($b, "\r\n"); $ct = preg_match('/content-type:\s*([^\r\n;]+)/i', $h, $m3) ? trim($m3[1]) : 'application/octet-stream'; break; }
            }
        }
        return ['body' => $body, 'type' => $ct ?: 'application/octet-stream'];
    }

    /** Voice-application call as one of this tenant's accounts (refuses guarded lines). */
    public function umAs(int $ia, string $m, ?array $p = null, bool $blob = false) { return $this->um($ia, $m, $p, $blob); }

    /** All accounts of this customer with selected service features (raw API records, secrets removed). */
    public function accounts(array $features = [], bool $openOnly = false): array
    {
        $p = ['i_customer' => $this->ic, 'limit' => 500];
        if ($features) $p['get_service_features'] = $features;
        if ($openOnly) $p['bill_status'] = 'O';
        $out = [];
        foreach ($this->call('Account/get_account_list', $p)['account_list'] ?? [] as $a) {
            if ((int) ($a['i_customer'] ?? 0) !== $this->ic) continue;
            foreach (array_keys($a) as $f) if (preg_match('/pass|pin$|secret|token/i', $f)) unset($a[$f]);
            $out[] = $a;
        }
        return $out;
    }

    public static function sf(array $a, string $name): ?array
    {
        foreach ($a['service_features'] ?? [] as $s) if (($s['name'] ?? '') === $name) {
            $at = []; foreach ($s['attributes'] ?? [] as $t) $at[$t['name']] = $t['effective_values'][0] ?? ($t['values'][0] ?? null);
            return ['flag' => (string) ($s['flag_value'] ?? ''), 'effective' => (string) ($s['effective_flag_value'] ?? $s['flag_value'] ?? ''), 'attr' => $at];
        }
        return null;
    }

    /** The account record (billing level) of one of THIS customer's accounts, detailed, for secret reveal only. */
    public function secretOf(int $ia, string $field): ?string
    {
        $a = $this->call('Account/get_account_info', ['i_account' => $ia, 'detailed_info' => 1])['account_info'] ?? null;
        if (!$a || (int) ($a['i_customer'] ?? 0) !== $this->ic) throw new \DomainException('not yours');
        $v = $a[$field] ?? null;
        return $v === null ? null : (string) $v;
    }

    /** Several billing calls at once. $jobs: key => [method, params]. Returns key => array|null. */
    public function many(array $jobs): array
    {
        if (!$jobs) return [];
        $k = $this->k; $base = rtrim($k['base'], '/'); $hdr = [];
        if (($k['csrf_token'] ?? '') !== '') { $hdr['CSRF-Token'] = $k['csrf_token']; $hdr['X-CSRF-Token'] = $k['csrf_token']; }
        if (($k['access_token'] ?? '') !== '') $hdr['Authorization'] = 'Bearer ' . $k['access_token'];
        $auth = json_encode(['session_id' => $k['session_id']]);
        $res = Http::pool(function ($pool) use ($jobs, $base, $hdr, $auth) {
            foreach ($jobs as $key => [$m, $p]) $pool->as((string) $key)->asForm()->withHeaders($hdr)->timeout(30)->connectTimeout(5)
                ->post("$base/$m", ['params' => json_encode((object) $p), 'auth_info' => $auth]);
        });
        $o = [];
        foreach ($jobs as $key => $_) {
            $x = $res[(string) $key] ?? null;
            $j = $x instanceof \Illuminate\Http\Client\Response ? $x->json() : null;
            $o[$key] = is_array($j) && empty($j['faultcode']) ? $j : null;
        }
        return $o;
    }
}
