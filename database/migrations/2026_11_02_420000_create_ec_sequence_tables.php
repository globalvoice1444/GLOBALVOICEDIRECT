<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
/** 2026-11-02 Customer sequences (drip) + automations. Sent through the customer's own mailbox. Everything starts PAUSED. */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('ec_sequences', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('user_id')->index(); $t->string('name', 150); $t->string('status', 10)->default('PAUSED'); $t->string('starter', 30)->nullable(); $t->timestamps();
        });
        Schema::create('ec_seq_steps', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('sequence_id')->index(); $t->unsignedSmallInteger('position'); $t->unsignedSmallInteger('day_offset')->default(0);
            $t->string('subject', 200); $t->string('preview', 200)->nullable(); $t->mediumText('body_html'); $t->timestamps();
        });
        Schema::create('ec_seq_enrollments', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('sequence_id'); $t->unsignedBigInteger('contact_id'); $t->string('email', 190); $t->string('status', 12)->default('active');
            $t->dateTime('enrolled_at'); $t->string('source', 30)->nullable(); $t->timestamps(); $t->unique(['sequence_id', 'contact_id']); $t->index(['status', 'sequence_id']);
        });
        Schema::create('ec_seq_sends', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('enrollment_id'); $t->unsignedBigInteger('step_id'); $t->unsignedBigInteger('user_id')->index(); $t->string('email', 190);
            $t->string('status', 12); $t->string('reason', 255)->nullable(); $t->dateTime('sent_at')->nullable(); $t->timestamps(); $t->unique(['enrollment_id', 'step_id']);
        });
        Schema::create('ec_automations', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('user_id')->index(); $t->string('name', 150); $t->string('status', 10)->default('PAUSED');
            $t->string('trigger', 20); $t->string('trigger_tag', 40)->nullable(); $t->string('action', 20); $t->string('action_value', 150)->nullable();
            $t->unsignedInteger('runs')->default(0); $t->dateTime('last_run_at')->nullable(); $t->timestamps();
        });
    }
    public function down(): void { foreach (['ec_automations', 'ec_seq_sends', 'ec_seq_enrollments', 'ec_seq_steps', 'ec_sequences'] as $x) Schema::dropIfExists($x); }
};
