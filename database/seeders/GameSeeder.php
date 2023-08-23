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
            [
                'id'    => 4,
                'name'  => '64',
                'slug'  => 'smash-64',
                'color' => '#FAC41A'
            ],
            [
                'id'    => 1,
                'name'  => 'Melee',
                'slug'  => 'melee',
                'color' => '#A30010'],
            [
                'id'    => 5,
                'name'  => 'Brawl',
                'slug'  => 'brawl',
                'color' => '#660d02'
            ],
            [
                'id'    => 2,
                'name'  => 'Project M',
                'slug'  => 'project-m',
                'color' => '#3B448B'
            ],
            [
                'id'    => 33602,
                'name'  => 'Project +',
                'slug'  => 'project-plus',
                'color' => '#6FD19C'
            ],
            [
                'id'    => 3,
                'name'  => '3DS & WiiU',
                'slug'  => 'smash-4',
                'color' => '#AFC1EE'
            ],
            [
                'id'    =>1386,
                'name'  => 'Ultimate',
                'slug'  => 'ultimate',
                'color' => '#F18A41'
            ]
        ];

        DB::table('games')->insert($games);
    }
}
