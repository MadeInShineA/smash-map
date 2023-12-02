<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Address\MapAddressResource;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AddressController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection | JsonResponse
    {
        try {
            $addresses = Address::query();

//            if ($request->has('startDate') && $request->has('endDate')){
//                $startDate = $request->input('startDate');
//                $endDate = $request->input('endDate');
//
//                if ($startDate !== 'default' && $endDate !== 'default'){
//                    $addresses->whereDate('end_date_time', '>=', $startDate)->whereDate('end_date_time', '<=', $endDate);
//                }
//            }

            if($request->has('games')){
                $games = $request->input('games');
                switch ($games){
                    case 'default':
                        break;
                    default:
                        $games = explode(',', $games);
                        // The addresses don't have a game id but their events relation havea game id column
                        // So we need to check if the address has an event with the game id
                        $addresses->whereHas('events', function ($query) use ($games){
                            $query->whereIn('game_id', $games);
                        });
                    }
            }

//            if($request->has('name')){
//                $name = $request->input('name');
//                switch ($name){
//                    case 'default':
//                        break;
//                    default:
//                        $addresses->whereRaw("UPPER(name) LIKE ?", ['%' . strtoupper($name) . '%']);;
//                }
//            }

//            if ($request->has('continents')){
//                $continents = $request->input('continents');
//                switch ($continents){
//                    case 'default':
//                        break;
//                    default:
//                        $continents = explode(',', $continents);
//                        $addresses->continents($continents);
//                }
//            }

//            if($request->has('countries')){
//                $countries = $request->input('countries');
//                switch ($countries){
//                    case 'default':
//                        break;
//                    default:
//                        $countries = explode(',', $countries);
//                        $addresses->countries($countries);
//                }
//            }

            if ($request->has('type')){
                $type = $request->input('type');
                switch ($type){
                    case 'default':
                        break;
                    case 'events':
                        $addresses->whereHas('events');
                        break;
                    case 'users':
                        $addresses->whereHas('users');
                }
            }

            return MapAddressResource::collection($addresses->get());
        }catch (\Error $error){
            return $this->sendError($error, ['An error occurred while retrieving the addresses E512'], 500);
        }
    }
}
