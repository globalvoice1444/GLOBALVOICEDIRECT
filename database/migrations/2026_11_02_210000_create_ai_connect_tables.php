<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('ai_keys')) Schema::create('ai_keys', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('user_id')->index(); $t->string('name', 80); $t->string('prefix', 16);
            $t->char('hash', 64)->unique(); $t->text('scopes'); $t->unsignedInteger('daily_limit')->default(200);
            $t->timestamp('last_used_at')->nullable(); $t->timestamp('revoked_at')->nullable(); $t->timestamps();
        });
        if (!Schema::hasTable('ai_settings')) Schema::create('ai_settings', function (Blueprint $t) {
            $t->unsignedBigInteger('user_id')->primary(); $t->boolean('enabled')->default(true); $t->timestamps();
        });
        if (!Schema::hasTable('ai_activity')) Schema::create('ai_activity', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('user_id')->index(); $t->unsignedBigInteger('key_id')->nullable()->index();
            $t->string('method', 40); $t->string('tool', 40)->nullable(); $t->string('mode', 10)->nullable(); // preview|confirm|read
            $t->string('status', 12); $t->string('summary', 255)->nullable(); $t->string('ip', 45)->nullable(); $t->timestamp('created_at')->nullable()->index();
        });
    }
    public function down(): void { Schema::dropIfExists('ai_activity'); Schema::dropIfExists('ai_settings'); Schema::dropIfExists('ai_keys'); }
};
