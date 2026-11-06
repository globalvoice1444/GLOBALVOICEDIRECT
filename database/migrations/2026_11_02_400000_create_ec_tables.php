<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** 2026-11-02 Customer email campaigns (rebuild). Legacy tables email_contact / email_campaign_group / email_template are left untouched. */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('ec_contacts', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('user_id')->index(); $t->string('email', 190);
            $t->string('first_name', 100)->nullable(); $t->string('last_name', 100)->nullable(); $t->string('business_name', 200)->nullable();
            $t->string('tags', 500)->nullable(); $t->string('status', 20)->default('active'); $t->string('source', 30)->nullable();
            $t->timestamps(); $t->unique(['user_id', 'email']);
        });
        Schema::create('ec_suppressions', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('user_id'); $t->string('email', 190); $t->string('reason', 30); $t->string('ip', 64)->nullable();
            $t->timestamps(); $t->unique(['user_id', 'email']);
        });
        Schema::create('ec_templates', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('user_id')->index(); $t->string('name', 150); $t->mediumText('body_html'); $t->timestamps();
        });
        Schema::create('ec_campaigns', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('user_id')->index(); $t->string('name', 150); $t->string('subject', 200)->nullable(); $t->string('preview', 200)->nullable();
            $t->mediumText('body_html')->nullable(); $t->string('audience', 500)->nullable(); $t->string('status', 20)->default('DRAFT')->index();
            $t->dateTime('scheduled_at')->nullable(); $t->dateTime('started_at')->nullable(); $t->dateTime('finished_at')->nullable();
            $t->string('pause_reason', 255)->nullable(); $t->timestamps();
        });
        Schema::create('ec_recipients', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('campaign_id'); $t->unsignedBigInteger('contact_id')->nullable(); $t->string('email', 190);
            $t->string('first_name', 100)->nullable(); $t->string('last_name', 100)->nullable(); $t->string('business_name', 200)->nullable();
            $t->string('status', 20)->default('pending'); $t->string('reason', 255)->nullable(); $t->dateTime('sent_at')->nullable(); $t->dateTime('opened_at')->nullable();
            $t->timestamps(); $t->unique(['campaign_id', 'email']); $t->index(['campaign_id', 'status']); $t->index(['status', 'sent_at']);
        });
        Schema::create('ec_senders', function (Blueprint $t) {
            $t->unsignedBigInteger('user_id')->primary(); $t->boolean('blocked')->default(false); $t->string('blocked_reason', 255)->nullable();
            $t->unsignedInteger('daily_limit')->nullable(); $t->dateTime('first_sent_at')->nullable(); $t->timestamps();
        });
    }
    public function down(): void
    {
        foreach (['ec_senders', 'ec_recipients', 'ec_campaigns', 'ec_templates', 'ec_suppressions', 'ec_contacts'] as $x) Schema::dropIfExists($x);
    }
};
