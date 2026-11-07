<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Jobs\ProvisionVoiceAccount;
use App\Models\AiVoiceAccount;
use App\Services\VoiceAccountProvisioner;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class VoiceBotTest extends TestCase
{
    private array $logs = [];

    protected function setUp(): void
    {
        parent::setUp();
        $key = str_repeat('b', 32);
        config(['app.key' => 'base64:' . base64_encode($key)]);
        Crypt::swap(new \Illuminate\Encryption\Encrypter($key, 'AES-256-CBC'));

        $migration = require database_path('migrations/2026_11_06_120000_create_ai_voice_accounts_table.php');
        $migration->up();
        if (!Schema::hasTable('user_phone_numbers')) {
            Schema::create('user_phone_numbers', function ($table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('i_customer')->nullable();
                $table->text('numbers')->nullable();
            });
        }
        Schema::dropIfExists('ai_call_lines');

        config([
            'ai_connect.voice_provisioning_enabled' => true,
            'ai_connect.enabled' => true,
            'ai_connect.voice_gateway_url' => 'https://voice.example.test',
            'ai_connect.voice_gateway_token' => 'gateway-token-test',
            'queue.default' => 'sync',
        ]);
        $this->withoutMiddleware(VerifyCsrfToken::class);
        Log::listen(function ($event) {
            $this->logs[] = $event->message . ' ' . json_encode($event->context);
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('ai_voice_accounts');
        Schema::dropIfExists('user_phone_numbers');
        Schema::dropIfExists('ai_call_lines');
        parent::tearDown();
    }

    public function test_routes_are_not_found_when_provisioning_is_off(): void
    {
        config(['ai_connect.voice_provisioning_enabled' => false]);
        Http::fake();
        $this->get('/user/ai-connect/voice-bot')->assertNotFound();
        $this->withSession(['user_login' => true, 'user_id' => 42])
            ->postJson('/user/ai-connect/voice-bot', ['username' => 'alice'])
            ->assertNotFound();
        Http::assertNothingSent();
    }

    public function test_hidden_from_customers_who_cannot_see_ai_connect(): void
    {
        config(['ai_connect.enabled' => false]);
        $this->withSession(['user_login' => true, 'user_id' => 42])
            ->get('/user/ai-connect/voice-bot')
            ->assertNotFound();
    }

    public function test_page_shows_mcp_endpoint_and_only_the_signed_in_account(): void
    {
        DB::table('user_phone_numbers')->insert(['user_id' => 42, 'numbers' => '(561) 555-0100']);
        AiVoiceAccount::create([
            'user_id' => 42,
            'account_id' => '42',
            'username' => 'alice-voice',
            'sip_password' => 'alice-secret-pw',
            'enabled' => true,
            'caller_id' => '5615550100',
            'provider' => 'grok',
            'api_key' => 'provider-key-alice',
            'status' => 'active',
            'provisioned_at' => now(),
        ]);
        AiVoiceAccount::create([
            'user_id' => 99,
            'account_id' => '99',
            'username' => 'bob-voice',
            'sip_password' => 'bob-secret-pw',
            'enabled' => true,
            'caller_id' => '3055550199',
            'provider' => 'openai',
            'api_key' => 'provider-key-bob',
            'status' => 'active',
            'provisioned_at' => now(),
        ]);

        $response = $this->asUser(42)->get('/user/ai-connect/voice-bot');
        $response->assertOk();
        $response->assertSee('https://app.globalvoicedirect.com/mcp', false);
        $response->assertSee('Voice bot', false);
        $response->assertSee('readonly', false);
        $response->assertSee('value="15615550100"', false);
        $response->assertSee('Taken from the phone number on your account.', false);
        $response->assertDontSee('alice-voice', false);
        $response->assertDontSee('bob-voice', false);
        $response->assertDontSee('provider-key-alice', false);
        $response->assertDontSee('provider-key-bob', false);
        $response->assertDontSee('alice-secret-pw', false);
        $response->assertDontSee('bob-secret-pw', false);
        $response->assertDontSee('gateway-token-test', false);
        $this->assertSecretsAbsent(['provider-key-alice', 'provider-key-bob', 'alice-secret-pw', 'bob-secret-pw', 'gateway-token-test']);
    }

    public function test_provision_is_idempotent_and_omits_an_unchanged_key(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://voice.example.test/provision/accounts/*' => Http::sequence()
                ->push(['ok' => true], 201)
                ->push(['ok' => true], 200)
                ->push(['ok' => true], 200)
                ->push(['ok' => true], 200)
                ->push(['ok' => true], 200),
        ]);

        $this->givePhone(42, '5615550100');
        $create = $this->asUser(42)->postJson('/user/ai-connect/voice-bot', [
            'username' => 'client-picked-name',
            'caller_id' => '(561) 555-0100',
            'provider' => 'grok',
            'enabled' => true,
            'api_key' => 'provider-key-one',
            'account_id' => '999',
        ]);
        $create->assertOk()->assertJson(['ok' => true, 'status' => 'active']);
        $create->assertDontSee('provider-key-one', false);

        $row = AiVoiceAccount::where('user_id', 42)->first();
        $this->assertSame('42', $row->account_id);
        $this->assertSame('15615550100', $row->username);
        $this->assertSame('5615550100', $row->caller_id);
        $this->assertTrue(VoiceAccountProvisioner::passwordIsValid($row->sip_password));
        $this->assertNotNull($row->api_key_updated_at);
        $this->assertSame('Created', $row->last_result);
        $password = $row->sip_password;
        $stored = DB::table('ai_voice_accounts')->where('user_id', 42)->first();
        $this->assertStringNotContainsString('provider-key-one', $stored->api_key);
        $this->assertStringNotContainsString($password, $stored->sip_password);
        $this->assertStringNotContainsString($password, serialize(new ProvisionVoiceAccount(42, ProvisionVoiceAccount::PUT)));

        $again = $this->asUser(42)->postJson('/user/ai-connect/voice-bot', [
            'username' => 'client-picked-name',
            'caller_id' => '5615550100',
            'provider' => 'openai',
            'enabled' => false,
        ]);
        $again->assertOk()->assertJson(['ok' => true, 'status' => 'active']);
        $row->refresh();
        $this->assertSame($password, $row->sip_password);
        $this->assertSame('provider-key-one', $row->api_key);
        $this->assertFalse($row->api_key_dirty);
        $this->assertTrue($row->api_key_on_gateway);
        $this->assertSame('Updated', $row->last_result);

        $rotate = $this->asUser(42)->postJson('/user/ai-connect/voice-bot', [
            'username' => 'still-ignored',
            'caller_id' => '5615550100',
            'provider' => 'hermes',
            'enabled' => true,
            'api_key' => 'provider-key-two',
        ]);
        $rotate->assertOk();
        $row->refresh();
        $this->assertSame('provider-key-two', $row->api_key);
        $this->assertSame($password, $row->sip_password);

        $this->asUser(42)->postJson('/user/ai-connect/voice-bot/key/remove', [])->assertOk();
        $row->refresh();
        $this->assertNull($row->api_key);
        $this->assertFalse($row->api_key_on_gateway);

        $this->asUser(42)->postJson('/user/ai-connect/voice-bot/remove', [])->assertOk();
        $this->assertNull(AiVoiceAccount::where('user_id', 42)->first());

        Http::assertSentInOrder([
            function ($request) use ($password) {
                $data = $request->data();
                return $request->method() === 'PUT'
                    && $request->url() === 'https://voice.example.test/provision/accounts/42'
                    && $request->hasHeader('Authorization', 'Bearer gateway-token-test')
                    && $data['sip_username'] === '15615550100'
                    && $data['username'] === '15615550100'
                    && !array_key_exists('client-picked-name', $data)
                    && $data['sip_password'] === $password
                    && $data['enabled'] === true
                    && $data['caller_id'] === '5615550100'
                    && $data['provider'] === 'grok'
                    && $data['api_key'] === 'provider-key-one';
            },
            function ($request) use ($password) {
                $data = $request->data();
                return $request->method() === 'PUT'
                    && $data['provider'] === 'openai'
                    && $data['enabled'] === false
                    && $data['sip_password'] === $password
                    && !array_key_exists('api_key', $data);
            },
            function ($request) {
                return $request->method() === 'PUT' && $request->data()['api_key'] === 'provider-key-two';
            },
            function ($request) {
                $data = $request->data();
                return $request->method() === 'PUT' && array_key_exists('api_key', $data) && $data['api_key'] === null;
            },
            function ($request) {
                return $request->method() === 'DELETE'
                    && $request->url() === 'https://voice.example.test/provision/accounts/42';
            },
        ]);
        $this->assertSecretsAbsent(['provider-key-one', 'provider-key-two', $password, 'gateway-token-test']);
    }

    public function test_gateway_conflicts_and_validation_do_not_echo_secrets(): void
    {
        $this->givePhone(42, '5615550100');
        Http::preventStrayRequests();
        Http::fake([
            'https://voice.example.test/provision/accounts/*' => Http::sequence()
                ->push(['error' => 'provider-key-one sip_password leaked'], 409)
                ->push(['error' => 'provider-key-one invalid'], 422),
        ]);

        $conflict = $this->asUser(42)->postJson('/user/ai-connect/voice-bot', $this->payload());
        $conflict->assertStatus(422)->assertJson(['ok' => false, 'error' => 'That username is already in use.']);
        $conflict->assertDontSee('provider-key-one', false);

        $invalid = $this->asUser(42)->postJson('/user/ai-connect/voice-bot', $this->payload());
        $invalid->assertStatus(422)->assertJson(['ok' => false, 'error' => 'The voice gateway rejected those account details.']);
        $invalid->assertDontSee('provider-key-one', false);

        $row = AiVoiceAccount::where('user_id', 42)->first();
        $this->assertSame('error', $row->status);
        $this->assertSame('The voice gateway rejected those account details.', $row->last_result);
        $this->assertStringNotContainsString('provider-key-one', (string) $row->last_error);
        $this->assertStringNotContainsString('provider-key-one', (string) $row->last_result);
        $this->assertSecretsAbsent(['provider-key-one', $row->sip_password]);
    }

    public function test_unreachable_gateway_is_logged_without_the_exception_message(): void
    {
        $this->givePhone(42, '5615550100');
        Http::preventStrayRequests();
        Http::fake(function () {
            throw new ConnectionException('connection failed for provider-key-one');
        });
        $response = $this->asUser(42)->postJson('/user/ai-connect/voice-bot', $this->payload());
        $response->assertStatus(422)->assertJson(['error' => 'Could not reach the voice gateway. Try again.']);
        $joined = implode("\n", $this->logs);
        $this->assertStringContainsString('voice bot gateway unreachable', $joined);
        $this->assertStringContainsString('ConnectionException', $joined);
        $this->assertStringNotContainsString('provider-key-one', $joined);
        $this->assertStringNotContainsString('connection failed', $joined);
    }

    public function test_queue_failure_falls_back_to_sync(): void
    {
        $this->givePhone(42, '5615550100');
        config(['queue.default' => 'database']);
        Http::preventStrayRequests();
        Http::fake(['https://voice.example.test/provision/accounts/*' => Http::response(['ok' => true], 201)]);

        $this->asUser(42)->postJson('/user/ai-connect/voice-bot', $this->payload())->assertOk()->assertJson(['status' => 'active']);
        $this->assertNotNull(AiVoiceAccount::where('user_id', 42)->first());
        $this->assertStringContainsString('voice bot queue unavailable, running sync', implode("\n", $this->logs));
    }

    public function test_demo_account_cannot_provision(): void
    {
        config(['gvd_demo.enabled' => true, 'gvd_demo.user_id' => 42]);
        Http::preventStrayRequests();
        Http::fake();
        $this->asUser(42)->postJson('/user/ai-connect/voice-bot', $this->payload())
            ->assertStatus(422)
            ->assertJson(['ok' => false]);
        Http::assertNothingSent();
        $this->assertNull(AiVoiceAccount::where('user_id', 42)->first());
    }

    public function test_removing_a_missing_key_does_not_call_the_gateway(): void
    {
        Http::fake();
        $this->asUser(42)->postJson('/user/ai-connect/voice-bot/key/remove', [])->assertOk()->assertJson(['ok' => true]);
        Http::assertNothingSent();
    }

    public function test_validation_rejects_bad_caller_id_and_provider(): void
    {
        $this->givePhone(42, '5615550100');
        Http::fake();
        $this->asUser(42)->postJson('/user/ai-connect/voice-bot', [
            'username' => 'ab',
            'caller_id' => '5615550100',
            'provider' => 'claude',
        ])->assertStatus(422)->assertJson(['error' => 'Choose Grok, OpenAI, or Hermes.']);
        $this->asUser(42)->postJson('/user/ai-connect/voice-bot', [
            'username' => 'client-picked-name',
            'caller_id' => '123',
            'provider' => 'grok',
        ])->assertStatus(422)->assertJson(['error' => 'Caller ID must be 10 to 15 digits, or left blank.']);
        Http::assertNothingSent();
    }

    public function test_missing_phone_disables_provisioning_and_ignores_a_typed_username(): void
    {
        Http::fake();
        $page = $this->asUser(42)->get('/user/ai-connect/voice-bot');
        $page->assertOk();
        $page->assertSee('No phone number is on file for this account.', false);
        $page->assertSee('id="vbSave" type="button" disabled', false);

        $this->asUser(42)->postJson('/user/ai-connect/voice-bot', [
            'username' => 'client-picked-name',
            'provider' => 'grok',
            'caller_id' => '5615550100',
        ])->assertStatus(422)->assertJson(['ok' => false, 'error' => 'No phone number is on file for this account. Add your number, then try again.']);
        $this->assertNull(AiVoiceAccount::where('user_id', 42)->first());
        Http::assertNothingSent();
    }

    public function test_calling_line_wins_over_the_registered_number_and_blank_caller_id_is_null(): void
    {
        Schema::create('ai_call_lines', function ($table) {
            $table->unsignedBigInteger('user_id')->primary();
            $table->string('account', 64);
            $table->text('secret_enc')->nullable();
        });
        DB::table('ai_call_lines')->insert(['user_id' => 42, 'account' => '3055550199']);
        $this->givePhone(42, json_encode(['(561) 555-0100']));
        Http::preventStrayRequests();
        Http::fake(['https://voice.example.test/provision/accounts/*' => Http::response(['ok' => true], 201)]);

        $this->asUser(42)->postJson('/user/ai-connect/voice-bot', [
            'username' => 'client-picked-name',
            'provider' => 'hermes',
            'caller_id' => '',
            'enabled' => true,
        ])->assertOk();

        $row = AiVoiceAccount::where('user_id', 42)->first();
        $this->assertSame('13055550199', $row->username);
        $this->assertNull($row->caller_id);
        Http::assertSent(function ($request) {
            $data = $request->data();
            return $data['sip_username'] === '13055550199'
                && $data['username'] === '13055550199'
                && array_key_exists('caller_id', $data)
                && $data['caller_id'] === null;
        });
    }

    public function test_ai_connect_page_links_to_voice_bot_only_when_enabled(): void
    {
        $this->asUser(42)->view('users.gvd-ai-connect', ['d' => $this->aiPage()])
            ->assertSee('/user/ai-connect/voice-bot', false);

        config(['ai_connect.voice_provisioning_enabled' => false]);
        $this->asUser(42)->view('users.gvd-ai-connect', ['d' => $this->aiPage()])
            ->assertDontSee('/user/ai-connect/voice-bot', false)
            ->assertDontSee('>Voice bot<', false);
    }

    private function asUser(int $id)
    {
        return $this->withSession(['user_login' => true, 'user_id' => $id, 'nice_name' => 'Ada']);
    }

    private function givePhone(int $userId, string $numbers): void
    {
        DB::table('user_phone_numbers')->insert(['user_id' => $userId, 'numbers' => $numbers]);
    }

    private function payload(): array
    {
        return [
            'username' => 'alice',
            'caller_id' => '5615550100',
            'provider' => 'grok',
            'enabled' => true,
            'api_key' => 'provider-key-one',
        ];
    }

    private function aiPage(): array
    {
        return [
            'keys' => [], 'activity' => [], 'enabled' => true, 'scopes' => ['contacts:read' => 'Read contacts'],
            'endpoint' => 'https://app.globalvoicedirect.com/mcp', 'live' => true, 'demo' => false,
            'default_limit' => 200, 'max_limit' => 2000, 'calls' => false, 'oauth' => false, 'apps' => [],
            'line' => null, 'lines' => [], 'billing' => false,
        ];
    }

    private function assertSecretsAbsent(array $secrets): void
    {
        $blob = implode("\n", $this->logs);
        foreach ($secrets as $secret) {
            $this->assertNotSame('', (string) $secret);
            $this->assertStringNotContainsString((string) $secret, $blob);
        }
    }
}
