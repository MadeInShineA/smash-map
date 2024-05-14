<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Image\ImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MapUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'username'          => $this->username,
            'profilePicture'    => new ImageResource($this->profile_picture),
            'games'             => $this->games_characters_array,
            'isModder'          => $this->is_modder,
            'isOnMap'           => $this->is_on_map,
        ];
    }
}
