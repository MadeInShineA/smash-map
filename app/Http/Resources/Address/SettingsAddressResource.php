<?php

namespace App\Http\Resources\Address;

use App\Http\Resources\Country\CountryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class SettingsAddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'countryCode'   => $this->country->code,
            'name'          => $this->name,
            'latitude'      => $this->latitude,
            'longitude'     => $this->longitude
        ];
    }
}
