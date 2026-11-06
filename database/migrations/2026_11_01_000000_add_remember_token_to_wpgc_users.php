<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // legacy table has zero-date defaults; relax sql_mode for this session only
        $mode = DB::selectOne("SELECT @@SESSION.sql_mode AS m")->m;
        DB::statement("SET SESSION sql_mode = ''");
        if (!Schema::hasColumn("wpgc_users", "remember_token")) {
            Schema::table("wpgc_users", function (Blueprint $table) {
                $table->string("remember_token", 100)->nullable();
            });
        }
        DB::statement("SET SESSION sql_mode = ?", [$mode]);
    }

    public function down(): void
    {
        if (Schema::hasColumn("wpgc_users", "remember_token")) {
            Schema::table("wpgc_users", function (Blueprint $table) {
                $table->dropColumn("remember_token");
            });
        }
    }
};
