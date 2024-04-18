<?php

namespace App\Http\Resources\Address;

use App\Http\Resources\Event\MapEventResource;
use App\Http\Resources\User\MapUserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class MapAddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'position'              => ['lat' => $this->latitude, 'lng' => $this->longitude],
            'icon'                  => $this->icon,
            'events'                => MapEventResource::collection($this->events),
            'users'                 => MapUserResource::collection($this->map_users),
        ];
    }
}
