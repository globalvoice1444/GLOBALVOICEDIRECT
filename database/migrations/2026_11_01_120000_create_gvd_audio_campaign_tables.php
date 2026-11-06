<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('gvd_audio_recordings')) Schema::create('gvd_audio_recordings', function (Blueprint $t) {
            $t->engine = 'InnoDB';
            $t->id(); $t->unsignedBigInteger('user_id')->index();
            $t->string('name', 150); $t->string('path', 255); $t->unsignedInteger('size')->default(0);
            $t->timestamps();
        });
        if (!Schema::hasTable('gvd_audio_campaigns')) Schema::create('gvd_audio_campaigns', function (Blueprint $t) {
            $t->engine = 'InnoDB';
            $t->id(); $t->unsignedBigInteger('user_id')->index(); $t->unsignedBigInteger('i_customer')->nullable()->index();
            $t->string('name', 150); $t->unsignedBigInteger('recording_id')->nullable();
            $t->string('caller_account', 64)->default('');      // billing account ID used for the callback
            $t->text('caller_secret')->nullable();               // encrypted; wiped when the campaign ends
            $t->string('status', 20)->default('draft')->index(); // draft, ready, queued, running, done, paused, cancelled
            $t->timestamp('scheduled_at')->nullable(); $t->timestamp('started_at')->nullable(); $t->timestamp('finished_at')->nullable();
            $t->timestamps();
        });
        if (!Schema::hasTable('gvd_audio_campaign_targets')) Schema::create('gvd_audio_campaign_targets', function (Blueprint $t) {
            $t->engine = 'InnoDB';
            $t->id(); $t->unsignedBigInteger('campaign_id')->index();
            $t->string('number', 20); $t->string('status', 20)->default('waiting'); // waiting, calling, placed, failed, skipped
            $t->string('result', 120)->default(''); $t->timestamp('attempted_at')->nullable();
            $t->timestamps();
            $t->unique(['campaign_id', 'number']);
        });
        if (!Schema::hasTable('jobs')) Schema::create('jobs', function (Blueprint $t) {
            $t->engine = 'InnoDB';
            $t->bigIncrements('id'); $t->string('queue')->index(); $t->longText('payload');
            $t->unsignedTinyInteger('attempts'); $t->unsignedInteger('reserved_at')->nullable();
            $t->unsignedInteger('available_at'); $t->unsignedInteger('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gvd_audio_campaign_targets');
        Schema::dropIfExists('gvd_audio_campaigns');
        Schema::dropIfExists('gvd_audio_recordings');
        // the jobs table is left in place (shared queue infrastructure)
    }
};
