<?php

use App\Enums\GameEnum;
use App\Enums\ImageTypeEnum;
use App\Models\Character;
use App\Models\Image;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table("characters", function (Blueprint $table) {
            // Create the character
            $la_reina = Character::create([
                "game_id" => GameEnum::RIVALS2,
                "name" => "La Reina",
                "image_link" => "",
                "created_at" => now(),
                "updated_at" => now(),
            ]);

            // Create the associated image
            Image::create([
                "parentable_type" => Character::class,
                "parentable_id" => $la_reina->id,
                "type" => ImageTypeEnum::ICON,
                "extension" => "png",
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("characters", function (Blueprint $table) {
            $la_reina = Character::where("name", "La Reina")
                ->where("game_id", GameEnum::RIVALS2)
                ->first();

            if ($la_reina) {
                // Delete the associated image
                Image::where("parentable_id", $la_reina->id)
                    ->where("parentable_type", Character::class)
                    ->delete();

                // Delete the character
                $la_reina->delete();
            }
        });
    }
};
