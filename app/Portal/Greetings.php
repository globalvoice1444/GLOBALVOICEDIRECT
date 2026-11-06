<?php

namespace App\Portal;

use Illuminate\Support\Facades\Http;

/**
 * Company (auto attendant) and extension (voicemail) greetings, read and written with the customer's own
 * billing session. Voice-application calls sign in as the tenant's own accounts; nothing is logged.
 */
class Greetings
{
    public array $k;
    public int $ic;
    private array $accounts = [];
    private array $umSessions = [];

    public function __construct(array $k) { $this->k = $k; $this->ic = (int) $k['i_customer']; }

    public function isAdmin(): bool { return in_array($this->k['realm'] ?? '', ['customer', ''], true); }

    protected function b(string $m, array $p = []): array { return BillingSession::call($this->k, $m, $p); }

    protected static function never(array $a): bool
    {
        if (in_array((int) ($a['i_account'] ?? 0), config('greetings.never_accounts', []), true)) return true;
        foreach ([(string) ($a['id'] ?? ''), (string) ($a['login'] ?? '')] as $v) {
            $d = preg_replace('/\D/', '', $v);
            foreach (config('greetings.never_lines', []) as $n) if ($d !== '' && str_contains($d, $n)) return true;
        }
        return false;
    }

    /** Full account record (with the voice-application login) for one of THIS tenant's accounts. */
    protected function account(int $ia): array
    {
        if (isset($this->accounts[$ia])) return $this->accounts[$ia];
        $a = $this->b('Account/get_account_info', ['i_account' => $ia, 'detailed_info' => 1])['account_info'] ?? null;
        if (!$a || (int) ($a['i_customer'] ?? 0) !== $this->ic || self::never($a)) throw new \DomainException('not yours');
        return $this->accounts[$ia] = $a;
    }

    /** POST to the voice-application REST as one account. $file = [path, filename] for uploads. */
    protected function um(int $ia, string $method, ?array $params = null, bool $blob = false, ?array $file = null)
    {
        $a = $this->account($ia);
        $base = rtrim(config('greetings.um_rest'), '/');
        if (!isset($this->umSessions[$ia])) {
            $s = Http::asMultipart()->timeout(20)->connectTimeout(5)->post("$base/Session/login", [['name' => 'params', 'contents' => json_encode([
                'login' => (string) $a['id'], 'password' => (string) ($a['password'] ?? ''), 'domain' => (string) ($a['um_domain'] ?? config('greetings.um_domain'))])]])->json();
            if (empty($s['session_id'])) throw new \RuntimeException('voice login failed');
            $this->umSessions[$ia] = $s['session_id'];
        }
        $parts = [['name' => 'auth_info', 'contents' => json_encode(['session_id' => $this->umSessions[$ia]])]];
        if ($params !== null) $parts[] = ['name' => 'params', 'contents' => json_encode((object) $params)];
        if ($file) $parts[] = ['name' => 'upload_file', 'contents' => fopen($file[0], 'r'), 'filename' => $file[1], 'headers' => ['Content-Type' => 'audio/wav']];
        $r = Http::asMultipart()->timeout(60)->connectTimeout(5)->post("$base/$method", $parts);
        if ($blob) {
            $ct = (string) $r->header('Content-Type');
            if ($r->status() !== 200 || str_contains($ct, 'json') || strlen($r->body()) < 32) return null;
            return $r->body();
        }
        $j = $r->json();
        if (!is_array($j)) throw new \RuntimeException('bad voice response');
        if (!empty($j['faultcode'])) throw new \RuntimeException('voice fault: ' . preg_replace('/[^\w.]/', '', (string) $j['faultcode']));
        return $j;
    }

    public function close(): void
    {
        foreach ($this->umSessions as $ia => $sid) { try { $this->um($ia, 'Session/logout', ['session_id' => $sid]); } catch (\Throwable $e) {} }
        $this->umSessions = [];
    }

    /** The tenant's auto-attendant account (the one the Virtual Office portal uses: "<i_customer>-vo-did"). */
    public function aaAccount(): ?int
    {
        $want = $this->ic . '-vo-did';
        foreach ($this->b('Account/get_account_list', ['i_customer' => $this->ic, 'limit' => 1000])['account_list'] ?? [] as $a)
            if ((string) ($a['id'] ?? '') === $want && !self::never($a)) return (int) $a['i_account'];
        return null;
    }

