<?php

namespace App\Portal;

/**
 * Every phone-number account on the signed-in customer's own billing account, read and changed with that
 * customer's own session. Values come from the API; nothing about the numbers is hard-coded.
 */
class PhoneNumbers extends Greetings
{
    const NEVER_PERIOD = 'hr{0}min{0}'; // the value VO itself writes for "never"

    /** Forwarding guard: nothing this page saves may change routing/forwarding on these lines (config, silent). */
    public static function forwardingGuarded(array $a): bool
    {
        if (in_array((int) ($a['i_account'] ?? 0), config('greetings.never_accounts', []), true)) return true;
        $d = preg_replace('/\D/', '', (string) ($a['id'] ?? ''));
        foreach (config('greetings.never_lines', []) as $n) if ($d !== '' && str_contains($d, $n)) return true;
        return false;
    }

    public static function fmt(string $id): string
    {
        if (preg_match('/^1?([2-9]\d{2})(\d{3})(\d{4})$/', $id, $m)) return "($m[1]) $m[2]-$m[3]";
        return $id;
    }

    /** All accounts of this customer (any status), classified. */
    public function all(): array
    {
        $r = $this->b('Account/get_account_list', ['i_customer' => $this->ic, 'get_service_features' => ['cli'], 'limit' => 500]);
        if (!empty($r['faultcode'])) throw new \RuntimeException('account list fault');
        $ic = $this->ic; $out = [];
        foreach ($r['account_list'] ?? [] as $a) {
            if ((int) ($a['i_customer'] ?? 0) !== $ic) continue;
            $id = (string) ($a['id'] ?? ''); $batch = (string) ($a['batch_name'] ?? '');
            $system = ($a['extension_id'] ?? null) !== null || str_starts_with($batch, "$ic-vo-user") || in_array($id, ["$ic-vo-did", "$ic-vo-conf"], true) || preg_match('/^fax/i', $id);
            $kind = $system ? 'system' : (str_starts_with($batch, "$ic-vo-did") ? 'vo' : 'other');
            $bs = (string) ($a['bill_status'] ?? '');
            $status = ($a['blocked'] ?? 'N') === 'Y' ? 'Blocked' : ($bs === 'C' ? 'Closed' : ($bs !== '' && $bs !== 'O' ? 'Inactive/legacy' : 'Active'));
            if ($status === 'Active' && !$system && !preg_match('/^1?\d{10}$/', $id)) $status = 'Inactive/legacy';
            $cli = ['mode' => 'DEFAULT', 'text' => 'Company default'];
            foreach ($a['service_features'] ?? [] as $s) if (($s['name'] ?? '') === 'cli') {
                $at = []; foreach ($s['attributes'] ?? [] as $t) $at[$t['name']] = $t['effective_values'][0] ?? null;
                $fv = (string) ($s['flag_value'] ?? '');
                if ($fv === 'Y' && !empty($at['display_number'])) $cli = ['mode' => 'VODIDNUMBER', 'text' => 'This number (' . self::fmt((string) $at['display_number']) . ')'];
                elseif ($fv === 'N') $cli = ['mode' => 'CALLERNUMBER', 'text' => "Caller's own number"];
                elseif (!empty($at['centrex'])) $cli = ['mode' => 'DEFAULT', 'text' => 'Company default (' . self::fmt((string) $at['centrex']) . ')'];
            }
            $out[] = [
                'i_account' => (int) $a['i_account'], 'id' => $id, 'display' => self::fmt($id), 'kind' => $kind,
                'plan' => (string) (($a['product_visible_name'] ?? '') ?: ($a['product_name'] ?? '')),
                'status' => $status, 'custom' => ($a['cont1'] ?? '') === 'CUSTOM', 'cli' => $cli,
                'extName' => $system && ($a['extension_id'] ?? null) !== null ? 'Ext ' . $a['extension_id'] . ' – ' . ($a['extension_name'] ?? '') : null,
            ];
        }
        usort($out, fn ($x, $y) => [$x['kind'] === 'system', $x['kind'] !== 'vo', $x['status'] !== 'Active', $x['id']] <=> [$y['kind'] === 'system', $y['kind'] !== 'vo', $y['status'] !== 'Active', $y['id']]);
        return $out;
    }

    /** Billing record of one of THIS customer's accounts (no forwarding guard here: billing-level only). */
    public function own(int $ia): array
    {
        $a = $this->b('Account/get_account_info', ['i_account' => $ia])['account_info'] ?? null;
        if (!$a || (int) ($a['i_customer'] ?? 0) !== $this->ic) throw new \DomainException('not yours');
        return $a;
    }

    private function rootMenus(int $ia): array
    {
        $menus = []; foreach ($this->um($ia, 'AutoAttendant/get_menu_list', [])['menu_list'] ?? [] as $m) $menus[$m['name']] = $m;
        return [$menus['ROOT'] ?? null, $menus['After Hours'] ?? null];
    }

