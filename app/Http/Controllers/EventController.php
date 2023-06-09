<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Event\EventCalendarResource;
use App\Http\Resources\Event\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request){
        $events = Event::paginate(12);
        return EventResource::collection($events);
    }

    public function calendar_index(Request $request){
        $events = Event::all();
//        $events = Event::all();
        return EventCalendarResource::collection($events);
    }
}
