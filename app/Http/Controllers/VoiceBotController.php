<?php

namespace App\Http\Controllers;

use App\Jobs\ProvisionVoiceAccount;
use App\Models\AiVoiceAccount;
use App\Portal\Demo;
use App\Services\VoiceAccountProvisioner;
use Illuminate\Http\Request;

/** Customer Voice bot page. Every row is the signed-in user's. Secrets are never rendered. */
class VoiceBotController extends Controller
{
    const NC = ['Cache-Control' => 'private, no-store, max-age=0'];
    const NO_EXTENSION = 'No SIP extension is available on this account. Sign in with your phone-line login so we can read your extensions. Provisioning stays off until an extension is available.';

    private function uid(): int
    {
        $u = (int) session()->get('user_id');
        abort_unless($u > 0, 403);
        abort_unless(VoiceAccountProvisioner::visibleTo($u), 404);
        return $u;
    }

    private function j(array $d, int $s = 200)
    {
        return response()->json($d, $s, self::NC);
    }

    public function index()
    {
        $uid = $this->uid();
        $account = AiVoiceAccount::where('user_id', $uid)->first();
        $lookup = VoiceAccountProvisioner::lookupExtensions($uid);
        $extensions = $lookup['extensions'];
        $allowed = array_column($extensions, 'username');
        $presented = $this->present($account);
        $selected = $presented['username'];
        if (!in_array($selected, $allowed, true)) $selected = count($allowed) === 1 ? $allowed[0] : '';
        $presented['username'] = $selected;
        $keep = (string) ($presented['provider'] ?? '');
        $providers = VoiceAccountProvisioner::customerProviders($keep !== '' ? $keep : null);
        return response()->view('users.gvd-voice-bot', [
            'account' => $presented,
            'extensions' => $extensions,
            'extensionMessage' => $lookup['status'] === 'ok' ? '' : $lookup['message'],
            'providers' => $providers,
            'providerGroups' => VoiceAccountProvisioner::providersByCategory($providers),
            'endpoint' => (string) config('ai_connect.mcp_endpoint', VoiceAccountProvisioner::MCP_ENDPOINT),
            'demo' => Demo::isDemoUser($uid),
            'canProvision' => $lookup['status'] === 'ok' && $allowed !== [],
        ])->withHeaders(self::NC);
    }

