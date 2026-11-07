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
