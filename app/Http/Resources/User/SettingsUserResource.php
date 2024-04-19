<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Address\SettingsAddressResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingsUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'email'         => $this->email,
            'username'      => $this->username,
            'games'         => $this->games->pluck('id'),
            'characters'    => $this->characters->pluck('id'),
            'isModder'      => boolval($this->is_modder),
            'address'       => new SettingsAddressResource($this->address),
            'isOnMap'       => boolval($this->is_on_map),
            'notifications' => $this->notification_settings()
        ];
    }

}
