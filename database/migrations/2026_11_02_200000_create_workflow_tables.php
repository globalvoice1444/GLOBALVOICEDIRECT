<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** 2026-11-02 Login tracking + automation workflows. Additive only. */
return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('user_activity')) Schema::create('user_activity', function (Blueprint $t) {
            $t->engine = 'InnoDB';
            $t->unsignedBigInteger('user_id')->primary();
            $t->dateTime('last_login_at')->nullable()->index(); $t->unsignedInteger('login_count')->default(0);
            $t->string('last_login_ip', 45)->nullable(); $t->dateTime('first_tracked_at')->nullable();
            $t->dateTime('last_call_at')->nullable(); $t->dateTime('last_call_checked_at')->nullable();
            $t->timestamps();
        });
        if (!Schema::hasTable('user_tags')) Schema::create('user_tags', function (Blueprint $t) {
            $t->engine = 'InnoDB';
            $t->id(); $t->unsignedBigInteger('user_id')->index(); $t->string('tag', 60)->index();
            $t->string('source', 60)->nullable(); $t->timestamps(); $t->unique(['user_id', 'tag']);
        });
        if (!Schema::hasTable('workflows')) Schema::create('workflows', function (Blueprint $t) {
            $t->engine = 'InnoDB';
            $t->id(); $t->string('name', 150); $t->text('description')->nullable();
            $t->string('trigger', 40)->default('manual'); $t->json('trigger_config')->nullable();
            $t->string('status', 12)->default('PAUSED'); $t->dateTime('activated_at')->nullable();
            $t->unsignedBigInteger('watermark')->default(0); $t->dateTime('last_run_at')->nullable();
            $t->timestamps();
        });
        if (!Schema::hasTable('workflow_nodes')) Schema::create('workflow_nodes', function (Blueprint $t) {
            $t->engine = 'InnoDB';
            $t->id(); $t->unsignedBigInteger('workflow_id')->index(); $t->unsignedBigInteger('parent_id')->nullable();
            $t->string('branch', 4)->default('main'); $t->unsignedInteger('position')->default(0);
            $t->string('type', 20); $t->json('config')->nullable(); $t->timestamps();
        });
        if (!Schema::hasTable('workflow_enrollments')) Schema::create('workflow_enrollments', function (Blueprint $t) {
            $t->engine = 'InnoDB';
            $t->id(); $t->unsignedBigInteger('workflow_id')->index(); $t->unsignedBigInteger('user_id')->nullable()->index();
            $t->string('email', 190); $t->string('first_name', 100)->nullable(); $t->string('cycle_key', 80)->default('once');
            $t->string('status', 12)->default('active'); $t->unsignedBigInteger('current_node_id')->nullable();
            $t->dateTime('wait_until')->nullable(); $t->json('context')->nullable();
            $t->dateTime('enrolled_at'); $t->dateTime('completed_at')->nullable(); $t->timestamps();
            $t->unique(['workflow_id', 'user_id', 'cycle_key']);
        });
        if (!Schema::hasTable('workflow_runs_log')) Schema::create('workflow_runs_log', function (Blueprint $t) {
            $t->engine = 'InnoDB';
            $t->id(); $t->unsignedBigInteger('workflow_id')->index(); $t->unsignedBigInteger('enrollment_id')->nullable()->index();
            $t->unsignedBigInteger('node_id')->nullable(); $t->string('action', 20); $t->string('result', 12);
            $t->string('detail', 255)->nullable(); $t->string('email', 190)->nullable()->index();
            $t->string('claim', 80)->nullable()->unique(); $t->dateTime('created_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        foreach (['workflow_runs_log', 'workflow_enrollments', 'workflow_nodes', 'workflows', 'user_tags', 'user_activity'] as $t) Schema::dropIfExists($t);
    }
};
