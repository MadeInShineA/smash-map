<?php

use App\Enums\GameEnum;
use App\Enums\ImageTypeEnum;
use App\Models\Character;
use App\Models\Image;
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
        Schema::table('characters', function (Blueprint $table) {
            // Insert the character using Eloquent to get the model instance
            $roy = Character::create([
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Roy',
                'image_link' => 'https://ssb.wiki.gallery/images/2/25/RoyHeadPM.png',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create the associated image
            Image::create([
                'parentable_type' => Character::class,
                'parentable_id' => $roy->id,
                'type' => ImageTypeEnum::ICON,
                'extension' => 'png'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            // Retrieve the character
            $roy = Character::where('name', 'Roy')
                ->where('game_id', GameEnum::PROJECT_PLUS)
                ->first();

            if ($roy) {
                // Delete the associated image
                Image::where('parentable_id', $roy->id)
                    ->where('parentable_type', Character::class)
                    ->delete();

                // Delete the character
                $roy->delete();
            }
        });
    }
};
