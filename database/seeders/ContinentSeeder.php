<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContinentSeeder extends Seeder
{
    public function run()
    {
        DB::table('continents')->delete();

        $continents = [
            ['code' => 'AF', 'name' => 'Africa'],
            ['code' => 'AN', 'name' => 'Antarctica'],
            ['code' => 'AS', 'name' => 'Asia'],
            ['code' => 'EU', 'name' => 'Europe'],
            ['code' => 'NA', 'name' => 'North America'],
            ['code' => 'OC', 'name' => 'Oceania'],
            ['code' => 'SA', 'name' => 'South America'],
        ];

        DB::table('continents')->insert($continents);
    }
}
