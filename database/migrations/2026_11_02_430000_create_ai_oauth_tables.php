<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
/** 2026-11-02 AI Connect OAuth 2.1 (for ChatGPT and other OAuth-only MCP clients). Only hashes of codes/tokens/secrets are stored. Gated by AI_CONNECT_OAUTH_ENABLED. */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('ai_oauth_clients', function (Blueprint $t) {
            $t->id(); $t->string('client_id', 64)->unique(); $t->string('secret_hash', 64)->nullable(); $t->string('name', 120);
            $t->text('redirect_uris'); $t->string('auth_method', 30)->default('none'); $t->string('ip', 45)->nullable(); $t->timestamps();
        });
        Schema::create('ai_oauth_codes', function (Blueprint $t) {
            $t->id(); $t->string('code_hash', 64)->unique(); $t->string('client_id', 64); $t->unsignedBigInteger('user_id'); $t->unsignedBigInteger('key_id');
            $t->text('redirect_uri'); $t->string('challenge', 128); $t->string('resource', 255)->nullable(); $t->dateTime('expires_at'); $t->dateTime('used_at')->nullable(); $t->timestamps();
        });
        Schema::create('ai_oauth_tokens', function (Blueprint $t) {
            $t->id(); $t->string('access_hash', 64)->unique(); $t->string('refresh_hash', 64)->unique(); $t->string('client_id', 64)->index();
            $t->unsignedBigInteger('user_id')->index(); $t->unsignedBigInteger('key_id')->index();
            $t->dateTime('access_expires_at'); $t->dateTime('refresh_expires_at'); $t->dateTime('revoked_at')->nullable(); $t->timestamps();
        });
    }
    public function down(): void { foreach (['ai_oauth_tokens', 'ai_oauth_codes', 'ai_oauth_clients'] as $x) Schema::dropIfExists($x); }
};
