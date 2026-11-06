<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('gvd_sms_campaigns')) Schema::create('gvd_sms_campaigns', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->index();
            $t->unsignedBigInteger('i_customer')->nullable()->index();
            $t->string('name', 120);
            $t->text('message');
            $t->unsignedBigInteger('list_id')->nullable();
            $t->string('sender', 20)->nullable();
            $t->string('status', 20)->default('draft');
            $t->dateTime('scheduled_at')->nullable();
            $t->unsignedSmallInteger('segments')->default(1);
            $t->string('encoding', 10)->default('GSM-7');
            $t->unsignedInteger('sent_count')->default(0);
            $t->unsignedInteger('failed_count')->default(0);
            $t->dateTime('sent_at')->nullable();
            $t->dateTime('last_dry_run_at')->nullable();
            $t->timestamps();
        });
        if (!Schema::hasTable('gvd_sms_messages')) Schema::create('gvd_sms_messages', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('campaign_id')->index();
            $t->string('to', 20);
            $t->string('status', 20);
            $t->timestamp('created_at')->nullable();
        });
    }
    public function down(): void { Schema::dropIfExists('gvd_sms_messages'); Schema::dropIfExists('gvd_sms_campaigns'); }
};
