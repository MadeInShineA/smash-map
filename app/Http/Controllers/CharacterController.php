<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CharacterController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if ($request->input('characters')){
            return $this->sendResponse([], 'test');
        }
    }
}
