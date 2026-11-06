<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Audio Campaign (2026-11-01): converted .au copy of each uploaded recording and its unguessable playback token.
return new class extends Migration {
    public function up(): void
    {
        Schema::table('gvd_audio_recordings', function (Blueprint $t) {
            if (!Schema::hasColumn('gvd_audio_recordings', 'au_path')) $t->string('au_path', 255)->nullable()->after('path');
            if (!Schema::hasColumn('gvd_audio_recordings', 'play_token')) $t->string('play_token', 48)->nullable()->unique()->after('au_path');
        });
    }

    public function down(): void
    {
        Schema::table('gvd_audio_recordings', function (Blueprint $t) { $t->dropUnique(['play_token']); $t->dropColumn(['au_path', 'play_token']); });
    }
};
