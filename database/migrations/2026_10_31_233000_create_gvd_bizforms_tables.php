<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('gvd_marketing_requests')) Schema::create('gvd_marketing_requests', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->index();
            $t->unsignedBigInteger('i_customer')->nullable()->index();
            $t->string('contact_name', 120); $t->string('business_name', 160); $t->string('email', 160); $t->string('phone', 40);
            $t->string('website', 200)->nullable(); $t->string('industry', 80)->nullable();
            $t->json('goals')->nullable(); $t->string('target_area', 200)->nullable(); $t->string('budget_range', 40)->nullable();
            $t->string('contact_time', 60)->nullable(); $t->text('message')->nullable();
            $t->string('status', 20)->default('new')->index();
            $t->timestamps();
        });
        if (!Schema::hasTable('gvd_credit_applications')) Schema::create('gvd_credit_applications', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->index();
            $t->unsignedBigInteger('i_customer')->nullable()->index();
            $t->string('legal_name', 160); $t->string('dba', 160)->nullable(); $t->string('entity_type', 40); $t->unsignedSmallInteger('years_in_business');
            $t->text('ein_encrypted')->nullable();
            $t->string('address', 300); $t->string('phone', 40); $t->string('email', 160);
            $t->string('applicant_name', 120); $t->string('applicant_title', 80);
            $t->unsignedInteger('requested_amount'); $t->unsignedInteger('monthly_usage');
            $t->string('ref1_name', 120); $t->string('ref1_phone', 40); $t->string('ref2_name', 120); $t->string('ref2_phone', 40);
            $t->string('bank_name', 120);
            $t->boolean('agreed')->default(false); $t->string('signature', 120); $t->date('signed_date');
            $t->string('status', 20)->default('new')->index();
            $t->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('gvd_marketing_requests'); Schema::dropIfExists('gvd_credit_applications'); }
};
