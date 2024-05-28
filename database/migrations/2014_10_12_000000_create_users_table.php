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
            $table->string('uuid');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->foreignId('address_id')->nullable()->constrained('addresses');
            $table->boolean('has_default_profile_picture')->default(true);
            $table->string('connect_code')->nullable();
            $table->string('discord')->nullable();
            $table->string('x')->nullable();
            $table->string('description')->nullable();
            $table->string('color')->nullable();
            $table->boolean('has_distance_notifications')->default(true);
            $table->unsignedInteger('distance_notifications_radius')->default(500);
            $table->boolean('has_attendees_notifications')->default(true);
            // Default value is set inside the User model
            $table->json('attendees_notifications_thresholds')->nullable();
            $table->boolean('has_time_notifications')->default(true);
            // Default value is set inside the User model
            $table->json('time_notifications_thresholds')->nullable();
            $table->boolean('is_modder')->default(false);
            $table->boolean('is_on_map')->default(false);
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
