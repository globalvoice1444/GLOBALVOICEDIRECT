<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** One voice-gateway account per customer. Secrets use encrypted casts and are never serialized. */
class AiVoiceAccount extends Model
{
    protected $table = 'ai_voice_accounts';

    protected $fillable = [
        'user_id',
        'account_id',
        'username',
        'sip_password',
        'enabled',
        'caller_id',
        'provider',
        'api_key',
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
    ];

    protected $casts = [
        'sip_password' => 'encrypted',
        'api_key' => 'encrypted',
        'enabled' => 'boolean',
        'api_key_dirty' => 'boolean',
        'api_key_on_gateway' => 'boolean',
        'api_key_updated_at' => 'datetime',
        'provisioned_at' => 'datetime',
    ];

    public function hasApiKey(): bool
    {
        return is_string($this->api_key) && $this->api_key !== '';
    }
}