    public function provision(Request $r)
    {
        $uid = $this->uid();
        if (Demo::isDemoUser($uid)) return $this->j(['ok' => false, 'error' => Demo::READONLY], 422);

        $lookup = VoiceAccountProvisioner::lookupExtensions($uid);
        if ($lookup['status'] === 'error') return $this->j(['ok' => false, 'error' => $lookup['message']], 422);
        $extensions = $lookup['extensions'];
        if ($extensions === []) return $this->j(['ok' => false, 'error' => $lookup['message'] ?: self::NO_EXTENSION], 422);
        $match = $this->ownedExtension($extensions, $r);
        if ($match === null) return $this->j(['ok' => false, 'error' => 'That extension is not on your account.'], 422);
        $creds = VoiceAccountProvisioner::extensionCredentials($uid, (int) $match['i_account'], $match['username']);
        if ($creds === null || !VoiceAccountProvisioner::passwordIsValid($creds['password'])) {
            return $this->j(['ok' => false, 'error' => 'We could not use the SIP password already on that extension.'], 422);
        }
        $password = $creds['password'];
        $username = $creds['username'];
        $domain = $creds['domain'];
        $provider = (string) $r->input('provider', '');
        $caller = preg_replace('/\D/', '', (string) $r->input('caller_id', ''));
        $key = trim((string) $r->input('api_key', ''));
        if (!array_key_exists($provider, VoiceAccountProvisioner::providers())) {
            return $this->j(['ok' => false, 'error' => VoiceAccountProvisioner::providerChoiceError()], 422);
        }
        $existingProvider = (string) (AiVoiceAccount::where('user_id', $uid)->value('provider') ?? '');
        if (!VoiceAccountProvisioner::providerIsOffered($provider, $existingProvider !== '' ? $existingProvider : null)) {
            return $this->j(['ok' => false, 'error' => 'That provider is not available.'], 422);
        }
        if ($caller !== '' && !preg_match('/\A[0-9]{10,15}\z/', $caller)) {
            return $this->j(['ok' => false, 'error' => 'Caller ID must be 10 to 15 digits, or left blank.'], 422);
        }
        if (strlen($key) > 2000) {
            return $this->j(['ok' => false, 'error' => 'That provider key is too long.'], 422);
        }

        $account = AiVoiceAccount::firstOrNew(['user_id' => $uid]);
        if (!$account->exists) $account->account_id = (string) $uid;
        $incoming = $this->incomingCredentials($r, $key);
        $stored = is_array($account->provider_credentials) ? $account->provider_credentials : [];
        if (is_string($account->api_key) && $account->api_key !== '') $stored['api_key'] = $account->api_key;
        $errors = VoiceAccountProvisioner::credentialErrors($provider, $incoming, $stored);
        if ($errors) return $this->j(['ok' => false, 'error' => $errors[0]], 422);
        $merged = $stored;
        $secretChanged = false;
        foreach (VoiceAccountProvisioner::credentialFields($provider) as $field) {
            $name = (string) $field['name'];
            $value = array_key_exists($name, $incoming) ? trim((string) $incoming[$name]) : '';
            if (!empty($field['secret'])) {
                if ($value === '') continue;
                $merged[$name] = $value;
                $secretChanged = true;
            } else {
                $merged[$name] = $value;
            }
        }
        if (isset($merged['api_key']) && $merged['api_key'] !== ($stored['api_key'] ?? null)) {
            $account->api_key = $merged['api_key'];
            $account->api_key_dirty = true;
            $account->api_key_updated_at = now();
        }
        $keep = [];
        foreach (VoiceAccountProvisioner::credentialFields($provider) as $field) {
            $name = (string) $field['name'];
            if ($name === 'api_key' || !array_key_exists($name, $merged)) continue;
            if ($merged[$name] === null || $merged[$name] === '') continue;
            $keep[$name] = $merged[$name];
        }
        $account->provider_credentials = $keep === [] ? null : $keep;
        if ($secretChanged) $account->credentials_dirty = true;
        $account->sip_password = $password;
        $account->sip_domain = $domain !== '' ? $domain : null;
        $account->username = $username;
        $account->caller_id = $caller === '' ? null : $caller;
        $account->provider = $provider;
        $account->enabled = filter_var($r->input('enabled', false), FILTER_VALIDATE_BOOLEAN);
        if ($key !== '') {
            $account->api_key = $key;
            $account->api_key_dirty = true;
            $account->api_key_updated_at = now();
        }
        $account->status = 'pending';
        $account->last_error = null;
        $account->save();

        ProvisionVoiceAccount::enqueue($uid, ProvisionVoiceAccount::PUT);
        return $this->afterJob($uid, 'Could not provision the voice account.');
    }

    private function incomingCredentials(Request $r, string $key): array
    {
        $incoming = $r->input('credentials', []);
        if (!is_array($incoming)) $incoming = [];
        $clean = [];
        foreach ($incoming as $name => $value) {
            if (!is_string($name) || !is_scalar($value)) continue;
            $clean[$name] = trim((string) $value);
        }
        if ($r->exists('api_key')) $clean['api_key'] = $key;
        return $clean;
    }

    /** The extension id or SIP login must be one returned by the fresh PortaSwitch list. */
    private function ownedExtension(array $extensions, Request $r): ?array
    {
        $byUser = [];
        $byId = [];
        foreach ($extensions as $ex) {
            $byUser[$ex['username']] = $ex;
            if ((int) ($ex['i_account'] ?? 0) > 0) $byId[(int) $ex['i_account']] = $ex;
        }
        $extId = (int) $r->input('extension');
        $picked = trim((string) $r->input('sip_username', $r->input('username', '')));
        if ($extId > 0) {
            $match = $byId[$extId] ?? null;
            if ($match && $picked !== '' && $picked !== $match['username']) return null;
            return $match;
        }
        if ($picked !== '') return $byUser[$picked] ?? null;
        return count($extensions) === 1 ? $extensions[0] : null;
    }

