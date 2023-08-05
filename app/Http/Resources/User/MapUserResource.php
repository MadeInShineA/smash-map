<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MapUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
           'username'   => $this->username,
        ];
    }
}
