<?php

namespace App\Http\Resources\Country;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class CountryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name'      => $this->name,
            'timezone'  => $this->timezone
        ];
    }
}
