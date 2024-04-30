<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocalStorageUser extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'profilePicture'    => $this->profile_picture,
            'settings'        => [
                'hasDistanceNotifications' => $this->has_distance_notifications,
                'distanceNotificationsRadius' => $this->distance_notifications_radius
            ],
        ];
    }
}
