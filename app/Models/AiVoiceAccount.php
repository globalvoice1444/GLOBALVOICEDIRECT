<?php

namespace App\Models;

use App\Services\VoiceAccountProvisioner;
use Illuminate\Database\Eloquent\Model;

/** One voice-gateway account per customer. Secrets use encrypted casts and are never serialized. */
class AiVoiceAccount extends Model
{
    protected $table = 'ai_voice_accounts';

    protected $fillable = [
        'user_id',
        'account_id',
        'username',
        'sip_domain',
        'sip_password',
        'enabled',
        'caller_id',
        'provider',
        'api_key',
        'provider_credentials',
        'credentials_dirty',
        'api_key_updated_at',
        'api_key_dirty',
        'api_key_on_gateway',
        'status',
        'last_error',
        'last_result',
        'provisioned_at',
    ];

    protected $hidden = [
        'sip_password',
        'api_key',
        'provider_credentials',
    ];

    protected $casts = [
        'sip_password' => 'encrypted',
        'api_key' => 'encrypted',
        'provider_credentials' => 'encrypted:array',
        'enabled' => 'boolean',
        'api_key_dirty' => 'boolean',
        'credentials_dirty' => 'boolean',
        'api_key_on_gateway' => 'boolean',
        'api_key_updated_at' => 'datetime',
        'provisioned_at' => 'datetime',
    ];

    public function hasApiKey(): bool
    {
        if (is_string($this->api_key) && $this->api_key !== '') return true;
        $creds = is_array($this->provider_credentials) ? $this->provider_credentials : [];
        foreach (VoiceAccountProvisioner::credentialFields((string) $this->provider) as $field) {
            if (empty($field['secret'])) continue;
            $value = $creds[$field['name']] ?? null;
            if (is_string($value) && $value !== '') return true;
        }
        return false;
    }
}
