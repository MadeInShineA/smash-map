<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Notification\NotificationResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function get_notifications(Request $request, User $user): JsonResponse
    {
        $notifications = $user->notifications()->orderBy('created_at', 'desc');
        $notifications->update(['seen' => true]);
        return $this->sendResponse(NotificationResource::collection($notifications->get()), 'Notifications retrieved with success');
    }

    public function get_notifications_count(Request $request, User $user): JsonResponse
    {
        $notifications_count = $user->notifications()->where('seen', false)->count();
        return $this->sendResponse([$notifications_count], 'Notifications count retrieved with success');
    }
}
