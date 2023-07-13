<?php

namespace Database\Seeders;

use App\Enums\GameEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CharacterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void{

        DB::table('characters')->delete();

        $characters = [
            [
                'game_id'       => GameEnum::MELEE,
                'name'          => 'Bowser',
                'image_link'    => 'https://ssb.wiki.gallery/images/3/3b/BowserHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Captain Falcon',
                'image_link' => 'https://ssb.wiki.gallery/images/5/5f/CaptainFalconHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Crazy Hand',
                'image_link' => 'https://ssb.wiki.gallery/images/2/22/CrazyHandHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Dark Link',
                'image_link' => 'https://ssb.wiki.gallery/images/d/d6/DarkLinkHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Donkey Kong',
                'image_link' => 'https://ssb.wiki.gallery/images/9/9b/DonkeyKongHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Dr. Mario',
                'image_link' => 'https://ssb.wiki.gallery/images/6/61/DrMarioHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Falco',
                'image_link' => 'https://ssb.wiki.gallery/images/d/d6/FalcoHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Fighting Wire Frames',
                'image_link' => 'https://ssb.wiki.gallery/images/7/7a/FightingWireFramesHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Fox',
                'image_link' => 'https://ssb.wiki.gallery/images/d/db/FoxHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Ganondorf',
                'image_link' => 'https://ssb.wiki.gallery/images/7/77/GanondorfHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Giga Bowser',
                'image_link' => 'https://ssb.wiki.gallery/images/9/9e/GigaBowserHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Ice Climbers',
                'image_link' => 'https://ssb.wiki.gallery/images/d/d1/IceClimbersHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Jigglypuff',
                'image_link' => 'https://ssb.wiki.gallery/images/5/5a/JigglypuffHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Kirby',
                'image_link' => 'https://ssb.wiki.gallery/images/7/7a/KirbyHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Link',
                'image_link' => 'https://ssb.wiki.gallery/images/1/17/LinkHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Luigi',
                'image_link' => 'https://ssb.wiki.gallery/images/d/d1/LuigiHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Mario',
                'image_link' => 'https://ssb.wiki.gallery/images/d/d2/MarioHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Marth',
                'image_link' => 'https://ssb.wiki.gallery/images/3/34/MarthHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Master Hand',
                'image_link' => 'https://ssb.wiki.gallery/images/1/1e/MasterHandHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Mewtwo',
                'image_link' => 'https://ssb.wiki.gallery/images/9/98/MewtwoHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Mr. Game & Watch',
                'image_link' => 'https://ssb.wiki.gallery/images/4/4f/MrGame%26WatchHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Ness',
                'image_link' => 'https://ssb.wiki.gallery/images/3/37/NessHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Peach',
                'image_link' => 'https://ssb.wiki.gallery/images/9/94/PeachHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Pichu',
                'image_link' => 'https://ssb.wiki.gallery/images/4/4b/PichuHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Pikachu',
                'image_link' => 'https://ssb.wiki.gallery/images/2/28/PikachuHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Roy',
                'image_link' => 'https://ssb.wiki.gallery/images/5/57/RoyHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Samus',
                'image_link' => 'https://ssb.wiki.gallery/images/3/3d/SamusHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Sheik',
                'image_link' => 'https://ssb.wiki.gallery/images/5/5f/SheikHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Yoshi',
                'image_link' => 'https://ssb.wiki.gallery/images/5/50/YoshiHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Young Link',
                'image_link' => 'https://ssb.wiki.gallery/images/6/63/YoungLinkHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Zelda',
                'image_link' => 'https://ssb.wiki.gallery/images/f/fa/ZeldaHeadSSBM.png'
            ]
        ];

        DB::table('characters')->insert($characters);

    }
}
