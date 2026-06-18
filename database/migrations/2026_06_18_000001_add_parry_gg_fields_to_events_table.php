<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table("events", function (Blueprint $table) {
            $table->integer("start_gg_id")->nullable()->change();
            $table->dateTime("start_gg_updated_at")->nullable()->change();
            $table->string("parry_gg_id")->nullable()->after("start_gg_updated_at");
            $table->dateTime("parry_gg_updated_at")->nullable()->after("parry_gg_id");
        });
    }

    public function down(): void
    {
        Schema::table("events", function (Blueprint $table) {
            $table->dateTime("start_gg_updated_at")->nullable(false)->change();
            $table->integer("start_gg_id")->nullable(false)->change();
            $table->dropColumn(["parry_gg_id", "parry_gg_updated_at"]);
        });
    }
};
