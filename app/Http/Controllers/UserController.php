<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Address;
use App\Models\Country;
use App\Models\Event;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
                'username'=> $request->input('username'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'address_id' => $address->id,
            ]);

            $user->games()->attach($request->input('games'));
            $user->characters()->attach($request->input('characters'));

            return $this->sendResponse(['user' => $user, 'token' => $user->createToken('API Token')->plainTextToken], 'You are registered and connected');
        }catch (\Error $error){
            return $this->sendError($error, ['An error occurred while registering'], 500);
        }
    }
    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->json()->all())) {
            return $this->sendError('Unauthorized - 402E -',
                [
                    'login' => ['Email or password are incorrect']
                ],
                401);
        }
        return $this->sendResponse(['user' => auth::user(), 'token' => auth::user()->createToken('API Token')->plainTextToken], 'You are connected');
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
