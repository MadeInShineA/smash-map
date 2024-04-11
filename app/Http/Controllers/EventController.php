<?php

namespace App\Http\Controllers;

use App\Enums\GameEnum;
use App\Http\Resources\Event\EventCalendarResource;
use App\Http\Resources\Event\EventResource;
use App\Models\Event;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    #TODO Return timezone as well to avoid a double api call
    # Avoid calling the getTimeZone every fetch ?
                        public function index(Request $request): AnonymousResourceCollection | JsonResponse
    {

        #TODO Update the timezone based on the user's IP

//        try {
//            $timeZone = $this->getVisitorTimezone($request);
//
//        }catch (Exception $exception){
//            $timeZone = 'UTC';
//        }

        try {
            $events = Event::query();

            if ($request->has('startDate') && $request->has('endDate')){
                $startDate = $request->input('startDate');
                $endDate = $request->input('endDate');

                if ($startDate !== 'default' && $endDate !== 'default'){
                    $events->whereDate('end_date_time', '>=', $startDate)->whereDate('end_date_time', '<=', $endDate);
                }
            }

            if($request->has('games')){
                $games = $request->input('games');
                switch ($games){
                    case 'default':
                        break;
                    default:
                        $games = explode(',', $games);
                        $events->whereIn('game_id', $games);
                }
            }

            if($request->has('name')){
                $name = $request->input('name');
                switch ($name){
                    case 'default':
                        break;
                    default:
                        $events->whereRaw("UPPER(name) LIKE ?", ['%' . strtoupper($name) . '%']);;
                }
            }

            if ($request->has('type')){
                $type = $request->input('type');
                switch ($type){
                    case 'default':
                        break;
                    case 'online':
                        $events->where('is_online', true);
                        break;
                    case 'offline':
                        $events->where('is_online', false);
                }
            }

            if ($request->has('continents')){
                $continents = $request->input('continents');
                switch ($continents){
                    case 'default':
                        break;
                    default:
                        $continents = explode(',', $continents);
                        $events->continents($continents);
                }
            }

            if($request->has('countries')){
                $countries = $request->input('countries');
                switch ($countries){
                    case 'default':
                        break;
                    default:
                        $countries = explode(',', $countries);
                        $events->countries($countries);
                }
            }

            if ($request->has('orderBy')){
                $ordering = $request->input('orderBy');
                switch ($ordering){
                    case 'default':
                        break;
                    case 'attendeesASC':
                        $events->orderBy('attendees', 'asc');
                        break;
                    case 'attendeesDESC':
                        $events->orderBy('attendees', 'desc');
                        break;
                    case 'dateASC':
                        $events->orderBy('start_date_time', 'asc');
                        break;
                    case 'dateDESC':
                        $events->orderBy('start_date_time', 'desc');
                        break;
                }
            }

            $events = $events->paginate(12);
            return EventResource::collection($events);
        }catch (\Error $error){
            return $this->sendError($error, ['An error occurred while retrieving the events E 006'], 500);
        }
    }

    public function calendar_index(Request $request): AnonymousResourceCollection | JsonResponse
    {
        try {
            $events = Event::all();
            return $this->sendResponse(EventCalendarResource::collection($events), 'Events retrieved with success');
        }catch (\Error $error){
            return $this->sendError($error, ['An error occurred while retrieving the events E 007'], 500);
        }
    }

    public function get_statistics(Request $request): JsonResponse
    {
        try {
            $statisticsType = $request->input('type');


            $statistics = [
                'labels' => [],
                'datasets' => [
                    [
                        'data' => [],
                        'backgroundColor' => [],
                        'hoverBackgroundColor' => []

                    ]

                ]
            ];

            if($statisticsType === 'games'){
                $events = Event::all();

                foreach (GameEnum::ABBREVIATION as $id => $title){
                    $statistics['labels'][] = $title;
                    $statistics['datasets'][0]['data'][] = $events->where('game_id', $id)->count();
                    $statistics['datasets'][0]['backgroundColor'][] = GameEnum::COLORS[$id];
                    $statistics['datasets'][0]['hoverBackgroundColor'][] = GameEnum::HOVER_COLORS[$id];
                }
            }else if($statisticsType === 'months'){
                $currentMonth = (int)date('m');

                for ($x = $currentMonth; $x < $currentMonth + 6; $x++) {
                    $statistics['labels'][] = date('F', mktime(0, 0, 0, $x, 1));
                }

                $datasetIndex = 0;
                foreach (GameEnum::ABBREVIATION as $id => $title){
                    $statistics['datasets'][$datasetIndex]['label'] = $title;
                    $statistics['datasets'][$datasetIndex]['backgroundColor'][] = GameEnum::COLORS[$id];
                    $statistics['datasets'][$datasetIndex]['hoverBackgroundColor'][] = GameEnum::HOVER_COLORS[$id];
                    $eventsEachMonth = [];
                    for ($x = $currentMonth; $x < $currentMonth + 6; $x++) {
                        $events = Event::query();
                        $eventsEachMonth[] = $events->where('game_id', $id)->whereMonth('start_date_time', $x)->count();
                    }
                    $statistics['datasets'][$datasetIndex]['data'] = $eventsEachMonth;
                    $datasetIndex += 1;
                }



            }

            return $this->sendResponse($statistics, 'Statistics retrieved with success');
        }catch (\Error $error){
            return $this->sendError($error, ['An error occurred while retrieving the events statistics E 010'], 500);
        }


    }
}
