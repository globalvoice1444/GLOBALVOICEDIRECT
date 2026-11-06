<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('gvd_admin_alerts')) return;
        Schema::create('gvd_admin_alerts', function (Blueprint $t) {
            $t->id();
            $t->string('type', 40)->index();
            $t->string('severity', 20)->default('info')->index();
            $t->string('title', 200);
            $t->json('details')->nullable();
            $t->unsignedBigInteger('i_customer')->nullable()->index();
            $t->unsignedBigInteger('user_id')->nullable()->index();
            $t->string('ip', 45)->nullable();
            $t->timestamp('read_at')->nullable()->index();
            $t->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('gvd_admin_alerts'); }
};
