<?php

namespace App\Http\Resources\Event;

use App\Http\Resources\Game\GameResource;
use App\Http\Resources\Image\ImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MapEventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                        => $this->id,
            'title'                     => $this->name,
            'start'                     => $this->start_date_time,
            'end'                       => $this->end_date_time,
            'game'                      => new GameResource($this->game),
            'timezone_start_date_time'  => $this->timezone_start_date_time,
            'timezone_end_date_time'    => $this->timezone_end_date_time,
            'timezone'                  => $this->timezone_label,
            'attendees'                 => $this->attendees,
            'link'                      => $this->link,
            'image'                     => $this->image ? new ImageResource($this->image) : null,
            'user_subscribed'           => $this->user_subscribed($request),

        ];
    }

}
