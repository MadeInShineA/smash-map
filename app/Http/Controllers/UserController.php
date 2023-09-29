<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function login(LoginRequest $request){
        if (!Auth::attempt($request->json()->all())) {
            return $this->sendError('Unauthorized - 402E -',
                [
                    'login' => ['Email or password are incorrect']
                ],
                401);
        }
        return $this->sendResponse(['user' => auth::user(), 'token' => auth::user()->createToken('API Token')->plainTextToken], 'You are connected');
    }

    public function logout(Request $request){
        if($request->user()){
            $request->user()->currentAccessToken()->delete();
        }
        return $this->sendResponse([], 'You are disconnected');
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
