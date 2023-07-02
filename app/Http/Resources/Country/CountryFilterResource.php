<?php

namespace App\Http\Resources\Country;

use App\Http\Resources\Image\ImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryFilterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name'  => $this->name,
            'value' => $this->hex,
            'image' => new ImageResource($this->image)
        ];
    }

}
