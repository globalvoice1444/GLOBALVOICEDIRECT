<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
/** 2026-11-02 Customer's own SMTP for campaigns (password encrypted with APP_KEY via Crypt). */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('ec_smtp', function (Blueprint $t) {
            $t->unsignedBigInteger('user_id')->primary(); $t->string('provider', 20); $t->string('host', 190); $t->unsignedSmallInteger('port');
            $t->string('encryption', 10); $t->string('username', 190); $t->text('password_enc'); $t->string('from_name', 100); $t->string('from_email', 190);
            $t->unsignedInteger('daily_limit')->nullable(); $t->dateTime('verified_at')->nullable(); $t->string('last_error', 255)->nullable(); $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('ec_smtp'); }
};
