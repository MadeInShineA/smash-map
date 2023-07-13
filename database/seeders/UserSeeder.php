<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('users')->delete();

        DB::table('users')->insert([
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('1234'),
            'created_at' => now(),
            'updated_at' => now(),
            'address_id' => null,
            'discord' => 'ssbm_misa',
            'twitter' => 'MadeInShineA',
            'description' => 'Smash Map Developer',
            'color' => '#00FFFF',
            'main_character_id' => 18,
            'main_game_id' => 1,
            'is_admin' => true,
            'is_subscribed' => true,
        ]);
    }
}
