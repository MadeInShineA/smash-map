<?php

namespace App\Http\Controllers;

use App\Enums\ImageTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\SettingsDistanceNotificationsRadiusUpdateRequest;
use App\Http\Requests\SettingsUpdateRequest;
use App\Http\Resources\User\SettingsUserResource;
use App\Models\Address;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function get_settings(Request $request, User $user): JsonResponse
    {
        try{
            if ($user->id != $request->user('sanctum')->id){
                return $this->sendError('You are not authorized to access this page', [], 401);
            }

            $settings = new SettingsUserResource($user);

            return $this->sendResponse($settings, 'User retrieved with success');
        }catch (\Error $error){
            return $this->sendError('An error occurred while retrieving the user E 011', [$error], 500);
        }
    }

    public function update_settings(SettingsUpdateRequest $request, User $user):JsonResponse
    {
        try {
//            if ($user->id != $request->user('sanctum')->id) {
//                return $this->sendError('You are not authorized to update this settings', [], 401);
//            }

            $country = Country::where('code', $request->input('address.countryCode'))->first();
            $address = Address::firstOrCreate([
                'latitude' => $request->input('address.latitude'),
                'longitude' => $request->input('address.longitude')],
                ['name' => $request->input('address.name'),
                    'country_id' => $country->id
                ]);
            $old_address = $user->address;
            if ($old_address->id != $address->id && $old_address->users->count() == 1 && $old_address->events->count() == 0) {
                $user->update(['address_id' => $address->id]);
                $old_address->delete();
            }

            $distance_notifications_radius = $request->input('distanceNotificationsRadius');
            if (!$distance_notifications_radius) {
                $distance_notifications_radius = $user->distance_notifications_radius;
            }

            $attendees_notifications_thresholds = $request->input('attendeesNotificationsThresholds');
            if (!$attendees_notifications_thresholds) {
                $attendees_notifications_thresholds = $user->attendees_notifications_thresholds;
            }else{
                sort($attendees_notifications_thresholds);
            }


            $time_notifications_thresholds = $request->input('timeNotificationsThresholds');
            if (!$time_notifications_thresholds) {
                $time_notifications_thresholds = $user->time_notifications_thresholds;
            }else{
                sort($time_notifications_thresholds);
                $time_notifications_thresholds = array_reverse($time_notifications_thresholds);
            }



            $old_username = $user->username;

            if ($request->input('password') != null) {
                $user->update([
                    'username' => $request->input('username'),
                    'email' => $request->input('email'),
                    'password' => Hash::make($request->input('password')),
                    'address_id' => $address->id,
                    'is_modder' => $request->input('isModder'),
                    'is_on_map' => $request->input('isOnMap'),
                    'has_distance_notifications' => in_array('hasDistanceNotifications', $request->input('notifications')),
                    'distance_notifications_radius' => $distance_notifications_radius,
                    'has_attendees_notifications' => in_array('hasAttendeesNotifications', $request->input('notifications')),
                    'attendees_notifications_thresholds' => $attendees_notifications_thresholds,
                    'has_time_notifications' => in_array('hasTimeNotifications', $request->input('notifications')),
                    'time_notifications_thresholds' => $time_notifications_thresholds,
                ]);
            } else {
                $user->update([
                    'username' => $request->input('username'),
                    'email' => $request->input('email'),
                    'address_id' => $address->id,
                    'is_modder' => $request->input('isModder'),
                    'is_on_map' => $request->input('isOnMap'),
                    'has_distance_notifications' => in_array('hasDistanceNotifications', $request->input('notifications')),
                    'distance_notifications_radius' => $distance_notifications_radius,
                    'has_attendees_notifications' => in_array('hasAttendeesNotifications', $request->input('notifications')),
                    'attendees_notifications_thresholds' => $attendees_notifications_thresholds,
                    'has_time_notifications' => in_array('hasTimeNotifications', $request->input('notifications')),
                    'time_notifications_thresholds' => $time_notifications_thresholds,
                ]);
            }

            $user->games()->sync($request->input('games'));
            $user->characters()->sync($request->input('characters'));

            if ($user->has_default_profile_picture && $old_username[0] != $user->username[0]) {
                $profile_picture = file_get_contents('https://ui-avatars.com/api/?name=' . $user->username . '&rounded=true&length=1&background=random');
                $user_directory_path = '/users-images/' . $user->uuid;
                Storage::put($user_directory_path . '/' . ImageTypeEnum::USER_PROFILE . '.png', $profile_picture);
            }


            return $this->sendResponse([], 'Settings updated with success');

        } catch (\Error $error) {
            return $this->sendError('An error occurred updating the settings E 012', [$error], 500);
        }
    }

    public function update_distance_notifications_radius(SettingsDistanceNotificationsRadiusUpdateRequest $request, User $user): JsonResponse
    {
        try {
            if ($user->id != $request->user('sanctum')->id) {
                return $this->sendError('You are not authorized to update this settings', [], 401);
            }

            $user->update(['distance_notifications_radius' => $request->input('distanceNotificationsRadius')]);

            return $this->sendResponse([], 'Distance notifications radius updated with success');

        } catch (\Error $error) {
            return $this->sendError('An error occurred while updating the distance notifications radius E 013', [$error], 500);
        }
    }
}
