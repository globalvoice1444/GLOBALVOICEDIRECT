<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('gvd_number_labels')) return;
        Schema::create('gvd_number_labels', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->unsignedBigInteger('i_customer')->index();
            $t->unsignedBigInteger('i_account');
            $t->string('label', 60);
            $t->timestamps();
            $t->unique(['i_customer', 'i_account']);
        });
    }
    public function down(): void { Schema::dropIfExists('gvd_number_labels'); }
};
