<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\AiVoiceAccount;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminVoiceBotTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SERVER['HTTP_HOST'] = 'app.globalvoicedirect.com';
        $_SERVER['REQUEST_URI'] = '/admin/ai-connect/voice-bots';
        $_SERVER['HTTPS'] = 'on';
        $key = str_repeat('b', 32);
        config(['app.key' => 'base64:' . base64_encode($key)]);
        Crypt::swap(new Encrypter($key, 'AES-256-CBC'));

        $migration = require database_path('migrations/2026_11_06_120000_create_ai_voice_accounts_table.php');
        $migration->up();
        if (!Schema::hasTable('wpgc_users')) {
            Schema::create('wpgc_users', function ($table) {
                $table->increments('ID');
                $table->string('user_login')->nullable();
                $table->string('display_name')->nullable();
                $table->string('user_nicename')->nullable();
                $table->string('user_email')->nullable();
            });
        }
        config([
            'ai_connect.voice_provisioning_enabled' => true,
            'ai_connect.voice_gateway_url' => 'https://voice.example.test',
            'ai_connect.voice_gateway_token' => 'gateway-token-test',
            'queue.default' => 'sync',
        ]);
        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('ai_voice_accounts');
        Schema::dropIfExists('wpgc_users');
        parent::tearDown();
    }

    public function test_admin_routes_are_not_found_when_provisioning_is_off(): void
    {
        config(['ai_connect.voice_provisioning_enabled' => false]);
        Http::fake();
        $this->withSession(['admin_id' => 1])->get('/admin/ai-connect/voice-bots')->assertNotFound();
        $this->withSession(['admin_id' => 1])->post('/admin/ai-connect/voice-bots/reprovision', ['user_id' => 7])->assertNotFound();
        $this->withSession(['admin_id' => 1])->post('/admin/ai-connect/voice-bots/deprovision', ['user_id' => 7])->assertNotFound();
        $this->view('admin.partials.ai-connect-voice-nav')->assertDontSee('/admin/ai-connect/voice-bots', false);
        Http::assertNothingSent();
    }

    public function test_customers_are_sent_to_the_admin_login(): void
    {
        $this->seedAccount();
        Http::fake();
        $this->withSession(['user_login' => true, 'user_id' => 7])
            ->get('/admin/ai-connect/voice-bots')
            ->assertRedirect('/adminlogin');
        $this->withSession(['user_login' => true, 'user_id' => 7])
            ->post('/admin/ai-connect/voice-bots/deprovision', ['user_id' => 7])
            ->assertRedirect('/adminlogin');
        $this->assertNotNull(AiVoiceAccount::where('user_id', 7)->first());
        Http::assertNothingSent();
    }

    public function test_admin_list_shows_every_account_without_secrets(): void
    {
        $this->seedAccount();
        AiVoiceAccount::create([
            'user_id' => 8,
            'account_id' => '8',
            'username' => 'bob-sip',
            'sip_password' => 'bob-secret-pw',
            'enabled' => false,
            'caller_id' => '3055550199',
            'provider' => 'openai',
            'api_key' => null,
            'status' => 'error',
            'last_error' => 'That username is already in use.',
            'last_result' => 'That username is already in use.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('wpgc_users')->insert([
            'ID' => 8,
            'user_login' => 'bob',
            'display_name' => 'Bob Buyer',
            'user_email' => 'bob@example.test',
        ]);

        $response = $this->asAdmin()->get('/admin/ai-connect/voice-bots');
        $response->assertOk();
        $response->assertSee('AI Connect &gt; Voice bots', false);
        $response->assertSee('href="/admin/ai-connect/voice-bots"', false);
        $response->assertSee('Ada Owner', false);
        $response->assertSee('ada', false);
        $response->assertSee('Bob Buyer', false);
        $response->assertSee('alice-sip', false);
        $response->assertSee('bob-sip', false);
        $response->assertSee('Grok', false);
        $response->assertSee('OpenAI', false);
        $response->assertSee('>Yes<', false);
        $response->assertSee('>No<', false);
        $response->assertSee('That username is already in use.', false);
        $response->assertSee('Created', false);
        $response->assertSee('active', false);
        $response->assertSee('error', false);
        $response->assertDontSee('provider-key-ada', false);
        $response->assertDontSee('ada-secret-pw', false);
        $response->assertDontSee('bob-secret-pw', false);
        $response->assertDontSee('gateway-token-test', false);
    }

    public function test_admin_can_reprovision_and_deprovision_through_the_same_job(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://voice.example.test/provision/accounts/*' => Http::sequence()
                ->push(['ok' => true], 200)
                ->push(['ok' => true], 200),
        ]);
        $this->seedAccount();
        $before = AiVoiceAccount::where('user_id', 7)->first()->sip_password;

        $this->asAdmin()->post('/admin/ai-connect/voice-bots/reprovision', ['user_id' => 7])
            ->assertRedirect('/admin/ai-connect/voice-bots');
        $row = AiVoiceAccount::where('user_id', 7)->first();
        $this->assertSame('active', $row->status);
        $this->assertSame('Updated', $row->last_result);
        $this->assertSame($before, $row->sip_password);
        $this->assertSame('provider-key-ada', $row->api_key);

        $this->asAdmin()->post('/admin/ai-connect/voice-bots/deprovision', ['user_id' => 7])
            ->assertRedirect('/admin/ai-connect/voice-bots');
        $this->assertNull(AiVoiceAccount::where('user_id', 7)->first());

        Http::assertSentInOrder([
            function ($request) use ($before) {
                $data = $request->data();
                return $request->method() === 'PUT'
                    && $request->url() === 'https://voice.example.test/provision/accounts/7'
                    && $request->hasHeader('Authorization', 'Bearer gateway-token-test')
                    && $data['sip_username'] === 'alice-sip'
                    && $data['username'] === 'alice-sip'
                    && $data['caller_id'] === '5615550100'
                    && $data['sip_password'] === $before
                    && !array_key_exists('api_key', $data);
            },
            function ($request) {
                return $request->method() === 'DELETE'
                    && $request->url() === 'https://voice.example.test/provision/accounts/7';
            },
        ]);
    }

    public function test_menu_item_is_hidden_when_the_flag_is_off_and_shown_when_on(): void
    {
        $this->view('admin.partials.ai-connect-voice-nav')
            ->assertSee('AI Connect &gt; Voice bots', false)
            ->assertSee('/admin/ai-connect/voice-bots', false);
        config(['ai_connect.voice_provisioning_enabled' => false]);
        $this->view('admin.partials.ai-connect-voice-nav')
            ->assertDontSee('Voice bots', false)
            ->assertDontSee('/admin/ai-connect/voice-bots', false);
    }

    private function asAdmin()
    {
        return $this->withSession(['admin_id' => 1]);
    }

    private function seedAccount(): void
    {
        DB::table('wpgc_users')->insert([
            'ID' => 7,
            'user_login' => 'ada',
            'display_name' => 'Ada Owner',
            'user_email' => 'ada@example.test',
        ]);
        AiVoiceAccount::create([
            'user_id' => 7,
            'account_id' => '7',
            'username' => 'alice-sip',
            'sip_password' => 'ada-secret-pw',
            'enabled' => true,
            'caller_id' => '5615550100',
            'provider' => 'grok',
            'api_key' => 'provider-key-ada',
            'api_key_updated_at' => now(),
            'api_key_on_gateway' => true,
            'status' => 'active',
            'last_result' => 'Created',
            'provisioned_at' => now(),
        ]);
    }
}
