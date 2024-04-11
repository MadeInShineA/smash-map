<?php

namespace App\Http\Controllers;

use App\Enums\ImageTypeEnum;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
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
            ]);

            $user->games()->attach($request->input('games'));
            $user->characters()->attach($request->input('characters'));

            $profile_picture = file_get_contents('https://ui-avatars.com/api/?name=' . $user->username . '&rounded=true&length=1&background=random');
            Image::Create(['parentable_type' =>User::class, 'parentable_id' =>$user->id, 'type' =>ImageTypeEnum::USER_PROFILE]);

            $user_directory_path = '/users-images/' . $user->uuid;
            Storage::put($user_directory_path . '/' . ImageTypeEnum::USER_PROFILE . '.png', $profile_picture);


            return $this->sendResponse(['user' => $user, 'token' => $user->createToken('API Token')->plainTextToken], 'You are registered and connected!');
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
            return $this->sendResponse(['user' => auth::user(), 'token' => auth::user()->createToken('API Token')->plainTextToken], 'You are connected');
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
            $settings = [
                'email' => $user->email,
                'username' => $user->username,
                'isModder' => boolval($user->is_modder),
                'games' => $user->games,
                'characters' => $user->characters,
                'address' => $user->address,
            ];
            return $this->sendResponse($settings, 'User retrieved with success');
        }catch (\Error $error){
            return $this->sendError('An error occurred while retrieving the user E 011', [$error], 500);
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
        return $this->sendResponse([], 'Event subscribed with success');
    }

    public function event_unsubscribe(Request $request, Event $event): JsonResponse
    {
        $user = Auth::user();
        $user->subscribed_events()->detach($event);
        return $this->sendResponse([], 'Event unsubscribed with success');
    }
}
