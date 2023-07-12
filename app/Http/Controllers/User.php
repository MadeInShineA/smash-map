<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;

class User extends Controller
{
    public function login(LoginRequest $request){
        return $this->sendResponse('Logged in successfully', 200);
    }
}
