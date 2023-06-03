<?php

namespace App\Http\Resources\Image;

use App\Http\Resources\Address\AddressResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class ImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'type'      => $this->type,
            'url'       => $this->url
        ];
    }
}