    /** Voice-application details (routing for VO numbers, voicemail email + fax format). Null parts = not available. */
    public function detail(int $ia, bool $vo, bool $custom): array
    {
        $d = ['route' => null, 'ext' => null, 'email' => null, 'emailSet' => null, 'fax' => null];
        try {
            $vs = $this->um($ia, 'Voicemail/get_vm_settings', [])['vm_settings'] ?? [];
            $d['email'] = $vs['ext_email_action'] ?? null; $d['emailSet'] = !empty($vs['ext_email']); $d['fax'] = $vs['fax_file'] ?? null;
            if ($vo) {
                [$root] = $this->rootMenus($ia);
                if ($root) {
                    $na = null; foreach ($this->um($ia, 'AutoAttendant/get_menu_transition_list', ['i_menu' => (int) $root['i_menu']])['transition_list'] ?? [] as $t) if (($t['event'] ?? '') === 'Not Active') $na = $t;
                    $never = in_array(strtolower((string) ($root['period'] ?? '')), ['never', 'none', 'hr{0}min{0}'], true);
                    $d['route'] = $custom ? 'CUSTOM' : ($never ? 'TRANSFER' : 'ROOT');
                    if ($d['route'] === 'TRANSFER') $d['ext'] = (string) ($na['destination'] ?? '');
                }
            }
        } catch (\Throwable $e) { /* not available for this line with this sign-in */ }
        return $d;
    }

    public function setRoute(int $ia, string $route, string $ext): void
    {
        $a = $this->own($ia);
        if (self::forwardingGuarded($a)) throw new \DomainException('guarded');
        [$root, $after] = $this->rootMenus($ia);
        if (!$root) throw new \RuntimeException('no menu');
        if ($route === 'ROOT') {
            if (!$after) throw new \RuntimeException('no after-hours menu');
            $this->um($ia, 'AutoAttendant/update_menu', ['menu_info' => ['i_menu' => (int) $root['i_menu'], 'name' => 'ROOT', 'period' => 'Always']]);
            $this->um($ia, 'AutoAttendant/set_menu_transition', ['i_menu' => (int) $root['i_menu'], 'transition_info' => ['event' => 'Not Active', 'action' => 'Menu', 'target_i_menu' => (int) $after['i_menu'], 'destination' => '']]);
        } else {
            $this->um($ia, 'AutoAttendant/update_menu', ['menu_info' => ['i_menu' => (int) $root['i_menu'], 'name' => 'ROOT', 'period' => self::NEVER_PERIOD]]);
            $this->um($ia, 'AutoAttendant/set_menu_transition', ['i_menu' => (int) $root['i_menu'], 'transition_info' => ['event' => 'Not Active', 'action' => 'Transfer', 'destination' => $ext]]);
        }
        if (($a['cont1'] ?? '') === 'CUSTOM') $this->b('Account/update_account', ['account_info' => ['i_account' => $ia, 'cont1' => '']]);
    }

    public function setCli(int $ia, string $mode): void
    {
        $a = $this->own($ia);
        $sf = $mode === 'VODIDNUMBER'
            ? ['name' => 'cli', 'flag_value' => 'Y', 'attributes' => [['name' => 'centrex', 'values' => [(string) $a['id']]], ['name' => 'display_number', 'values' => [(string) $a['id']]], ['name' => 'display_number_check', 'values' => ['D']]]]
            : ($mode === 'CALLERNUMBER'
                ? ['name' => 'cli', 'flag_value' => 'N', 'attributes' => [['name' => 'centrex', 'values' => ['']], ['name' => 'display_number', 'values' => ['']], ['name' => 'display_number_check', 'values' => ['Y']]]]
                : ['name' => 'cli', 'flag_value' => '^']);
        $r = $this->b('Account/update_account', ['account_info' => ['i_account' => $ia, 'service_features' => [$sf]]]);
        if (!empty($r['faultcode'])) throw new \RuntimeException('cli fault');
    }

    public function setVm(int $ia, ?string $email, ?string $fax): void
    {
        $vs = [];
        if ($email !== null) $vs['ext_email_action'] = $email;
        if ($fax !== null) $vs['fax_file'] = $fax;
        if ($vs) $this->um($ia, 'Voicemail/set_vm_settings', ['vm_settings' => $vs]);
    }

