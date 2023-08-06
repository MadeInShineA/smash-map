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
            'latitude'      => $this->latitude,
            'longitude'     => $this->longitude,
            'events'        => MapEventResource::collection($this->events),
            'users'         => MapUserResource::collection($this->users),
            'color'         => $this->color
        ];
    }
}