<?php

namespace Tests\Unit;

use App\Services\VoiceAccountProvisioner;
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
        foreach (['grok', 'openai', 'gemini', 'claude', 'hermes', 'elevenlabs', 'deepgram', 'ultravox', 'hume'] as $id) {
            $this->assertArrayHasKey($id, $registry);
            $this->assertNotSame('', $registry[$id]['label']);
            $this->assertArrayHasKey('setup_note', $registry[$id]);
            $this->assertFalse((bool) $registry[$id]['gateway_supported']);
        }
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
        $validUltravox = 'abcdEF12.' . str_repeat('a', 32);
        $this->assertNull(VoiceAccountProvisioner::providerKeyError('ultravox', $validUltravox));
        $this->assertNotNull(VoiceAccountProvisioner::providerKeyError('ultravox', 'not-an-ultravox-key'));
        $this->assertStringContainsString('Gemini', VoiceAccountProvisioner::providerChoiceError());
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
