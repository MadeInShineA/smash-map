<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Address\SettingsAddressResource;
use App\Http\Resources\Image\ImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'profilePicture'    => new ImageResource($this->profile_picture),
            'username'          => $this->username,
            'games'             => $this->games_characters_array,
            'isModder'          => $this->is_modder,
            'description'       => $this->description,
            'connectCode'       => $this->connect_code,
            'discord'           => $this->discord,
            'twitter'           => $this->twitter,
        ];
    }

}
