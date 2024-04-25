<?php

namespace App\Http\Controllers;

use App\Enums\ImageTypeEnum;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SettingsUpdateRequest;
use App\Http\Resources\Character\CharacterResource;
use App\Http\Resources\Game\GameResource;
use App\Http\Resources\User\LocalStorageUser;
use App\Http\Resources\User\SettingsUserResource;
use App\Models\Address;
use App\Models\Country;
use App\Models\Event;
use App\Models\Image;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function register(RegisterRequest $request):JsonResponse
    {
        try{
            $country = Country::where('code',$request->input('countryCode'))->first();
            $address = Address::firstOrCreate([
                'latitude' =>$request->input('latitude'),
                'longitude' =>$request->input('longitude')],
                ['name'=>$request->input('addressName'),
                    'country_id' =>$country->id
                ]);
            $user = User::create([
                'uuid'          => Str::uuid()->toString(),
                'username'      => $request->input('username'),
                'email'         => $request->input('email'),
                'password'      => Hash::make($request->input('password')),
                'address_id'    => $address->id,
                'is_modder'      => $request->input('isModder'),
                'is_on_map'      => $request->input('isOnMap'),
            ]);

            $user->games()->attach($request->input('games'));
            $user->characters()->attach($request->input('characters'));

            $profile_picture = file_get_contents('https://ui-avatars.com/api/?name=' . $user->username . '&rounded=true&length=1&background=random');
            Image::Create(['parentable_type' =>User::class, 'parentable_id' =>$user->id, 'type' =>ImageTypeEnum::USER_PROFILE]);

            $user_directory_path = '/users-images/' . $user->uuid;
            Storage::put($user_directory_path . '/' . ImageTypeEnum::USER_PROFILE . '.png', $profile_picture);


            return $this->sendResponse(['user' => new LocalStorageUser($user), 'token' => $user->createToken('API Token')->plainTextToken], 'You are registered and connected!');
        }catch (\Error $error){
            return $this->sendError('An error occurred while registering E 001', [$error], 500);
        }
    }
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            if (!Auth::attempt($request->json()->all())) {
                return $this->sendError('Unauthorized  E 002',
                    [
                        'login' => ['Username or password are incorrect']
                    ],
                    401);
            }
            return $this->sendResponse(['user' => new LocalStorageUser(auth::user()), 'token' => auth::user()->createToken('API Token')->plainTextToken], 'You are connected');
        }catch (\Error $error) {
            return $this->sendError($error, ['login' => ['An error occurred while logging in E 003']], 500);
        }
    }

    public function logout(Request $request):JsonResponse
    {
        try {
            if ($request->user()) {
                $request->user()->currentAccessToken()->delete();
            }
            return $this->sendResponse([], 'You are disconnected');
        } catch (\Error $error) {
            return $this->sendError('An error occurred while logging out E 007', [$error], 500);
        }
    }

    public function forgot_password(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );
            return $status === Password::RESET_LINK_SENT
                ? $this->sendResponse([], __($status))
                : $this->sendError( __($status),[], 500);
        } catch (\Error $error) {
            return $this->sendError('An error occurred while sending the email E 004', [$error], 500);
        }
    }

    public function reset_password(ResetPasswordRequest $request):JsonResponse
    {
        try{
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function (User $user, string $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->setRememberToken(Str::random(60));

                    $user->save();

                    event(new PasswordReset($user));
                }
            );

            return $status === Password::PASSWORD_RESET
                ? $this->sendResponse([], __($status))
                : $this->sendError(__($status), [], 500);
        }catch (\Error $error) {
            return $this->sendError('An error occurred while sending the email E 005', [$error], 500);
        }

    }

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
        try{
            if ($user->id != $request->user('sanctum')->id){
                return $this->sendError('You are not authorized to update this settings', [], 401);
            }

            $country = Country::where('code',$request->input('address.countryCode'))->first();
            $address = Address::firstOrCreate([
                'latitude' =>$request->input('address.latitude'),
                'longitude' =>$request->input('address.longitude')],
                ['name'=>$request->input('address.name'),
                    'country_id' =>$country->id
                ]);
            $old_address = $user->address;
            if($old_address->id != $address->id && $old_address->users->count() == 1 && $old_address->events->count() == 0){
                $user->update(['address_id' => $address->id]);
                $old_address->delete();
            }

            $distance_notifications_radius = $request->input('distanceNotificationsRadius');
            if(!$distance_notifications_radius){
                $distance_notifications_radius = $user->distance_notifications_radius;
            }

            $attendees_notifications_thresholds = $request->input('attendeesNotificationsThresholds');
            sort($attendees_notifications_thresholds);
            if(!$attendees_notifications_thresholds){
                $attendees_notifications_thresholds = $user->attendees_notifications_thresholds;
            }

            $time_notifications_thresholds = $request->input('timeNotificationsThresholds');
            sort($time_notifications_thresholds);
            $time_notifications_thresholds = array_reverse($time_notifications_thresholds);
            if(!$time_notifications_thresholds){
                $time_notifications_thresholds = $user->time_notifications_thresholds;
            }

            $old_username = $user->username;

            if($request->input('password') != null) {
                $user->update([
                    'username' => $request->input('username'),
                    'email' => $request->input('email'),
                    'password' => Hash::make($request->input('password')),
                    'address_id' => $address->id,
                    'is_modder' => $request->input('isModder'),
                    'is_on_map' => $request->input('isOnMap'),
                    'distance_notifications' => in_array('distanceNotifications', $request->input('notifications')),
                    'distance_notifications_radius' => $distance_notifications_radius,
                    'attendees_notifications' => in_array('attendeesNotifications', $request->input('notifications')),
                    'attendees_notifications_thresholds' => $attendees_notifications_thresholds,
                    'time_notifications' => in_array('timeNotifications', $request->input('notifications')),
                    'time_notifications_thresholds' => $time_notifications_thresholds,
                ]);
            }else{
                $user->update([
                    'username' => $request->input('username'),
                    'email' => $request->input('email'),
                    'address_id' => $address->id,
                    'is_modder' => $request->input('isModder'),
                    'is_on_map' => $request->input('isOnMap'),
                    'distance_notifications' => in_array('distanceNotifications', $request->input('notifications')),
                    'distance_notifications_radius' => $distance_notifications_radius,
                    'attendees_notifications' => in_array('attendeesNotifications', $request->input('notifications')),
                    'attendees_notifications_thresholds' => $attendees_notifications_thresholds,
                    'time_notifications' => in_array('timeNotifications', $request->input('notifications')),
                    'time_notifications_thresholds' => $time_notifications_thresholds,
                ]);
            }

            $user->games()->sync($request->input('games'));
            $user->characters()->sync($request->input('characters'));

            if ($user->has_default_profile_picture && $old_username[0] != $user->username[0]){
                $profile_picture = file_get_contents('https://ui-avatars.com/api/?name=' . $user->username . '&rounded=true&length=1&background=random');
                $user_directory_path = '/users-images/' . $user->uuid;
                Storage::put($user_directory_path . '/' . ImageTypeEnum::USER_PROFILE . '.png', $profile_picture);
            }


            return $this->sendResponse([], 'Settings updated with success');

        }catch (\Error $error){
            return $this->sendError('An error occurred while retrieving the user E 012', [$error], 500);
        }


    }

    public function is_authenticated(Request $request): JsonResponse
    {
        if(Auth::check()){
            return $this->sendResponse([], 'You are authenticated');
        }
        return $this->sendError('You are not authenticated', [], 401);
    }

    public function event_subscribe(Request $request, Event $event): JsonResponse
    {
        $user =request()->user();
        $user->subscribed_events()->attach($event);
        return $this->sendResponse([], 'Event followed with success');
    }

    public function event_unsubscribe(Request $request, Event $event): JsonResponse
    {
        $user = Auth::user();
        $user->subscribed_events()->detach($event);
        return $this->sendResponse([], 'Event unfollowed with success');
    }

    public function get_notifications_count(Request $request, User $user): JsonResponse
    {
        $notifications_count = $user->notifications()->where('seen', false)->count();
        return $this->sendResponse([$notifications_count], 'Notifications count retrieved with success');
    }
}
