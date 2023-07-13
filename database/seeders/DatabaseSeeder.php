<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(GameSeeder::class);
        $this->call(CharacterSeeder::class);
        $this->call(ContinentSeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(UserSeeder::class);
    }
}
