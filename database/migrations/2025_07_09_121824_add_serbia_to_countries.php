<?php

use App\Enums\ImageTypeEnum;
use App\Models\Country;
use App\Models\Image;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $country = Country::create([
            'continent_id' => 4, // Europe
            'code' => 'RS',
            'name' => 'Serbia',
            'timezone' => 'Europe/Belgrade',
            'has_image' => true,
        ]);

        Image::create([
            'parentable_type' => Country::class,
            'parentable_id' => $country->id,
            'type' => ImageTypeEnum::ICON,
            'extension' => 'png',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $country = Country::where('code', 'RS')->first();

        if ($country) {
            Image::where('parentable_type', Country::class)
                ->where('parentable_id', $country->id)
                ->where('type', ImageTypeEnum::ICON)
                ->delete();

            $country->delete();
        }
    }
};
