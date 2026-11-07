<?php

namespace App\Services;

use App\Models\AiVoiceAccount;
use App\Support\AiConnect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

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

    /** Gateway username rule. Digits only, after US 10-digit numbers gain a leading 1. */
    public static function normalizeSipUsername(?string $raw): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $raw);
        if ($digits === null || $digits === '') return null;
        if (strlen($digits) === 10) $digits = '1' . $digits;
        if (strlen($digits) < 11 || strlen($digits) > 15) return null;
        if (!preg_match('/\A[A-Za-z0-9][A-Za-z0-9._-]{2,63}\z/', $digits)) return null;
        return $digits;
    }

    /**
     * SIP username for this customer. Linked calling line (PortaSwitch account id) wins,
     * otherwise the first registered number in user_phone_numbers.numbers.
     */
    public static function sipUsernameFor(int $userId): ?string
    {
        if ($userId <= 0) return null;
        if (Schema::hasTable('ai_call_lines')) {
            $line = DB::table('ai_call_lines')->where('user_id', $userId)->value('account');
            if ($name = self::normalizeSipUsername(is_string($line) ? $line : null)) return $name;
        }
        if (!Schema::hasTable('user_phone_numbers') || !Schema::hasColumn('user_phone_numbers', 'numbers')) return null;
        $rows = DB::table('user_phone_numbers')->where('user_id', $userId)->orderBy('id')->pluck('numbers');
        foreach ($rows as $raw) {
            $decoded = json_decode((string) $raw, true);
            $candidates = is_array($decoded) ? $decoded : [$raw];
            foreach ($candidates as $candidate) {
                if ($name = self::normalizeSipUsername(is_scalar($candidate) ? (string) $candidate : null)) return $name;
            }
        }
        return null;
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

        $username = self::sipUsernameFor((int) $account->user_id);
        if ($username === null) {
            return VoiceProvisionResult::failure('No phone number is on file for this account.');
        }
        if ((string) $account->username !== $username) {
            DB::table('ai_voice_accounts')->where('user_id', $account->user_id)->update(['username' => $username, 'updated_at' => now()]);
            $account->username = $username;
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
