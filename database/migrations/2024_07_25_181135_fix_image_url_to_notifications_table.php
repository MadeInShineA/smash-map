<?php

use App\Models\Notification;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $notifications = Notification::where('image_url', 'like', '%/storage/map-icons/%')->get();
            foreach ($notifications as $notification) {
                $notification->image_url =  URL::to('/storage/map-icons/' . $notification->game->slug . '.png');
                $notification->save();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $notifications = Notification::where('image_url', 'like', '%/storage/map-icons/%')->get();
            foreach ($notifications as $notification) {
                $notification->image_url =  URL::to('/storage/map-icons/' . $notification->game->name . '.png');
                $notification->save();
            }
        });
    }
};
