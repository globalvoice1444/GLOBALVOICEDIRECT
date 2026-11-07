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
    private ?string $billingSession = null;
    private array $extensionRows = [];
    private array $gatewayQueue = [];
    private bool $gatewayThrows = false;
    private bool $extensionsFault = false;
    private string $sipPassword = 'live-sip-pass1';
    private array $sipPasswords = [];

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
        $this->billingSession = null;
        $this->extensionRows = [];
        $this->gatewayQueue = [];
        $this->gatewayThrows = false;
        $this->extensionsFault = false;
        $this->sipPassword = 'live-sip-pass1';
        $this->sipPasswords = [];

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
        $this->bill();
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
        $response->assertSee('<select id="vbUser"', false);
        $response->assertSee('value="9" selected', false);
        $response->assertSee('0205500101', false);
        $response->assertSee('calls not yet supported', false);
        $response->assertDontSee('sip.example.test', false);
        $response->assertSee('Ext 101', false);
        $response->assertDontSee('live-sip-pass1', false);
        $response->assertDontSee('foreign-secret-pw', false);
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
        $this->queueGateway(201);
        $this->queueGateway(200);
        $this->queueGateway(200);
        $this->queueGateway(200);
        $this->queueGateway(200);
        $this->bill();
        $create = $this->asUser(42)->postJson('/user/ai-connect/voice-bot', [
            'sip_username' => '0205500101',
            'username' => 'client-picked-name',
            'caller_id' => '(561) 555-0100',
            'provider' => 'grok',
            'enabled' => true,
            'api_key' => 'xai-example',
            'account_id' => '999',
        ]);
        $create->assertOk()->assertJson(['ok' => true, 'status' => 'active']);
        $create->assertDontSee('xai-example', false);

        $row = AiVoiceAccount::where('user_id', 42)->first();
        $this->assertSame('42', $row->account_id);
        $this->assertSame('0205500101', $row->username);
        $this->assertSame('sip.example.test', $row->sip_domain);
        $create->assertDontSee('sip.example.test', false);
        $create->assertDontSee('live-sip-pass1', false);
        $this->assertSame('5615550100', $row->caller_id);
        $this->assertSame('live-sip-pass1', $row->sip_password);
        $this->assertTrue(VoiceAccountProvisioner::passwordIsValid($row->sip_password));
        $this->assertNotNull($row->api_key_updated_at);
        $this->assertSame('Created', $row->last_result);
        $password = $row->sip_password;
        $stored = DB::table('ai_voice_accounts')->where('user_id', 42)->first();
        $this->assertStringNotContainsString('xai-example', $stored->api_key);
        $this->assertStringNotContainsString($password, $stored->sip_password);
        $this->assertStringNotContainsString($password, serialize(new ProvisionVoiceAccount(42, ProvisionVoiceAccount::PUT)));

        $again = $this->asUser(42)->postJson('/user/ai-connect/voice-bot', [
            'sip_username' => '0205500101',
            'username' => 'client-picked-name',
            'caller_id' => '5615550100',
            'provider' => 'openai',
            'enabled' => false,
        ]);
        $again->assertOk()->assertJson(['ok' => true, 'status' => 'active']);
        $row->refresh();
        $this->assertSame($password, $row->sip_password);
        $this->assertSame('xai-example', $row->api_key);
        $this->assertFalse($row->api_key_dirty);
        $this->assertTrue($row->api_key_on_gateway);
        $this->assertSame('Updated', $row->last_result);

        $rotate = $this->asUser(42)->postJson('/user/ai-connect/voice-bot', [
            'sip_username' => '0205500101',
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

        $voice = collect(Http::recorded())->map(fn ($pair) => $pair[0])->filter(fn ($request) => str_contains($request->url(), 'voice.example.test'))->values();
        $this->assertCount(5, $voice);
        $first = $voice[0]->data();
        $this->assertSame('PUT', $voice[0]->method());
        $this->assertSame('https://voice.example.test/provision/accounts/42', $voice[0]->url());
        $this->assertTrue($voice[0]->hasHeader('Authorization', 'Bearer gateway-token-test'));
        $this->assertSame('0205500101', $first['sip_username']);
        $this->assertSame('0205500101', $first['username']);
        $this->assertSame('live-sip-pass1', $first['sip_password']);
        $this->assertSame($password, $first['sip_password']);
        $this->assertTrue($first['enabled']);
        $this->assertSame('5615550100', $first['caller_id']);
        $this->assertSame('grok', $first['provider']);
        $this->assertSame('xai-example', $first['api_key']);
        $second = $voice[1]->data();
        $this->assertSame('openai', $second['provider']);
        $this->assertFalse($second['enabled']);
        $this->assertSame($password, $second['sip_password']);
        $this->assertArrayNotHasKey('api_key', $second);
        $this->assertSame('0205500101', $second['sip_username']);
        $this->assertSame('provider-key-two', $voice[2]->data()['api_key']);
        $this->assertArrayHasKey('api_key', $voice[3]->data());
        $this->assertNull($voice[3]->data()['api_key']);
        $this->assertSame('DELETE', $voice[4]->method());
        $this->assertSecretsAbsent(['xai-example', 'provider-key-two', $password, 'live-sip-pass1', 'gateway-token-test']);
    }

    public function test_gateway_conflicts_and_validation_do_not_echo_secrets(): void
    {
        $this->queueGateway(409, ['error' => 'xai-example sip_password leaked']);
        $this->queueGateway(422, ['error' => 'xai-example invalid']);
        $this->bill();

        $conflict = $this->asUser(42)->postJson('/user/ai-connect/voice-bot', $this->payload());
        $conflict->assertStatus(422)->assertJson(['ok' => false, 'error' => 'That username is already in use.']);
        $conflict->assertDontSee('xai-example', false);

        $invalid = $this->asUser(42)->postJson('/user/ai-connect/voice-bot', $this->payload());
        $invalid->assertStatus(422)->assertJson(['ok' => false, 'error' => 'The voice gateway rejected those account details.']);
        $invalid->assertDontSee('xai-example', false);

        $row = AiVoiceAccount::where('user_id', 42)->first();
        $this->assertSame('error', $row->status);
        $this->assertSame('The voice gateway rejected those account details.', $row->last_result);
        $this->assertStringNotContainsString('xai-example', (string) $row->last_error);
        $this->assertStringNotContainsString('xai-example', (string) $row->last_result);
        $this->assertSecretsAbsent(['xai-example', $row->sip_password]);
    }

    public function test_unreachable_gateway_is_logged_without_the_exception_message(): void
    {
        $this->gatewayThrows = true;
        $this->bill();
        $response = $this->asUser(42)->postJson('/user/ai-connect/voice-bot', $this->payload());
        $response->assertStatus(422)->assertJson(['error' => 'Could not reach the voice gateway. Try again.']);
        $joined = implode("\n", $this->logs);
        $this->assertStringContainsString('voice bot gateway unreachable', $joined);
        $this->assertStringContainsString('ConnectionException', $joined);
        $this->assertStringNotContainsString('xai-example', $joined);
        $this->assertStringNotContainsString('connection failed', $joined);
    }

    public function test_queue_failure_falls_back_to_sync(): void
    {
        config(['queue.default' => 'database']);
        $this->queueGateway(201);
        $this->bill();

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
        $this->bill();
        $this->asUser(42)->postJson('/user/ai-connect/voice-bot', [
            'sip_username' => '0205500101',
            'username' => 'ab',
            'caller_id' => '5615550100',
            'provider' => 'other',
        ])->assertStatus(422)->assertJson(['error' => VoiceAccountProvisioner::providerChoiceError()]);
        $this->asUser(42)->postJson('/user/ai-connect/voice-bot', [
            'sip_username' => '0205500101',
            'caller_id' => '123',
            'provider' => 'grok',
        ])->assertStatus(422)->assertJson(['error' => 'Caller ID must be 10 to 15 digits, or left blank.']);
        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'voice.example.test'));
    }

    public function test_missing_extension_disables_provisioning_and_ignores_a_typed_username(): void
    {
        Http::fake();
        $page = $this->asUser(42)->get('/user/ai-connect/voice-bot');
        $page->assertOk();
        $page->assertSee('No SIP extension is available on this account.', false);
        $page->assertSee('id="vbSave" type="button" disabled', false);

        $this->asUser(42)->postJson('/user/ai-connect/voice-bot', [
            'username' => 'client-picked-name',
            'sip_username' => 'client-picked-name',
            'provider' => 'grok',
            'caller_id' => '5615550100',
        ])->assertStatus(422)->assertJson(['ok' => false, 'error' => 'No SIP extension is available on this account. Sign in with your phone-line login so we can read your extensions. Provisioning stays off until an extension is available.']);
        $this->assertNull(AiVoiceAccount::where('user_id', 42)->first());
        Http::assertNothingSent();
    }

    public function test_phone_system_with_no_extensions_disables_provisioning(): void
    {
        $this->bill(42, []);
        $page = $this->asUser(42)->get('/user/ai-connect/voice-bot');
        $page->assertOk();
        $page->assertSee('No SIP extension is on your phone account yet.', false);
        $page->assertSee('id="vbSave" type="button" disabled', false);
        $this->asUser(42)->postJson('/user/ai-connect/voice-bot', [
            'sip_username' => 'foreign-login',
            'provider' => 'grok',
        ])->assertStatus(422)->assertJson(['error' => 'No SIP extension is on your phone account yet.']);
        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'voice.example.test'));
    }

    public function test_phone_system_error_is_shown_without_calling_the_gateway(): void
    {
        $this->extensionsFault = true;
        $this->bill();
        $page = $this->asUser(42)->get('/user/ai-connect/voice-bot');
        $page->assertOk();
        $page->assertSee('We could not load your extensions from the phone system.', false);
        $page->assertSee('id="vbSave" type="button" disabled', false);
        $this->asUser(42)->postJson('/user/ai-connect/voice-bot', [
            'sip_username' => '0205500101',
            'provider' => 'grok',
        ])->assertStatus(422)->assertJson(['error' => 'We could not load your extensions from the phone system. Please try again.']);
        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'voice.example.test'));
        $joined = implode("\n", $this->logs);
        $this->assertStringNotContainsString('live-sip-pass1', $joined);
    }

    public function test_customer_picks_one_of_their_extensions_and_blank_caller_id_is_null(): void
    {
        $this->sipPasswords = [9 => 'live-sip-pass1', 10 => 'live-sip-pass2'];
        $this->queueGateway(201);
        $this->bill(42, [
            ['i_account' => 9, 'id' => '101', 'account_id' => '0205500101', 'name' => 'Front Desk', 'type' => 'Account'],
            ['i_account' => 10, 'id' => '102', 'account_id' => '0205500102', 'name' => 'Sales', 'type' => 'Account'],
            ['i_account' => 0, 'id' => '200', 'account_id' => '', 'name' => 'Main Menu', 'type' => 'Auto Attendant'],
        ]);
        $page = $this->asUser(42)->get('/user/ai-connect/voice-bot');
        $page->assertOk();
        $page->assertSee('0205500101', false);
        $page->assertSee('0205500102', false);
        $page->assertSee('<select id="vbUser"', false);
        $page->assertDontSee('>Main Menu<', false);

        $this->asUser(42)->postJson('/user/ai-connect/voice-bot', [
            'sip_username' => 'foreign-login',
            'provider' => 'hermes',
            'caller_id' => '',
        ])->assertStatus(422)->assertJson(['error' => 'That extension is not on your account.']);

        $this->asUser(42)->postJson('/user/ai-connect/voice-bot', [
            'username' => 'client-picked-name',
            'sip_username' => '0205500102',
            'provider' => 'hermes',
            'caller_id' => '',
            'enabled' => true,
        ])->assertOk();

        $row = AiVoiceAccount::where('user_id', 42)->first();
        $this->assertSame('0205500102', $row->username);
        $this->assertNull($row->caller_id);
        Http::assertSent(function ($request) {
            if (!str_contains($request->url(), 'voice.example.test')) return false;
            $data = $request->data();
            return $data['sip_username'] === '0205500102'
                && $data['username'] === '0205500102'
                && $data['sip_password'] === 'live-sip-pass2'
                && array_key_exists('caller_id', $data)
                && $data['caller_id'] === null;
        });
    }

    public function test_claude_key_is_sent_to_the_gateway_and_a_bad_prefix_is_rejected(): void
    {
        $bad = 'not-an-anthropic-key';
        $good = 'sk-ant-example';
        $this->queueGateway(201);
        $this->bill();
        $rejected = $this->asUser(42)->postJson('/user/ai-connect/voice-bot', [
            'sip_username' => '0205500101',
            'provider' => 'claude',
            'api_key' => $bad,
            'caller_id' => '',
        ]);
        $rejected->assertStatus(422)->assertJson(['error' => 'An Anthropic key starts with sk-ant-.']);
        $rejected->assertDontSee($bad, false);
        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'voice.example.test'));

        $saved = $this->asUser(42)->postJson('/user/ai-connect/voice-bot', [
            'sip_username' => '0205500101',
            'provider' => 'claude',
            'api_key' => $good,
            'caller_id' => '',
        ]);
        $saved->assertOk()->assertDontSee($good, false);
        $row = AiVoiceAccount::where('user_id', 42)->first();
        $this->assertSame('claude', $row->provider);
        $this->assertSame($good, $row->api_key);
        $this->assertStringNotContainsString($good, (string) DB::table('ai_voice_accounts')->where('user_id', 42)->value('api_key'));
        Http::assertSent(function ($request) use ($good) {
            if (!str_contains($request->url(), 'voice.example.test')) return false;
            $data = $request->data();
            return $data['provider'] === 'claude'
                && $data['api_key'] === $good
                && $data['sip_username'] === '0205500101'
                && array_key_exists('caller_id', $data)
                && $data['caller_id'] === null;
        });
        $page = $this->asUser(42)->get('/user/ai-connect/voice-bot');
        $page->assertSee('Claude — calls not yet supported', false);
        $page->assertSee('https://aistudio.google.com/apikey', false);
        $page->assertSee('https://console.anthropic.com', false);
        $page->assertDontSee($good, false);
        $this->assertSecretsAbsent([$bad, $good, 'live-sip-pass1']);
    }

    public function test_multi_field_credentials_are_stored_encrypted_and_not_rendered(): void
    {
        $access = 'AKIA' . str_repeat('A', 16);
        $secret = 'bedrock-secret-example';
        $this->queueGateway(201);
        $this->queueGateway(201);
        $this->bill();
        $missing = $this->asUser(42)->postJson('/user/ai-connect/voice-bot', [
            'sip_username' => '0205500101',
            'provider' => 'bedrock',
            'credentials' => ['access_key_id' => $access, 'secret_access_key' => $secret],
        ]);
        $missing->assertStatus(422);
        $missing->assertDontSee($secret, false);
        $this->assertStringContainsString('Region', $missing->json('error'));

        $saved = $this->asUser(42)->postJson('/user/ai-connect/voice-bot', [
            'sip_username' => '0205500101',
            'provider' => 'bedrock',
            'caller_id' => '',
            'credentials' => ['access_key_id' => $access, 'secret_access_key' => $secret, 'region' => 'us-east-1'],
        ]);
        $saved->assertOk()->assertDontSee($secret, false)->assertDontSee($access, false);
        $row = AiVoiceAccount::where('user_id', 42)->first();
        $this->assertSame('bedrock', $row->provider);
        $this->assertSame($secret, $row->provider_credentials['secret_access_key']);
        $this->assertSame('us-east-1', $row->provider_credentials['region']);
        $raw = (string) DB::table('ai_voice_accounts')->where('user_id', 42)->value('provider_credentials');
        $this->assertStringNotContainsString($secret, $raw);
        $this->assertStringNotContainsString($access, $raw);
        Http::assertSent(function ($request) use ($access, $secret) {
            if (!str_contains($request->url(), 'voice.example.test')) return false;
            $data = $request->data();
            return ($data['credentials']['access_key_id'] ?? null) === $access
                && ($data['credentials']['secret_access_key'] ?? null) === $secret
                && ($data['credentials']['region'] ?? null) === 'us-east-1'
                && !array_key_exists('api_key', $data)
                && array_key_exists('caller_id', $data)
                && $data['caller_id'] === null;
        });

        $azure = $this->asUser(42)->postJson('/user/ai-connect/voice-bot', [
            'sip_username' => '0205500101',
            'provider' => 'azure_openai',
            'caller_id' => '',
            'credentials' => [
                'api_key' => 'azure-key-example',
                'endpoint' => 'http://example.openai.azure.com',
                'deployment' => 'gpt-demo',
            ],
        ]);
        $azure->assertStatus(422)->assertDontSee('azure-key-example', false);

        $azureOk = $this->asUser(42)->postJson('/user/ai-connect/voice-bot', [
            'sip_username' => '0205500101',
            'provider' => 'azure_openai',
            'caller_id' => '',
            'credentials' => [
                'api_key' => 'azure-key-example',
                'endpoint' => 'https://example.openai.azure.com',
                'deployment' => 'gpt-demo',
            ],
        ]);
        $azureOk->assertOk()->assertDontSee('azure-key-example', false);
        $page = $this->asUser(42)->get('/user/ai-connect/voice-bot');
        $page->assertSee('value="https://example.openai.azure.com"', false);
        $page->assertSee('gpt-demo', false);
        $page->assertDontSee('azure-key-example', false);
        $page->assertSee('Realtime voice', false);
        $page->assertSee('Text models (speech pipeline needed)', false);
        $this->assertSecretsAbsent([$secret, $access, 'azure-key-example', 'live-sip-pass1']);
    }

    public function test_provider_details_open_from_an_info_button(): void
    {
        $this->bill();
        $page = $this->asUser(42)->get('/user/ai-connect/voice-bot');
        $page->assertOk();
        $page->assertSee('<select id="vbProv"', false);
        $page->assertSee('<optgroup label="Realtime voice">', false);
        $page->assertSee('aria-label="Provider details"', false);
        $page->assertSee('id="vbProvInfo"', false);
        $page->assertSee('id="vbProvModal"', false);
        $page->assertSee('role="dialog"', false);
        $page->assertSee('aria-modal="true"', false);
        $page->assertSee('id="vbProvClose"', false);
        $page->assertSee('data-vb-close="1"', false);
        $page->assertSee('id="vbProvDetail"', false);
        $page->assertSee('id="vbProvData"', false);
        $page->assertSee('"grok":', false);
        $page->assertSee('"claude":', false);
        $page->assertSee('Create an API key in the xAI console.', false);
        $page->assertSee('Create a key at console.anthropic.com under API keys.', false);
        $page->assertSee('noopener noreferrer', false);
        $page->assertSee('Calls are not yet supported.', false);
        $page->assertSee("e.key==='Escape'", false);
        $page->assertDontSee('<li class="vb-detail"', false);
        $page->assertDontSee('padding-left:18px', false);
    }

    public function test_ai_connect_activity_is_five_per_page(): void
    {
        config(['ai_connect.enabled' => true]);
        foreach ([
            database_path('migrations/2026_11_02_210000_create_ai_connect_tables.php'),
            database_path('migrations/2026_11_02_220000_create_ai_call_lines.php'),
        ] as $file) {
            (require $file)->up();
        }
        foreach (range(1, 6) as $n) {
            DB::table('ai_activity')->insert([
                'user_id' => 42,
                'key_id' => null,
                'method' => 'tools/call',
                'tool' => 'list_contacts',
                'mode' => 'read',
                'status' => 'ok',
                'summary' => 'act-' . $n,
                'created_at' => now(),
            ]);
        }
        DB::table('ai_activity')->insert([
            'user_id' => 99,
            'key_id' => null,
            'method' => 'tools/call',
            'tool' => 'list_contacts',
            'mode' => 'read',
            'status' => 'ok',
            'summary' => 'other-customer-act',
            'created_at' => now(),
        ]);

        $first = $this->asUser(42)->get('/user/ai-connect');
        $first->assertOk();
        $first->assertSee('Page 1 of 2', false);
        $first->assertSee('act-6', false);
        $first->assertSee('act-2', false);
        $first->assertDontSee('act-1', false);
        $first->assertDontSee('other-customer-act', false);
        $first->assertSee('activity_page=2', false);
        $first->assertSee('>Next<', false);

        $second = $this->asUser(42)->get('/user/ai-connect?activity_page=2');
        $second->assertOk();
        $second->assertSee('Page 2 of 2', false);
        $second->assertSee('act-1', false);
        $second->assertDontSee('act-6', false);
        $second->assertSee('>Previous<', false);
    }

    public function test_ai_connect_page_links_to_voice_bot_only_when_enabled(): void
    {
        $this->asUser(42)->view('users.gvd-ai-connect', ['d' => $this->aiPage()])
            ->assertSee('/user/ai-connect/voice-bot', false)
            ->assertSee('console.anthropic.com', false)
            ->assertSee('Claude', false);

        config(['ai_connect.voice_provisioning_enabled' => false]);
        $this->asUser(42)->view('users.gvd-ai-connect', ['d' => $this->aiPage()])
            ->assertDontSee('/user/ai-connect/voice-bot', false)
            ->assertDontSee('>Voice bot<', false)
            ->assertDontSee('console.anthropic.com', false);
    }

    private function asUser(int $id)
    {
        $session = ['user_login' => true, 'user_id' => $id, 'nice_name' => 'Ada'];
        if ($this->billingSession) $session['gvd_billing'] = $this->billingSession;
        return $this->withSession($session);
    }

    private function bill(int $userId = 42, ?array $extensions = null): void
    {
        $this->extensionRows = $extensions ?? [[
            'i_account' => 9, 'id' => '101', 'account_id' => '0205500101', 'name' => 'Front Desk', 'type' => 'Account',
        ]];
        DB::table('user_phone_numbers')->where('user_id', $userId)->delete();
        DB::table('user_phone_numbers')->insert(['user_id' => $userId, 'i_customer' => 55, 'numbers' => '5615550100']);
        $this->billingSession = Crypt::encryptString(json_encode([
            'base' => 'https://billing.example.test',
            'session_id' => 'sess-test',
            'csrf_token' => '',
            'access_token' => '',
            'refresh_token' => '',
            'realm' => 'customer',
            'i_customer' => 55,
        ]));
        $this->installHttpFake();
    }

    private function queueGateway(int $status, array $body = ['ok' => true]): void
    {
        $this->gatewayQueue[] = Http::response($body, $status);
    }

    private function installHttpFake(): void
    {
        Http::fake(function ($request) {
            $url = $request->url();
            if (str_contains($url, 'billing.example.test')) {
                if (str_contains($url, 'get_extensions_list')) {
                    if ($this->extensionsFault) return Http::response(['faultcode' => 'Server.Session.check_auth.auth_failed']);
                    return Http::response(['extensions_list' => $this->extensionRows]);
                }
                if (str_contains($url, 'get_account_list')) {
                    $list = [];
                    foreach ($this->extensionRows as $e) {
                        $ia = (int) ($e['i_account'] ?? 0);
                        $list[] = ['i_account' => $ia, 'i_customer' => 55, 'id' => $e['account_id'] ?? '', 'h323_password' => $this->sipPasswords[$ia] ?? $this->sipPassword];
                    }
                    $list[] = ['i_account' => 77, 'i_customer' => 99, 'id' => 'foreign-login', 'h323_password' => 'foreign-secret-pw'];
                    return Http::response(['account_list' => $list]);
                }
                if (str_contains($url, 'get_account_info')) {
                    $params = json_decode((string) ($request->data()['params'] ?? '{}'), true);
                    $ia = (int) (is_array($params) ? ($params['i_account'] ?? 0) : 0);
                    foreach ($this->extensionRows as $e) {
                        if ((int) ($e['i_account'] ?? 0) !== $ia) continue;
                        return Http::response(['account_info' => [
                            'i_account' => $ia, 'i_customer' => 55, 'id' => $e['account_id'] ?? '',
                            'h323_password' => $this->sipPasswords[$ia] ?? $this->sipPassword,
                            'sip_domain' => 'sip.example.test',
                        ]]);
                    }
                    return Http::response(['account_info' => ['i_account' => $ia, 'i_customer' => 99, 'id' => 'foreign-login', 'h323_password' => 'foreign-secret-pw']]);
                }
                return Http::response(['customer_info' => ['i_customer' => 55]]);
            }
            if (str_contains($url, 'voice.example.test')) {
                if ($this->gatewayThrows) throw new ConnectionException('connection failed for xai-example');
                return array_shift($this->gatewayQueue) ?: Http::response(['ok' => true], 201);
            }
            return Http::response(['faultcode' => 'unexpected'], 500);
        });
    }

    private function payload(): array
    {
        return [
            'sip_username' => '0205500101',
            'username' => 'alice',
            'caller_id' => '5615550100',
            'provider' => 'grok',
            'enabled' => true,
            'api_key' => 'xai-example',
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
