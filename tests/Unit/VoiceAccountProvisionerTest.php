<?php

namespace Tests\Unit;

use App\Models\AiVoiceAccount;
use App\Services\VoiceAccountProvisioner;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class VoiceAccountProvisionerTest extends TestCase
{
    public function test_generated_sip_passwords_are_printable_without_spaces_or_semicolons(): void
    {
        for ($i = 0; $i < 40; $i++) {
            $password = VoiceAccountProvisioner::generateSipPassword();
            $this->assertTrue(VoiceAccountProvisioner::passwordIsValid($password), $password);
            $this->assertSame(24, strlen($password));
        }
        $this->assertTrue(VoiceAccountProvisioner::passwordIsValid(VoiceAccountProvisioner::generateSipPassword(12)));
        $this->assertTrue(VoiceAccountProvisioner::passwordIsValid(VoiceAccountProvisioner::generateSipPassword(128)));
        $this->assertSame(12, strlen(VoiceAccountProvisioner::generateSipPassword(1)));
        $this->assertSame(128, strlen(VoiceAccountProvisioner::generateSipPassword(500)));
    }

    public function test_password_rules_reject_spaces_semicolons_and_bad_lengths(): void
    {
        $this->assertFalse(VoiceAccountProvisioner::passwordIsValid('short-pass'));
        $this->assertFalse(VoiceAccountProvisioner::passwordIsValid(str_repeat('a', 11)));
        $this->assertFalse(VoiceAccountProvisioner::passwordIsValid(str_repeat('a', 129)));
        $this->assertFalse(VoiceAccountProvisioner::passwordIsValid('abcdefghijkl '));
        $this->assertFalse(VoiceAccountProvisioner::passwordIsValid('abcdefghijkl;'));
        $this->assertFalse(VoiceAccountProvisioner::passwordIsValid("abcdefghijkl\n"));
        $this->assertTrue(VoiceAccountProvisioner::passwordIsValid('abcdefghijkl'));
        $this->assertTrue(VoiceAccountProvisioner::passwordIsValid('abc!@#$%^&*()'));
    }

    public function test_sip_choices_use_the_extension_account_id(): void
    {
        $choices = VoiceAccountProvisioner::sipChoices([
            ['i_account' => 9, 'id' => '101', 'account_id' => '0205500101', 'name' => 'Front Desk', 'type' => 'Account'],
            ['i_account' => 9, 'id' => '101', 'account_id' => '0205500101', 'name' => 'Front Desk', 'type' => 'Account'],
            ['i_account' => 0, 'id' => '200', 'account_id' => '', 'name' => 'Main Menu', 'type' => 'Auto Attendant'],
            ['i_account' => 10, 'id' => '102', 'account_id' => 'ab', 'name' => 'Too short', 'type' => 'Account'],
            ['ext' => '102', 'account' => 'demo102', 'name' => 'Sales', 'type' => 'Account'],
        ]);
        $this->assertSame(['0205500101', 'demo102'], array_column($choices, 'username'));
        $this->assertSame('Ext 101 — Front Desk · 0205500101', $choices[0]['label']);
    }

    public function test_owned_extensions_keep_only_this_customers_accounts_and_drop_passwords(): void
    {
        $rows = VoiceAccountProvisioner::ownedExtensions(
            [
                ['i_account' => 9, 'id' => '101', 'account_id' => '0205500101', 'name' => 'Front Desk', 'type' => 'Account'],
                ['i_account' => 77, 'id' => '109', 'account_id' => 'foreign-login', 'name' => 'Other', 'type' => 'Account'],
            ],
            [
                ['i_account' => 9, 'i_customer' => 55, 'id' => '0205500101', 'h323_password' => 'live-sip-pass1'],
                ['i_account' => 77, 'i_customer' => 99, 'id' => 'foreign-login', 'h323_password' => 'foreign-secret-pw'],
            ],
            55
        );
        $this->assertSame(['0205500101'], array_column($rows, 'username'));
        $this->assertStringNotContainsString('live-sip-pass1', json_encode($rows));
        $this->assertStringNotContainsString('foreign-secret-pw', json_encode($rows));
    }

    public function test_provider_registry_marks_calls_unsupported_and_checks_documented_key_formats(): void
    {
        $registry = VoiceAccountProvisioner::providers();
        foreach (['grok', 'openai', 'gemini', 'elevenlabs', 'deepgram', 'ultravox', 'hume', 'claude', 'hermes', 'deepseek', 'mistral', 'cohere', 'perplexity', 'llama', 'groq', 'together', 'fireworks', 'openrouter', 'azure_openai', 'bedrock'] as $id) {
            $this->assertArrayHasKey($id, $registry);
            $this->assertNotSame('', $registry[$id]['label']);
            $this->assertArrayHasKey('setup_note', $registry[$id]);
            $this->assertSame($id === 'grok', (bool) $registry[$id]['gateway_supported']);
            $this->assertContains($registry[$id]['category'], ['realtime_voice', 'text_llm']);
            $this->assertNotEmpty($registry[$id]['credential_fields']);
        }
        $this->assertTrue((bool) $registry['grok']['gateway_supported']);
        $this->assertFalse((bool) $registry['openai']['gateway_supported']);
        $this->assertSame('realtime_voice', $registry['gemini']['category']);
        $this->assertSame('text_llm', $registry['deepseek']['category']);
        $this->assertSame('text_llm', $registry['bedrock']['category']);
        $this->assertNull(VoiceAccountProvisioner::providerKeyError('grok', 'xai-example'));
        $this->assertNotNull(VoiceAccountProvisioner::providerKeyError('grok', 'sk-example'));
        $this->assertNull(VoiceAccountProvisioner::providerKeyError('openai', 'sk-example'));
        $this->assertNotNull(VoiceAccountProvisioner::providerKeyError('openai', 'sk-ant-example'));
        $this->assertNull(VoiceAccountProvisioner::providerKeyError('gemini', 'AIzaExampleKey'));
        $this->assertNotNull(VoiceAccountProvisioner::providerKeyError('gemini', 'xai-example'));
        $this->assertNull(VoiceAccountProvisioner::providerKeyError('grok', '', 'xai-example'));
        $this->assertNull(VoiceAccountProvisioner::providerKeyError('claude', 'sk-ant-example'));
        $this->assertNotNull(VoiceAccountProvisioner::providerKeyError('claude', 'sk-example'));
        $this->assertNull(VoiceAccountProvisioner::providerKeyError('elevenlabs', 'any-customer-key'));
        $this->assertNull(VoiceAccountProvisioner::providerKeyError('deepgram', 'any-customer-key'));
        $this->assertNull(VoiceAccountProvisioner::providerKeyError('hume', 'any-customer-key'));
        $this->assertNull(VoiceAccountProvisioner::providerKeyError('hermes', 'any-customer-key'));
        $this->assertNull(VoiceAccountProvisioner::providerKeyError('perplexity', 'pplx-example'));
        $this->assertNotNull(VoiceAccountProvisioner::providerKeyError('perplexity', 'sk-example'));
        $this->assertNull(VoiceAccountProvisioner::providerKeyError('llama', 'LLM_example'));
        $this->assertNotNull(VoiceAccountProvisioner::providerKeyError('llama', 'sk-example'));
        $this->assertNull(VoiceAccountProvisioner::providerKeyError('groq', 'gsk_example'));
        $this->assertNotNull(VoiceAccountProvisioner::providerKeyError('groq', 'sk-example'));
        $this->assertNull(VoiceAccountProvisioner::providerKeyError('openrouter', 'sk-or-example'));
        $this->assertNotNull(VoiceAccountProvisioner::providerKeyError('openrouter', 'sk-example'));
        $this->assertNull(VoiceAccountProvisioner::providerKeyError('deepseek', 'any-customer-key'));
        $this->assertNull(VoiceAccountProvisioner::providerKeyError('mistral', 'any-customer-key'));
        $this->assertNull(VoiceAccountProvisioner::providerKeyError('cohere', 'any-customer-key'));
        $this->assertNull(VoiceAccountProvisioner::providerKeyError('together', 'any-customer-key'));
        $this->assertNull(VoiceAccountProvisioner::providerKeyError('fireworks', 'any-customer-key'));
        $access = 'AKIA' . str_repeat('A', 16);
        $this->assertSame([], VoiceAccountProvisioner::credentialErrors('bedrock', [
            'access_key_id' => $access, 'secret_access_key' => 'secret-example', 'region' => 'us-east-1',
        ], []));
        $this->assertNotSame([], VoiceAccountProvisioner::credentialErrors('bedrock', [
            'access_key_id' => 'not-an-aws-key', 'secret_access_key' => 'secret-example', 'region' => 'us-east-1',
        ], []));
        $this->assertNotSame([], VoiceAccountProvisioner::credentialErrors('azure_openai', [
            'api_key' => 'azure-key', 'endpoint' => 'http://bad.example', 'deployment' => 'gpt-demo',
        ], []));
        $this->assertSame([], VoiceAccountProvisioner::credentialErrors('azure_openai', [
            'api_key' => 'azure-key', 'endpoint' => 'https://example.openai.azure.com', 'deployment' => 'gpt-demo',
        ], []));
        $validUltravox = 'abcdEF12.' . str_repeat('a', 32);
        $this->assertNull(VoiceAccountProvisioner::providerKeyError('ultravox', $validUltravox));
        $this->assertNotNull(VoiceAccountProvisioner::providerKeyError('ultravox', 'not-an-ultravox-key'));
        $this->assertStringContainsString('Gemini', VoiceAccountProvisioner::providerChoiceError());
    }

    public function test_put_payload_contains_sip_username_and_null_caller_id(): void
    {
        $key = str_repeat('c', 32);
        config([
            'app.key' => 'base64:' . base64_encode($key),
            'ai_connect.voice_gateway_url' => 'https://voice.example.test',
            'ai_connect.voice_gateway_token' => 'gateway-token',
        ]);
        Crypt::swap(new \Illuminate\Encryption\Encrypter($key, 'AES-256-CBC'));
        Http::fake([
            'https://voice.example.test/*' => Http::response(['ok' => true], 201),
        ]);

        $account = new AiVoiceAccount([
            'account_id' => '42',
            'username' => '0205500101',
            'sip_password' => 'live-sip-pass1',
            'enabled' => true,
            'caller_id' => '',
            'provider' => 'grok',
            'api_key' => 'xai-example',
            'api_key_dirty' => true,
        ]);

        $result = (new VoiceAccountProvisioner())->put($account);
        $this->assertTrue($result->ok);
        $this->assertSame(201, $result->httpStatus);

        Http::assertSent(function ($request) {
            $this->assertSame('PUT', $request->method());
            $this->assertSame('https://voice.example.test/provision/accounts/42', $request->url());
            $this->assertSame([
                'sip_username' => '0205500101',
                'username' => '0205500101',
                'sip_password' => 'live-sip-pass1',
                'enabled' => true,
                'caller_id' => null,
                'provider' => 'grok',
                'api_key' => 'xai-example',
            ], $request->data());
            return true;
        });
    }

    public function test_menu_visibility_follows_the_feature_flag_and_ai_connect(): void
    {
        config(['ai_connect.voice_provisioning_enabled' => false, 'ai_connect.enabled' => true]);
        $this->assertFalse(VoiceAccountProvisioner::visibleTo(7));

        config(['ai_connect.voice_provisioning_enabled' => true, 'ai_connect.enabled' => false]);
        $this->assertFalse(VoiceAccountProvisioner::visibleTo(7));

        config(['ai_connect.voice_provisioning_enabled' => true, 'ai_connect.enabled' => true]);
        $this->assertTrue(VoiceAccountProvisioner::visibleTo(7));
    }
}
