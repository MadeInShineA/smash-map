<?php

namespace App\Http\Controllers;

use App\Http\Resources\Event\EventCalendarResource;
use App\Http\Resources\Event\EventResource;
use App\Models\Event;
use Exception;
use Illuminate\Http\Request;

class EventController extends Controller
{
    #TODO Return timezone as well to avoid a double api call
    # Avoid calling the getTimeZone every fetch ?
    public function index(Request $request){

        try {
            $timeZone = $this->getVisitorTimezone($request);
        }catch (Exception $exception){
            $timeZone = 'UTC';
        }
        $events = Event::paginate(12);
        return EventResource::collection($events);
    }

    public function calendar_index(Request $request){
        $events = Event::all();
//        $events = Event::all();
        return EventCalendarResource::collection($events);
    }
}
