<?php

namespace App\Http\Controllers;

use App\Http\Resources\Event\EventCalendarResource;
use App\Http\Resources\Event\EventResource;
use App\Models\Event;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
            $ordering = $request->input('ordering');
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
            $events = $events->paginate(12);
            return EventResource::collection($events);
        }catch (Exception $exception){
            return $this->sendError($exception, ['An error occurred while retrieving the events'], 500);
        }
    }

    public function calendar_index(Request $request): AnonymousResourceCollection | JsonResponse
    {
        try {
            $events = Event::all();
            return $this->sendResponse(EventCalendarResource::collection($events), 'Events retrieved with succes');
        }catch (Exception $exception){
            return $this->sendError($exception, ['An error occurred while retrieving the events'], 500);
        }
    }
}
