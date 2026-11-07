<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Services\VoiceAccountProvisioner;
use App\Support\AiConnect;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminAiConnectTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SERVER['HTTP_HOST'] = 'app.globalvoicedirect.com';
        $_SERVER['REQUEST_URI'] = '/admin/ai-connect';
        $_SERVER['HTTPS'] = 'on';
        $key = str_repeat('b', 32);
        config(['app.key' => 'base64:' . base64_encode($key)]);
        Crypt::swap(new Encrypter($key, 'AES-256-CBC'));
        foreach ([
            database_path('migrations/2026_11_02_210000_create_ai_connect_tables.php'),
            database_path('migrations/2026_11_07_000000_create_ai_provider_overrides_table.php'),
        ] as $file) {
            (require $file)->up();
        }
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
            'ai_connect.enabled' => true,
            'ai_connect.voice_provisioning_enabled' => true,
        ]);
        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('user_phone_numbers');
        Schema::dropIfExists('ai_voice_accounts');
        Schema::dropIfExists('ai_provider_overrides');
        Schema::dropIfExists('ai_activity');
        Schema::dropIfExists('ai_settings');
        Schema::dropIfExists('ai_keys');
        Schema::dropIfExists('wpgc_users');
        parent::tearDown();
    }

    public function test_customers_are_sent_to_the_admin_login(): void
    {
        $this->withSession(['user_login' => true, 'user_id' => 7])
            ->get('/admin/ai-connect')
            ->assertRedirect('/adminlogin');
        $this->withSession(['user_login' => true, 'user_id' => 7])
            ->post('/admin/ai-connect/keys/revoke', ['key_id' => 1])
            ->assertRedirect('/adminlogin');
    }

    public function test_admin_lists_key_fingerprints_searches_and_revokes_without_the_hash(): void
    {
        $this->customer(7, 'Ada', 'ada@example.test');
        $this->customer(8, 'Bob Buyer', 'bob@example.test');
        $secret = hash('sha256', 'do-not-render-this-key');
        $ada = $this->key(7, 'Office', 'gvd_live_ada1', $secret);
        $this->key(8, 'Bob key', 'gvd_live_bob1', hash('sha256', 'bob-key'));

        $page = $this->asAdmin()->get('/admin/ai-connect?key_q=Bob');
        $page->assertOk();
        $page->assertSee('Bob key', false);
        $page->assertSee('gvd_live_bob1', false);
        $page->assertSee('contacts:read', false);
        $page->assertDontSee('Office', false);
        $page->assertDontSee($secret, false);
        $page->assertDontSee('do-not-render-this-key', false);

        $this->asAdmin()->post('/admin/ai-connect/keys/revoke', ['key_id' => $ada])
            ->assertRedirect('/admin/ai-connect');
        $this->assertNotNull(DB::table('ai_keys')->where('id', $ada)->value('revoked_at'));
        $log = DB::table('ai_activity')->where('tool', 'revoke')->first();
        $this->assertNotNull($log);
        $this->assertSame('admin', $log->mode);
        $this->assertStringContainsString('actor admin #1', (string) $log->summary);
        $this->assertStringNotContainsString($secret, (string) $log->summary);
        $this->asAdmin()->get('/admin/ai-connect')->assertDontSee($secret, false)->assertSee('Revoked', false);
    }

    public function test_activity_log_filters_and_pages_twenty_five_rows(): void
    {
        $this->customer(7, 'Ada', 'ada@example.test');
        $this->customer(8, 'Bob Buyer', 'bob@example.test');
        $adaKey = $this->key(7, 'Office', 'gvd_live_ada1', hash('sha256', 'ada'));
        foreach (range(1, 26) as $n) {
            DB::table('ai_activity')->insert([
                'user_id' => 7,
                'key_id' => $adaKey,
                'method' => 'tools/call',
                'tool' => 'list_contacts',
                'mode' => 'read',
                'status' => 'ok',
                'summary' => sprintf('row-%02d', $n),
                'created_at' => now()->subDays(2),
            ]);
        }
        DB::table('ai_activity')->insert([
            'user_id' => 8,
            'key_id' => null,
            'method' => 'tools/call',
            'tool' => 'send_email',
            'mode' => 'preview',
            'status' => 'ok',
            'summary' => 'bob-sent',
            'created_at' => now(),
        ]);

        $page = $this->asAdmin()->get('/admin/ai-connect');
        $page->assertOk();
        $page->assertSee('Page 1 of 2', false);
        $page->assertSee('bob-sent', false);
        $page->assertDontSee('row-01', false);
        $this->asAdmin()->get('/admin/ai-connect?act_page=2')->assertSee('row-01', false)->assertDontSee('bob-sent', false);

        $byCustomer = $this->asAdmin()->get('/admin/ai-connect?act_customer=8');
        $byCustomer->assertSee('bob-sent', false);
        $byCustomer->assertDontSee('row-26', false);

        $byAction = $this->asAdmin()->get('/admin/ai-connect?act_action=send_email');
        $byAction->assertSee('bob-sent', false);
        $byAction->assertDontSee('row-26', false);

        $byKey = $this->asAdmin()->get('/admin/ai-connect?act_key=Office');
        $byKey->assertSee('row-26', false);
        $byKey->assertDontSee('bob-sent', false);

        $byDate = $this->asAdmin()->get('/admin/ai-connect?act_date=' . now()->toDateString());
        $byDate->assertSee('bob-sent', false);
        $byDate->assertDontSee('row-26', false);
    }

    public function test_admin_master_switch_uses_the_customer_setting_and_logs_the_admin(): void
    {
        $this->customer(7, 'Ada', 'ada@example.test');
        $this->key(7, 'Office', 'gvd_live_ada1', hash('sha256', 'ada'));
        $this->asAdmin()->get('/admin/ai-connect')->assertSee('>On<', false);

        $this->asAdmin()->post('/admin/ai-connect/customers/toggle', ['user_id' => 7, 'enabled' => '0'])
            ->assertRedirect('/admin/ai-connect');
        $this->assertFalse(AiConnect::userEnabled(7));
        $log = DB::table('ai_activity')->where('tool', 'switch')->first();
        $this->assertSame('admin', $log->mode);
        $this->assertStringContainsString('actor admin #1', (string) $log->summary);
        $this->assertStringContainsString('turned AI Connect off', (string) $log->summary);

        $this->asAdmin()->post('/admin/ai-connect/customers/toggle', ['user_id' => 7, 'enabled' => '1']);
        $this->assertTrue(AiConnect::userEnabled(7));
    }

    public function test_provider_override_is_read_only_registry_plus_customer_hide(): void
    {
        $page = $this->asAdmin()->get('/admin/ai-connect');
        $page->assertOk();
        $page->assertSee('Grok', false);
        $page->assertSee('>Yes<', false);
        $page->assertSee('>No<', false);

        $this->asAdmin()->post('/admin/ai-connect/providers', ['provider' => 'claude', 'enabled' => '0'])
            ->assertRedirect('/admin/ai-connect');
        $this->assertFalse((bool) DB::table('ai_provider_overrides')->where('provider', 'claude')->value('enabled'));
        $log = DB::table('ai_activity')->where('tool', 'provider')->first();
        $this->assertSame('admin', $log->mode);
        $this->assertStringContainsString('actor admin #1', (string) $log->summary);
        $this->assertStringContainsString('hid provider claude', (string) $log->summary);

        $this->assertArrayNotHasKey('claude', VoiceAccountProvisioner::customerProviders(null));
        $this->assertArrayHasKey('claude', VoiceAccountProvisioner::customerProviders('claude'));
        $this->assertFalse(VoiceAccountProvisioner::providerIsOffered('claude', null));

        (require database_path('migrations/2026_11_06_120000_create_ai_voice_accounts_table.php'))->up();
        if (!Schema::hasTable('user_phone_numbers')) {
            Schema::create('user_phone_numbers', function ($table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('i_customer')->nullable();
                $table->text('numbers')->nullable();
            });
        }
        $customer = $this->withSession(['user_login' => true, 'user_id' => 7])->get('/user/ai-connect/voice-bot');
        $customer->assertOk();
        $customer->assertDontSee('value="claude"', false);
        $customer->assertSee('value="grok"', false);
    }

    private function asAdmin()
    {
        return $this->withSession(['admin_id' => 1]);
    }

    private function customer(int $id, string $name, string $email): void
    {
        DB::table('wpgc_users')->insert([
            'ID' => $id,
            'user_login' => strtolower(strtok($name, ' ')),
            'display_name' => $name,
            'user_email' => $email,
        ]);
    }

    private function key(int $userId, string $name, string $prefix, string $hash): int
    {
        return DB::table('ai_keys')->insertGetId([
            'user_id' => $userId,
            'name' => $name,
            'prefix' => $prefix,
            'hash' => $hash,
            'scopes' => json_encode(['contacts:read']),
            'daily_limit' => 200,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
