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
        Schema::table('events', function (Blueprint $table) {
            $table->uuid('uuid')->after('id')->unique();
        });

        $events = \App\Models\Event::all();
        foreach ($events as $event) {
            $event->uuid = \Illuminate\Support\Str::uuid();
            $event->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
