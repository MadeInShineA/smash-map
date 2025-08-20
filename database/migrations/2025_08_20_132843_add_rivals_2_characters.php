<?php

use App\Enums\GameEnum;
use App\Enums\ImageTypeEnum;
use App\Models\Character;
use App\Models\Image;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $characters = [
            [
                'game_id' => GameEnum::RIVALS2,
                'name' => "Etalus",
                'image_link' => "",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'game_id' => GameEnum::RIVALS2,
                'name' => "Olympia",
                'image_link' => "",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'game_id' => GameEnum::RIVALS2,
                'name' => "Absa",
                'image_link' => "",
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('characters')->insert($characters);

        $rivals2Characters = Character::where('game_id', GameEnum::RIVALS2)
            ->whereIn('name', ["Etalus", "Olympia", "Absa"])
            ->get();

        foreach ($rivals2Characters as $character) {
            Image::create([
                'parentable_type' => Character::class,
                'parentable_id'   => $character->id,
                'type'            => ImageTypeEnum::ICON,
                'extension'       => 'png',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $characterNames = ["Etalus", "Olympia", "Absa"];

        $characters = Character::where('game_id', GameEnum::RIVALS2)
            ->whereIn('name', $characterNames)
            ->get();

        foreach ($characters as $character) {
            // Delete related images
            Image::where('parentable_type', Character::class)
                ->where('parentable_id', $character->id)
                ->delete();

            // Delete the character itself
            $character->delete();
        }
    }
};
