<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('gvd_portal_links', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('i_customer')->unique();
            $t->unsignedBigInteger('user_id')->index(); // wpgc_users.ID (shadow user)
            $t->string('source', 32)->default('virtual_office');
            $t->timestamp('last_login_at')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('gvd_portal_links'); }
};
