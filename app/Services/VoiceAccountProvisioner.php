<?php

namespace App\Services;

use App\Fax\FaxInbox;
use App\Models\AiVoiceAccount;
use App\Portal\BillingSession;
use App\Portal\Demo;
use App\Portal\VoData;
use App\Support\AiConnect;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Idempotent voice-gateway provisioning for the signed-in customer's own account.
 * PUT {voice_gateway_url}/provision/accounts/{account_id}
 * DELETE the same path to remove it.
 * The provider API key is omitted when unchanged, and JSON null removes it.
 * Passwords, API keys, and the gateway token are never logged.
 */
class VoiceAccountProvisioner
{
    public const MCP_ENDPOINT = 'https://app.globalvoicedirect.com/mcp';

    /** Printable ASCII except space (0x20) and semicolon (0x3B). */
    private const PASSWORD_ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!#$%&*+-?@^_';

    public static function enabled(): bool
    {
        return (bool) config('ai_connect.voice_provisioning_enabled');
    }

    public static function visibleTo(?int $uid): bool
    {
        return self::enabled() && AiConnect::visibleTo($uid);
    }

    public static function providers(): array
    {
        $providers = config('ai_connect.voice_providers');
        return is_array($providers) ? $providers : [];
    }

    public static function generateSipPassword(int $length = 24): string
    {
        $length = max(12, min(128, $length));
        $alphabet = self::PASSWORD_ALPHABET;
        $max = strlen($alphabet) - 1;
        $out = '';
        for ($i = 0; $i < $length; $i++) {
            $out .= $alphabet[random_int(0, $max)];
        }
        return $out;
    }

    public static function passwordIsValid(string $password): bool
    {
        return (bool) preg_match('/\A[\x21-\x3A\x3C-\x7E]{12,128}\z/', $password);
    }

    /** PortaSwitch extension SIP login: Customer/get_extensions_list account_id (account id / login). */
    public const SIP_USERNAME = '/\A[A-Za-z0-9][A-Za-z0-9._-]{2,63}\z/';

    /**
     * SIP usernames from extension rows.
     * Live rows are Customer/get_extensions_list (account_id, id = extension number, type, name).
     * The extensions page uses the same account_id as "account".
     *
     * @return array<int, array{username: string, label: string, ext: string, name: string}>
     */
    public static function sipChoices(array $rows): array
    {
        $out = [];
        foreach ($rows as $e) {
            if (!is_array($e)) continue;
            if ((string) ($e['type'] ?? 'Account') !== 'Account') continue;
            $login = trim((string) ($e['account_id'] ?? ($e['account'] ?? '')));
            if (!preg_match(self::SIP_USERNAME, $login)) continue;
            if (isset($out[$login])) continue;
            $ext = trim((string) ($e['ext'] ?? ($e['id'] ?? '')));
            if ($ext === $login) $ext = '';
            $name = trim((string) ($e['name'] ?? ''));
            $label = $login;
            if ($ext !== '') $label = 'Ext ' . $ext . ($name !== '' ? ' — ' . $name : '') . ' · ' . $login;
            elseif ($name !== '') $label = $name . ' · ' . $login;
            $out[$login] = ['username' => $login, 'label' => $label, 'ext' => $ext, 'name' => $name];
        }
        return array_values($out);
    }

    /** This customer's own extension SIP logins. Empty when billing has no extensions. */
    public static function extensionsFor(int $userId): array
    {
        if ($userId <= 0) return [];
        if (Demo::isDemoUser($userId)) return self::sipChoices(Demo::extensions()['rows'] ?? []);
        try {
            $k = BillingSession::get();
            if (!$k) return [];
            if (!in_array((int) ($k['i_customer'] ?? 0), FaxInbox::tenantsForUser($userId), true)) return [];
            $v = new VoData($k);
            try {
                $list = $v->call('Customer/get_extensions_list', ['i_customer' => $v->ic])['extensions_list'] ?? [];
            } finally {
                try { $v->close(); } catch (\Throwable $e) {}
            }
            return self::sipChoices(is_array($list) ? $list : []);
        } catch (\Throwable $e) {
            Log::warning('voice bot extensions unavailable', ['user_id' => $userId, 'error' => $e::class]);
            return [];
        }
    }

