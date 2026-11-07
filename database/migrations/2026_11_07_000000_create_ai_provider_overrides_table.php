<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('ai_provider_overrides')) return;
        Schema::create('ai_provider_overrides', function (Blueprint $t) {
            $t->string('provider', 20)->primary();
            $t->boolean('enabled')->default(true);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_provider_overrides');
    }
};
