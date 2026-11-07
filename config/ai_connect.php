<?php
// 2026-11-02 AI Connect (MCP). Hidden from customers until AI_CONNECT_ENABLED=true (demo login + admins can preview).
// AI_CALLS_ENABLED gates the place_call tool separately. No secrets live here.
return [
    'enabled' => filter_var(env('AI_CONNECT_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
    'calls_enabled' => filter_var(env('AI_CALLS_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
    // 2026-11-05: AI two-way voice via Contabo Asterisk gateway (ai_call tool)
    'voice_calls_enabled' => filter_var(env('AI_VOICE_CALLS_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
    'voice_gateway_url' => rtrim((string) env('GVD_VOICE_GATEWAY_URL', ''), '/'),
    'voice_gateway_token' => (string) env('GVD_VOICE_GATEWAY_TOKEN', ''),
    // 2026-11-06: customer Voice bot provisioning. Off hides the menu link and 404s the routes.
    'voice_provisioning_enabled' => filter_var(env('AI_VOICE_PROVISIONING_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
    // Bring-your-own-key voice providers. gateway_supported is false for every entry:
    // this repo has no provision-gateway adapter contract. AiConnect's /v1/calls path
    // mentions Grok only, which is not a list of provision adapters.
    'voice_providers' => [
        'grok' => [
            'label' => 'Grok',
            'key_prefix' => 'xai-',
            'key_hint' => 'An xAI key starts with xai-.',
            'setup_url' => 'https://console.x.ai',
            'setup_note' => 'Create an API key in the xAI console. Grok has a realtime voice API.',
            'gateway_supported' => false,
        ],
        'openai' => [
            'label' => 'OpenAI',
            'key_prefix' => 'sk-',
            'key_hint' => 'An OpenAI key starts with sk-.',
            'setup_url' => 'https://platform.openai.com/api-keys',
            'setup_note' => 'Create a secret key for the OpenAI Realtime API.',
            'gateway_supported' => false,
        ],
        'gemini' => [
            'label' => 'Gemini',
            'key_prefix' => 'AIza',
            'key_hint' => 'A Gemini key from Google AI Studio starts with AIza.',
            'setup_url' => 'https://aistudio.google.com/apikey',
            'setup_note' => 'Create a Gemini API key in Google AI Studio for the Gemini Live API.',
            'gateway_supported' => false,
        ],
        'claude' => [
            'label' => 'Claude',
            'key_prefix' => 'sk-ant-',
            'key_hint' => 'An Anthropic key starts with sk-ant-.',
            'setup_url' => 'https://console.anthropic.com',
            'setup_note' => 'Create a key at console.anthropic.com under API keys. Anthropic has no realtime speech API.',
            'gateway_supported' => false,
        ],
        'hermes' => [
            'label' => 'Hermes',
            'key_prefix' => '',
            'key_hint' => '',
            'setup_url' => '',
            'setup_note' => 'Paste the API key from your Hermes account. A customer realtime voice API for Hermes was not confirmed.',
            'gateway_supported' => false,
        ],
        'elevenlabs' => [
            'label' => 'ElevenLabs',
            'key_prefix' => '',
            'key_hint' => '',
            'setup_url' => 'https://elevenlabs.io/app/settings/api-keys',
            'setup_note' => 'ElevenLabs Conversational AI uses your API key for realtime voice agents.',
            'gateway_supported' => false,
        ],
        'deepgram' => [
            'label' => 'Deepgram',
            'key_prefix' => '',
            'key_hint' => '',
            'setup_url' => 'https://console.deepgram.com',
            'setup_note' => 'Deepgram Voice Agent uses your API key on the realtime agent WebSocket.',
            'gateway_supported' => false,
        ],
        'ultravox' => [
            'label' => 'Ultravox',
            'key_prefix' => '',
            'key_pattern' => '/\A[A-Za-z0-9]{8}\.[A-Za-z0-9]{32}\z/',
            'key_hint' => 'An Ultravox key is 8 letters or digits, a period, then 32 letters or digits.',
            'setup_url' => 'https://app.ultravox.ai/settings',
            'setup_note' => 'Create an Ultravox API key under Settings. Ultravox provides a realtime voice API.',
            'gateway_supported' => false,
        ],
        'hume' => [
            'label' => 'Hume',
            'key_prefix' => '',
            'key_hint' => '',
            'setup_url' => 'https://platform.hume.ai/settings/keys',
            'setup_note' => 'Hume EVI is a realtime empathic voice API. Create an API key in the Hume platform.',
            'gateway_supported' => false,
        ],
    ],
    'mcp_endpoint' => 'https://app.globalvoicedirect.com/mcp',
    // 2026-11-02 oauth: OAuth 2.1 sign-in for ChatGPT and other OAuth-only MCP clients. Off by default.
    'oauth_enabled' => filter_var(env('AI_CONNECT_OAUTH_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
    'oauth_access_ttl' => 3600,   // seconds
    'oauth_refresh_days' => 30,
    'max_keys' => 10,
    'default_daily_limit' => 200,
    'max_daily_limit' => 2000,
    'scopes' => [
        'contacts:read' => 'Read and add contacts',
        'campaigns:read' => 'Read email templates',
        'email:send' => 'Send emails from your connected mailbox',
        'sequences:run' => 'Add contacts to your email sequences',
        'calls:place' => 'Place calls from your line',
        'calls:ai' => 'Place AI voice calls (two-way Grok conversation via the GVD voice gateway)',
        'reports:read' => 'Read campaign reports',
    ],
];
