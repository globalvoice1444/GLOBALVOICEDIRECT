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

    /** @return array<string, array<string, mixed>> */
    public static function providers(): array
    {
        $providers = config('ai_connect.voice_providers');
        if (!is_array($providers)) return [];
        $out = [];
        foreach ($providers as $id => $row) {
            if (!is_array($row) || !isset($row['label'])) continue;
            $out[(string) $id] = $row;
        }
        return $out;
    }

    public static function provider(string $id): ?array
    {
        return self::providers()[$id] ?? null;
    }

    public static function providerChoiceError(): string
    {
        $names = array_map(fn ($row) => (string) $row['label'], self::providers());
        if (count($names) < 2) return 'Choose a provider.';
        $last = array_pop($names);
        return 'Choose ' . implode(', ', $names) . ', or ' . $last . '.';
    }

    /** Null when the key may be saved. A blank key means leave the stored key unchanged. */
    public static function providerKeyError(string $provider, string $key, string $stored = ''): ?string
    {
        $def = self::provider($provider);
        if (!$def) return self::providerChoiceError();
        $prefix = (string) ($def['key_prefix'] ?? '');
        $pattern = (string) ($def['key_pattern'] ?? '');
        if ($prefix === '' && $pattern === '') return null;
        $hint = (string) ($def['key_hint'] ?? 'That API key does not match the expected format.');
        if ($key === '') {
            if ($stored !== '') return null;
            return 'Enter your ' . $def['label'] . ' API key. ' . $hint;
        }
        if ($provider === 'openai' && str_starts_with($key, 'sk-ant-')) {
            return 'An OpenAI key starts with sk- and is not an Anthropic key.';
        }
        if ($pattern !== '' && !preg_match($pattern, $key)) return $hint;
        if ($prefix !== '' && !str_starts_with($key, $prefix)) return $hint;
        return null;
    }

    /** @return array<int, array<string, mixed>> */
    public static function credentialFields(string $provider): array
    {
        $def = self::provider($provider);
        $fields = is_array($def['credential_fields'] ?? null) ? $def['credential_fields'] : [];
        if ($fields === []) return [['name' => 'api_key', 'label' => 'API key', 'secret' => true, 'required' => true]];
        return $fields;
    }

    public static function providersByCategory(): array
    {
        $groups = ['realtime_voice' => [], 'text_llm' => []];
        foreach (self::providers() as $id => $row) {
            $cat = ($row['category'] ?? '') === 'realtime_voice' ? 'realtime_voice' : 'text_llm';
            $groups[$cat][$id] = $row;
        }
        return $groups;
    }

    /** @param array<string, mixed> $incoming @param array<string, mixed> $stored */
    public static function credentialErrors(string $provider, array $incoming, array $stored): array
    {
        $def = self::provider($provider);
        if (!$def) return [self::providerChoiceError()];
        $errors = [];
        $fields = self::credentialFields($provider);
        $singleKey = count($fields) === 1 && (($fields[0]['name'] ?? '') === 'api_key');
        foreach ($fields as $field) {
            $name = (string) $field['name'];
            $label = (string) ($field['label'] ?? $name);
            $value = array_key_exists($name, $incoming) ? trim((string) $incoming[$name]) : '';
            $previous = trim((string) ($stored[$name] ?? ''));
            if ($value === '') {
                if ($previous !== '') continue;
                if ($name === 'api_key' && $singleKey) {
                    $prefix = (string) ($def['key_prefix'] ?? '');
                    $keyPattern = (string) ($def['key_pattern'] ?? '');
                    if ($prefix !== '' || $keyPattern !== '') $errors[] = 'Enter your ' . ($def['label'] ?? 'provider') . ' API key.';
                    continue;
                }
                if (!empty($field['required'])) $errors[] = $label . ' is required.';
                continue;
            }
            if ($name === 'api_key') {
                $format = self::providerKeyError($provider, $value, '');
                if ($format) $errors[] = $format;
            }
            $pattern = (string) ($field['pattern'] ?? '');
            if ($pattern !== '' && !preg_match($pattern, $value)) {
                $errors[] = (string) ($field['hint'] ?? ($label . ' is not in the expected form.'));
            }
        }
        return $errors;
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
            $out[$login] = [
                'username' => $login,
                'label' => $label,
                'ext' => $ext,
                'name' => $name,
                'i_account' => (int) ($e['i_account'] ?? 0),
            ];
        }
        return array_values($out);
    }

    /**
     * Keep extensions whose PortaSwitch account belongs to this i_customer.
     * SIP username is Account/get_account_list id (falls back to the extension account_id). Passwords are dropped.
     *
     * @return array<int, array{username: string, label: string, ext: string, name: string, i_account: int}>
     */
    public static function ownedExtensions(array $extensionList, array $accountList, int $iCustomer): array
    {
        $ids = [];
        foreach ($accountList as $a) {
            if (!is_array($a)) continue;
            $ia = (int) ($a['i_account'] ?? 0);
            if ($ia <= 0 || (int) ($a['i_customer'] ?? 0) !== $iCustomer) continue;
            $ids[$ia] = trim((string) ($a['id'] ?? ''));
        }
        $rows = [];
        foreach ($extensionList as $e) {
            if (!is_array($e) || (string) ($e['type'] ?? 'Account') !== 'Account') continue;
            $ia = (int) ($e['i_account'] ?? 0);
            if ($ia <= 0 || !array_key_exists($ia, $ids)) continue;
            $rows[] = [
                'i_account' => $ia,
                'account_id' => $ids[$ia] !== '' ? $ids[$ia] : (string) ($e['account_id'] ?? ''),
                'id' => (string) ($e['id'] ?? ($e['ext'] ?? '')),
                'name' => (string) ($e['name'] ?? ''),
                'type' => 'Account',
            ];
        }
        return self::sipChoices($rows);
    }

    /**
     * Live extension list for this customer.
     * Calls Customer/get_extensions_list and Account/get_account_list for the session i_customer.
     *
     * @return array{status: string, message: string, extensions: array}
     */
    public static function lookupExtensions(int $userId): array
    {
        $none = 'No SIP extension is available on this account. Sign in with your phone-line login so we can read your extensions. Provisioning stays off until an extension is available.';
        $failed = 'We could not load your extensions from the phone system. Please try again.';
        if ($userId <= 0) return ['status' => 'empty', 'message' => $none, 'extensions' => []];
        if (Demo::isDemoUser($userId)) {
            $rows = self::sipChoices(Demo::extensions()['rows'] ?? []);
            return $rows
                ? ['status' => 'ok', 'message' => '', 'extensions' => $rows]
                : ['status' => 'empty', 'message' => $none, 'extensions' => []];
        }
        try {
            $k = BillingSession::get();
            if (!$k) return ['status' => 'empty', 'message' => $none, 'extensions' => []];
            if (!in_array((int) ($k['i_customer'] ?? 0), FaxInbox::tenantsForUser($userId), true)) {
                return ['status' => 'empty', 'message' => $none, 'extensions' => []];
            }
            $v = new VoData($k);
            try {
                $ext = $v->call('Customer/get_extensions_list', ['i_customer' => $v->ic]);
                $acc = $v->call('Account/get_account_list', ['i_customer' => $v->ic, 'limit' => 1000]);
            } finally {
                try { $v->close(); } catch (\Throwable $e) {}
            }
            $rows = self::ownedExtensions($ext['extensions_list'] ?? [], $acc['account_list'] ?? [], (int) $v->ic);
            if ($rows === []) return ['status' => 'empty', 'message' => 'No SIP extension is on your phone account yet.', 'extensions' => []];
            return ['status' => 'ok', 'message' => '', 'extensions' => $rows];
        } catch (\Throwable $e) {
            Log::warning('voice bot extensions unavailable', ['user_id' => $userId, 'error' => $e::class]);
            return ['status' => 'error', 'message' => $failed, 'extensions' => []];
        }
    }

    /**
     * SIP login, domain, and password from Account/get_account_info.
     * Returned only when i_customer and the account id match. Never log this array.
     *
     * @return array{username: string, domain: string, password: string}|null
     */
    public static function extensionCredentials(int $userId, int $iAccount, string $username): ?array
    {
        try {
            $k = BillingSession::get();
            if (!$k || $iAccount <= 0) return null;
            if (!in_array((int) ($k['i_customer'] ?? 0), FaxInbox::tenantsForUser($userId), true)) return null;
            $v = new VoData($k);
            try {
                $info = $v->call('Account/get_account_info', ['i_account' => $iAccount, 'detailed_info' => 1])['account_info'] ?? null;
            } finally {
                try { $v->close(); } catch (\Throwable $e) {}
            }
            if (!is_array($info)) return null;
            if ((int) ($info['i_customer'] ?? 0) !== (int) $k['i_customer']) return null;
            if ((string) ($info['id'] ?? '') !== $username) return null;
            $password = (string) ($info['h323_password'] ?? '');
            if ($password === '') return null;
            return [
                'username' => $username,
                'domain' => trim((string) ($info['sip_domain'] ?? ($info['domain'] ?? ''))),
                'password' => $password,
            ];
        } catch (\Throwable $e) {
            Log::warning('voice bot extension password unavailable', ['user_id' => $userId, 'i_account' => $iAccount, 'error' => $e::class]);
            return null;
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
        $fields = self::credentialFields((string) $account->provider);
        $singleKey = count($fields) === 1 && ($fields[0]['name'] ?? '') === 'api_key';
        $touched = (bool) $account->api_key_dirty || (bool) $account->credentials_dirty;
        $keyOnGateway = false;
        if ($singleKey && !self::hasPublicCredential($fields)) {
            if ($account->api_key_dirty) {
                $key = $account->api_key;
                $payload['api_key'] = (is_string($key) && $key !== '') ? $key : null;
                $keyOnGateway = $payload['api_key'] !== null;
            }
        } elseif ($touched || self::hasPublicCredential($fields)) {
            $stored = is_array($account->provider_credentials) ? $account->provider_credentials : [];
            $cred = [];
            foreach ($fields as $field) {
                $name = (string) $field['name'];
                $secret = !empty($field['secret']);
                if ($secret && !$account->credentials_dirty && $name !== 'api_key') continue;
                if ($secret && $name === 'api_key' && !$account->api_key_dirty) continue;
                $value = $name === 'api_key' ? $account->api_key : ($stored[$name] ?? null);
                $cred[$name] = (is_string($value) && $value !== '') ? $value : null;
                if ($secret && $cred[$name] !== null) $keyOnGateway = true;
            }
            $payload['credentials'] = $cred;
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

    private static function hasPublicCredential(array $fields): bool
    {
        foreach ($fields as $field) if (empty($field['secret'])) return true;
        return false;
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
