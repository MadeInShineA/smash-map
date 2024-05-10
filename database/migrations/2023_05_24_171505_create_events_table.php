<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('game_id')->constrained('games')->cascadeOnDelete();
            $table->foreignId('address_id')->nullable()->constrained('addresses');
            $table->integer('start_gg_id');
            $table->dateTime('start_gg_updated_at');
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_online')->default(false);
            $table->string('name');
            $table->string('timezone')->default('+00:00');
            $table->dateTime('start_date_time');
            $table->dateTime('end_date_time');
            $table->integer('attendees')->nullable();
            $table->string('link')->nullable();



        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
