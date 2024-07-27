<?php

use App\Models\Scopes\ShownScope;
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
            $table->uuid('uuid')->after('id')->nullable();
        });

        $events = \App\Models\Event::withoutGlobalScope(ShownScope::class)->all();
        foreach ($events as $event) {
            $event->uuid = \Illuminate\Support\Str::uuid();
            $event->save();
        }

        Schema::table('events', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->unique()->change();
        });
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