    /**
     * Details for many lines at once, in parallel (4 network round trips total instead of ~5 per line).
     * $rows: [['i_account'=>, 'vo'=>bool, 'custom'=>bool]]. Guarded lines and failures come back with nulls.
     */
    public function details(array $rows): array
    {
        $empty = ['route' => null, 'ext' => null, 'email' => null, 'emailSet' => null, 'fax' => null];
        $out = []; foreach ($rows as $r) $out[$r['i_account']] = $empty;
        if (!$rows) return $out;
        $k = $this->k; $base = rtrim($k['base'], '/');
        $hdr = [];
        if (($k['csrf_token'] ?? '') !== '') { $hdr['CSRF-Token'] = $k['csrf_token']; $hdr['X-CSRF-Token'] = $k['csrf_token']; }
        if (($k['access_token'] ?? '') !== '') $hdr['Authorization'] = 'Bearer ' . $k['access_token'];
        $auth = json_encode(['session_id' => $k['session_id']]);
        // 1) account records (with the voice login), billing session, in parallel
        $res = \Illuminate\Support\Facades\Http::pool(function ($pool) use ($rows, $base, $hdr, $auth) {
            foreach ($rows as $r) $pool->as((string) $r['i_account'])->asForm()->withHeaders($hdr)->timeout(20)->connectTimeout(5)
                ->post("$base/Account/get_account_info", ['params' => json_encode(['i_account' => (int) $r['i_account'], 'detailed_info' => 1]), 'auth_info' => $auth]);
        });
        $acc = [];
        foreach ($rows as $r) {
            $x = $res[(string) $r['i_account']] ?? null; if (!$x instanceof \Illuminate\Http\Client\Response) continue;
            $a = $x->json()['account_info'] ?? null;
            if (!$a || (int) ($a['i_customer'] ?? 0) !== $this->ic || self::never($a)) continue;
            $acc[$r['i_account']] = $a;
        }
        $um = rtrim(config('greetings.um_rest'), '/');
        $call = function (array $jobs) use ($um) { // $jobs: key => [method, params|null, session|null]
            $res = \Illuminate\Support\Facades\Http::pool(function ($pool) use ($jobs, $um) {
                foreach ($jobs as $key => [$m, $p, $sid]) {
                    $parts = [];
                    if ($sid !== null) $parts[] = ['name' => 'auth_info', 'contents' => json_encode(['session_id' => $sid])];
                    if ($p !== null) $parts[] = ['name' => 'params', 'contents' => json_encode((object) $p)];
                    $pool->as((string) $key)->asMultipart()->timeout(20)->connectTimeout(5)->post("$um/$m", $parts);
                }
            });
            $o = []; foreach ($jobs as $key => $_) { $x = $res[(string) $key] ?? null; $j = $x instanceof \Illuminate\Http\Client\Response ? $x->json() : null; $o[$key] = is_array($j) && empty($j['faultcode']) ? $j : null; }
            return $o;
        };
        // 2) voice logins
        $jobs = []; foreach ($acc as $ia => $a) $jobs[$ia] = ['Session/login', ['login' => (string) $a['id'], 'password' => (string) ($a['password'] ?? ''), 'domain' => (string) ($a['um_domain'] ?? config('greetings.um_domain'))], null];
        $sid = []; foreach ($call($jobs) as $ia => $j) if (!empty($j['session_id'])) $sid[$ia] = $j['session_id'];
        $vo = []; $custom = []; foreach ($rows as $r) { $vo[$r['i_account']] = !empty($r['vo']); $custom[$r['i_account']] = !empty($r['custom']); }
        // 3) voicemail settings + menu lists
        $jobs = [];
        foreach ($sid as $ia => $s) { $jobs["v$ia"] = ['Voicemail/get_vm_settings', [], $s]; if ($vo[$ia]) $jobs["m$ia"] = ['AutoAttendant/get_menu_list', [], $s]; }
        $r3 = $call($jobs); $roots = [];
        foreach ($sid as $ia => $s) {
            $vs = $r3["v$ia"]['vm_settings'] ?? null;
            if ($vs !== null) { $out[$ia]['email'] = $vs['ext_email_action'] ?? null; $out[$ia]['emailSet'] = !empty($vs['ext_email']); $out[$ia]['fax'] = $vs['fax_file'] ?? null; }
            foreach ($r3["m$ia"]['menu_list'] ?? [] as $m) if (($m['name'] ?? '') === 'ROOT') $roots[$ia] = $m;
        }
        // 4) ROOT transitions
        $jobs = []; foreach ($roots as $ia => $m) $jobs[$ia] = ['AutoAttendant/get_menu_transition_list', ['i_menu' => (int) $m['i_menu']], $sid[$ia]];
        foreach ($call($jobs) as $ia => $j) {
            if ($j === null) continue;
            $na = null; foreach ($j['transition_list'] ?? [] as $t) if (($t['event'] ?? '') === 'Not Active') $na = $t;
            $never = in_array(strtolower((string) ($roots[$ia]['period'] ?? '')), ['never', 'none', 'hr{0}min{0}'], true);
            $out[$ia]['route'] = $custom[$ia] ? 'CUSTOM' : ($never ? 'TRANSFER' : 'ROOT');
            if ($out[$ia]['route'] === 'TRANSFER') $out[$ia]['ext'] = (string) ($na['destination'] ?? '');
        }
        // close voice sessions (best effort)
        if ($sid) { $jobs = []; foreach ($sid as $ia => $s) $jobs[$ia] = ['Session/logout', null, $s]; try { $call($jobs); } catch (\Throwable $e) {} }
        return $out;
    }
}
