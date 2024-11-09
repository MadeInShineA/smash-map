<?php

use App\Enums\GameEnum;
use App\Enums\ImageTypeEnum;
use App\Models\Character;
use App\Models\Game;
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
        Game::create([
            'id' => GameEnum::RIVALS2,
            'name' => 'Rivals 2',
            'slug' => 'rivals-2',
            'color' => '#B19EEF',
        ]);

        $characters = [
            [
                'game_id' => GameEnum::RIVALS2,
                'name' => "Clairen" ,
                'image_link' => "",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'game_id' => GameEnum::RIVALS2,
                'name' => "Fleet" ,
                'image_link' => "",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'game_id' => GameEnum::RIVALS2,
                'name' => "Forsburn" ,
                'image_link' => "",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'game_id' => GameEnum::RIVALS2,
                'name' => "Kragg" ,
                'image_link' => "",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'game_id' => GameEnum::RIVALS2,
                'name' => "Loxodont" ,
                'image_link' => "",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'game_id' => GameEnum::RIVALS2,
                'name' => "Maypul" ,
                'image_link' => "",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'game_id' => GameEnum::RIVALS2,
                'name' => "Orcane" ,
                'image_link' => "",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'game_id' => GameEnum::RIVALS2,
                'name' => "Ranno" ,
                'image_link' => "",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'game_id' => GameEnum::RIVALS2,
                'name' => "Wrastor" ,
                'image_link' => "",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'game_id' => GameEnum::RIVALS2,
                'name' => "Zetterburn" ,
                'image_link' => "",
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('characters')->insert($characters);

        $rivals_2_characters = Character::where('game_id', GameEnum::RIVALS2)->get();

        foreach ($rivals_2_characters as $character){
            Image::Create(['parentable_type' => Character::class, 'parentable_id' => $character->id, 'type' => ImageTypeEnum::ICON, 'extension' => 'png']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $rivals_2_game = Game::where('id', GameEnum::RIVALS2)->first();
        $rivals_2_game->delete();
    }
};
