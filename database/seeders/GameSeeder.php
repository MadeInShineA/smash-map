<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameSeeder extends Seeder
{
    public function run()
    {
        DB::table('games')->delete();

        $games = [
            ['id' => 4,     'name' => '64',         'color' => '#FAC41A'],
            ['id' => 1,     'name' => 'Melee',      'color' => '#A30010'],
            ['id' => 5,     'name' => 'Brawl',      'color' => '#660d02'],
            ['id' => 2,     'name' => 'Project M',  'color' => '#3B448B'],
            ['id' => 33602, 'name' => 'Project +',  'color' => '#6FD19C'],
            ['id' => 3,     'name' => '3DS/WiiU',   'color' => '#AFC1EE'],
            ['id' =>1386,   'name' => 'Ultimate',   'color' => '#F18A41']
        ];

        DB::table('games')->insert($games);
    }
}
