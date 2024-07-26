<?php

namespace App\Http\Controllers;

use App\Enums\GameEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\Address\MapAddressResource;
use App\Models\Address;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Artisan;

class AddressController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection | JsonResponse
    {
        try {
            $addresses = Address::query();

            $addresses->where(function ($query) {
                $query->whereHas('users', function ($subQuery) {
                    $subQuery->where('is_on_map', true);
                })->orWhereHas('events');
            });

            if ($request->has('games')) {
                $games = $request->input('games');
                switch ($games) {
                    case 'default':
                        break;
                    default:
                        $games = explode(',', $games);
                        $addresses->where(function($query) use ($games) {
                            $query->whereHas('events', function ($query) use ($games) {
                                $query->whereIn('game_id', $games);
                            })->orWhereHas('users', function ($query) use ($games) {
                                $query->whereHas('games', function ($query) use ($games) {
                                    $query->whereIn('game_id', $games);
                                });
                            });
                        });
                }
            }

            if ($request->has('startDate') && $request->has('endDate')) {
                $startDate = $request->input('startDate');
                $endDate = $request->input('endDate');

                if ($startDate !== 'default' && $endDate !== 'default') {
                    $startDateUTC = Carbon::parse($startDate)->setTimezone('UTC')->startOfDay()->toDateTimeString();
                    $endDateUTC = Carbon::parse($endDate)->setTimezone('UTC')->endOfDay()->toDateTimeString();

                    $addresses->whereHas('events', function ($query) use ($startDateUTC, $endDateUTC, $request) {
                        $query->where(function ($query) use ($startDateUTC, $endDateUTC, $request) {

                            if($request->has('games') && $request->input('games') !== 'default'){
                                $games = explode(',', $request->input('games'));
                                $query->whereIn('game_id', $games);
                            };

                            $query->where(function ($query) use ($startDateUTC, $endDateUTC) {
                                    $query->whereBetween('start_date_time', [$startDateUTC, $endDateUTC])
                                        ->orWhereBetween('end_date_time', [$startDateUTC, $endDateUTC])
                                        ->orWhere(function ($query) use ($startDateUTC, $endDateUTC) {
                                            $query->where('start_date_time', '<', $startDateUTC)
                                                ->where('end_date_time', '>=', $startDateUTC);
                                        })
                                        ->orWhere(function ($query) use ($startDateUTC, $endDateUTC) {
                                            $query->where('start_date_time', '<=', $endDateUTC)
                                                ->where('end_date_time', '>', $endDateUTC);
                                        });
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
                                $query->orWhereRaw("UPPER(connect_code) LIKE ?", ['%' . strtoupper($name) . '%']);
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
                    case 'followedEvents':
                        $addresses->whereHas('events', function ($query) use ($request){
                            $query->whereHas('subscribed_users', function ($query) use ($request){
                                $query->where('user_id', $request->user('sanctum')->id);
                            });
                        });
                        break;
                    case 'users':
                        $addresses->whereHas('users');
                        break;
                    case 'modders':
                        $addresses->whereHas('users', function ($query){
                            $query->where('is_modder', true);
                        });
                        break;
                }
            }

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

//            return $this->sendResponse($addresses->get(), 'Addresses retrieved successfully');
            return MapAddressResource::collection($addresses->get());
        }catch (\Error $error){
            return $this->sendError($error, ['An error occurred while retrieving the addresses E 009'], 500);
        }
    }

    public function delete_addresses(): void
    {
        Artisan::call('delete-addresses');
    }
}
