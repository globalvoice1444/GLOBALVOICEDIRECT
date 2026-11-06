<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('gvd_business_listings')) return;
        Schema::create('gvd_business_listings', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->index();
            $t->unsignedBigInteger('i_customer')->nullable()->index();
            $t->string('phone', 20)->unique();
            $t->string('status', 20)->default('new')->index();
            $t->string('business_name', 200); $t->string('street', 200); $t->string('suite', 100)->default('');
            $t->string('city', 100); $t->string('state', 50); $t->string('zip', 20); $t->string('country', 2)->default('US');
            $t->string('website', 255)->default(''); $t->string('email', 190);
            $t->string('category', 150); $t->text('extra_categories')->nullable();
            $t->text('description')->nullable(); $t->text('hours')->nullable();
            $t->string('contact_name', 150); $t->boolean('consent')->default(false);
            $t->string('ip', 45)->default('');
            $t->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('gvd_business_listings'); }
};
