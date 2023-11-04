<?php

namespace Database\Seeders;

use App\Enums\GameEnum;
use App\Models\Character;
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
                'game_id' => GameEnum::SMASH64,
                'name' => 'Captain Falcon',
                'image_link' => 'https://ssb.wiki.gallery/images/7/75/CaptainFalconHeadSSB.png'
            ],
            [
                'game_id' => GameEnum::SMASH64,
                'name' => 'Donkey Kong',
                'image_link' => 'https://ssb.wiki.gallery/images/a/af/DonkeyKongHeadSSB.png'
            ],
            [
                'game_id' => GameEnum::SMASH64,
                'name' => 'Fox',
                'image_link' => 'https://ssb.wiki.gallery/images/8/80/FoxHeadSSB.png'
            ],
            [
                'game_id' => GameEnum::SMASH64,
                'name' => 'Jigglypuff',
                'image_link' => 'https://ssb.wiki.gallery/images/8/84/JigglypuffHeadSSB.png'
            ],
            [
                'game_id' => GameEnum::SMASH64,
                'name' => 'Kirby',
                'image_link' => 'https://ssb.wiki.gallery/images/e/e0/KirbyHeadSSB.png'
            ],
            [
                'game_id' => GameEnum::SMASH64,
                'name' => 'Link',
                'image_link' => 'https://ssb.wiki.gallery/images/3/3b/LinkHeadSSB.png'
            ],
            [
                'game_id' => GameEnum::SMASH64,
                'name' => 'Luigi',
                'image_link' => 'https://ssb.wiki.gallery/images/6/69/LuigiHeadSSB.png'
            ],
            [
                'game_id' => GameEnum::SMASH64,
                'name' => 'Mario',
                'image_link' => 'https://ssb.wiki.gallery/images/a/aa/MarioHeadSSB.png'
            ],
            [
                'game_id' => GameEnum::SMASH64,
                'name' => 'Ness',
                'image_link' => 'https://ssb.wiki.gallery/images/c/cd/NessHeadSSB.png'
            ],
            [
                'game_id' => GameEnum::SMASH64,
                'name' => 'Pikachu',
                'image_link' => 'https://ssb.wiki.gallery/images/8/80/PikachuHeadSSB.png'
            ],
            [
                'game_id' => GameEnum::SMASH64,
                'name' => 'Samus',
                'image_link' => 'https://ssb.wiki.gallery/images/1/14/SamusHeadSSB.png'
            ],
            [
                'game_id' => GameEnum::SMASH64,
                'name' => 'Yoshi',
                'image_link' => 'https://ssb.wiki.gallery/images/4/44/YoshiHeadSSB.png'
            ],
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
                'image_link' => 'https://ssb.wiki.gallery/images/e/ec/MarioHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Marth',
                'image_link' => 'https://ssb.wiki.gallery/images/9/9b/MarthHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Mewtwo',
                'image_link' => 'https://ssb.wiki.gallery/images/5/5b/MewtwoHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Mr. Game & Watch',
                'image_link' => 'https://ssb.wiki.gallery/images/b/ba/MrGame%26WatchHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Ness',
                'image_link' => 'https://ssb.wiki.gallery/images/4/47/NessHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Peach',
                'image_link' => 'https://ssb.wiki.gallery/images/3/3f/PeachHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Pichu',
                'image_link' => 'https://ssb.wiki.gallery/images/3/30/PichuHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Pikachu',
                'image_link' => 'https://ssb.wiki.gallery/images/8/88/PikachuHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Roy',
                'image_link' => 'https://ssb.wiki.gallery/images/f/f2/RoyHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Samus',
                'image_link' => 'https://ssb.wiki.gallery/images/f/f6/SamusHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Sheik',
                'image_link' => 'https://ssb.wiki.gallery/images/7/76/SheikHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Yoshi',
                'image_link' => 'https://ssb.wiki.gallery/images/6/6d/YoshiHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Young Link',
                'image_link' => 'https://ssb.wiki.gallery/images/a/ac/YoungLinkHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::MELEE,
                'name' => 'Zelda',
                'image_link' => 'https://ssb.wiki.gallery/images/2/29/ZeldaHeadSSBM.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Bowser',
                'image_link' => 'https://ssb.wiki.gallery/images/0/05/BowserHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Captain Falcon',
                'image_link' => 'https://ssb.wiki.gallery/images/6/61/CaptainFalconHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Diddy Kong',
                'image_link' => 'https://ssb.wiki.gallery/images/1/1c/DiddyKongHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Donkey Kong',
                'image_link' => 'https://ssb.wiki.gallery/images/0/0b/DonkeyKongHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Falco',
                'image_link' => 'https://ssb.wiki.gallery/images/4/4b/FalcoHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Fox',
                'image_link' => 'https://ssb.wiki.gallery/images/1/15/FoxHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Ganondorf',
                'image_link' => 'https://ssb.wiki.gallery/images/8/82/GanondorfHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Ice Climbers',
                'image_link' => 'https://ssb.wiki.gallery/images/e/e4/IceClimbersHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Ike',
                'image_link' => 'https://ssb.wiki.gallery/images/3/32/IkeHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Jigglypuff',
                'image_link' => 'https://ssb.wiki.gallery/images/4/4a/JigglypuffHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'King Dedede',
                'image_link' => 'https://ssb.wiki.gallery/images/6/6b/KingDededeHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Kirby',
                'image_link' => 'https://ssb.wiki.gallery/images/c/ca/KirbyHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Link',
                'image_link' => 'https://ssb.wiki.gallery/images/2/28/LinkHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Lucario',
                'image_link' => 'https://ssb.wiki.gallery/images/e/e3/LucarioHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Lucas',
                'image_link' => 'https://ssb.wiki.gallery/images/e/e6/LucasHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Luigi',
                'image_link' => 'https://ssb.wiki.gallery/images/a/af/LuigiHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Mario',
                'image_link' => 'https://ssb.wiki.gallery/images/0/07/MarioHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Marth',
                'image_link' => 'https://ssb.wiki.gallery/images/5/52/MarthHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Meta Knight',
                'image_link' => 'https://ssb.wiki.gallery/images/f/f1/MetaKnightHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Mr. Game & Watch',
                'image_link' => 'https://ssb.wiki.gallery/images/4/48/MrGame%26WatchHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Ness',
                'image_link' => 'https://ssb.wiki.gallery/images/5/51/NessHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Olimar',
                'image_link' => 'https://ssb.wiki.gallery/images/f/fd/OlimarHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Peach',
                'image_link' => 'https://ssb.wiki.gallery/images/0/0c/PeachHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Pikachu',
                'image_link' => 'https://ssb.wiki.gallery/images/c/ca/PikachuHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Pit',
                'image_link' => 'https://ssb.wiki.gallery/images/c/c8/PitHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Pokémon Trainer',
                'image_link' => 'https://ssb.wiki.gallery/images/0/01/Pok%C3%A9monTrainerHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Rob',
                'image_link' => 'https://ssb.wiki.gallery/images/c/cb/ROBHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Samus',
                'image_link' => 'https://ssb.wiki.gallery/images/e/ee/SamusHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Sheik',
                'image_link' => 'https://ssb.wiki.gallery/images/2/2e/SheikHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Snake',
                'image_link' => 'https://ssb.wiki.gallery/images/6/6b/SnakeHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Sonic',
                'image_link' => 'https://ssb.wiki.gallery/images/4/4a/SonicHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Toon Link',
                'image_link' => 'https://ssb.wiki.gallery/images/6/66/ToonLinkHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Wario',
                'image_link' => 'https://ssb.wiki.gallery/images/e/e1/WarioHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Wolf',
                'image_link' => 'https://ssb.wiki.gallery/images/6/63/WolfHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Yoshi',
                'image_link' => 'https://ssb.wiki.gallery/images/a/ab/YoshiHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Zelda',
                    'image_link' => 'https://ssb.wiki.gallery/images/5/59/ZeldaHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::BRAWL,
                'name' => 'Zero Suit Samus',
                'image_link' => 'https://ssb.wiki.gallery/images/8/8e/ZeroSuitSamusHeadSSBB.png'
            ],

            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Bowser',
                'image_link' => 'https://ssb.wiki.gallery/images/0/05/BowserHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Captain Falcon',
                'image_link' => 'https://ssb.wiki.gallery/images/6/61/CaptainFalconHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Diddy Kong',
                'image_link' => 'https://ssb.wiki.gallery/images/1/1c/DiddyKongHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Donkey Kong',
                'image_link' => 'https://ssb.wiki.gallery/images/0/0b/DonkeyKongHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Falco',
                'image_link' => 'https://ssb.wiki.gallery/images/4/4b/FalcoHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Fox',
                'image_link' => 'https://ssb.wiki.gallery/images/1/15/FoxHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Ganondorf',
                'image_link' => 'https://ssb.wiki.gallery/images/8/82/GanondorfHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Ice Climbers',
                'image_link' => 'https://ssb.wiki.gallery/images/e/e4/IceClimbersHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Ike',
                'image_link' => 'https://ssb.wiki.gallery/images/3/32/IkeHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Jigglypuff',
                'image_link' => 'https://ssb.wiki.gallery/images/4/4a/JigglypuffHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'King Dedede',
                'image_link' => 'https://ssb.wiki.gallery/images/6/6b/KingDededeHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Kirby',
                'image_link' => 'https://ssb.wiki.gallery/images/c/ca/KirbyHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Link',
                'image_link' => 'https://ssb.wiki.gallery/images/2/28/LinkHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Lucario',
                'image_link' => 'https://ssb.wiki.gallery/images/e/e3/LucarioHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Lucas',
                'image_link' => 'https://ssb.wiki.gallery/images/e/e6/LucasHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Luigi',
                'image_link' => 'https://ssb.wiki.gallery/images/a/af/LuigiHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Mario',
                'image_link' => 'https://ssb.wiki.gallery/images/0/07/MarioHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Marth',
                'image_link' => 'https://ssb.wiki.gallery/images/5/52/MarthHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Meta Knight',
                'image_link' => 'https://ssb.wiki.gallery/images/f/f1/MetaKnightHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Mr. Game & Watch',
                'image_link' => 'https://ssb.wiki.gallery/images/4/48/MrGame%26WatchHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Ness',
                'image_link' => 'https://ssb.wiki.gallery/images/5/51/NessHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Olimar',
                'image_link' => 'https://ssb.wiki.gallery/images/f/fd/OlimarHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Peach',
                'image_link' => 'https://ssb.wiki.gallery/images/0/0c/PeachHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Pikachu',
                'image_link' => 'https://ssb.wiki.gallery/images/c/ca/PikachuHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Pit',
                'image_link' => 'https://ssb.wiki.gallery/images/c/c8/PitHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Pokémon Trainer',
                'image_link' => 'https://ssb.wiki.gallery/images/0/01/Pok%C3%A9monTrainerHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Rob',
                'image_link' => 'https://ssb.wiki.gallery/images/c/cb/ROBHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Samus',
                'image_link' => 'https://ssb.wiki.gallery/images/e/ee/SamusHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Sheik',
                'image_link' => 'https://ssb.wiki.gallery/images/2/2e/SheikHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Snake',
                'image_link' => 'https://ssb.wiki.gallery/images/6/6b/SnakeHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Sonic',
                'image_link' => 'https://ssb.wiki.gallery/images/4/4a/SonicHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Toon Link',
                'image_link' => 'https://ssb.wiki.gallery/images/6/66/ToonLinkHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Wario',
                'image_link' => 'https://ssb.wiki.gallery/images/e/e1/WarioHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Wolf',
                'image_link' => 'https://ssb.wiki.gallery/images/6/63/WolfHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Yoshi',
                'image_link' => 'https://ssb.wiki.gallery/images/a/ab/YoshiHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Zelda',
                'image_link' => 'https://ssb.wiki.gallery/images/5/59/ZeldaHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_M,
                'name' => 'Zero Suit Samus',
                'image_link' => 'https://ssb.wiki.gallery/images/8/8e/ZeroSuitSamusHeadSSBB.png'
            ],
            [
                'game_id'       => GameEnum::PROJECT_M,
                'name'          => 'Mewtwo',
                'image_link'    => 'https://ssb.wiki.gallery/images/9/9f/MewtwoHeadPM.png'
            ],
            [
                'game_id'       => GameEnum::PROJECT_M,
                'name'          => 'Roy',
                'image_link'    => 'https://ssb.wiki.gallery/images/2/25/RoyHeadPM.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Bowser',
                'image_link' => 'https://ssb.wiki.gallery/images/0/05/BowserHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Captain Falcon',
                'image_link' => 'https://ssb.wiki.gallery/images/6/61/CaptainFalconHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Diddy Kong',
                'image_link' => 'https://ssb.wiki.gallery/images/1/1c/DiddyKongHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Donkey Kong',
                'image_link' => 'https://ssb.wiki.gallery/images/0/0b/DonkeyKongHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Falco',
                'image_link' => 'https://ssb.wiki.gallery/images/4/4b/FalcoHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Fox',
                'image_link' => 'https://ssb.wiki.gallery/images/1/15/FoxHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Ganondorf',
                'image_link' => 'https://ssb.wiki.gallery/images/8/82/GanondorfHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Ice Climbers',
                'image_link' => 'https://ssb.wiki.gallery/images/e/e4/IceClimbersHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Ike',
                'image_link' => 'https://ssb.wiki.gallery/images/3/32/IkeHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Jigglypuff',
                'image_link' => 'https://ssb.wiki.gallery/images/4/4a/JigglypuffHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'King Dedede',
                'image_link' => 'https://ssb.wiki.gallery/images/6/6b/KingDededeHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Kirby',
                'image_link' => 'https://ssb.wiki.gallery/images/c/ca/KirbyHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Link',
                'image_link' => 'https://ssb.wiki.gallery/images/2/28/LinkHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Lucario',
                'image_link' => 'https://ssb.wiki.gallery/images/e/e3/LucarioHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Lucas',
                'image_link' => 'https://ssb.wiki.gallery/images/e/e6/LucasHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Luigi',
                'image_link' => 'https://ssb.wiki.gallery/images/a/af/LuigiHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Mario',
                'image_link' => 'https://ssb.wiki.gallery/images/0/07/MarioHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Marth',
                'image_link' => 'https://ssb.wiki.gallery/images/5/52/MarthHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Meta Knight',
                'image_link' => 'https://ssb.wiki.gallery/images/f/f1/MetaKnightHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Mr. Game & Watch',
                'image_link' => 'https://ssb.wiki.gallery/images/4/48/MrGame%26WatchHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Ness',
                'image_link' => 'https://ssb.wiki.gallery/images/5/51/NessHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Olimar',
                'image_link' => 'https://ssb.wiki.gallery/images/f/fd/OlimarHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Peach',
                'image_link' => 'https://ssb.wiki.gallery/images/0/0c/PeachHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Pikachu',
                'image_link' => 'https://ssb.wiki.gallery/images/c/ca/PikachuHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Pit',
                'image_link' => 'https://ssb.wiki.gallery/images/c/c8/PitHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Pokémon Trainer',
                'image_link' => 'https://ssb.wiki.gallery/images/0/01/Pok%C3%A9monTrainerHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Rob',
                'image_link' => 'https://ssb.wiki.gallery/images/c/cb/ROBHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Samus',
                'image_link' => 'https://ssb.wiki.gallery/images/e/ee/SamusHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Sheik',
                'image_link' => 'https://ssb.wiki.gallery/images/2/2e/SheikHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Snake',
                'image_link' => 'https://ssb.wiki.gallery/images/6/6b/SnakeHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Sonic',
                'image_link' => 'https://ssb.wiki.gallery/images/4/4a/SonicHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Toon Link',
                'image_link' => 'https://ssb.wiki.gallery/images/6/66/ToonLinkHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Wario',
                'image_link' => 'https://ssb.wiki.gallery/images/e/e1/WarioHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Wolf',
                'image_link' => 'https://ssb.wiki.gallery/images/6/63/WolfHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Yoshi',
                'image_link' => 'https://ssb.wiki.gallery/images/a/ab/YoshiHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Zelda',
                'image_link' => 'https://ssb.wiki.gallery/images/5/59/ZeldaHeadSSBB.png'
            ],
            [
                'game_id' => GameEnum::PROJECT_PLUS,
                'name' => 'Zero Suit Samus',
                'image_link' => 'https://ssb.wiki.gallery/images/8/8e/ZeroSuitSamusHeadSSBB.png'
            ],
            [
                'game_id'       => GameEnum::PROJECT_PLUS,
                'name'          => 'Knuckles',
                'image_link'    => 'https://ssb.wiki.gallery/images/4/4b/KnucklesHeadP%2B.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Bayonetta',
                'image_link'    => 'https://ssb.wiki.gallery/images/b/b4/BayonettaHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Bowser Jr',
                'image_link'    => 'https://ssb.wiki.gallery/images/f/fa/BowserJrHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Captain Falcon',
                'image_link'    => 'https://ssb.wiki.gallery/images/4/4d/CaptainFalconHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Charizard',
                'image_link'    => 'https://ssb.wiki.gallery/images/7/7d/CharizardHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Cloud',
                'image_link'    => 'https://ssb.wiki.gallery/images/2/2a/CloudHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Corrin',
                'image_link'    => 'https://ssb.wiki.gallery/images/8/89/CorrinHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Dark Pit',
                'image_link'    => 'https://ssb.wiki.gallery/images/9/9b/DarkPitHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Diddy Kong',
                'image_link'    => 'https://ssb.wiki.gallery/images/a/a3/DiddyKongHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Donkey Kong',
                'image_link'    => 'https://ssb.wiki.gallery/images/b/b2/DonkeyKongHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Dr Mario',
                'image_link'    => 'https://ssb.wiki.gallery/images/5/5d/DrMarioHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Duck Hunt',
                'image_link'    => 'https://ssb.wiki.gallery/images/1/15/DuckHuntHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Falco',
                'image_link'    => 'https://ssb.wiki.gallery/images/e/ee/FalcoHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Female Mii',
                'image_link'    => 'https://ssb.wiki.gallery/images/3/32/FemaleMiiHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Fox',
                'image_link'    => 'https://ssb.wiki.gallery/images/4/40/FoxHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Ganondorf',
                'image_link'    => 'https://ssb.wiki.gallery/images/f/fd/GanondorfHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Greninja',
                'image_link'    => 'https://ssb.wiki.gallery/images/b/b0/GreninjaHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Ike',
                'image_link'    => 'https://ssb.wiki.gallery/images/d/dd/IkeHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Jigglypuff',
                'image_link'    => 'https://ssb.wiki.gallery/images/5/53/JigglypuffHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'King Dedede',
                'image_link'    => 'https://ssb.wiki.gallery/images/b/bf/KingDededeHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Kirby',
                'image_link'    => 'https://ssb.wiki.gallery/images/b/bd/KirbyHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Link',
                'image_link'    => 'https://ssb.wiki.gallery/images/e/ea/LinkHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Little Mac',
                'image_link'    => 'https://ssb.wiki.gallery/images/0/06/LittleMacHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Lucario',
                'image_link'    => 'https://ssb.wiki.gallery/images/4/49/LucarioHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Lucas',
                'image_link'    => 'https://ssb.wiki.gallery/images/c/cf/LucasHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Lucina',
                'image_link'    => 'https://ssb.wiki.gallery/images/e/e2/LucinaHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Luigi',
                'image_link'    => 'https://ssb.wiki.gallery/images/d/d4/LuigiHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Male Mii',
                'image_link'    => 'https://ssb.wiki.gallery/images/3/39/MaleMiiHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Mario',
                'image_link'    => 'https://ssb.wiki.gallery/images/2/2b/MarioHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Marth',
                'image_link'    => 'https://ssb.wiki.gallery/images/4/47/MarthHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Mega Man',
                'image_link'    => 'https://ssb.wiki.gallery/images/0/0d/MegaManHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Meta Knight',
                'image_link'    => 'https://ssb.wiki.gallery/images/6/65/MetaKnightHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Mewtwo',
                'image_link'    => 'https://ssb.wiki.gallery/images/1/12/MewtwoHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Mr Game & Watch',
                'image_link'    => 'https://ssb.wiki.gallery/images/e/ea/MrGame%26WatchHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Ness',
                'image_link'    => 'https://ssb.wiki.gallery/images/c/c6/NessHeadSSB4-U.png'
            ],[
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Olimar',
                'image_link'    => 'https://ssb.wiki.gallery/images/7/74/OlimarHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Pac-Man',
                'image_link'    => 'https://ssb.wiki.gallery/images/b/bb/Pac-ManHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Palutena',
                'image_link'    => 'https://ssb.wiki.gallery/images/a/ad/PalutenaHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Peach',
                'image_link'    => 'https://ssb.wiki.gallery/images/5/50/PeachHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Pikachu',
                'image_link'    => 'https://ssb.wiki.gallery/images/d/d6/PikachuHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Pit',
                'image_link'    => 'https://ssb.wiki.gallery/images/7/7a/PitHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Rob',
                'image_link'    => 'https://ssb.wiki.gallery/images/3/39/ROBHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Robin',
                'image_link'    => 'https://ssb.wiki.gallery/images/e/ef/RobinHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Rosalina & Luma',
                'image_link'    => 'https://ssb.wiki.gallery/images/2/2d/Rosalina%26LumaHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Roy',
                'image_link'    => 'https://ssb.wiki.gallery/images/5/58/RoyHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Ryu',
                'image_link'    => 'https://ssb.wiki.gallery/images/f/f1/RyuHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Samus',
                'image_link'    => 'https://ssb.wiki.gallery/images/b/b9/SamusHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Sheik',
                'image_link'    => 'https://ssb.wiki.gallery/images/6/6f/SheikHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Shulk',
                'image_link'    => 'https://ssb.wiki.gallery/images/9/9d/ShulkHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Sonic',
                'image_link'    => 'https://ssb.wiki.gallery/images/d/de/SonicHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Toon Link',
                'image_link'    => 'https://ssb.wiki.gallery/images/a/a0/ToonLinkHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Villager',
                'image_link'    => 'https://ssb.wiki.gallery/images/5/5d/VillagerHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Wario',
                'image_link'    => 'https://ssb.wiki.gallery/images/e/e6/WarioHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Wii Fit Trainer',
                'image_link'    => 'https://ssb.wiki.gallery/images/8/85/WiiFitTrainerHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Yoshi',
                'image_link'    => 'https://ssb.wiki.gallery/images/c/c2/YoshiHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Zelda',
                'image_link'    => 'https://ssb.wiki.gallery/images/5/50/ZeldaHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::SMASH4,
                'name'          => 'Zero Suit Samus',
                'image_link'    => 'https://ssb.wiki.gallery/images/c/c1/ZeroSuitSamusHeadSSB4-U.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Banjo & Kazooie',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/1/12/Banjo%26KazooieHeadSSBUWebsite.png/120px-Banjo%26KazooieHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Bayonetta',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/2/27/BayonettaHeadSSBUWebsite.png/120px-BayonettaHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Bowser',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/0/0b/BowserHeadSSBUWebsite.png/120px-BowserHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Bowser Jr',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/3/3e/BowserJrHeadSSBUWebsite.png/120px-BowserJrHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Byleth',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/8/86/BylethHeadSSBUWebsite.png/120px-BylethHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Captain Falcon',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/6/6b/CaptainFalconHeadSSBUWebsite.png/120px-CaptainFalconHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Chrom',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/7/70/ChromHeadSSBUWebsite.png/120px-ChromHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Cloud',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/c/cb/CloudHeadSSBUWebsite.png/120px-CloudHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Corrin',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/6/6a/CorrinHeadSSBUWebsite.png/120px-CorrinHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Daisy',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/2/2d/DaisyHeadSSBUWebsite.png/120px-DaisyHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Dark Pit',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/8/8b/DarkPitHeadSSBUWebsite.png/120px-DarkPitHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Dark Samus',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/2/24/DarkSamusHeadSSBUWebsite.png/120px-DarkSamusHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Diddy Kong',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/5/5d/DiddyKongHeadSSBUWebsite.png/120px-DiddyKongHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Donkey Kong',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/2/21/DonkeyKongHeadSSBUWebsite.png/120px-DonkeyKongHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Daisy',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/2/2d/DaisyHeadSSBUWebsite.png/120px-DaisyHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Dr Mario',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/c/c8/DrMarioHeadSSBUWebsite.png/120px-DrMarioHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Duck Hunt',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/3/38/DuckHuntHeadSSBUWebsite.png/120px-DuckHuntHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Falco',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/6/6e/FalcoHeadSSBUWebsite.png/120px-FalcoHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Fox',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/c/c9/FoxHeadSSBUWebsite.png/120px-FoxHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Ganondorf',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/b/b6/GanondorfHeadSSBUWebsite.png/120px-GanondorfHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Greninja',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/7/79/GreninjaHeadSSBUWebsite.png/120px-GreninjaHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Hero',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/1/1e/HeroHeadSSBUWebsite.png/120px-HeroHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Ice Climbers',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/0/0c/IceClimbersHeadSSBUWebsite.png/120px-IceClimbersHeadSSBUWebsite.png'
            ],

            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Ike',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/2/25/IkeHeadSSBUWebsite.png/120px-IkeHeadSSBUWebsite.png'
            ],

            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Incineroar',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/e/e3/IncineroarHeadSSBUWebsite.png/120px-IncineroarHeadSSBUWebsite.png'
            ],

            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Inkling',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/0/04/InklingHeadSSBUWebsite.png/120px-InklingHeadSSBUWebsite.png'
            ],

            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Isabelle',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/2/2e/IsabelleHeadSSBUWebsite.png/120px-IsabelleHeadSSBUWebsite.png'
            ],

            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Jigglypuff',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/9/90/JigglypuffHeadSSBUWebsite.png/120px-JigglypuffHeadSSBUWebsite.png'
            ],

            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Joker',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/6/63/JokerHeadSSBUWebsite.png/120px-JokerHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Kazuya',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/8/85/KazuyaHeadSSBUWebsite.png/120px-KazuyaHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Ken',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/e/ef/KenHeadSSBUWebsite.png/120px-KenHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'King Dedede',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/f/fe/KingDededeHeadSSBUWebsite.png/120px-KingDededeHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'King K Rool',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/3/35/KingKRoolHeadSSBUWebsite.png/120px-KingKRoolHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Kirby',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/1/15/KirbyHeadSSBUWebsite.png/120px-KirbyHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Link',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/2/2b/LinkHeadSSBUWebsite.png/120px-LinkHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Little Mac',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/8/87/LittleMacHeadSSBUWebsite.png/120px-LittleMacHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Lucario',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/2/20/LucarioHeadSSBUWebsite.png/120px-LucarioHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Lucas',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/3/31/LucasHeadSSBUWebsite.png/120px-LucasHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Lucina',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/d/d8/LucinaHeadSSBUWebsite.png/120px-LucinaHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Luigi',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/9/9d/LuigiHeadSSBUWebsite.png/120px-LuigiHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Mario',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/9/9e/MarioHeadSSBUWebsite.png/120px-MarioHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Marth',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/a/ae/MarthHeadSSBUWebsite.png/120px-MarthHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Mega Man',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/2/26/MegaManHeadSSBUWebsite.png/120px-MegaManHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Meta Knight',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/3/3d/MetaKnightHeadSSBUWebsite.png/120px-MetaKnightHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Mewtwo',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/7/7e/MewtwoHeadSSBUWebsite.png/120px-MewtwoHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Mii Fighter',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/c/cf/MiiFighterHeadSSBUWebsite.png/120px-MiiFighterHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Min Min',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/f/fc/MinMinHeadSSBUWebsite.png/120px-MinMinHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Mr Game and Watch',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/1/15/MrGame%26WatchHeadSSBUWebsite.png/120px-MrGame%26WatchHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Ness',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/d/d5/NessHeadSSBUWebsite.png/120px-NessHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Olimar',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/9/97/OlimarHeadSSBUWebsite.png/120px-OlimarHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Pac-Man',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/3/3d/Pac-ManHeadSSBUWebsite.png/120px-Pac-ManHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Palutena',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/d/d7/PalutenaHeadSSBUWebsite.png/120px-PalutenaHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Peach',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/1/14/PeachHeadSSBUWebsite.png/120px-PeachHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Pichu',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/5/50/PichuHeadSSBUWebsite.png/120px-PichuHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Pikachu',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/5/52/PikachuHeadSSBUWebsite.png/120px-PikachuHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Piranha Plant',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/c/cf/PiranhaPlantHeadSSBUWebsite.png/120px-PiranhaPlantHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Pit',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/d/d7/PitHeadSSBUWebsite.png/120px-PitHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Pokémon Trainer',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/2/2c/Pok%C3%A9monTrainerHeadSSBUWebsite.png/120px-Pok%C3%A9monTrainerHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Pyra Mythra',
                'image_link'    => 'https://ssb.wiki.gallery/images/f/fc/PyraMythraHeadSSBU.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Richter',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/a/ab/RichterHeadSSBUWebsite.png/120px-RichterHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Ridley',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/7/7c/RidleyHeadSSBUWebsite.png/120px-RidleyHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Rob',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/b/be/ROBHeadSSBUWebsite.png/120px-ROBHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Robin',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/4/43/RobinHeadSSBUWebsite.png/120px-RobinHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Rosalina & Luma',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/6/63/RosalinaHeadSSBUWebsite.png/120px-RosalinaHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Roy',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/2/22/RoyHeadSSBUWebsite.png/120px-RoyHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Ryu',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/2/20/RyuHeadSSBUWebsite.png/120px-RyuHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Samus',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/d/d0/SamusHeadSSBUWebsite.png/120px-SamusHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Sephiroth',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/6/64/SephirothHeadSSBUWebsite.png/120px-SephirothHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Sheik',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/1/1e/SheikHeadSSBUWebsite.png/120px-SheikHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Shulk',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/b/bf/ShulkHeadSSBUWebsite.png/120px-ShulkHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Simon',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/5/52/SimonHeadSSBUWebsite.png/120px-SimonHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Snake',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/9/9f/SnakeHeadSSBUWebsite.png/120px-SnakeHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Sonic',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/b/b7/SonicHeadSSBUWebsite.png/120px-SonicHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Sora',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/6/61/SoraHeadSSBUWebsite.png/120px-SoraHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Steve',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/4/4f/SteveHeadSSBUWebsite.png/120px-SteveHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Terry',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/2/2e/TerryHeadSSBUWebsite.png/120px-TerryHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Toon Link',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/b/bf/ToonLinkHeadSSBUWebsite.png/120px-ToonLinkHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Villager',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/f/f9/VillagerHeadSSBUWebsite.png/120px-VillagerHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Wario',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/7/7f/WarioHeadSSBUWebsite.png/120px-WarioHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Wii Fit Trainer',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/f/fc/WiiFitTrainerHeadSSBUWebsite.png/120px-WiiFitTrainerHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Wolf',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/0/06/WolfHeadSSBUWebsite.png/120px-WolfHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Yoshi',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/9/93/YoshiHeadSSBUWebsite.png/120px-YoshiHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Young Link',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/c/c0/YoungLinkHeadSSBUWebsite.png/120px-YoungLinkHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Zelda',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/c/c8/ZeldaHeadSSBUWebsite.png/120px-ZeldaHeadSSBUWebsite.png'
            ],
            [
                'game_id'       => GameEnum::ULTIMATE,
                'name'          => 'Zero Suit Samus',
                'image_link'    => 'https://ssb.wiki.gallery/images/thumb/5/5a/ZeroSuitSamusHeadSSBUWebsite.png/120px-ZeroSuitSamusHeadSSBUWebsite.png'
            ],
        ];

        DB::table('characters')->insert($characters);

    }
}
