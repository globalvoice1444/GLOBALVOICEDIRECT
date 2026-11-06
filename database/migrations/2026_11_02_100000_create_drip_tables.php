<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('drip_sequences')) Schema::create('drip_sequences', function (Blueprint $t) {
            $t->engine = 'InnoDB';
            $t->id(); $t->string('name', 150); $t->string('slug', 80)->unique();
            $t->string('type', 20)->index();                  // signup | winback | dunning | reengage
            $t->string('trigger', 40)->default('manual');     // verified_signup | segment | manual
            $t->string('status', 12)->default('PAUSED');      // PAUSED | ACTIVE
            $t->text('description')->nullable();
            $t->dateTime('enroll_after')->nullable();         // only users verified/registered after this are auto-enrolled
            $t->timestamps();
        });
        if (!Schema::hasTable('drip_steps')) Schema::create('drip_steps', function (Blueprint $t) {
            $t->engine = 'InnoDB';
            $t->id(); $t->unsignedBigInteger('sequence_id')->index();
            $t->unsignedInteger('position')->default(0);
            $t->integer('day_offset')->default(0);            // any integer; > 30 allowed
            $t->string('name', 150); $t->string('subject', 255); $t->string('preview', 255)->nullable();
            $t->string('purpose', 255)->nullable(); $t->longText('body_html')->nullable();
            $t->json('conditions')->nullable();
            $t->string('status', 12)->default('DRAFT');       // DRAFT | ACTIVE | PAUSED
            $t->timestamps();
        });
        if (!Schema::hasTable('drip_enrollments')) Schema::create('drip_enrollments', function (Blueprint $t) {
            $t->engine = 'InnoDB';
            $t->id(); $t->unsignedBigInteger('sequence_id'); $t->unsignedBigInteger('user_id')->nullable()->index();
            $t->string('email', 190)->index(); $t->string('first_name', 100)->nullable();
            $t->string('source', 40)->default('auto');        // auto | segment:<name> | manual
            $t->string('status', 12)->default('active');      // active | completed | stopped
            $t->dateTime('enrolled_at'); $t->dateTime('completed_at')->nullable();
            $t->timestamps();
            $t->unique(['sequence_id', 'email']);
        });
        if (!Schema::hasTable('drip_sends')) Schema::create('drip_sends', function (Blueprint $t) {
            $t->engine = 'InnoDB';
            $t->id(); $t->unsignedBigInteger('enrollment_id'); $t->unsignedBigInteger('step_id')->index();
            $t->unsignedBigInteger('sequence_id')->index(); $t->string('email', 190)->index();
            $t->string('status', 12);                         // sending | sent | failed | skipped
            $t->string('reason', 255)->nullable(); $t->string('subject', 255)->nullable();
            $t->dateTime('sent_at')->nullable();
            $t->timestamps();
            $t->unique(['enrollment_id', 'step_id']);         // idempotency
        });
        if (!Schema::hasTable('email_suppressions')) Schema::create('email_suppressions', function (Blueprint $t) {
            $t->engine = 'InnoDB';
            $t->id(); $t->string('email', 190)->unique();
            $t->string('reason', 30)->default('unsubscribe'); // unsubscribe | bounce | complaint | manual
            $t->string('source', 60)->nullable(); $t->string('ip', 45)->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['email_suppressions', 'drip_sends', 'drip_enrollments', 'drip_steps', 'drip_sequences'] as $t) Schema::dropIfExists($t);
    }
};
