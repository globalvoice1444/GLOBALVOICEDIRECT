<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SignupFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('payment_gateways', function ($table) {
            $table->id();
            $table->string('gateway_name');
            $table->boolean('is_active')->default(0);
        });

        Schema::create('wpgc_users', function ($table) {
            $table->id();
            $table->string('user_login')->nullable();
            $table->string('user_pass')->nullable();
            $table->string('user_nicename')->nullable();
            $table->integer('reward')->default(0);
            $table->integer('bonus_status')->default(0);
            $table->string('user_email')->nullable();
            $table->string('user_url')->nullable();
            $table->timestamp('user_registered')->nullable();
            $table->string('user_activation_key')->nullable();
            $table->string('display_name')->nullable();
            $table->string('salt')->nullable();
            $table->integer('user_status')->default(0);
        });

        Schema::create('unsuccess_membership', function ($table) {
            $table->id();
            $table->string('display_name')->nullable();
            $table->string('email')->nullable();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('company_name')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('reason')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('phone_state')->nullable();
            $table->string('phone_ratecenter')->nullable();
            $table->string('phone_pkg')->nullable();
        });
    }

    /**
     * RegisterController::signupaction() reads PHP's native $_SESSION superglobal directly
     * (mirroring what HomeController::maintainSession() writes), not Laravel's session() helper.
     */
    protected function setSelectedNumberInfoInNativeSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['SelectNumberInfo'] = [
            'number' => json_encode(['4045551234']),
            'package' => 'basic',
        ];
    }

    /** Gateway name with mixed case + surrounding whitespace must still render the Stripe card element, not the "no gateway" fallback. */
    public function test_credit_apps_page_normalizes_gateway_name_before_matching()
    {
        DB::table('payment_gateways')->insert([
            'gateway_name' => '  Stripe  ',
            'is_active' => 1,
        ]);

        $response = $this->get(route('credit-apps'));

        $response->assertOk();
        $response->assertSee('id="card-element"', false);
        $response->assertDontSee('No payment gateway is active');
    }

    /** Same normalization, this time for Authorize.Net -- must render the raw card-number field, not the Stripe element. */
    public function test_credit_apps_page_normalizes_authorize_gateway_name()
    {
        DB::table('payment_gateways')->insert([
            'gateway_name' => 'AUTHORIZE',
            'is_active' => 1,
        ]);

        $response = $this->get(route('credit-apps'));

        $response->assertOk();
        $response->assertSee('id="cardNumber"', false);
        $response->assertDontSee('No payment gateway is active');
    }

    /** No active gateway row at all -- should still render the page with the explicit fallback message, not crash. */
    public function test_credit_apps_page_shows_fallback_when_no_gateway_active()
    {
        $response = $this->get(route('credit-apps'));

        $response->assertOk();
        $response->assertSee('No payment gateway is active');
    }

    /** Posting signupaction with no phone package selected in session must redirect back to credit-apps, not crash. */
    public function test_signupaction_redirects_when_no_package_selected()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $response = $this->post('signupaction', [
            'email' => 'newcustomer@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'user_name' => 'newcustomer',
            'nicename' => 'New Customer',
            'display_name' => 'New Customer',
        ]);

        $response->assertRedirect(route('credit-apps', ['message' => 'Please Select the Phone Package And Try Again.']));
        $this->assertDatabaseCount('wpgc_users', 0);
    }

    /** Posting signupaction with a missing email must be rejected by server-side validation -- logged and redirected,
     *  not a hard crash from an unguarded $request->email access further down the method. */
    public function test_signupaction_rejects_missing_email_via_validation()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $this->setSelectedNumberInfoInNativeSession();

        $response = $this->post('signupaction', [
            // 'email' intentionally omitted
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'user_name' => 'newcustomer',
            'nicename' => 'New Customer',
            'display_name' => 'New Customer',
        ]);

        $response->assertRedirect(route('credit-apps', ['message' => 'The email field is required.']));
        $this->assertDatabaseCount('wpgc_users', 0);
        $this->assertDatabaseHas('unsuccess_membership', [
            'reason' => 'The email field is required.',
        ]);
    }

    /** Mismatched password/password_confirmation must be rejected by validation before any DB write or card charge. */
    public function test_signupaction_rejects_mismatched_password_confirmation()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $this->setSelectedNumberInfoInNativeSession();

        $response = $this->post('signupaction', [
            'email' => 'newcustomer@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'doesnotmatch',
            'user_name' => 'newcustomer',
            'nicename' => 'New Customer',
            'display_name' => 'New Customer',
        ]);

        $response->assertRedirect(route('credit-apps', ['message' => 'The password confirmation does not match.']));
        $this->assertDatabaseCount('wpgc_users', 0);
    }

    /** Duplicate email must still be caught after adding validation (regression guard for the pre-existing check). */
    public function test_signupaction_rejects_duplicate_email()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        DB::table('wpgc_users')->insert([
            'user_email' => 'existing@example.com',
            'user_login' => 'existinguser',
        ]);

        $this->setSelectedNumberInfoInNativeSession();

        $response = $this->post('signupaction', [
            'email' => 'existing@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'user_name' => 'newcustomer',
            'nicename' => 'New Customer',
            'display_name' => 'New Customer',
        ]);

        $response->assertRedirect(route('credit-apps', ['message' => 'This Email Already Registered']));
        $this->assertDatabaseHas('unsuccess_membership', [
            'reason' => 'This Email Already Registered',
        ]);
    }
}
