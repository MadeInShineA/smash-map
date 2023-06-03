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
//            'time'              => $this->calendar_time,
            'start'             => $this->start_date_time,
            'end'               => $this->end_date_time,
            'colorScheme'       => $this->color_scheme,
            'location'      => $this->address->name,
            'description'       =>'',
        ];
    }

}
