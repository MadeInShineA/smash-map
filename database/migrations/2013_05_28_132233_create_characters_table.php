<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("characters", function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table
                ->foreignId("game_id")
                ->constrained("games")
                ->cascadeOnDelete();
            $table->string("name");
            $table->string("image_link");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("characters");
    }
};
