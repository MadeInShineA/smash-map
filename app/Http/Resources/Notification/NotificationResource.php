<?php

namespace App\Http\Resources\Notification;

use App\Enums\GameEnum;
use App\Enums\NotificationTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'notificationType'      => NotificationTypeEnum::LABELS[$this->type],
            'gameName'              => GameEnum::GAMES[$this->event->game_id],
            'message'               => $this->message,
            'imageUrl'              => $this->image_url,
        ];
    }
}
{

}
