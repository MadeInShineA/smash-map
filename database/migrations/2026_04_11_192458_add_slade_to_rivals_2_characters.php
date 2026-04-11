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
            $slade = Character::create([
                "game_id" => GameEnum::RIVALS2,
                "name" => "Slade",
                "image_link" => "",
                "created_at" => now(),
                "updated_at" => now(),
            ]);

            // Create the associated image
            Image::create([
                "parentable_type" => Character::class,
                "parentable_id" => $slade->id,
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
            $slade = Character::where("name", "Slade")
                ->where("game_id", GameEnum::RIVALS2)
                ->first();

            if ($slade) {
                // Delete the associated image
                Image::where("parentable_id", $slade->id)
                    ->where("parentable_type", Character::class)
                    ->delete();

                // Delete the character
                $slade->delete();
            }
        });
    }
};
