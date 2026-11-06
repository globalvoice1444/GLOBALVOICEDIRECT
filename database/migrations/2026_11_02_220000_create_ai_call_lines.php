<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('ai_call_lines')) Schema::create('ai_call_lines', function (Blueprint $t) {
            $t->unsignedBigInteger('user_id')->primary(); $t->string('account', 64); $t->text('secret_enc'); $t->timestamp('linked_at')->nullable(); $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('ai_call_lines'); }
};
