<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** 2026-11-02 Admin > Automations > Broadcasts (one-off campaigns, throttled through drip:run). */
return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('broadcasts')) Schema::create('broadcasts', function (Blueprint $t) {
            $t->engine = 'InnoDB';
            $t->id(); $t->string('name', 150); $t->string('subject', 255); $t->string('preview', 255)->nullable();
            $t->longText('body_html')->nullable();
            $t->string('first_name_fallback', 60)->default('there'); $t->string('business_fallback', 100)->default('your business');
            $t->string('audience', 20)->default('customers');     // customers | csv
            $t->string('status', 12)->default('DRAFT');           // DRAFT | SCHEDULED | SENDING | PAUSED | DONE
            $t->unsignedInteger('daily_cap')->default(300);
            $t->dateTime('start_at')->nullable();                 // UTC
            $t->dateTime('finished_at')->nullable();
            $t->string('created_by', 100)->nullable();
            $t->timestamps();
        });
        if (!Schema::hasTable('broadcast_recipients')) Schema::create('broadcast_recipients', function (Blueprint $t) {
            $t->engine = 'InnoDB';
            $t->id(); $t->unsignedBigInteger('broadcast_id'); $t->unsignedBigInteger('user_id')->nullable();
            $t->string('email', 190); $t->string('first_name', 100)->nullable(); $t->string('business_name', 200)->nullable(); $t->string('extra', 255)->nullable();
            $t->string('source', 20)->default('customers');
            $t->string('status', 12)->default('pending');         // pending | sending | sent | failed | skipped
            $t->string('reason', 255)->nullable(); $t->dateTime('sent_at')->nullable();
            $t->timestamps();
            $t->unique(['broadcast_id', 'email']); $t->index(['broadcast_id', 'status']); $t->index(['email', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_recipients');
        Schema::dropIfExists('broadcasts');
    }
};
