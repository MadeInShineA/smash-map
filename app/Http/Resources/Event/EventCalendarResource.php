<?php

namespace App\Http\Resources\Event;

use App\Http\Resources\Address\AddressResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventCalendarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'title'             => $this->name,
            'time'              =>'',
            'color'             =>'',
            'description'       =>'',
        ];
    }

}