    public function removeKey(Request $r)
    {
        $uid = $this->uid();
        if (Demo::isDemoUser($uid)) return $this->j(['ok' => false, 'error' => Demo::READONLY], 422);

        $account = AiVoiceAccount::where('user_id', $uid)->first();
        if (!$account || (!$account->hasApiKey() && !$account->api_key_on_gateway)) {
            return $this->j(['ok' => true, 'status' => $account?->status]);
        }
        $account->api_key = null;
        $creds = is_array($account->provider_credentials) ? $account->provider_credentials : [];
        foreach (VoiceAccountProvisioner::credentialFields((string) $account->provider) as $field) {
            if (!empty($field['secret'])) $creds[$field['name']] = null;
        }
        unset($creds['api_key']);
        $account->provider_credentials = $creds === [] ? null : $creds;
        $account->credentials_dirty = true;
        $account->api_key_updated_at = now();
        $account->api_key_dirty = (bool) $account->api_key_on_gateway || $account->provisioned_at !== null;
        $account->save();
        if (!$account->api_key_dirty) {
            return $this->j(['ok' => true, 'status' => $account->status]);
        }
        $account->status = 'pending';
        $account->last_error = null;
        $account->save();
        ProvisionVoiceAccount::enqueue($uid, ProvisionVoiceAccount::PUT);
        return $this->afterJob($uid, 'Could not remove the provider key.');
    }

    public function remove(Request $r)
    {
        $uid = $this->uid();
        if (Demo::isDemoUser($uid)) return $this->j(['ok' => false, 'error' => Demo::READONLY], 422);

        $account = AiVoiceAccount::where('user_id', $uid)->first();
        if (!$account) return $this->j(['ok' => true]);
        if ($account->provisioned_at === null) {
            $account->delete();
            return $this->j(['ok' => true, 'status' => 'removed']);
        }
        $account->status = 'removing';
        $account->last_error = null;
        $account->save();
        ProvisionVoiceAccount::enqueue($uid, ProvisionVoiceAccount::DELETE);
        $left = AiVoiceAccount::where('user_id', $uid)->first();
        if ($left && $left->status === 'error') {
            return $this->j(['ok' => false, 'error' => $left->last_error ?: 'Could not remove the voice account.', 'status' => 'error'], 422);
        }
        return $this->j(['ok' => true, 'queued' => (bool) $left, 'status' => $left->status ?? 'removed']);
    }

    private function afterJob(int $uid, string $fallback)
    {
        $account = AiVoiceAccount::where('user_id', $uid)->first();
        if (!$account) return $this->j(['ok' => true, 'status' => 'removed']);
        if ($account->status === 'error') {
            return $this->j(['ok' => false, 'error' => $account->last_error ?: $fallback, 'status' => 'error'], 422);
        }
        return $this->j([
            'ok' => true,
            'queued' => $account->status === 'pending',
            'status' => $account->status,
            'has_key' => $account->hasApiKey(),
        ]);
    }

    /** Fields the page may render. No password and no provider key. */
    private function present(?AiVoiceAccount $account): array
    {
        if (!$account) {
            return [
                'username' => '',
                'caller_id' => '',
                'provider' => 'grok',
                'enabled' => true,
                'has_key' => false,
                'public_credentials' => [],
                'status' => null,
                'last_error' => null,
                'provisioned' => false,
            ];
        }
        return [
            'username' => (string) $account->username,
            'caller_id' => (string) ($account->caller_id ?? ''),
            'provider' => (string) $account->provider,
            'enabled' => (bool) $account->enabled,
            'has_key' => $account->hasApiKey(),
            'public_credentials' => $this->publicCredentials($account),
            'status' => (string) $account->status,
            'last_error' => $account->last_error ? (string) $account->last_error : null,
            'provisioned' => $account->provisioned_at !== null,
        ];
    }

    private function publicCredentials(AiVoiceAccount $account): array
    {
        $stored = is_array($account->provider_credentials) ? $account->provider_credentials : [];
        $out = [];
        foreach (VoiceAccountProvisioner::credentialFields((string) $account->provider) as $field) {
            if (!empty($field['secret'])) continue;
            $name = (string) $field['name'];
            $out[$name] = (string) ($stored[$name] ?? '');
        }
        return $out;
    }
}
