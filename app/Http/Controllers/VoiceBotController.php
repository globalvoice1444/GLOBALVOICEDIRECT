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
        return response()->view('users.gvd-voice-bot', [
            'account' => $this->present($account),
            'providers' => VoiceAccountProvisioner::providers(),
            'endpoint' => (string) config('ai_connect.mcp_endpoint', VoiceAccountProvisioner::MCP_ENDPOINT),
            'demo' => Demo::isDemoUser($uid),
        ])->withHeaders(self::NC);
    }

    public function provision(Request $r)
    {
        $uid = $this->uid();
        if (Demo::isDemoUser($uid)) return $this->j(['ok' => false, 'error' => Demo::READONLY], 422);

        $username = trim((string) $r->input('username', ''));
        $provider = (string) $r->input('provider', '');
        $caller = preg_replace('/\D/', '', (string) $r->input('caller_id', ''));
        $key = trim((string) $r->input('api_key', ''));
        if (!preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]{2,63}$/', $username)) {
            return $this->j(['ok' => false, 'error' => 'Username must be 3 to 64 letters, numbers, dots, underscores, or hyphens.'], 422);
        }
        if (!array_key_exists($provider, VoiceAccountProvisioner::providers())) {
            return $this->j(['ok' => false, 'error' => 'Choose Grok, OpenAI, or Hermes.'], 422);
        }
        if (!preg_match('/^[0-9]{10,15}$/', $caller)) {
            return $this->j(['ok' => false, 'error' => 'Caller ID must be 10 to 15 digits.'], 422);
        }
        if (strlen($key) > 2000) {
            return $this->j(['ok' => false, 'error' => 'That provider key is too long.'], 422);
        }

        $account = AiVoiceAccount::firstOrNew(['user_id' => $uid]);
        if (!$account->exists) {
            $account->account_id = (string) $uid;
            $account->sip_password = VoiceAccountProvisioner::generateSipPassword();
        } elseif (!VoiceAccountProvisioner::passwordIsValid((string) $account->sip_password)) {
            $account->sip_password = VoiceAccountProvisioner::generateSipPassword();
        }
        $account->username = $username;
        $account->caller_id = $caller;
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

    public function removeKey(Request $r)
    {
        $uid = $this->uid();
        if (Demo::isDemoUser($uid)) return $this->j(['ok' => false, 'error' => Demo::READONLY], 422);

        $account = AiVoiceAccount::where('user_id', $uid)->first();
        if (!$account || (!$account->hasApiKey() && !$account->api_key_on_gateway)) {
            return $this->j(['ok' => true, 'status' => $account?->status]);
        }
        $account->api_key = null;
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
                'status' => null,
                'last_error' => null,
                'provisioned' => false,
            ];
        }
        return [
            'username' => (string) $account->username,
            'caller_id' => (string) $account->caller_id,
            'provider' => (string) $account->provider,
            'enabled' => (bool) $account->enabled,
            'has_key' => $account->hasApiKey(),
            'status' => (string) $account->status,
            'last_error' => $account->last_error ? (string) $account->last_error : null,
            'provisioned' => $account->provisioned_at !== null,
        ];
    }
}
