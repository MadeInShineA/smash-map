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

            if($request->has('games')){
                $games = $request->input('games');
                switch ($games){
                    case 'default':
                        break;
                    default:
                        $games = explode(',', $games);
                        $addresses->where(function($query) use ($games){
                            $query->whereHas('events', function ($query) use ($games){
                                $query->whereIn('game_id', $games);
                            })->orWhereHas('users', function ($query) use ($games){
                                $query->whereHas('games', function ($query) use ($games){
                                    $query->whereIn('game_id', $games);
                                });
                            });
                        });
                }
            }

            if($request->has('name')){
                $name = $request->input('name');
                switch ($name){
                    case 'default':
                        break;
                    default:
                        $addresses->where(function($query) use ($name){
                            $query->whereHas('events', function ($query) use ($name){
                                $query->whereRaw("UPPER(name) LIKE ?", ['%' . strtoupper($name) . '%']);
                            })->orWhereHas('users', function ($query) use ($name){
                                $query->whereRaw("UPPER(username) LIKE ?", ['%' . strtoupper($name) . '%']);
                            });
                        });
                }
            }

            if ($request->has('continents')){
                $continents = $request->input('continents');
                switch ($continents){
                    case 'default':
                        break;
                    default:
                        $continents = explode(',', $continents);
                        $addresses->whereHas('country', function ($query) use ($continents){
                            $query->whereHas('continent', function ($query) use ($continents){
                                $query->whereIn('code', $continents);
                            });
                        });
                }
            }

            if($request->has('countries')){
                $countries = $request->input('countries');
                switch ($countries){
                    case 'default':
                        break;
                    default:
                        $countries = explode(',', $countries);
                        $addresses->whereHas('country', function ($query) use ($countries){
                            $query->whereIn('code', $countries);
                        });
                }
            }

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

            if ($request->has('startDate') && $request->has('endDate')){
                $startDate = $request->input('startDate');
                $endDate = $request->input('endDate');

                if ($startDate !== 'default' && $endDate !== 'default'){
                    $addresses->whereHas('events', function ($query) use ($startDate, $endDate){
                        $query->whereDate('start_date_time', '>=', $startDate)->whereDate('end_date_time', '<=', $endDate);
                    });
                }
            }

            // TODO Fix when combined with the continents or other filters
            if ($request->has('characters')){
                $characters = $request->input('characters');
                switch ($characters){
                    case 'default':
                        break;
                    default:
                        $characters = explode(',', $characters);
                        $addresses->whereHas('users', function ($query) use ($characters){
                            $query->whereHas('characters', function ($query) use ($characters){
                                $query->whereIn('character_id', $characters);
                            });
                        });
                }
            }

            return MapAddressResource::collection($addresses->get());
        }catch (\Error $error){
            return $this->sendError($error, ['An error occurred while retrieving the addresses E512'], 500);
        }
    }
}
