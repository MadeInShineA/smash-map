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
            $mewtwo = Character::create([
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Mewtwo',
                'image_link' => 'https://ssb.wiki.gallery/images/9/9f/MewtwoHeadPM.png',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create the associated image
            Image::create([
                'parentable_type' => Character::class,
                'parentable_id' => $mewtwo->id,
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
            $mewtwo = Character::where('name', 'Mewtwo')
                ->where('game_id', GameEnum::PROJECT_PLUS)
                ->first();

            if ($mewtwo) {
                // Delete the associated image
                Image::where('parentable_id', $mewtwo->id)
                    ->where('parentable_type', Character::class)
                    ->delete();

                // Delete the character
                $mewtwo->delete();
            }
        });
    }
};
