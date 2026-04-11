<?php

namespace App\Http\Controllers;

use App\Enums\GameEnum;
use App\Http\Resources\Event\EventCalendarResource;
use App\Http\Resources\Event\EventResource;
use App\Models\Event;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    /**
     * List events with filtering, sorting, and pagination support.
     *
     * @group Events
     *
     * @queryParam startDate string Filter events starting from this date (Y-m-d format). No-example
     * @queryParam endDate string Filter events ending before this date (Y-m-d format). No-example
     * @queryParam games string Comma-separated game IDs to filter by. Use "default" for no filter.
     *
     * <i></i>Example: 1, 1386
     * @queryParam name string Search events by name (case-insensitive partial match). No-example
     *
     * <i></i>Example: Smash
     * @queryParam type string Event type filter.
     *     - "default": All events (no filter)
     *     - "online": Online-only events
     *     - "offline": In-person events
     *     - "followed": Events subscribed by the authenticated user No-example
     *
     * <i></i>Example: offline
     * @queryParam continents string Comma-separated continent codes to filter by. Use "default" for no filter.
     *
     * <i></i>Example: NA,EU
     * @queryParam countries string Comma-separated country codes to filter by. Use "default" for no filter.
     *
     * <i></i>Example: US,CA
     * @queryParam orderBy string Sort order for events.
     *     - "default": Sort by start date (ascending)
     *     - "attendeesASC": Sort by attendee count (ascending)
     *     - "attendeesDESC": Sort by attendee count (descending)
     *     - "dateASC": Sort by start date (ascending)
     *     - "dateDESC": Sort by start date (descending) No-example
     * <i></i> Example: attendeesASC
     * @queryParam lat float Latitude for location filtering. Must be used with lng and radius. No-example
     * @queryParam lng float Longitude for location filtering. Must be used with lat and radius. No-example
     * @queryParam radius int Search radius in kilometers. Must be used with lat and lng. No-example
     * @queryParam paginate string Set to "false" to return all events without pagination. Defaults to true (12 per page). No-example
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "game": {
     *         "name": "Super Smash Bros. Melee",
     *         "color": "#A30010"
     *       },
     *       "address": {
     *         "country": {
     *           "name": "United States",
     *           "timezone": "America/Los_Angeles"
     *         },
     *         "name": "Anaheim Convention Center",
     *         "latitude": 33.8003,
     *         "longitude": -117.9211
     *       },
     *       "image": {
     *         "url": "https://smashmap.example.com/storage/images/events/1.png"
     *       },
     *       "is_online": false,
     *       "name": "Genesis 10",
     *       "timezone_start_date_time": "2024-01-05T10:00:00-08:00",
     *       "timezone_end_date_time": "2024-01-07T20:00:00-08:00",
     *       "timezone": "America/Los_Angeles (PST)",
     *       "attendees": 5000,
     *       "link": "https://start.gg/tournament/genesis-10",
     *       "user_subscribed": false
     *     }
     *   ],
     *   "links": {
     *     "first": "https://smashmap.example.com/api/events?page=1",
     *     "last": "https://smashmap.example.com/api/events?page=10",
     *     "prev": null,
     *     "next": "https://smashmap.example.com/api/events?page=2"
     *   },
     *   "meta": {
     *     "current_page": 1,
     *     "from": 1,
     *     "last_page": 10,
     *     "per_page": 12,
     *     "to": 12,
     *     "total": 120
     *   }
     * }
     * @response 500 {
     *   "message": "An error occurred while retrieving the events E 006",
     *   "error": "Error message details"
     * }
     */
    public function index(
        Request $request,
    ): AnonymousResourceCollection|JsonResponse {
        try {
            $events = Event::query();

            if ($request->has("startDate") && $request->has("endDate")) {
                $startDate = $request->input("startDate");
                $endDate = $request->input("endDate");

                if ($startDate !== "default" && $endDate !== "default") {
                    $startDateUTC = Carbon::parse($startDate)
                        ->startOfDay()
                        ->toDateTimeString();
                    $endDateUTC = Carbon::parse($endDate)
                        ->endOfDay()
                        ->toDateTimeString();

                    $events->where(function ($query) use (
                        $startDateUTC,
                        $endDateUTC,
                    ) {
                        $query
                            ->whereBetween("start_date_time", [
                                $startDateUTC,
                                $endDateUTC,
                            ])
                            ->orWhereBetween("end_date_time", [
                                $startDateUTC,
                                $endDateUTC,
                            ])
                            ->orWhere(function ($query) use (
                                $startDateUTC,
                                $endDateUTC,
                            ) {
                                $query
                                    ->where(
                                        "start_date_time",
                                        "<",
                                        $startDateUTC,
                                    )
                                    ->where(
                                        "end_date_time",
                                        ">=",
                                        $startDateUTC,
                                    );
                            })
                            ->orWhere(function ($query) use (
                                $startDateUTC,
                                $endDateUTC,
                            ) {
                                $query
                                    ->where(
                                        "start_date_time",
                                        "<=",
                                        $endDateUTC,
                                    )
                                    ->where("end_date_time", ">", $endDateUTC);
                            });
                    });
                }
            }

            if ($request->has("games")) {
                $games = $request->input("games");
                switch ($games) {
                    case "default":
                        break;
                    default:
                        $games = explode(",", $games);
                        $events->whereIn("game_id", $games);
                }
            }

            if ($request->has("name")) {
                $name = $request->input("name");
                switch ($name) {
                    case "default":
                        break;
                    default:
                        $events->whereRaw("UPPER(events.name) LIKE ?", [
                            "%" . strtoupper($name) . "%",
                        ]);
                }
            }

            if ($request->has("type")) {
                $type = $request->input("type");
                switch ($type) {
                    case "default":
                        break;
                    case "online":
                        $events->where("is_online", true);
                        break;
                    case "offline":
                        $events->where("is_online", false);
                        break;
                    case "followed":
                        $events->whereHas("subscribed_users", function (
                            $query,
                        ) use ($request) {
                            $query->where(
                                "user_id",
                                $request->user("sanctum")->id,
                            );
                        });
                }
            }

            if ($request->has("continents")) {
                $continents = $request->input("continents");
                switch ($continents) {
                    case "default":
                        break;
                    default:
                        $continents = explode(",", $continents);
                        $events->continents($continents);
                }
            }

            if ($request->has("countries")) {
                $countries = $request->input("countries");
                switch ($countries) {
                    case "default":
                        break;
                    default:
                        $countries = explode(",", $countries);
                        $events->countries($countries);
                }
            }

            if ($request->has("orderBy")) {
                $ordering = $request->input("orderBy");
                switch ($ordering) {
                    case "default":
                        break;
                    case "attendeesASC":
                        $events->orderBy("attendees", "asc");
                        break;
                    case "attendeesDESC":
                        $events->orderBy("attendees", "desc");
                        break;
                    case "dateASC":
                        $events->orderBy("start_date_time", "asc");
                        break;
                    case "dateDESC":
                        $events->orderBy("start_date_time", "desc");
                        break;
                }
            }

            if (
                $request->has("lat") &&
                $request->has("lng") &&
                $request->has("radius")
            ) {
                $events->withinRadius(
                    $request->input("lat"),
                    $request->input("lng"),
                    $request->input("radius"),
                );
            }

            $paginate = $request->input("paginate");

            if ($paginate == "false") {
                return EventResource::collection($events->get());
            }
            $events = $events->paginate(12);

            return EventResource::collection($events);
        } catch (\Error $error) {
            return $this->sendError(
                $error,
                ["An error occurred while retrieving the events E 006"],
                500,
            );
        }
    }

    public function calendar_index(
        Request $request,
    ): AnonymousResourceCollection|JsonResponse {
        try {
            $events = Event::all();
            return $this->sendResponse(
                EventCalendarResource::collection($events),
                "Events retrieved with success",
            );
        } catch (\Error $error) {
            return $this->sendError(
                $error,
                ["An error occurred while retrieving the events E 007"],
                500,
            );
        }
    }

    /**
     * Get event statistics in a format suitable for charts.
     *
     * @group Events
     *
     * @queryParam type string The type of statistics to retrieve.
     *     - "games": Event count by game
     *     - "months": Event count by month for the next 6 months, broken down by game No-example
     *
     *  <i></i>Example: games
     *
     * @response 200 {
     *   "data": {
     *     "labels": ["64", "Melee", "Brawl", "Project M", "Project +", "3DS / WiiU", "Ultimate", "HDR", "Rivals 2"],
     *     "datasets": [
     *       {
     *         "data": [12, 450, 80, 95, 150, 60, 800, 30, 120],
     *         "backgroundColor": ["#FAC41A", "#A30010", "#660d02", "#3B448B", "#6FD19C", "#AFC1EE", "#F18A41", "#015500", "#B19EEF"],
     *         "hoverBackgroundColor": ["#D8A504", "#82000C", "#510A01", "#2F366F", "#3EC17A", "#6A8CDF", "#E46810", "#013D00", "#9A85D1"]
     *       }
     *     ],
     *     "eventCount": 1797
     *   },
     *   "message": "Statistics retrieved with success"
     * }
     * @response 200 (type=months) {
     *   "data": {
     *     "labels": ["January", "February", "March", "April", "May", "June"],
     *     "datasets": [
     *       {
     *         "label": "64",
     *         "backgroundColor": ["#FAC41A"],
     *         "hoverBackgroundColor": ["#D8A504"],
     *         "data": [2, 3, 4, 2, 1, 0]
     *       },
     *       {
     *         "label": "Melee",
     *         "backgroundColor": ["#A30010"],
     *         "hoverBackgroundColor": ["#82000C"],
     *         "data": [80, 95, 120, 85, 70, 0]
     *       },
     *       {
     *         "label": "Brawl",
     *         "backgroundColor": ["#660d02"],
     *         "hoverBackgroundColor": ["#510A01"],
     *         "data": [10, 15, 20, 18, 17, 0]
     *       },
     *       {
     *         "label": "Project M",
     *         "backgroundColor": ["#3B448B"],
     *         "hoverBackgroundColor": ["#2F366F"],
     *         "data": [20, 25, 30, 20, 0, 0]
     *       },
     *       {
     *         "label": "Project +",
     *         "backgroundColor": ["#6FD19C"],
     *         "hoverBackgroundColor": ["#3EC17A"],
     *         "data": [30, 40, 50, 30, 0, 0]
     *       },
     *       {
     *         "label": "3DS / WiiU",
     *         "backgroundColor": ["#AFC1EE"],
     *         "hoverBackgroundColor": ["#6A8CDF"],
     *         "data": [5, 8, 10, 7, 30, 0]
     *       },
     *       {
     *         "label": "Ultimate",
     *         "backgroundColor": ["#F18A41"],
     *         "hoverBackgroundColor": ["#E46810"],
     *         "data": [150, 200, 250, 150, 50, 0]
     *       },
     *       {
     *         "label": "HDR",
     *         "backgroundColor": ["#015500"],
     *         "hoverBackgroundColor": ["#013D00"],
     *         "data": [5, 8, 9, 8, 0, 0]
     *       },
     *       {
     *         "label": "Rivals 2",
     *         "backgroundColor": ["#B19EEF"],
     *         "hoverBackgroundColor": ["#9A85D1"],
     *         "data": [25, 35, 40, 20, 0, 0]
     *       }
     *     ],
     *     "eventCount": 0
     *   },
     *   "message": "Statistics retrieved with success"
     * }
     * @response 500 {
     *   "message": "An error occurred while retrieving the events statistics E 010",
     *   "error": "Error message details"
     * }
     */
    public function get_statistics(Request $request): JsonResponse
    {
        try {
            $statisticsType = $request->input("type");

            $statistics = [
                "labels" => [],
                "datasets" => [
                    [
                        "data" => [],
                        "backgroundColor" => [],
                        "hoverBackgroundColor" => [],
                    ],
                ],
                "eventCount" => 0,
            ];

            if ($statisticsType === "games") {
                $events = Event::all();
                $statistics["eventCount"] = $events->count();

                foreach (GameEnum::ABBREVIATIONS as $id => $title) {
                    $statistics["labels"][] = $title;
                    $statistics["datasets"][0]["data"][] = $events
                        ->where("game_id", $id)
                        ->count();
                    $statistics["datasets"][0]["backgroundColor"][] =
                        GameEnum::COLORS[$id];
                    $statistics["datasets"][0]["hoverBackgroundColor"][] =
                        GameEnum::HOVER_COLORS[$id];
                }
            } elseif ($statisticsType === "months") {
                $currentMonth = (int) date("m");

                for ($x = $currentMonth; $x < $currentMonth + 6; $x++) {
                    $statistics["labels"][] = date("F", mktime(0, 0, 0, $x, 1));
                }

                $datasetIndex = 0;
                foreach (GameEnum::ABBREVIATIONS as $id => $title) {
                    $statistics["datasets"][$datasetIndex]["label"] = $title;
                    $statistics["datasets"][$datasetIndex][
                        "backgroundColor"
                    ][] = GameEnum::COLORS[$id];
                    $statistics["datasets"][$datasetIndex][
                        "hoverBackgroundColor"
                    ][] = GameEnum::HOVER_COLORS[$id];
                    $eventsEachMonth = [];
                    for ($x = $currentMonth; $x < $currentMonth + 6; $x++) {
                        $month = (($x - 1) % 12) + 1;
                        $events = Event::query();
                        $eventsEachMonth[] = $events
                            ->where("game_id", $id)
                            ->whereMonth("start_date_time", $month)
                            ->count();
                    }
                    $statistics["datasets"][$datasetIndex][
                        "data"
                    ] = $eventsEachMonth;
                    $datasetIndex += 1;
                }
            }

            return $this->sendResponse(
                $statistics,
                "Statistics retrieved with success",
            );
        } catch (\Error $error) {
            return $this->sendError(
                $error,
                [
                    "An error occurred while retrieving the events statistics E 010",
                ],
                500,
            );
        }
    }

    public function event_subscribe(
        Request $request,
        Event $event,
    ): JsonResponse {
        try {
            $user = request()->user();
            $user->subscribed_events()->attach($event, [
                "original_attendees" => $event->attendees ?? 0,
            ]);
            return $this->sendResponse([], "Event followed with success");
        } catch (\Error $error) {
            return $this->sendError(
                $error,
                ["An error occurred while following the event E 016"],
                500,
            );
        }
    }

    public function event_unsubscribe(
        Request $request,
        Event $event,
    ): JsonResponse {
        try {
            $user = Auth::user();
            $user->subscribed_events()->detach($event);
            return $this->sendResponse([], "Event unfollowed with success");
        } catch (\Error $error) {
            return $this->sendError(
                $error,
                ["An error occurred while unfollowing the event E 017"],
                500,
            );
        }
    }

    public function import_500_events(): void
    {
        Artisan::call("import-500-events-all-games");
    }

    public function delete_events(): void
    {
        Artisan::call("delete-events");
    }
}
