<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Image\ImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocalStorageUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'profilePicture'    => new ImageResource($this->profile_picture),
            'settings'          => [
                'hasDistanceNotifications'      => boolval($this->has_distance_notifications),
                'distanceNotificationsRadius'   => $this->distance_notifications_radius,
                'address'                       => [
                    'lat'      => $this->address->latitude,
                    'lng'     => $this->address->longitude,
                ],
            ],
        ];
    }
}
