<?php

namespace App\Http\Resources\Address;

use App\Http\Resources\Country\CountryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class AddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'country'       => new CountryResource($this->country),
            'name'          => $this->name,
            'latitude'      => $this->latitude,
            'longitude'     => $this->longitude
        ];
    }
}
