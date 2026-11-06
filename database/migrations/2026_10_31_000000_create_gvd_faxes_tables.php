<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('gvd_faxes', function (Blueprint $t) {
            $t->id();
            $t->uuid('uuid')->unique();
            $t->unsignedBigInteger('i_customer')->nullable()->index(); // null = quarantined
            $t->string('did', 20)->default('');
            $t->string('caller', 64)->default('');
            $t->dateTime('received_at')->index(); // UTC
            $t->string('filename', 191);
            $t->unsignedInteger('pages')->nullable();
            $t->unsignedInteger('size');
            $t->char('sha256', 64)->unique();
            $t->string('email_id', 100)->unique();
            $t->string('status', 16)->default('stored')->index();
            $t->string('reason', 100)->nullable();
            $t->timestamps();
        });
        Schema::create('gvd_fax_audit', function (Blueprint $t) {
            $t->id();
            $t->uuid('fax_uuid')->index();
            $t->unsignedBigInteger('i_customer')->index();
            $t->unsignedBigInteger('user_id')->nullable();
            $t->string('action', 16);
            $t->string('ip', 45)->nullable();
            $t->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gvd_fax_audit');
        Schema::dropIfExists('gvd_faxes');
    }
};