    public function extensions(): array
    {
        $out = [];
        foreach ($this->b('Customer/get_extensions_list', ['i_customer' => $this->ic])['extensions_list'] ?? [] as $x) {
            if (empty($x['i_account']) || ($x['type'] ?? 'Account') !== 'Account') continue;
            if (self::never(['i_account' => $x['i_account'], 'id' => $x['account_id'] ?? ''])) continue;
            $out[] = ['i_account' => (int) $x['i_account'], 'ext' => (string) $x['id'], 'name' => (string) ($x['name'] ?? '')];
        }
        usort($out, fn ($a, $b) => [(int) $a['ext'] > 999, (int) $a['ext']] <=> [(int) $b['ext'] > 999, (int) $b['ext']]);
        return $out;
    }

    /** Company menus with transitions, and who plays now. */
    public function company(\DateTimeInterface $now): ?array
    {
        $aa = $this->aaAccount();
        if (!$aa) return null;
        $menus = [];
        foreach ($this->um($aa, 'AutoAttendant/get_menu_list', [])['menu_list'] ?? [] as $m) {
            $tr = $this->um($aa, 'AutoAttendant/get_menu_transition_list', ['i_menu' => (int) $m['i_menu']])['transition_list'] ?? [];
            $m['transitions'] = [];
            foreach ($tr as $t) $m['transitions'][(string) $t['event']] = $t;
            $menus[(int) $m['i_menu']] = $m;
        }
        $root = null; foreach ($menus as $m) if (strtoupper((string) $m['name']) === 'ROOT') $root = $m;
        if (!$root) return ['aa' => $aa, 'menus' => $menus, 'root' => null, 'after' => null, 'now' => null];
        $afterId = (int) (($root['transitions']['Not Active']['target_i_menu'] ?? 0) ?: 0);
        // Who answers now: follow "Not Active" from ROOT while the menu's period is inactive.
        $cur = $root; $seen = [];
        while ($cur && !Period::active($cur['period'] ?? 'Always', $now) && !isset($seen[$cur['i_menu']])) {
            $seen[$cur['i_menu']] = 1;
            $t = $cur['transitions']['Not Active'] ?? null;
            $cur = ($t && ($t['action'] ?? '') === 'Menu' && !empty($t['target_i_menu'])) ? ($menus[(int) $t['target_i_menu']] ?? null) : null;
        }
        return ['aa' => $aa, 'menus' => $menus, 'root' => (int) $root['i_menu'], 'after' => $afterId ?: null, 'now' => $cur ? (int) $cur['i_menu'] : null];
    }

    public function noInputText(array $m, array $exts): string
    {
        $t = $m['transitions']['Timeout'] ?? null;
        if (!$t) return 'Not set';
        return match ($t['action'] ?? '') {
            'Transfer' => 'Transfer to ext ' . ($t['destination'] ?? '?'),
            'Menu' => 'Go to another menu',
            'Disabled' => 'Do nothing (hang up)',
            'Voicemail' => 'Go to voicemail' . (!empty($t['destination']) ? ' of ext ' . $t['destination'] : ''),
            default => (string) ($t['action'] ?? 'Not set'),
        };
    }

    /** "If there is no input after greeting plays": action label + target label, read from the menu's Timeout transition. */
    public function noInputDetail(array $m, array $exts): array
    {
        $t = $m['transitions']['Timeout'] ?? null;
        if (!$t) return ['action' => 'Not set', 'target' => null];
        $dest = (string) ($t['destination'] ?? '');
        $extLabel = function (string $d) use ($exts) {
            if ($d === '') return null;
            foreach ($exts as $e) if ($e['ext'] === $d) return 'Ext ' . $e['ext'] . ($e['name'] !== '' ? ' - ' . $e['name'] : '');
            return 'Ext ' . $d;
        };
        return match ($t['action'] ?? '') {
            'Transfer' => ['action' => 'Transfer to extension', 'target' => $extLabel($dest)],
            'Voicemail' => ['action' => 'Go to voicemail', 'target' => $extLabel($dest)],
            'Menu' => ['action' => 'Go to another menu', 'target' => null],
            'Disabled' => ['action' => 'Do nothing (hang up)', 'target' => null],
            default => ['action' => (string) ($t['action'] ?? 'Not set'), 'target' => $dest !== '' ? $dest : null],
        };
    }

    // ---------- audio ----------
    public function menuPrompt(int $aa, int $iMenu, string $type = 'menu'): ?string
    {
        return $this->um($aa, 'AutoAttendant/get_menu_prompt', ['i_menu' => $iMenu, 'prompt_type' => $type], true);
    }

