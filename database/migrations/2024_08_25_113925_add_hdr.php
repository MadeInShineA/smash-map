<?php

use App\Enums\GameEnum;
use App\Enums\ImageTypeEnum;
use App\Models\Character;
use App\Models\Game;
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
        Game::create([
            'id' => GameEnum::HDR,
            'name' => 'HDR',
            'slug' => 'hdr',
            'color' => '#008600',
        ]);

        $ultimate_characters = Character::where('game_id', GameEnum::ULTIMATE)->get();
        foreach ($ultimate_characters as $ultimate_character) {
            $character = Character::create([
                'game_id' => GameEnum::HDR,
                'name' => $ultimate_character->name,
                'image_link' => $ultimate_character->image_link,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            Image::Create(['parentable_type' => Character::class, 'parentable_id' => $character->id, 'type' => ImageTypeEnum::ICON, 'extension' => 'png']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $hdr_game = Game::where('id', GameEnum::HDR)->first();
        $hdr_game->delete();
    }
};
