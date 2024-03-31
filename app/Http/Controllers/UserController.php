<?php

namespace App\Http\Controllers;

use App\Enums\ImageTypeEnum;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Address;
use App\Models\Country;
use App\Models\Event;
use App\Models\Image;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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


            return $this->sendResponse(['user' => $user, 'token' => $user->createToken('API Token')->plainTextToken], 'You are registered and connected');
        }catch (\Error $error){
            return $this->sendError($error, ['An error occurred while registering'], 500);
        }
    }
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            if (!Auth::attempt($request->json()->all())) {
                return $this->sendError('Unauthorized - 402E -',
                    [
                        'login' => ['Email or password are incorrect']
                    ],
                    401);
            }
            return $this->sendResponse(['user' => auth::user(), 'token' => auth::user()->createToken('API Token')->plainTextToken], 'You are connected');
        }catch (\Error $error) {
            return $this->sendError($error, ['login' => ['An error occurred while logging in please contact the administration  - 500E -']], 500);
        }
    }

    public function logout(Request $request):JsonResponse
    {
        if($request->user()){
            $request->user()->currentAccessToken()->delete();
        }
        return $this->sendResponse([], 'You are disconnected');
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
