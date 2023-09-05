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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->foreignId('address_id')->nullable()->constrained('addresses');
            $table->string('discord')->nullable();
            $table->string('twitter')->nullable();
            $table->string('description')->nullable();
            $table->string('color')->nullable();
            $table->foreignId('main_character_id')->constrained('characters')->restrictOnDelete();
            $table->foreignId('main_game_id')->constrained('games')->restrictOnDelete();
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_subscribed')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