    public function endpoint(string $accountId): string
    {
        return rtrim((string) config('ai_connect.voice_gateway_url'), '/') . '/provision/accounts/' . rawurlencode($accountId);
    }

    public function put(AiVoiceAccount $account): VoiceProvisionResult
    {
        $ready = $this->ready();
        if ($ready !== null) return $ready;

        $password = (string) $account->sip_password;
        if (!self::passwordIsValid($password)) {
            return VoiceProvisionResult::failure('The voice account could not be saved. Try again.');
        }

        $username = trim((string) $account->username);
        if (!preg_match(self::SIP_USERNAME, $username)) {
            return VoiceProvisionResult::failure('This voice account has no SIP extension username.');
        }

        $caller = preg_replace('/\D/', '', (string) $account->caller_id);
        // Gateway rejects "". A blank caller ID is JSON null. 10-digit values are prefixed with 1 by the gateway.
        $payload = [
            'sip_username' => $username,
            'username' => $username,
            'sip_password' => $password,
            'enabled' => (bool) $account->enabled,
            'caller_id' => ($caller === null || $caller === '') ? null : $caller,
            'provider' => (string) $account->provider,
        ];
        $touched = (bool) $account->api_key_dirty;
        $keyOnGateway = false;
        if ($touched) {
            $key = $account->api_key;
            $payload['api_key'] = (is_string($key) && $key !== '') ? $key : null;
            $keyOnGateway = $payload['api_key'] !== null;
        }

        try {
            $response = Http::timeout(20)->connectTimeout(5)
                ->withToken((string) config('ai_connect.voice_gateway_token'))
                ->acceptJson()
                ->asJson()
                ->put($this->endpoint((string) $account->account_id), $payload);
        } catch (\Throwable $e) {
            $this->logFailure('unreachable', $account, null, $e);
            return VoiceProvisionResult::failure('Could not reach the voice gateway. Try again.');
        }

        $status = $response->status();
        if ($status === 201 || $status === 200) {
            return VoiceProvisionResult::success($status, $touched, $keyOnGateway);
        }
        $this->logFailure('refused', $account, $status);
        if ($status === 409) return VoiceProvisionResult::failure('That username is already in use.', 409);
        if ($status === 422) return VoiceProvisionResult::failure('The voice gateway rejected those account details.', 422);
        return VoiceProvisionResult::failure('The voice gateway could not save the account.', $status);
    }

    public function delete(AiVoiceAccount $account): VoiceProvisionResult
    {
        $ready = $this->ready();
        if ($ready !== null) return $ready;

        try {
            $response = Http::timeout(20)->connectTimeout(5)
                ->withToken((string) config('ai_connect.voice_gateway_token'))
                ->acceptJson()
                ->delete($this->endpoint((string) $account->account_id));
        } catch (\Throwable $e) {
            $this->logFailure('unreachable', $account, null, $e);
            return VoiceProvisionResult::failure('Could not reach the voice gateway. Try again.');
        }

        $status = $response->status();
        if (in_array($status, [200, 204, 404], true)) {
            return VoiceProvisionResult::success($status);
        }
        $this->logFailure('refused', $account, $status);
        return VoiceProvisionResult::failure('The voice gateway could not remove the account.', $status);
    }

    private function ready(): ?VoiceProvisionResult
    {
        $url = (string) config('ai_connect.voice_gateway_url');
        $token = (string) config('ai_connect.voice_gateway_token');
        if ($url === '' || $token === '') {
            return VoiceProvisionResult::failure('Voice provisioning is not configured.');
        }
        return null;
    }

    private function logFailure(string $kind, AiVoiceAccount $account, ?int $status, ?\Throwable $e = null): void
    {
        Log::warning('voice bot gateway ' . $kind, [
            'user_id' => (int) $account->user_id,
            'account_id' => (string) $account->account_id,
            'http' => $status,
            'error' => $e ? $e::class : null,
        ]);
    }
}
