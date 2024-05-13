<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Notification\NotificationResource;
use App\Models\User;
use Error;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function get_notifications(Request $request, User $user): JsonResponse
    {
        try{
            $notifications = $user->notifications()->orderBy('created_at', 'desc');
            $notifications->update(['seen' => true]);
            $last_notification_id = $request->input('lastNotificationId');
            if ($last_notification_id === null){
                $notifications = $notifications->get()->slice(0, 10);
            }else{
                $notifications = $notifications->where('id', '<', $last_notification_id)->get()->slice(0, 10);
            }
//            $notifications = $notifications->where('id', '<', $last_notification_id)->get();
            $data = ['notifications' => NotificationResource::collection($notifications->all()), 'lastNotificationId' => $notifications->last()? $notifications->last()->id : $last_notification_id];
            return $this->sendResponse($data, 'Notifications retrieved with success');
        }catch (Error $error){
            return $this->sendError($error, ['An error occurred while retrieving the notifications E 015'], 500);
        }

    }

    public function get_notifications_count(Request $request, User $user): JsonResponse
    {
        $notifications_count = $user->notifications()->where('seen', false)->count();
        return $this->sendResponse([$notifications_count], 'Notifications count retrieved with success');
    }
}
