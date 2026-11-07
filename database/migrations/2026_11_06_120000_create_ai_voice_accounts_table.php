<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('ai_voice_accounts')) return;
        Schema::create('ai_voice_accounts', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->unique();
            $t->string('account_id', 64)->unique();
            $t->string('username', 64);
            $t->string('sip_domain', 190)->nullable();
            $t->text('sip_password');
            $t->boolean('enabled')->default(true);
            $t->string('caller_id', 20)->nullable();
            $t->string('provider', 20);
            $t->text('api_key')->nullable();
            $t->timestamp('api_key_updated_at')->nullable();
            $t->boolean('api_key_dirty')->default(false);
            $t->boolean('api_key_on_gateway')->default(false);
            $t->string('status', 20)->default('pending');
            $t->string('last_error', 255)->nullable();
            $t->string('last_result', 255)->nullable();
            $t->timestamp('provisioned_at')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_voice_accounts');
    }
};
