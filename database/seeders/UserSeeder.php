<?php

namespace Database\Seeders;

use App\Enums\ImageTypeEnum;
use App\Models\Image;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $user = User::create([
            'uuid'          => Str::uuid()->toString(),
            'username'      => "admin",
            'email'         => "admin@gmail.com",
            'password'      => Hash::make("1234"),
            'is_modder'      => false,
        ]);

        $profile_picture = file_get_contents('https://ui-avatars.com/api/?name=' . $user->username . '&rounded=true&length=1&background=random');
        Image::Create(['parentable_type' =>User::class, 'parentable_id' =>$user->id, 'type' =>ImageTypeEnum::USER_PROFILE, 'extension' => 'png']);

        $user_directory_path = '/users-images/' . $user->uuid;
        Storage::put($user_directory_path . '/' . ImageTypeEnum::USER_PROFILE . '.png', $profile_picture);
    }
}
