<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Address\MapAddressResource;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request):JsonResponse
    {
        $addresses = Address::all();
        return $this->sendResponse(MapAddressResource::collection($addresses), 'Addresses retrieved with success');
    }
}
