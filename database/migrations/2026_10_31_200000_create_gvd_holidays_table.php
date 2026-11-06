<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('gvd_holidays')) return;
        Schema::create('gvd_holidays', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->unsignedBigInteger('i_customer')->index();
            $t->string('name', 100);
            $t->date('day');
            $t->timestamps();
            $t->unique(['i_customer', 'day']);
        });
    }
    public function down(): void { Schema::dropIfExists('gvd_holidays'); }
};
