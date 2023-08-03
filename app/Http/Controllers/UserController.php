<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function login(LoginRequest $request){
        if (!Auth::attempt($request->json()->all())) {
            return $this->sendError('Unauthorized - 402E - ',
                [
                    'login' => ['Email or password are incorrect']
                ],
                401);
        }
//        return $this->sendResponse(['user' => auth()->user(), 'token' => auth()->user()->createToken('User Token')->plainTextToken], 'You are connected');
        return $this->sendResponse(['user' => auth()->user()], 'You are connected');
    }

    public function logout(Request $request){
        Auth::guard('web')->logout();
        return $this->sendResponse([], 'You are disconnected');
    }
}
