<?php

namespace App\Http\Resources\Event;

use App\Http\Resources\Address\AddressResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'address'           => new  AddressResource($this->address),
            'is_online'         => $this->is_online,
            'name'              => $this->name,
            'video_game'        => $this->video_game,
            'timezone'          => $this->timezone,
            'start_date_time'   => $this->start_date_time,
            'end_date_time'     => $this->end_date_time,
            'link'              => $this->link
        ];
    }

}
