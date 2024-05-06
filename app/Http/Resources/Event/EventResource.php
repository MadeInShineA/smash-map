<?php

namespace App\Http\Resources\Event;

use App\Http\Resources\Address\AddressResource;
use App\Http\Resources\Game\GameResource;
use App\Http\Resources\Image\ImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                        => $this->id,
            'game'                      => new GameResource($this->game),
            'address'                   => new AddressResource($this->address),
            'image'                     => $this->image ? new ImageResource($this->image) : null,
            'is_online'                 => $this->is_online,
            'name'                      => $this->name,
            'timezone_start_date_time'  => $this->timezone_start_date_time,
            'timezone_end_date_time'    => $this->timezone_end_date_time,
            'timezone'                  => $this->timezone_label,
            'attendees'                 => $this->attendees,
            'link'                      => $this->link,
            'user_subscribed'           => $this->user_subscribed($request),
        ];
    }

}
