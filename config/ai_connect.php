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
    // Bring-your-own-key providers. gateway_supported is true only when this app's
    // gateway contract shows that provider can take a live call. Grok is true:
    // AiConnect posts /v1/calls and describes Asterisk → AudioSocket → Grok.
    // OpenAI stays false: that call path does not name an OpenAI adapter.
    // category realtime_voice: a speech API the customer can call with their key.
    // category text_llm: a text API; calls need a speech-to-text plus text-to-speech pipeline.
    'voice_providers' => [
        'grok' => [
            'label' => 'Grok', 'category' => 'realtime_voice', 'gateway_supported' => true,
            'key_prefix' => 'xai-', 'key_hint' => 'An xAI key starts with xai-.',
            'setup_url' => 'https://console.x.ai',
            'setup_note' => 'xAI realtime voice. Create an API key in the xAI console.',
            'credential_fields' => [['name' => 'api_key', 'label' => 'API key', 'secret' => true, 'required' => true]],
        ],
        'openai' => [
            'label' => 'OpenAI', 'category' => 'realtime_voice', 'gateway_supported' => false,
            'key_prefix' => 'sk-', 'key_hint' => 'An OpenAI key starts with sk-.',
            'setup_url' => 'https://platform.openai.com/api-keys',
            'setup_note' => 'OpenAI Realtime API. Create a secret key.',
            'credential_fields' => [['name' => 'api_key', 'label' => 'API key', 'secret' => true, 'required' => true]],
        ],
        'gemini' => [
            'label' => 'Gemini', 'category' => 'realtime_voice', 'gateway_supported' => false,
            'key_prefix' => 'AIza', 'key_hint' => 'A Gemini key from Google AI Studio starts with AIza.',
            'setup_url' => 'https://aistudio.google.com/apikey',
            'setup_note' => 'Gemini Live API. Create a key in Google AI Studio.',
            'credential_fields' => [['name' => 'api_key', 'label' => 'API key', 'secret' => true, 'required' => true]],
        ],
        'elevenlabs' => [
            'label' => 'ElevenLabs', 'category' => 'realtime_voice', 'gateway_supported' => false,
            'key_prefix' => '', 'key_hint' => '',
            'setup_url' => 'https://elevenlabs.io/app/settings/api-keys',
            'setup_note' => 'ElevenLabs Conversational AI. Create an API key in your ElevenLabs settings.',
            'credential_fields' => [['name' => 'api_key', 'label' => 'API key', 'secret' => true, 'required' => true]],
        ],
        'deepgram' => [
            'label' => 'Deepgram', 'category' => 'realtime_voice', 'gateway_supported' => false,
            'key_prefix' => '', 'key_hint' => '',
            'setup_url' => 'https://console.deepgram.com',
            'setup_note' => 'Deepgram Voice Agent. Create an API key in the Deepgram console.',
            'credential_fields' => [['name' => 'api_key', 'label' => 'API key', 'secret' => true, 'required' => true]],
        ],
        'ultravox' => [
            'label' => 'Ultravox', 'category' => 'realtime_voice', 'gateway_supported' => false,
            'key_prefix' => '', 'key_pattern' => '/\A[A-Za-z0-9]{8}\.[A-Za-z0-9]{32}\z/',
            'key_hint' => 'An Ultravox key is 8 letters or digits, a period, then 32 letters or digits.',
            'setup_url' => 'https://app.ultravox.ai/settings',
            'setup_note' => 'Ultravox realtime voice. Create an API key under Settings.',
            'credential_fields' => [['name' => 'api_key', 'label' => 'API key', 'secret' => true, 'required' => true]],
        ],
        'hume' => [
            'label' => 'Hume', 'category' => 'realtime_voice', 'gateway_supported' => false,
            'key_prefix' => '', 'key_hint' => '',
            'setup_url' => 'https://platform.hume.ai/settings/keys',
            'setup_note' => 'Hume EVI realtime voice. Create an API key in the Hume platform.',
            'credential_fields' => [['name' => 'api_key', 'label' => 'API key', 'secret' => true, 'required' => true]],
        ],
        'claude' => [
            'label' => 'Claude', 'category' => 'text_llm', 'gateway_supported' => false,
            'key_prefix' => 'sk-ant-', 'key_hint' => 'An Anthropic key starts with sk-ant-.',
            'setup_url' => 'https://console.anthropic.com',
            'setup_note' => 'Text model. Create a key at console.anthropic.com under API keys. Calls need a speech pipeline.',
            'credential_fields' => [['name' => 'api_key', 'label' => 'API key', 'secret' => true, 'required' => true]],
        ],
        'hermes' => [
            'label' => 'Hermes', 'category' => 'text_llm', 'gateway_supported' => false,
            'key_prefix' => '', 'key_hint' => '', 'setup_url' => '',
            'setup_note' => 'Text model. A customer realtime voice API for Hermes was not confirmed.',
            'credential_fields' => [['name' => 'api_key', 'label' => 'API key', 'secret' => true, 'required' => true]],
        ],
        'deepseek' => [
            'label' => 'DeepSeek', 'category' => 'text_llm', 'gateway_supported' => false,
            'key_prefix' => '', 'key_hint' => '',
            'setup_url' => 'https://platform.deepseek.com/api_keys',
            'setup_note' => 'Text model. Create an API key on the DeepSeek platform.',
            'credential_fields' => [['name' => 'api_key', 'label' => 'API key', 'secret' => true, 'required' => true]],
        ],
        'mistral' => [
            'label' => 'Mistral', 'category' => 'text_llm', 'gateway_supported' => false,
            'key_prefix' => '', 'key_hint' => '',
            'setup_url' => 'https://console.mistral.ai/api-keys',
            'setup_note' => 'Text model. Create an API key in the Mistral console.',
            'credential_fields' => [['name' => 'api_key', 'label' => 'API key', 'secret' => true, 'required' => true]],
        ],
        'cohere' => [
            'label' => 'Cohere', 'category' => 'text_llm', 'gateway_supported' => false,
            'key_prefix' => '', 'key_hint' => '',
            'setup_url' => 'https://dashboard.cohere.com/api-keys',
            'setup_note' => 'Text model. Create an API key in the Cohere dashboard.',
            'credential_fields' => [['name' => 'api_key', 'label' => 'API key', 'secret' => true, 'required' => true]],
        ],
        'perplexity' => [
            'label' => 'Perplexity', 'category' => 'text_llm', 'gateway_supported' => false,
            'key_prefix' => 'pplx-', 'key_hint' => 'A Perplexity key starts with pplx-.',
            'setup_url' => 'https://www.perplexity.ai/settings/api',
            'setup_note' => 'Text model with web search. Create an API key in Perplexity settings.',
            'credential_fields' => [['name' => 'api_key', 'label' => 'API key', 'secret' => true, 'required' => true]],
        ],
        'llama' => [
            'label' => 'Meta Llama API', 'category' => 'text_llm', 'gateway_supported' => false,
            'key_prefix' => 'LLM_', 'key_hint' => 'A Meta Model API key starts with LLM_.',
            'setup_url' => 'https://dev.meta.ai',
            'setup_note' => 'Text model. Create an API key in the Meta Model API dashboard.',
            'credential_fields' => [['name' => 'api_key', 'label' => 'API key', 'secret' => true, 'required' => true]],
        ],
        'groq' => [
            'label' => 'Groq', 'category' => 'text_llm', 'gateway_supported' => false,
            'key_prefix' => 'gsk_', 'key_hint' => 'A Groq key starts with gsk_.',
            'setup_url' => 'https://console.groq.com/keys',
            'setup_note' => 'Text model. Create an API key in the Groq console.',
            'credential_fields' => [['name' => 'api_key', 'label' => 'API key', 'secret' => true, 'required' => true]],
        ],
        'together' => [
            'label' => 'Together AI', 'category' => 'text_llm', 'gateway_supported' => false,
            'key_prefix' => '', 'key_hint' => '',
            'setup_url' => 'https://api.together.ai/settings/api-keys',
            'setup_note' => 'Text model. Create an API key in Together AI settings.',
            'credential_fields' => [['name' => 'api_key', 'label' => 'API key', 'secret' => true, 'required' => true]],
        ],
        'fireworks' => [
            'label' => 'Fireworks', 'category' => 'text_llm', 'gateway_supported' => false,
            'key_prefix' => '', 'key_hint' => '',
            'setup_url' => 'https://fireworks.ai/account/api-keys',
            'setup_note' => 'Text model. Create an API key in your Fireworks account.',
            'credential_fields' => [['name' => 'api_key', 'label' => 'API key', 'secret' => true, 'required' => true]],
        ],
        'openrouter' => [
            'label' => 'OpenRouter', 'category' => 'text_llm', 'gateway_supported' => false,
            'key_prefix' => 'sk-or-', 'key_hint' => 'An OpenRouter key starts with sk-or-.',
            'setup_url' => 'https://openrouter.ai/keys',
            'setup_note' => 'Text model router. Create an API key at OpenRouter.',
            'credential_fields' => [['name' => 'api_key', 'label' => 'API key', 'secret' => true, 'required' => true]],
        ],
        'azure_openai' => [
            'label' => 'Azure OpenAI', 'category' => 'text_llm', 'gateway_supported' => false,
            'key_prefix' => '', 'key_hint' => '',
            'setup_url' => 'https://portal.azure.com',
            'setup_note' => 'Text model on Azure. Use the resource key, endpoint, and deployment name.',
            'credential_fields' => [
                ['name' => 'api_key', 'label' => 'API key', 'secret' => true, 'required' => true],
                ['name' => 'endpoint', 'label' => 'Endpoint', 'secret' => false, 'required' => true, 'pattern' => '/\Ahttps:\/\/[A-Za-z0-9.-]+(?::\d+)?\z/', 'hint' => 'The Azure endpoint must be an https host.'],
                ['name' => 'deployment', 'label' => 'Deployment name', 'secret' => false, 'required' => true, 'pattern' => '/\A[A-Za-z0-9._-]{1,64}\z/', 'hint' => 'The deployment name may use letters, numbers, dots, underscores, and hyphens.'],
            ],
        ],
        'bedrock' => [
            'label' => 'Amazon Bedrock', 'category' => 'text_llm', 'gateway_supported' => false,
            'key_prefix' => '', 'key_hint' => '',
            'setup_url' => 'https://console.aws.amazon.com/bedrock',
            'setup_note' => 'Text models on AWS. Use an access key ID, secret access key, and region.',
            'credential_fields' => [
                ['name' => 'access_key_id', 'label' => 'Access key ID', 'secret' => true, 'required' => true, 'pattern' => '/\A(?:AKIA|ASIA)[A-Z0-9]{16}\z/', 'hint' => 'An AWS access key ID starts with AKIA or ASIA and is 20 characters.'],
                ['name' => 'secret_access_key', 'label' => 'Secret access key', 'secret' => true, 'required' => true],
                ['name' => 'region', 'label' => 'Region', 'secret' => false, 'required' => true, 'pattern' => '/\A[a-z]{2}-[a-z]+-\d\z/', 'hint' => 'Use an AWS region such as us-east-1.'],
            ],
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
