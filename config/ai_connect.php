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
    'voice_providers' => [
        'grok' => 'Grok',
        'openai' => 'OpenAI',
        'hermes' => 'Hermes',
        'claude' => 'Claude',
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