    public function vmGreeting(int $ia, string $type = 'standard'): ?string
    {
        // "standard" = what callers hear now: the custom (personal) recording when one is active, else the system default.
        if ($type === 'standard' && (($this->um($ia, 'Voicemail/get_vm_settings', [])['vm_settings']['greetings'] ?? '') === 'personal')) $type = 'personal';
        return $this->um($ia, 'Voicemail/get_vm_greeting', ['greeting_type' => $type], true);
    }

    public function setMenuPrompt(int $aa, int $iMenu, string $path, string $type = 'menu'): void
    {
        $this->um($aa, 'AutoAttendant/set_menu_prompt', ['prompt_type' => $type, 'i_menu' => $iMenu, 'prompt' => 'greeting.wav'], false, [$path, 'greeting.wav']);
    }

    public function setVmGreeting(int $ia, string $path, string $type = 'standard'): void
    {
        // The system "standard" greeting cannot be uploaded; a custom one is the "personal" greeting, then made active.
        $up = $type === 'standard' ? 'personal' : $type;
        $this->um($ia, 'Voicemail/set_vm_greeting', ['greeting_info' => ['greeting_type' => $up, 'filename' => 'greeting.wav']], false, [$path, 'greeting.wav']);
        if ($up === 'personal') {
            $vs = $this->um($ia, 'Voicemail/get_vm_settings', [])['vm_settings'] ?? [];
            if (($vs['greetings'] ?? '') !== 'personal') $this->um($ia, 'Voicemail/set_vm_settings', ['vm_settings' => ['greetings' => 'personal']]);
        }
    }

    /** Extension voicemail back to the built-in default greeting (the custom recording is kept, just not played). */
    public function restoreVmDefault(int $ia): void
    {
        $this->um($ia, 'Voicemail/set_vm_settings', ['vm_settings' => ['greetings' => 'standard']]);
    }

    public function updateMenu(int $aa, array $info): void { $this->um($aa, 'AutoAttendant/update_menu', ['menu_info' => $info]); }
    public function createMenu(int $aa, array $info): int { return (int) ($this->um($aa, 'AutoAttendant/create_menu', ['menu_info' => $info])['i_menu'] ?? 0); }
    public function delMenu(int $aa, int $iMenu): void { $this->um($aa, 'AutoAttendant/del_menu', ['i_menu' => $iMenu]); }
    public function vmAccountOk(int $ia): bool { foreach ($this->extensions() as $e) if ($e['i_account'] === $ia) return true; return false; }

    /** Sun .au (mu-law / A-law / PCM16) -> PCM16 WAV for any browser. */
    public static function auToWav(string $au): ?string
    {
        if (substr($au, 0, 4) !== '.snd') return str_starts_with($au, 'RIFF') ? $au : null;
        $h = unpack('Noff/Nsize/Nenc/Nrate/Nch', substr($au, 4, 20));
        $data = substr($au, $h['off']);
        $ch = max(1, $h['ch']); $rate = $h['rate'] ?: 8000;
        if ($h['enc'] === 1 || $h['enc'] === 27) {
            $pcm = '';
            $n = strlen($data);
            for ($i = 0; $i < $n; $i++) $pcm .= pack('v', $h['enc'] === 1 ? self::ulaw(ord($data[$i])) : self::alaw(ord($data[$i])));
        } elseif ($h['enc'] === 3) {
            $pcm = '';
            foreach (str_split($data, 2) as $s) if (strlen($s) === 2) $pcm .= $s[1] . $s[0];
        } else return null;
        return 'RIFF' . pack('V', 36 + strlen($pcm)) . 'WAVEfmt ' . pack('VvvVVvv', 16, 1, $ch, $rate, $rate * $ch * 2, $ch * 2, 16) . 'data' . pack('V', strlen($pcm)) . $pcm;
    }

    private static function ulaw(int $u): int
    {
        $u = ~$u & 0xFF; $t = (($u & 0x0F) << 3) + 0x84; $t <<= ($u & 0x70) >> 4;
        $v = ($u & 0x80) ? (0x84 - $t) : ($t - 0x84);
        return $v & 0xFFFF;
    }

    private static function alaw(int $a): int
    {
        $a ^= 0x55; $t = ($a & 0x0F) << 4; $seg = ($a & 0x70) >> 4;
        $t = $seg === 0 ? $t + 8 : (($t + 0x108) << ($seg - 1));
        $v = ($a & 0x80) ? $t : -$t;
        return $v & 0xFFFF;
    }
}
