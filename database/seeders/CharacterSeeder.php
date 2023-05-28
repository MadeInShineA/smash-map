<?php

namespace Database\Seeders;

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

        $characters = array(
            array(
                'name' => 'Bowser',
                'image_link' => 'https://ssb.wiki.gallery/images/3/3b/BowserHeadSSBM.png'
            ),
            array(
                'name' => 'Captain Falcon',
                'image_link' => 'https://ssb.wiki.gallery/images/5/5f/CaptainFalconHeadSSBM.png'
            ),
            array(
                'name' => 'Crazy Hand',
                'image_link' => 'https://ssb.wiki.gallery/images/2/22/CrazyHandHeadSSBM.png'
            ),
            array(
                'name' => 'Dark Link',
                'image_link' => 'https://ssb.wiki.gallery/images/d/d6/DarkLinkHeadSSBM.png'
            ),
            array(
                'name' => 'Donkey Kong',
                'image_link' => 'https://ssb.wiki.gallery/images/9/9b/DonkeyKongHeadSSBM.png'
            ),
            array(
                'name' => 'Dr. Mario',
                'image_link' => 'https://ssb.wiki.gallery/images/6/61/DrMarioHeadSSBM.png'
            ),
            array(
                'name' => 'Falco',
                'image_link' => 'https://ssb.wiki.gallery/images/d/d6/FalcoHeadSSBM.png'
            ),
            array(
                'name' => 'Fighting Wire Frames',
                'image_link' => 'https://ssb.wiki.gallery/images/7/7a/FightingWireFramesHeadSSBM.png'
            ),
            array(
                'name' => 'Fox',
                'image_link' => 'https://ssb.wiki.gallery/images/d/db/FoxHeadSSBM.png'
            ),
            array(
                'name' => 'Ganondorf',
                'image_link' => 'https://ssb.wiki.gallery/images/7/77/GanondorfHeadSSBM.png'
            ),
            array(
                'name' => 'Giga Bowser',
                'image_link' => 'https://ssb.wiki.gallery/images/9/9e/GigaBowserHeadSSBM.png'
            ),
            array(
                'name' => 'Ice Climbers',
                'image_link' => 'https://ssb.wiki.gallery/images/d/d1/IceClimbersHeadSSBM.png'
            ),
            array(
                'name' => 'Jigglypuff',
                'image_link' => 'https://ssb.wiki.gallery/images/5/5a/JigglypuffHeadSSBM.png'
            ),
            array(
                'name' => 'Kirby',
                'image_link' => 'https://ssb.wiki.gallery/images/7/7a/KirbyHeadSSBM.png'
            ),
            array(
                'name' => 'Link',
                'image_link' => 'https://ssb.wiki.gallery/images/1/17/LinkHeadSSBM.png'
            ),
            array(
                'name' => 'Luigi',
                'image_link' => 'https://ssb.wiki.gallery/images/d/d1/LuigiHeadSSBM.png'
            ),
            array(
                'name' => 'Mario',
                'image_link' => 'https://ssb.wiki.gallery/images/d/d2/MarioHeadSSBM.png'
            ),
            array(
                'name' => 'Marth',
                'image_link' => 'https://ssb.wiki.gallery/images/3/34/MarthHeadSSBM.png'
            ),
            array(
                'name' => 'Master Hand',
                'image_link' => 'https://ssb.wiki.gallery/images/1/1e/MasterHandHeadSSBM.png'
            ),
            array(
                'name' => 'Mewtwo',
                'image_link' => 'https://ssb.wiki.gallery/images/9/98/MewtwoHeadSSBM.png'
            ),
            array(
                'name' => 'Mr. Game & Watch',
                'image_link' => 'https://ssb.wiki.gallery/images/4/4f/MrGame%26WatchHeadSSBM.png'
            ),
            array(
                'name' => 'Ness',
                'image_link' => 'https://ssb.wiki.gallery/images/3/37/NessHeadSSBM.png'
            ),
            array(
                'name' => 'Peach',
                'image_link' => 'https://ssb.wiki.gallery/images/9/94/PeachHeadSSBM.png'
            ),
            array(
                'name' => 'Pichu',
                'image_link' => 'https://ssb.wiki.gallery/images/4/4b/PichuHeadSSBM.png'
            ),
            array(
                'name' => 'Pikachu',
                'image_link' => 'https://ssb.wiki.gallery/images/2/28/PikachuHeadSSBM.png'
            ),
            array(
                'name' => 'Roy',
                'image_link' => 'https://ssb.wiki.gallery/images/5/57/RoyHeadSSBM.png'
            ),
            array(
                'name' => 'Samus',
                'image_link' => 'https://ssb.wiki.gallery/images/3/3d/SamusHeadSSBM.png'
            ),
            array(
                'name' => 'Sheik',
                'image_link' => 'https://ssb.wiki.gallery/images/5/5f/SheikHeadSSBM.png'
            ),
            array(
                'name' => 'Yoshi',
                'image_link' => 'https://ssb.wiki.gallery/images/5/50/YoshiHeadSSBM.png'
            ),
            array(
                'name' => 'Young Link',
                'image_link' => 'https://ssb.wiki.gallery/images/6/63/YoungLinkHeadSSBM.png'
            ),
            array(
                'name' => 'Zelda',
                'image_link' => 'https://ssb.wiki.gallery/images/f/fa/ZeldaHeadSSBM.png'
            )
        );

        DB::table('characters')->insert($characters);

    }
}
