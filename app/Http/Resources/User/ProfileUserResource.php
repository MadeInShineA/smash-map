<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Address\SettingsAddressResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'connectCode'   => $this->connect_code,
            'discord'       => $this->discord,
            'twitter'       => $this->twitter,
            'description'   => $this->description,
//            'profileImage'  => $this->

        ];
    }

}
