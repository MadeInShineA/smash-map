<?php

use App\Enums\GameEnum;
use App\Enums\ImageTypeEnum;
use App\Enums\NotificationTypeEnum;
use App\Events\NotificationEvent;
use App\Models\Address;
use App\Models\Character;
use App\Models\Country;
use App\Models\Event;
use App\Models\Game;
use App\Models\Image;
use App\Models\Notification;
use App\Models\Scopes\ShownScope;
use App\Models\User;
use App\Notifications\PushNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Pusher\PushNotifications\PushNotifications;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command(
    "import-25-events-parry-gg {import_all_events?} {cursor?}",
    function (bool $import_all_events = false, ?string $cursor = null) {
        $apiToken = env("PARRY_GG_API_KEY");

        $endpointUrl =
            "https://grpcweb.parry.gg/parrygg.services.TournamentService/GetTournaments";

        $paginationRequest = [];
        if ($cursor) {
            $paginationRequest["cursor"] = ["id" => $cursor];
        }

        $payload = json_encode([
            "paginationRequest" => (object) $paginationRequest,
        ]);

        $headers = ["Content-Type: application/json", "X-API-KEY: $apiToken"];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $endpointUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $raw_response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo "Error: " . curl_error($ch);
        }

        curl_close($ch);

        $response = json_decode($raw_response);

        if (!$response) {
            Log::error("No data from Parry.gg API");
            return;
        }

        $tournaments = [];
        $paginationResponse = null;

        $data = $response;

        if (
            is_array($response) &&
            count($response) === 1 &&
            is_object($response[0] ?? null)
        ) {
            $data = $response[0];
        }

        if (is_object($data)) {
            foreach ($data as $key => $value) {
                if ($key === "paginationResponse") {
                    $paginationResponse = $value;
                } elseif (
                    is_object($value) &&
                    isset($value->events) &&
                    is_array($value->events)
                ) {
                    $tournaments[] = $value;
                }
            }
        } elseif (is_array($data)) {
            foreach ($data as $item) {
                if (
                    is_object($item) &&
                    isset($item->events) &&
                    is_array($item->events)
                ) {
                    $tournaments[] = $item;
                }
            }
        }

        $nameToGameId = [
            GameEnum::NAMES[GameEnum::SMASH64] => GameEnum::SMASH64,
            GameEnum::NAMES[GameEnum::MELEE] => GameEnum::MELEE,
            GameEnum::NAMES[GameEnum::BRAWL] => GameEnum::BRAWL,
            GameEnum::NAMES[GameEnum::PROJECT_M] => GameEnum::PROJECT_M,
            GameEnum::NAMES[GameEnum::PROJECT_PLUS] => GameEnum::PROJECT_PLUS,
            GameEnum::NAMES[GameEnum::SMASH4] => GameEnum::SMASH4,
            GameEnum::NAMES[GameEnum::ULTIMATE] => GameEnum::ULTIMATE,
            GameEnum::NAMES[GameEnum::HDR] => GameEnum::HDR,
            GameEnum::NAMES[GameEnum::RIVALS2] => GameEnum::RIVALS2,
        ];

        foreach ($tournaments as $tournament) {
            if (
                !isset($tournament->events) ||
                !is_array($tournament->events)
            ) {
                continue;
            }

            foreach ($tournament->events as $event) {
                if (!isset($event->game)) {
                    continue;
                }

                $parry_game_name = $event->game->name;
                $game_id = $nameToGameId[$parry_game_name] ?? null;

                if (!$game_id) {
                    Log::error(
                        "Parry.gg game not mapped: " . $parry_game_name,
                    );
                    continue;
                }

                $game = Game::find($game_id);
                if (!$game) {
                    Log::error(
                        "Game not found in DB for id: " .
                            $game_id .
                            " (" .
                            $parry_game_name .
                            ")",
                    );
                    continue;
                }

                $parry_gg_id = $event->id;
                $parry_gg_updated_at = new DateTime($event->updatedAt);
                $parry_gg_updated_at = $parry_gg_updated_at->format(
                    "Y-m-d H:i:s",
                );

                $event_model_instance = Event::withoutGlobalScope(
                    ShownScope::class,
                )
                    ->where("parry_gg_id", $parry_gg_id)
                    ->first();
                $event_old_attendees = null;

                if ($event_model_instance && $event_model_instance->show) {
                    foreach (
                        $event_model_instance->subscribed_users()->get()
                        as $user
                    ) {
                        if ($user->has_attendees_notifications) {
                            $event_attendees_when_subscribed =
                                $user->pivot->original_attendees;
                            $event_old_attendees =
                                $event_model_instance->attendees;
                            $event_attendees =
                                $event->entrantCount ?? $event->entrantSize ?? 0;

                            $last_notification = $user
                                ->notifications()
                                ->where(
                                    "event_id",
                                    $event_model_instance->id,
                                )
                                ->where(
                                    "type",
                                    NotificationTypeEnum::ATTENDEES,
                                )
                                ->orderBy("created_at", "desc")
                                ->first();
                            $user_attendees_notifications_thresholds =
                                $user->attendees_notifications_thresholds;

                            foreach (
                                $user_attendees_notifications_thresholds
                                as $user_attendees_notifications_threshold
                            ) {
                                if (
                                    $event_attendees_when_subscribed >=
                                    $user_attendees_notifications_threshold
                                ) {
                                    continue;
                                }

                                if (
                                    $last_notification &&
                                    $last_notification->attendees >=
                                        $user_attendees_notifications_threshold
                                ) {
                                    continue;
                                }

                                $send_notification = false;
                                if (
                                    !$last_notification &&
                                    $event_attendees >=
                                        $user_attendees_notifications_threshold
                                ) {
                                    $send_notification = true;
                                } elseif (
                                    $last_notification &&
                                    $event_attendees >
                                        $last_notification->attendees &&
                                    $event_attendees >=
                                        $user_attendees_notifications_threshold
                                ) {
                                    $send_notification = true;
                                }

                                if ($send_notification) {
                                    Log::info(
                                        "User: " .
                                            $user->username .
                                            " notified for event: " .
                                            $event_model_instance->name .
                                            " User old attendees: " .
                                            $event_old_attendees .
                                            " User new attendees: " .
                                            $event_attendees .
                                            " User attendees notifications thresholds: " .
                                            $user_attendees_notifications_threshold,
                                    );
                                    $message =
                                        'The Event: <a href="' .
                                        $event_model_instance->link .
                                        '"target="blank">' .
                                        $event_model_instance->name .
                                        "</a> has reached " .
                                        $event_attendees .
                                        " attendees!";
                                    $image =
                                        $event_model_instance->image?->url;

                                    if (!$image) {
                                        $image = URL::to(
                                            "/storage/map-icons/" .
                                                $event_model_instance->game
                                                    ->slug .
                                                ".png",
                                        );
                                    }
                                    $notification = Notification::create([
                                        "event_id" =>
                                            $event_model_instance->id,
                                        "game_id" =>
                                            $event_model_instance->game_id,
                                        "user_id" => $user->id,
                                        "message" => $message,
                                        "type" =>
                                            NotificationTypeEnum::ATTENDEES,
                                        "image_url" => $image,
                                        "attendees" => $event_attendees,
                                    ]);
                                    $user->notify(
                                        new PushNotification($notification),
                                    );
                                    break;
                                }
                            }
                        }

                        if ($user->has_time_notifications) {
                            $event_date = Carbon::parse(
                                $event_model_instance->start_date_time,
                            );
                            $days_until_event = $event_date->diffInDays(
                                Carbon::now(),
                            );

                            $event_subscription_date = Carbon::parse(
                                $user->pivot->created_at,
                            );
                            $days_until_event_when_subscribed = $event_date->diffInDays(
                                $event_subscription_date,
                            );

                            $user_time_notifications_thresholds =
                                $user->time_notifications_thresholds;

                            $last_notification = $user
                                ->notifications()
                                ->where(
                                    "event_id",
                                    $event_model_instance->id,
                                )
                                ->where("type", NotificationTypeEnum::TIME)
                                ->orderBy("created_at", "desc")
                                ->first();
                            foreach (
                                $user_time_notifications_thresholds
                                as $user_time_notifications_threshold
                            ) {
                                $send_notification = false;

                                if (
                                    $days_until_event_when_subscribed <=
                                    $user_time_notifications_threshold
                                ) {
                                    continue;
                                }

                                if (
                                    $last_notification &&
                                    $event_date->diffInDays(
                                        Carbon::parse(
                                            $last_notification->created_at,
                                        ),
                                    ) <= $user_time_notifications_threshold
                                ) {
                                    continue;
                                }

                                if (
                                    !$last_notification &&
                                    $days_until_event <=
                                        $user_time_notifications_threshold
                                ) {
                                    $send_notification = true;
                                } elseif ($last_notification) {
                                    $days_until_event_when_notified = $event_date->diffInDays(
                                        Carbon::parse(
                                            $last_notification->created_at,
                                        ),
                                    );
                                    if (
                                        $days_until_event <
                                            $days_until_event_when_notified &&
                                        $days_until_event <=
                                            $user_time_notifications_threshold
                                    ) {
                                        $send_notification = true;
                                    }
                                }

                                if ($send_notification) {
                                    Log::info(
                                        "User: " .
                                            $user->username .
                                            " notified for event: " .
                                            $event_model_instance->name .
                                            " User days until event: " .
                                            $days_until_event .
                                            " User time notifications thresholds: " .
                                            $user_time_notifications_threshold,
                                    );

                                    $message = null;
                                    if ($days_until_event === 0) {
                                        $message =
                                            'The Event: <a href="' .
                                            $event_model_instance->link .
                                            '"target="blank">' .
                                            $event_model_instance->name .
                                            "</a> starts in less than 24 hours!";
                                    } elseif ($days_until_event === 1) {
                                        $message =
                                            'The Event: <a href="' .
                                            $event_model_instance->link .
                                            '"target="blank">' .
                                            $event_model_instance->name .
                                            "</a> starts in " .
                                            $days_until_event .
                                            " day!";
                                    } else {
                                        $message =
                                            'The Event: <a href="' .
                                            $event_model_instance->link .
                                            '"target="blank">' .
                                            $event_model_instance->name .
                                            "</a> starts in " .
                                            $days_until_event .
                                            " days!";
                                    }

                                    $image =
                                        $event_model_instance->image?->url;

                                    if (!$image) {
                                        $image = URL::to(
                                            "/storage/map-icons/" .
                                                $event_model_instance->game
                                                    ->slug .
                                                ".png",
                                        );
                                    }

                                    $notification = Notification::create([
                                        "event_id" =>
                                            $event_model_instance->id,
                                        "game_id" =>
                                            $event_model_instance->game_id,
                                        "user_id" => $user->id,
                                        "message" => $message,
                                        "type" => NotificationTypeEnum::TIME,
                                        "image_url" => $image,
                                    ]);
                                    $user->notify(
                                        new PushNotification($notification),
                                    );
                                    break;
                                }
                            }
                        }
                    }
                }

                $is_online =
                    ($event->locationType ?? $tournament->locationType) ===
                    "LOCATION_TYPE_ONLINE";
            $name = trim($tournament->name);
                $timezone = $tournament->timeZone ?? null;
                if ($timezone) {
                    try {
                        $timezone = Carbon::now(
                            new DateTimeZone($timezone),
                        )->format("P");
                    } catch (Exception $e) {
                        $timezone = "+00:00";
                    }
                }
                if (!$timezone) {
                    $timezone = "+00:00";
                }

                $start_date = new DateTime($event->startDate);
                $epoch = new DateTime("1970-01-02T00:00:00Z");
                if ($start_date < $epoch) {
                    $start_date = new DateTime($tournament->startDate);
                }

                $end_date = new DateTime($tournament->endDate);

                if (
                    $start_date > $end_date ||
                    $end_date < Carbon::now("UTC")
                ) {
                    continue;
                }

                $attendees = $event->entrantCount ?? $event->entrantSize ?? 0;

                $tournament_slug = null;
                if (isset($tournament->slugs)) {
                    foreach ($tournament->slugs as $slug) {
                        if ($slug->type === "SLUG_TYPE_PRIMARY") {
                            $tournament_slug = $slug->slug;
                            break;
                        }
                    }
                }
                if (!$tournament_slug && isset($tournament->slugs[0])) {
                    $tournament_slug = $tournament->slugs[0]->slug;
                }
                $link = null;
                if ($tournament_slug) {
                    $link =
                        "https://parry.gg/tournaments/{$tournament_slug}";
                }

                if (
                    $import_all_events ||
                    !$event_model_instance ||
                    ($event_model_instance->show &&
                        ($event_model_instance->parry_gg_updated_at <
                            $parry_gg_updated_at ||
                            $event_model_instance->attendees < $attendees))
                ) {
                    if ($event_model_instance) {
                        $event_model_instance->update([
                            "parry_gg_updated_at" => $parry_gg_updated_at,
                            "is_online" => $is_online,
                            "name" => $name,
                            "timezone" => $timezone,
                            "start_date_time" => $start_date,
                            "end_date_time" => $end_date,
                            "attendees" => $attendees,
                            "link" => $link,
                            "game_id" => $game->id,
                        ]);
                        Log::info(
                            "Parry.gg event updated: " . $event->name,
                        );
                    } else {
                        $event_model_instance = Event::create([
                            "uuid" => Str::uuid()->toString(),
                            "parry_gg_id" => $parry_gg_id,
                            "parry_gg_updated_at" => $parry_gg_updated_at,
                            "game_id" => $game->id,
                            "is_online" => $is_online,
                            "name" => $name,
                            "timezone" => $timezone,
                            "start_date_time" => $start_date,
                            "end_date_time" => $end_date,
                            "attendees" => $attendees,
                            "link" => $link,
                        ]);
                        Log::info(
                            "Parry.gg event created: " . $event->name,
                        );
                    }

                    if (
                        !$is_online &&
                        isset($tournament->address)
                    ) {
                        $addr = $tournament->address;
                        $latitude = round($addr->latitude ?? 0, 7);
                        $longitude = round($addr->longitude ?? 0, 7);

                        $country_code = $addr->countryCode ?? null;
                        $country = null;
                        if ($country_code) {
                            $country = Country::where(
                                "code",
                                $country_code,
                            )->first();
                        }

                        if (!$country) {
                            Log::error(
                                "Country not found: " .
                                    ($country_code ?? "null") .
                                    " for Parry.gg event: " .
                                    $event->name,
                            );
                            $country_code = null;
                        }

                        $address_name =
                            $addr->formattedAddress ??
                            $tournament->venueAddress ??
                            null;

                        $address = $event_model_instance->address;

                        if (!$address && $latitude && $longitude) {
                            $address = Address::firstOrCreate(
                                [
                                    "latitude" => $latitude,
                                    "longitude" => $longitude,
                                ],
                                [
                                    "name" => $address_name,
                                    "country_id" => $country?->id,
                                ],
                            );
                            $event_model_instance->address_id =
                                $address->id;
                            $event_model_instance->save();
                        }
                    }

                    $event_directory_path =
                        "/events-images/" . $event_model_instance->uuid;

                    if (isset($tournament->images)) {
                        $image = null;
                        foreach ($tournament->images as $tournament_image) {
                            if (
                                $tournament_image->type ===
                                "IMAGE_TYPE_PROFILE"
                            ) {
                                $image = $tournament_image;
                                break;
                            } elseif (
                                $tournament_image->type ===
                                "IMAGE_TYPE_BANNER"
                            ) {
                                $image = $tournament_image;
                            }
                        }

                        if ($image) {
                            $image_type =
                                $image->type === "IMAGE_TYPE_PROFILE"
                                    ? ImageTypeEnum::EVENT_PROFILE
                                    : ImageTypeEnum::EVENT_BANNER;

                            $image_file = null;
                            try {
                                $image_file = file_get_contents($image->url);
                            } catch (Exception $e) {
                                Log::error(
                                    "Parry.gg image couldn't be retrieved: " .
                                        $image->url .
                                        " for event: " .
                                        $event->name,
                                );
                            }

                            if ($image_file) {
                                $image_md5 = md5($image_file);
                                $event_model_instance_old_image =
                                    $event_model_instance->image;

                                if (
                                    !$import_all_events &&
                                    $event_model_instance_old_image &&
                                    $event_model_instance_old_image->md5 ===
                                        $image_md5
                                ) {
                                    continue;
                                }

                                $isImageStored = Storage::put(
                                    $event_directory_path .
                                        "/" .
                                        $image_type .
                                        ".png",
                                    $image_file,
                                );
                                if ($isImageStored) {
                                    Image::updateOrCreate(
                                        [
                                            "parentable_type" => Event::class,
                                            "parentable_id" =>
                                                $event_model_instance->id,
                                            "extension" => "png",
                                        ],
                                        [
                                            "origin" => $image->url,
                                            "type" => $image_type,
                                            "md5" => $image_md5,
                                        ],
                                    );
                                    Log::info(
                                        $image_type .
                                            " image for:" .
                                            $event->name .
                                            " created",
                                    );
                                } else {
                                    Log::error(
                                        "Parry.gg image couldn't be stored: " .
                                            $image->url .
                                            " for event: " .
                                            $event->name,
                                    );
                                }
                            }
                        }
                    }

                    if (
                        $event_model_instance->wasRecentlyCreated &&
                        !$event_model_instance->is_online &&
                        isset($address)
                    ) {
                        foreach (
                            User::where(
                                "has_distance_notifications",
                                true,
                            )->get()
                            as $user
                        ) {
                            if (
                                $user->games->contains(
                                    $event_model_instance->game_id,
                                )
                            ) {
                                $distance_notifications_radius =
                                    $user->distance_notifications_radius;
                                $distance = $address->distanceToKM(
                                    $user->address,
                                );

                                if (
                                    $distance <=
                                    $distance_notifications_radius
                                ) {
                                    Log::info(
                                        "User: " .
                                            $user->username .
                                            " notified for event: " .
                                            $event_model_instance->name .
                                            " User to event distance: " .
                                            round($distance) .
                                            " User distance notifications radius: " .
                                            $distance_notifications_radius,
                                    );
                                    $message =
                                        'The Event: <a href="' .
                                        $event_model_instance->link .
                                        '"target="blank">' .
                                        $event_model_instance->name .
                                        '</a> is happening near you, it\'s only ' .
                                        round($distance) .
                                        " kilometers away!";

                                    $event_model_instance->load("image");
                                    $image =
                                        $event_model_instance->image?->url;
                                    if (!$image) {
                                        $image = URL::to(
                                            "/storage/map-icons/" .
                                                $event_model_instance->game
                                                    ->slug .
                                                ".png",
                                        );
                                    }
                                    $notification = Notification::create([
                                        "event_id" =>
                                            $event_model_instance->id,
                                        "game_id" =>
                                            $event_model_instance->game_id,
                                        "user_id" => $user->id,
                                        "message" => $message,
                                        "type" =>
                                            NotificationTypeEnum::DISTANCE,
                                        "image_url" => $image,
                                    ]);
                                    $user->notify(
                                        new PushNotification($notification),
                                    );
                                }
                            }
                        }
                    }
                }
            }
        }

    },
);

Artisan::command("import-all-events-all-games-parry-gg {import_all_events?}", function (
    bool $import_all_events = false,
) {
    $apiToken = env("PARRY_GG_API_KEY");
    $endpointUrl =
        "https://grpcweb.parry.gg/parrygg.services.TournamentService/GetTournaments";
    $headers = ["Content-Type: application/json", "X-API-KEY: $apiToken"];

    $nameToGameId = [
        GameEnum::NAMES[GameEnum::SMASH64] => GameEnum::SMASH64,
        GameEnum::NAMES[GameEnum::MELEE] => GameEnum::MELEE,
        GameEnum::NAMES[GameEnum::BRAWL] => GameEnum::BRAWL,
        GameEnum::NAMES[GameEnum::PROJECT_M] => GameEnum::PROJECT_M,
        GameEnum::NAMES[GameEnum::PROJECT_PLUS] => GameEnum::PROJECT_PLUS,
        GameEnum::NAMES[GameEnum::SMASH4] => GameEnum::SMASH4,
        GameEnum::NAMES[GameEnum::ULTIMATE] => GameEnum::ULTIMATE,
        GameEnum::NAMES[GameEnum::HDR] => GameEnum::HDR,
        GameEnum::NAMES[GameEnum::RIVALS2] => GameEnum::RIVALS2,
        "Rivals of Aether II" => GameEnum::RIVALS2,
    ];

    $createdCount = 0;
    $updatedCount = 0;
    $cursor = null;
    $totalFetched = 0;

    do {
        $paginationRequest = $cursor
            ? (object) ["cursor" => (object) ["id" => $cursor]]
            : (object) [];

        $payload = json_encode(["paginationRequest" => $paginationRequest]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $endpointUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $raw_response = curl_exec($ch);

        if (curl_errno($ch)) {
            dump("cURL error: " . curl_error($ch));
            curl_close($ch);
            break;
        }

        curl_close($ch);

        $response = json_decode($raw_response);

        if (!$response || !isset($response->tournaments) || !is_array($response->tournaments)) {
            dump("No tournaments found in response");
            break;
        }

        $tournaments = $response->tournaments;
        $totalFetched += count($tournaments);
        dump("Page fetched: " . count($tournaments) . " tournaments (total: {$totalFetched})");

    foreach ($tournaments as $ti => $tournament) {
        if (!isset($tournament->events) || !is_array($tournament->events)) {
            dump("Tournament #{$ti}: no events array, skipping");
            continue;
        }

        $events_count = count($tournament->events);
        dump("Tournament #{$ti}: {$events_count} events");

        foreach ($tournament->events as $ei => $event) {
            if (!isset($event->game)) {
                dump("  Event #{$ei} ({$event->name}): no game field, skipping");
                continue;
            }

            $parry_game_name = $event->game->name;
            $game_id = $nameToGameId[$parry_game_name] ?? null;

            if (!$game_id) {
                dump("  Event #{$ei} ({$event->name}): game '{$parry_game_name}' not in nameToGameId map, skipping");
                continue;
            }

            $game = Game::find($game_id);
            if (!$game) {
                dump("  Event #{$ei} ({$event->name}): game id {$game_id} not in DB, skipping");
                continue;
            }

            dump("  Event #{$ei} ({$event->name}): matched game '{$parry_game_name}' -> id {$game_id}");

            $event_name_lower = strtolower($event->name);
            $skip_singles = str_contains($event_name_lower, "doubles") ||
                str_contains($event_name_lower, "dubs") ||
                str_contains($event_name_lower, "dubers") ||
                str_contains($event_name_lower, "amateur") ||
                str_contains($event_name_lower, "ammy") ||
                str_contains($event_name_lower, "ammie") ||
                str_contains($event_name_lower, "side bracket") ||
                str_contains($event_name_lower, "randoms") ||
                str_contains($event_name_lower, "randubs");
            if ($skip_singles) {
                dump("  Event #{$ei} ({$event->name}): skipped (not singles)");
                continue;
            }

            $parry_gg_id = $event->id;
            $parry_gg_updated_at = new DateTime($event->updatedAt);
            $parry_gg_updated_at = $parry_gg_updated_at->format(
                "Y-m-d H:i:s",
            );

            $event_model_instance = Event::withoutGlobalScope(
                ShownScope::class,
            )
                ->where("parry_gg_id", $parry_gg_id)
                ->first();
            $event_old_attendees = null;

            if ($event_model_instance && $event_model_instance->show) {
                foreach (
                    $event_model_instance->subscribed_users()->get()
                    as $user
                ) {
                    if ($user->has_attendees_notifications) {
                        $event_attendees_when_subscribed =
                            $user->pivot->original_attendees;
                        $event_old_attendees =
                            $event_model_instance->attendees;
                        $event_attendees =
                            $event->entrantCount ?? $event->entrantSize ?? 0;

                        $last_notification = $user
                            ->notifications()
                            ->where(
                                "event_id",
                                $event_model_instance->id,
                            )
                            ->where(
                                "type",
                                NotificationTypeEnum::ATTENDEES,
                            )
                            ->orderBy("created_at", "desc")
                            ->first();
                        $user_attendees_notifications_thresholds =
                            $user->attendees_notifications_thresholds;

                        foreach (
                            $user_attendees_notifications_thresholds
                            as $user_attendees_notifications_threshold
                        ) {
                            if (
                                $event_attendees_when_subscribed >=
                                $user_attendees_notifications_threshold
                            ) {
                                continue;
                            }

                            if (
                                $last_notification &&
                                $last_notification->attendees >=
                                    $user_attendees_notifications_threshold
                            ) {
                                continue;
                            }

                            $send_notification = false;
                            if (
                                !$last_notification &&
                                $event_attendees >=
                                    $user_attendees_notifications_threshold
                            ) {
                                $send_notification = true;
                            } elseif (
                                $last_notification &&
                                $event_attendees >
                                    $last_notification->attendees &&
                                $event_attendees >=
                                    $user_attendees_notifications_threshold
                            ) {
                                $send_notification = true;
                            }

                            if ($send_notification) {
                                Log::info(
                                    "User: " .
                                        $user->username .
                                        " notified for event: " .
                                        $event_model_instance->name .
                                        " User old attendees: " .
                                        $event_old_attendees .
                                        " User new attendees: " .
                                        $event_attendees .
                                        " User attendees notifications thresholds: " .
                                        $user_attendees_notifications_threshold,
                                );
                                $message =
                                    'The Event: <a href="' .
                                    $event_model_instance->link .
                                    '"target="blank">' .
                                    $event_model_instance->name .
                                    "</a> has reached " .
                                    $event_attendees .
                                    " attendees!";
                                $image =
                                    $event_model_instance->image?->url;

                                if (!$image) {
                                    $image = URL::to(
                                        "/storage/map-icons/" .
                                            $event_model_instance->game
                                                ->slug .
                                            ".png",
                                    );
                                }
                                $notification = Notification::create([
                                    "event_id" =>
                                        $event_model_instance->id,
                                    "game_id" =>
                                        $event_model_instance->game_id,
                                    "user_id" => $user->id,
                                    "message" => $message,
                                    "type" =>
                                        NotificationTypeEnum::ATTENDEES,
                                    "image_url" => $image,
                                    "attendees" => $event_attendees,
                                ]);
                                $user->notify(
                                    new PushNotification($notification),
                                );
                                break;
                            }
                        }
                    }

                    if ($user->has_time_notifications) {
                        $event_date = Carbon::parse(
                            $event_model_instance->start_date_time,
                        );
                        $days_until_event = $event_date->diffInDays(
                            Carbon::now(),
                        );

                        $event_subscription_date = Carbon::parse(
                            $user->pivot->created_at,
                        );
                        $days_until_event_when_subscribed = $event_date->diffInDays(
                            $event_subscription_date,
                        );

                        $user_time_notifications_thresholds =
                            $user->time_notifications_thresholds;

                        $last_notification = $user
                            ->notifications()
                            ->where(
                                "event_id",
                                $event_model_instance->id,
                            )
                            ->where("type", NotificationTypeEnum::TIME)
                            ->orderBy("created_at", "desc")
                            ->first();
                        foreach (
                            $user_time_notifications_thresholds
                            as $user_time_notifications_threshold
                        ) {
                            $send_notification = false;

                            if (
                                $days_until_event_when_subscribed <=
                                $user_time_notifications_threshold
                            ) {
                                continue;
                            }

                            if (
                                $last_notification &&
                                $event_date->diffInDays(
                                    Carbon::parse(
                                        $last_notification->created_at,
                                    ),
                                ) <= $user_time_notifications_threshold
                            ) {
                                continue;
                            }

                            if (
                                !$last_notification &&
                                $days_until_event <=
                                    $user_time_notifications_threshold
                            ) {
                                $send_notification = true;
                            } elseif ($last_notification) {
                                $days_until_event_when_notified = $event_date->diffInDays(
                                    Carbon::parse(
                                        $last_notification->created_at,
                                    ),
                                );
                                if (
                                    $days_until_event <
                                        $days_until_event_when_notified &&
                                    $days_until_event <=
                                        $user_time_notifications_threshold
                                ) {
                                    $send_notification = true;
                                }
                            }

                            if ($send_notification) {
                                Log::info(
                                    "User: " .
                                        $user->username .
                                        " notified for event: " .
                                        $event_model_instance->name .
                                        " User days until event: " .
                                        $days_until_event .
                                        " User time notifications thresholds: " .
                                        $user_time_notifications_threshold,
                                );

                                $message = null;
                                if ($days_until_event === 0) {
                                    $message =
                                        'The Event: <a href="' .
                                        $event_model_instance->link .
                                        '"target="blank">' .
                                        $event_model_instance->name .
                                        "</a> starts in less than 24 hours!";
                                } elseif ($days_until_event === 1) {
                                    $message =
                                        'The Event: <a href="' .
                                        $event_model_instance->link .
                                        '"target="blank">' .
                                        $event_model_instance->name .
                                        "</a> starts in " .
                                        $days_until_event .
                                        " day!";
                                } else {
                                    $message =
                                        'The Event: <a href="' .
                                        $event_model_instance->link .
                                        '"target="blank">' .
                                        $event_model_instance->name .
                                        "</a> starts in " .
                                        $days_until_event .
                                        " days!";
                                }

                                $image =
                                    $event_model_instance->image?->url;

                                if (!$image) {
                                    $image = URL::to(
                                        "/storage/map-icons/" .
                                            $event_model_instance->game
                                                ->slug .
                                            ".png",
                                    );
                                }

                                $notification = Notification::create([
                                    "event_id" =>
                                        $event_model_instance->id,
                                    "game_id" =>
                                        $event_model_instance->game_id,
                                    "user_id" => $user->id,
                                    "message" => $message,
                                    "type" => NotificationTypeEnum::TIME,
                                    "image_url" => $image,
                                ]);
                                $user->notify(
                                    new PushNotification($notification),
                                );
                                break;
                            }
                        }
                    }
                }
            }

            $is_online =
                ($event->locationType ?? $tournament->locationType) ===
                "LOCATION_TYPE_ONLINE";
            $name = trim($event->name);
            $timezone = $tournament->timeZone ?? null;
            if ($timezone) {
                try {
                    $timezone = Carbon::now(
                        new DateTimeZone($timezone),
                    )->format("P");
                } catch (Exception $e) {
                    $timezone = "+00:00";
                }
            }
            if (!$timezone) {
                $timezone = "+00:00";
            }

            $start_date = new DateTime($event->startDate);
            $epoch = new DateTime("1970-01-02T00:00:00Z");
            if ($start_date < $epoch) {
                $start_date = new DateTime($tournament->startDate);
            }

            $end_date = new DateTime($tournament->endDate);

            $start_ts = $start_date->format("Y-m-d H:i:s");
            $end_ts = $end_date->format("Y-m-d H:i:s");
            $now_ts = Carbon::now("UTC")->format("Y-m-d H:i:s");

            if ($start_date > $end_date) {
                dump("  Event #{$ei} ({$event->name}): skipped, start_date ({$start_ts}) > end_date ({$end_ts})");
                continue;
            }

            if ($end_date < Carbon::now("UTC")) {
                dump("  Event #{$ei} ({$event->name}): skipped, end_date ({$end_ts}) < now ({$now_ts})");
                continue;
            }

            dump("  Event #{$ei} ({$event->name}): dates OK (start={$start_ts}, end={$end_ts}), proceeding to import");

            $attendees =
                $event->entrantCount ?? $event->entrantSize ?? 0;

            $tournament_slug = null;
            if (isset($tournament->slugs)) {
                foreach ($tournament->slugs as $slug) {
                    if ($slug->type === "SLUG_TYPE_PRIMARY") {
                        $tournament_slug = $slug->slug;
                        break;
                    }
                }
            }
            if (!$tournament_slug && isset($tournament->slugs[0])) {
                $tournament_slug = $tournament->slugs[0]->slug;
            }
            $event_slug = $event->slug;
            $link = null;
            if ($tournament_slug && $event_slug) {
                $link =
                    "https://parry.gg/tournaments/{$tournament_slug}/events/{$event_slug}";
            }

            $should_import = $import_all_events ||
                !$event_model_instance ||
                ($event_model_instance->show &&
                    ($event_model_instance->parry_gg_updated_at <
                        $parry_gg_updated_at ||
                        $event_model_instance->attendees < $attendees));

            if ($should_import) {
                if ($event_model_instance) {
                    dump("  Event #{$ei} ({$event->name}): UPDATING (parry_gg_id={$parry_gg_id})");
                    $event_model_instance->update([
                        "parry_gg_updated_at" => $parry_gg_updated_at,
                        "is_online" => $is_online,
                        "name" => $name,
                        "timezone" => $timezone,
                        "start_date_time" => $start_date,
                        "end_date_time" => $end_date,
                        "attendees" => $attendees,
                        "link" => $link,
                        "game_id" => $game->id,
                    ]);
                    $updatedCount++;
                } else {
                    dump("  Event #{$ei} ({$event->name}): CREATING (parry_gg_id={$parry_gg_id})");
                    $event_model_instance = Event::create([
                        "uuid" => Str::uuid()->toString(),
                        "parry_gg_id" => $parry_gg_id,
                        "parry_gg_updated_at" => $parry_gg_updated_at,
                        "game_id" => $game->id,
                        "is_online" => $is_online,
                        "name" => $name,
                        "timezone" => $timezone,
                        "start_date_time" => $start_date,
                        "end_date_time" => $end_date,
                        "attendees" => $attendees,
                        "link" => $link,
                    ]);
                    $createdCount++;
                }

                if (
                    !$is_online &&
                    isset($tournament->address)
                ) {
                    $addr = $tournament->address;
                    $latitude = round($addr->latitude ?? 0, 7);
                    $longitude = round($addr->longitude ?? 0, 7);

                    $country_code = $addr->countryCode ?? null;
                    $country = null;
                    if ($country_code) {
                        $country = Country::where(
                            "code",
                            $country_code,
                        )->first();
                    }

                    if (!$country) {
                        Log::error(
                            "Country not found: " .
                                ($country_code ?? "null") .
                                " for Parry.gg event: " .
                                $event->name,
                        );
                    }

                    $address_name =
                        $addr->formattedAddress ??
                        $tournament->venueAddress ??
                        null;

                    $address = $event_model_instance->address;

                    if (!$address && $latitude && $longitude) {
                        $address = Address::firstOrCreate(
                            [
                                "latitude" => $latitude,
                                "longitude" => $longitude,
                            ],
                            [
                                "name" => $address_name,
                                "country_id" => $country?->id,
                            ],
                        );
                        $event_model_instance->address_id =
                            $address->id;
                        $event_model_instance->save();
                    }
                }

                $event_directory_path =
                    "/events-images/" . $event_model_instance->uuid;

                if (isset($tournament->images)) {
                    $image = null;
                    foreach ($tournament->images as $tournament_image) {
                        if (
                            $tournament_image->type ===
                            "IMAGE_TYPE_PROFILE"
                        ) {
                            $image = $tournament_image;
                            break;
                        } elseif (
                            $tournament_image->type ===
                            "IMAGE_TYPE_BANNER"
                        ) {
                            $image = $tournament_image;
                        }
                    }

                    if ($image) {
                        $image_type =
                            $image->type === "IMAGE_TYPE_PROFILE"
                                ? ImageTypeEnum::EVENT_PROFILE
                                : ImageTypeEnum::EVENT_BANNER;

                        $image_file = null;
                        try {
                            $image_file = file_get_contents($image->url);
                        } catch (Exception $e) {
                            Log::error(
                                "Parry.gg image couldn't be retrieved: " .
                                    $image->url .
                                    " for event: " .
                                    $event->name,
                            );
                        }

                        if ($image_file) {
                            $image_md5 = md5($image_file);
                            $event_model_instance_old_image =
                                $event_model_instance->image;

                            if (
                                !$import_all_events &&
                                $event_model_instance_old_image &&
                                $event_model_instance_old_image->md5 ===
                                    $image_md5
                            ) {
                                continue;
                            }

                            $isImageStored = Storage::put(
                                $event_directory_path .
                                    "/" .
                                    $image_type .
                                    ".png",
                                $image_file,
                            );
                            if ($isImageStored) {
                                Image::updateOrCreate(
                                    [
                                        "parentable_type" => Event::class,
                                        "parentable_id" =>
                                            $event_model_instance->id,
                                        "extension" => "png",
                                    ],
                                    [
                                        "origin" => $image->url,
                                        "type" => $image_type,
                                        "md5" => $image_md5,
                                    ],
                                );
                            } else {
                                Log::error(
                                    "Parry.gg image couldn't be stored: " .
                                        $image->url .
                                        " for event: " .
                                        $event->name,
                                );
                            }
                        }
                    }
                }

                if (
                    $event_model_instance->wasRecentlyCreated &&
                    !$event_model_instance->is_online &&
                    isset($address)
                ) {
                    foreach (
                        User::where(
                            "has_distance_notifications",
                            true,
                        )->get()
                        as $user
                    ) {
                        if (
                            $user->games->contains(
                                $event_model_instance->game_id,
                            )
                        ) {
                            $distance_notifications_radius =
                                $user->distance_notifications_radius;
                            $distance = $address->distanceToKM(
                                $user->address,
                            );

                            if (
                                $distance <=
                                $distance_notifications_radius
                            ) {
                                Log::info(
                                    "User: " .
                                        $user->username .
                                        " notified for event: " .
                                        $event_model_instance->name .
                                        " User to event distance: " .
                                        round($distance) .
                                        " User distance notifications radius: " .
                                        $distance_notifications_radius,
                                );
                                $message =
                                    'The Event: <a href="' .
                                    $event_model_instance->link .
                                    '"target="blank">' .
                                    $event_model_instance->name .
                                    '</a> is happening near you, it\'s only ' .
                                    round($distance) .
                                    " kilometers away!";

                                $event_model_instance->load("image");
                                $image =
                                    $event_model_instance->image?->url;
                                if (!$image) {
                                    $image = URL::to(
                                        "/storage/map-icons/" .
                                            $event_model_instance->game
                                                ->slug .
                                            ".png",
                                    );
                                }
                                $notification = Notification::create([
                                    "event_id" =>
                                        $event_model_instance->id,
                                    "game_id" =>
                                        $event_model_instance->game_id,
                                    "user_id" => $user->id,
                                    "message" => $message,
                                    "type" =>
                                        NotificationTypeEnum::DISTANCE,
                                    "image_url" => $image,
                                ]);
                                $user->notify(
                                    new PushNotification($notification),
                                );
                            }
                        }
                    }
                }
            } else {
                dump("  Event #{$ei} ({$event->name}): skipped (should_import=false)");
            }
        }
    }

    $hasMore = $response->paginationResponse->hasMore ?? false;
    $nextCursor = $response->paginationResponse->nextCursor->id ?? null;

    if ($hasMore && $nextCursor) {
        $cursor = $nextCursor;
    }
} while ($hasMore && $nextCursor);

dump("Import complete - total fetched: {$totalFetched}, created: {$createdCount}, updated: {$updatedCount}");
});

Artisan::command("delete-addresses", function () {
    $addresses = Address::all();
    foreach ($addresses as $address) {
        $event_count = $address
            ->events()
            ->withoutGlobalScope(ShownScope::class)
            ->count();
        $user_count = $address->users->count();

        if ($event_count === 0 && $user_count === 0) {
            Log::info("Address: " . $address->name . " deleted");
            //            var_dump('Address: ' . $address->name . ' deleted');
            $address->delete();
        }
    }
});

// TODO Test correctly
Artisan::command("delete-events", function () {
    $events = Event::all();
    $current_time = Carbon::now("UTC");
    foreach ($events as $event) {
        $end_date_time = Carbon::parse($event->end_date_time, "UTC");
        if ($end_date_time < $current_time) {
            // The image belongs to the event: delete it with the event.
            // Notifications pointing to it fall back to the game icon
            // (see Notification::getImageUrlLabelAttribute).
            File::deleteDirectory(
                base_path() . "/storage/app/public/events-images/" . $event->uuid,
            );
            if ($event->image) {
                $event->image->delete();
                Log::info("Image for: " . $event->name . " deleted");
            }
            $event->delete();
            Log::info("Event: " . $event->name . " deleted");
            //            var_dump('Event: ' . $event->name . ' deleted');
        }
    }
});

Artisan::command(
    "import-25-events {game_id} {import_all_events?} {page?}",
    function (string $game_id, bool $import_all_events = false, int $page = 1) {
        $apiToken = env("START_GG_API_KEY");

        $endpointUrl = "https://api.start.gg/gql/alpha";
        $query = 'query TournamentsByVideogame($videogameId: ID!, $page: Int!) {
      tournaments(query: {
        sortBy: "startAt asc"
        perPage: 25
        page: $page
        filter: {
          upcoming: true
          videogameIds: [$videogameId]
        }
      }) {
        nodes {
          id
          updatedAt
          name
          lat
          lng
          venueAddress
          countryCode
          endAt
          url
          timezone
          images {
            url
            type
          }
          events(filter:{
            videogameId: [$videogameId]
  		    })
            {
            numEntrants
            startAt
            isOnline
            type
      }
    }
  }
}';

        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer " . $apiToken,
        ];

        $data = [
            "query" => $query,
            "variables" => ["videogameId" => $game_id, "page" => $page],
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $endpointUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $raw_response = curl_exec($ch);

        $response = json_decode($raw_response);

        if (curl_errno($ch)) {
            echo "Error: " . curl_error($ch);
        }

        curl_close($ch);
        $data = $response?->data ?? null;
        if (!$data) {
            Log::error("No data found");
            Log::error("Raw response : " . $raw_response);
            Log::error("Response in json : " . json_encode($response));
            if (!$response) {
                Log::info("Retrying in 1 second");
                //            var_dump('Retrying in 1 second');
                sleep(1);
                Artisan::call("import-25-events", [
                    "game_id" => $game_id,
                    "import_all_events" => $import_all_events,
                    "page" => $page,
                ]);
                return;
            } else {
                throw new Exception("No data found");
            }
            //        var_dump('No data found');
            //        var_dump('Response : ' . json_encode($response));
        }

        $events = $data->tournaments?->nodes;

        if (!$events) {
            Log::info("No events found");
            Log::info("Response : " . json_encode($response));
        } else {
            foreach ($events as $event) {
                $start_gg_id = $event->id;
                $start_gg_updated_at = new DateTime();
                $start_gg_updated_at->setTimestamp($event->updatedAt);
                $start_gg_updated_at = $start_gg_updated_at->format(
                    "Y-m-d H:i:s",
                );

                $game_event = null;
                if ($event->events) {
                    $single_events = array_values(
                        array_filter($event->events, function ($event) {
                            return $event->type === 1;
                        }),
                    );

                    if ($single_events) {
                        $game_event = array_reduce(
                            $single_events,
                            function ($carry, $event) {
                                if ($event->numEntrants > $carry->numEntrants) {
                                    return $event;
                                }
                                return $carry;
                            },
                            $single_events[0],
                        );
                    }
                }

                if ($game_event) {
                    $event_model_instance = Event::withoutGlobalScope(
                        ShownScope::class,
                    )
                        ->where("start_gg_id", $start_gg_id)
                        ->where("game_id", $game_id)
                        ->first();
                    $event_old_attendees = null;
                    if ($event_model_instance && $event_model_instance->show) {
                        foreach (
                            $event_model_instance->subscribed_users()->get()
                            as $user
                        ) {
                            if ($user->has_attendees_notifications) {
                                $event_attendees_when_subscribed =
                                    $user->pivot->original_attendees;
                                $event_old_attendees =
                                    $event_model_instance->attendees;
                                $event_attendees = $game_event->numEntrants;

                                $last_notification = $user
                                    ->notifications()
                                    ->where(
                                        "event_id",
                                        $event_model_instance->id,
                                    )
                                    ->where(
                                        "type",
                                        NotificationTypeEnum::ATTENDEES,
                                    )
                                    ->orderBy("created_at", "desc")
                                    ->first();
                                $user_attendees_notifications_thresholds =
                                    $user->attendees_notifications_thresholds;

                                foreach (
                                    $user_attendees_notifications_thresholds
                                    as $user_attendees_notifications_threshold
                                ) {
                                    if (
                                        $event_attendees_when_subscribed >=
                                        $user_attendees_notifications_threshold
                                    ) {
                                        continue;
                                    }

                                    if (
                                        $last_notification &&
                                        $last_notification->attendees >=
                                            $user_attendees_notifications_threshold
                                    ) {
                                        continue;
                                    }

                                    $send_notification = false;
                                    if (
                                        !$last_notification &&
                                        $event_attendees >=
                                            $user_attendees_notifications_threshold
                                    ) {
                                        $send_notification = true;
                                    } elseif (
                                        $last_notification &&
                                        $event_attendees >
                                            $last_notification->attendees &&
                                        $event_attendees >=
                                            $user_attendees_notifications_threshold
                                    ) {
                                        $send_notification = true;
                                    }

                                    if ($send_notification) {
                                        Log::info(
                                            "User: " .
                                                $user->username .
                                                " notified for event: " .
                                                $event_model_instance->name .
                                                " User old attendees: " .
                                                $event_old_attendees .
                                                " User new attendees: " .
                                                $event_attendees .
                                                " User attendees notifications thresholds: " .
                                                $user_attendees_notifications_threshold,
                                        );
                                        //                                    var_dump('User: ' . $user->username . ' notified for event: ' . $event_model_instance->name . ' User old attendees: ' . $event_old_attendees . ' User new attendees: ' . $event_attendees . ' User attendees notifications thresholds: ' . $user_attendees_notifications_threshold);
                                        $message =
                                            'The Event: <a href="' .
                                            $event_model_instance->link .
                                            '"target="blank">' .
                                            $event_model_instance->name .
                                            "</a> has reached " .
                                            $event_attendees .
                                            " attendees!";
                                        $image =
                                            $event_model_instance->image?->url;

                                        if (!$image) {
                                            $image = URL::to(
                                                "/storage/map-icons/" .
                                                    $event_model_instance->game
                                                        ->slug .
                                                    ".png",
                                            );
                                        }
                                        $notification = Notification::create([
                                            "event_id" =>
                                                $event_model_instance->id,
                                            "game_id" =>
                                                $event_model_instance->game_id,
                                            "user_id" => $user->id,
                                            "message" => $message,
                                            "type" =>
                                                NotificationTypeEnum::ATTENDEES,
                                            "image_url" => $image,
                                            "attendees" => $event_attendees,
                                        ]);
                                        //                                    broadcast(new NotificationEvent($notification, $user));
                                        $user->notify(
                                            new PushNotification($notification),
                                        );
                                        break;
                                    }
                                }
                            }

                            if ($user->has_time_notifications) {
                                $event_date = Carbon::parse(
                                    $event_model_instance->start_date_time,
                                );
                                $days_until_event = $event_date->diffInDays(
                                    Carbon::now(),
                                );

                                $event_subscription_date = Carbon::parse(
                                    $user->pivot->created_at,
                                );
                                $days_until_event_when_subscribed = $event_date->diffInDays(
                                    $event_subscription_date,
                                );

                                $user_time_notifications_thresholds =
                                    $user->time_notifications_thresholds;

                                $last_notification = $user
                                    ->notifications()
                                    ->where(
                                        "event_id",
                                        $event_model_instance->id,
                                    )
                                    ->where("type", NotificationTypeEnum::TIME)
                                    ->orderBy("created_at", "desc")
                                    ->first();
                                foreach (
                                    $user_time_notifications_thresholds
                                    as $user_time_notifications_threshold
                                ) {
                                    $send_notification = false;

                                    if (
                                        $days_until_event_when_subscribed <=
                                        $user_time_notifications_threshold
                                    ) {
                                        continue;
                                    }

                                    if (
                                        $last_notification &&
                                        $event_date->diffInDays(
                                            Carbon::parse(
                                                $last_notification->created_at,
                                            ),
                                        ) <= $user_time_notifications_threshold
                                    ) {
                                        continue;
                                    }

                                    if (
                                        !$last_notification &&
                                        $days_until_event <=
                                            $user_time_notifications_threshold
                                    ) {
                                        $send_notification = true;
                                    } elseif ($last_notification) {
                                        $days_until_event_when_notified = $event_date->diffInDays(
                                            Carbon::parse(
                                                $last_notification->created_at,
                                            ),
                                        );
                                        if (
                                            $days_until_event <
                                                $days_until_event_when_notified &&
                                            $days_until_event <=
                                                $user_time_notifications_threshold
                                        ) {
                                            $send_notification = true;
                                        }
                                    }

                                    if ($send_notification) {
                                        Log::info(
                                            "User: " .
                                                $user->username .
                                                " notified for event: " .
                                                $event_model_instance->name .
                                                " User days until event: " .
                                                $days_until_event .
                                                " User time notifications thresholds: " .
                                                $user_time_notifications_threshold,
                                        );
                                        //                                    var_dump('User: ' . $user->username . ' notified for event: ' . $event_model_instance->name . ' User days until event: ' . $days_until_event . ' User time notifications thresholds: ' . $user_time_notifications_threshold);

                                        $message = null;
                                        if ($days_until_event === 0) {
                                            $message =
                                                'The Event: <a href="' .
                                                $event_model_instance->link .
                                                '"target="blank">' .
                                                $event_model_instance->name .
                                                "</a> starts in less than 24 hours!";
                                        } elseif ($days_until_event === 1) {
                                            $message =
                                                'The Event: <a href="' .
                                                $event_model_instance->link .
                                                '"target="blank">' .
                                                $event_model_instance->name .
                                                "</a> starts in " .
                                                $days_until_event .
                                                " day!";
                                        } else {
                                            $message =
                                                'The Event: <a href="' .
                                                $event_model_instance->link .
                                                '"target="blank">' .
                                                $event_model_instance->name .
                                                "</a> starts in " .
                                                $days_until_event .
                                                " days!";
                                        }

                                        $image =
                                            $event_model_instance->image?->url;

                                        if (!$image) {
                                            $image = URL::to(
                                                "/storage/map-icons/" .
                                                    $event_model_instance->game
                                                        ->slug .
                                                    ".png",
                                            );
                                        }

                                        $notification = Notification::create([
                                            "event_id" =>
                                                $event_model_instance->id,
                                            "game_id" =>
                                                $event_model_instance->game_id,
                                            "user_id" => $user->id,
                                            "message" => $message,
                                            "type" =>
                                                NotificationTypeEnum::TIME,
                                            "image_url" => $image,
                                        ]);
                                        //                                    broadcast(new NotificationEvent($notification, $user));
                                        $user->notify(
                                            new PushNotification($notification),
                                        );
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    if (
                        $import_all_events ||
                        !$event_model_instance ||
                        ($event_model_instance->show &&
                            ($event_model_instance->start_gg_updated_at <
                                $start_gg_updated_at ||
                                $event_model_instance->attendees <
                                    $game_event->numEntrants))
                    ) {
                        $is_online = $game_event->isOnline;
                        $name = $event->name;
                        $timezone = $event->timezone;
                        if ($timezone) {
                            $timezone = Carbon::now(
                                new DateTimeZone($timezone),
                            )->format("P");
                        }
                        if (!$timezone) {
                            $timezone = "+00:00";
                        }

                        $start_date = new DateTime();
                        $start_date->format("Y-m-d H:i:s");

                        $start_date->setTimestamp($game_event->startAt);

                        $end_date = new DateTime();
                        $end_date->format("Y-m-d H:i:s");
                        $end_date->setTimestamp($event->endAt);

                        if (
                            $start_date > $end_date ||
                            $end_date < Carbon::now("UTC")
                        ) {
                            continue;
                        }

                        $attendees = $game_event->numEntrants;

                        $link = "https://www.start.gg" . $event->url;

                        if ($event_model_instance) {
                            $event_model_instance->update([
                                "start_gg_updated_at" => $start_gg_updated_at,
                                "is_online" => $is_online,
                                "name" => $name,
                                "timezone" => $timezone,
                                "start_date_time" => $start_date,
                                "end_date_time" => $end_date,
                                "attendees" => $attendees,
                                "link" => $link,
                            ]);
                            Log::info("Event: " . $event->name . " updated");
                            //                        var_dump('Event: ' . $event->name . ' updated');
                        } else {
                            $event_model_instance = Event::create([
                                "uuid" => Str::uuid()->toString(),
                                "start_gg_id" => $start_gg_id,
                                "game_id" => $game_id,
                                "start_gg_updated_at" => $start_gg_updated_at,
                                "is_online" => $is_online,
                                "name" => $name,
                                "timezone" => $timezone,
                                "start_date_time" => $start_date,
                                "end_date_time" => $end_date,
                                "attendees" => $attendees,
                                "link" => $link,
                            ]);
                            Log::info("Event: " . $event->name . " created");
                            //                        var_dump('Event: ' . $event->name . ' created');
                        }
                        if (!$event_model_instance->is_online) {
                            $latitude = $event->lat;
                            $latitude = round($latitude, 7);

                            $longitude = $event->lng;
                            $longitude = round($longitude, 7);

                            $address_name = $event->venueAddress;
                            $country_code = $event->countryCode;

                            $country = Country::where(
                                "code",
                                $country_code,
                            )->first();

                            # Handle the Oceania case
                            if (!$country) {
                                Log::error(
                                    "Country not found : " .
                                        $country_code .
                                        " for event: " .
                                        $event->name .
                                        " with start gg id: " .
                                        $start_gg_id,
                                );
                                //                            var_dump('Country not found : ' . $country_code . ' for event: ' . $event->name .' with start gg id: ' . $start_gg_id);
                                die();
                            }

                            $address = $event_model_instance->address;

                            if (!$address) {
                                $address = Address::findOrCreateNear(
                                    $latitude,
                                    $longitude,
                                    [
                                        "name" => $address_name,
                                        "country_id" => $country?->id,
                                    ],
                                );
                                $event_model_instance->address_id =
                                    $address->id;
                                $event_model_instance->save();
                            }
                        }

                        $event_directory_path =
                            "/events-images/" . $event_model_instance->uuid;

                        //                $event_db_md5s = $event_model_instance->images->pluck('md5')->toArray();

                        #TODO Delete the unused images (inside event_db_md5s but not in event_md5s)

                        //            $event_md5s = [];

                        $images = $event->images;

                        $image = null;
                        foreach ($images as $event_image) {
                            if ($event_image->type === "profile") {
                                $image = $event_image;
                                break;
                            } elseif ($event_image->type === "banner") {
                                $image = $event_image;
                            }
                        }

                        if ($image) {
                            $image_type =
                                $image->type == "profile"
                                    ? ImageTypeEnum::EVENT_PROFILE
                                    : ImageTypeEnum::EVENT_BANNER;

                            $image_file = null;
                            try {
                                $image_file = file_get_contents($image->url);
                            } catch (Exception $e) {
                                Log::error(
                                    "Event" .
                                        $event->name .
                                        " : Image " .
                                        $image->url .
                                        " couldn't be retrieved",
                                );
                            }
                            if ($image_file) {
                                $image_md5 = md5($image_file);
                                $event_model_instance_old_image =
                                    $event_model_instance->image;

                                if (
                                    !$import_all_events &&
                                    $event_model_instance_old_image &&
                                    $event_model_instance_old_image->md5 ===
                                        $image_md5
                                ) {
                                    continue;
                                }

                                $isImageStored = Storage::put(
                                    $event_directory_path .
                                        "/" .
                                        $image_type .
                                        ".png",
                                    $image_file,
                                );
                                if ($isImageStored) {
                                    Image::updateOrCreate(
                                        [
                                            "parentable_type" => Event::class,
                                            "parentable_id" =>
                                                $event_model_instance->id,
                                            "extension" => "png",
                                        ],
                                        [
                                            "origin" => $image->url,
                                            "type" => $image_type,
                                            "md5" => $image_md5,
                                        ],
                                    );
                                    Log::info(
                                        $image_type .
                                            " image for:" .
                                            $event->name .
                                            " created",
                                    );
                                    //                                var_dump($image_type .' image for:' . $event->name . ' created');
                                } else {
                                    Log::error(
                                        "Image " .
                                            $image->url .
                                            " couldn't be stored stored",
                                    );
                                    //                                var_dump("Image ". $image->url . " couldn't be stored stored");
                                    die();
                                }
                            }
                        }

                        if (
                            $event_model_instance->wasRecentlyCreated &&
                            !$event_model_instance->is_online
                        ) {
                            foreach (
                                User::where(
                                    "has_distance_notifications",
                                    true,
                                )->get()
                                as $user
                            ) {
                                if (
                                    $user->games->contains(
                                        $event_model_instance->game_id,
                                    )
                                ) {
                                    $distance_notifications_radius =
                                        $user->distance_notifications_radius;
                                    $distance = $address->distanceToKM(
                                        $user->address,
                                    );

                                    if (
                                        $distance <=
                                        $distance_notifications_radius
                                    ) {
                                        Log::info(
                                            "User: " .
                                                $user->username .
                                                " notified for event: " .
                                                $event_model_instance->name .
                                                " User to event distance: " .
                                                round($distance) .
                                                " User distance notifications radius: " .
                                                $distance_notifications_radius,
                                        );
                                        //                                    var_dump('User: ' . $user->username . ' notified for event: ' . $event_model_instance->name . ' User to event distance: ' . round($distance) . ' User distance notifications radius: ' . $distance_notifications_radius);
                                        $message =
                                            'The Event: <a href="' .
                                            $event_model_instance->link .
                                            '"target="blank">' .
                                            $event_model_instance->name .
                                            '</a> is happening near you, it\'s only ' .
                                            round($distance) .
                                            " kilometers away!";

                                        // Load the image relationship, it's not available it the event was recently created
                                        $event_model_instance->load("image");
                                        $image =
                                            $event_model_instance->image?->url;
                                        if (!$image) {
                                            $image = URL::to(
                                                "/storage/map-icons/" .
                                                    $event_model_instance->game
                                                        ->slug .
                                                    ".png",
                                            );
                                        }
                                        $notification = Notification::create([
                                            "event_id" =>
                                                $event_model_instance->id,
                                            "game_id" =>
                                                $event_model_instance->game_id,
                                            "user_id" => $user->id,
                                            "message" => $message,
                                            "type" =>
                                                NotificationTypeEnum::DISTANCE,
                                            "image_url" => $image,
                                        ]);
                                        $user->notify(
                                            new PushNotification($notification),
                                        );
                                        //                                    broadcast(new NotificationEvent($notification, $user));
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    },
);

Artisan::command("import-25-events-all-games {import_all_events?", function (
    bool $import_all_events = false,
) {
    foreach (Game::all() as $game) {
        Log::info("Starting the " . $game->name . " import");
        //        var_dump('Starting the ' . $game->name . ' import');
        Artisan::call("import-25-events", [
            "game_id" => $game->id,
            "import_all_events" => $import_all_events,
        ]);
    }
});

Artisan::command("import-500-events {game_id} {import_all_events?}", function (
    string $game_id,
    bool $import_all_events = false,
) {
    foreach (range(1, 20) as $page) {
        Artisan::call("import-25-events", [
            "game_id" => $game_id,
            "import_all_events" => $import_all_events,
            "page" => $page,
        ]);
        sleep(1);
    }
});

Artisan::command("import-500-events-all-games {import_all_events?}", function (
    bool $import_all_events = false,
) {
    foreach (Game::all() as $game) {
        Log::info("Starting the " . $game->name . " import");
        var_dump("Starting the " . $game->name . " import");
        Artisan::call("import-500-events", [
            "game_id" => $game->id,
            "import_all_events" => $import_all_events,
        ]);
    }
});

Artisan::command("import-characters-images", function () {
    $characters = Character::all();

    foreach ($characters as $character) {
        Image::Create([
            "parentable_type" => Character::class,
            "parentable_id" => $character->id,
            "type" => ImageTypeEnum::ICON,
            "extension" => "png",
        ]);
        //        var_dump('Image for: ' . $character->name . ' created');
    }
});

##TODO Add the image for Frencch Guiana
Artisan::command("import-countries-images", function () {
    //    $url = 'https://flagsapi.com/' . $country->code .'/flat/64.png';
    //
    //    $image = file_get_contents($url);
    //    $uuid = Str::uuid()->toString() . '.png';
    //    $image_md5 = md5($image);
    //
    //    Image::Create(['parentable_type' =>Country::class, 'parentable_id' =>$country->id, 'type' => ImageTypeEnum::ICON, 'uuid' => $uuid, 'md5' => $image_md5]);

    $countries = Country::where("has_image", true)->get();

    foreach ($countries as $country) {
        Image::Create([
            "parentable_type" => Country::class,
            "parentable_id" => $country->id,
            "type" => ImageTypeEnum::ICON,
            "extension" => "png",
        ]);
        //        var_dump('Image for: ' . $country->name . ' created');
    }
});

Artisan::command("setup", function () {
    Artisan::call("import-countries-images");
    Artisan::call("import-characters-images");
    //   Artisan::call('import-25-events-all-games');
});

Artisan::command("test-push-notification {username}", function (
    string $username,
) {
    $notification = Notification::first();
    $user = User::where("username", $username)->first();

    $user->notify(new PushNotification($notification));
});

Artisan::command("test-ws-notification {username}", function (
    string $username,
) {
    $notification = Notification::first();
    $user = User::where("username", $username)->first();
    broadcast(new NotificationEvent($notification, $user));
});

Artisan::command("move-events-images", function () {
    $events = Event::all();
    foreach ($events as $event) {
        $event_file_slug =
            Str::slug($event->name) ?? Str::slug("id-" . $event->id);
        $old_event_directory_path = "/events-images/" . $event_file_slug;
        $new_event_directory_path = "/events-images/" . $event->uuid;
        $image = $event->image;

        if ($image) {
            $image_old_path =
                $old_event_directory_path .
                "/" .
                $image->type .
                "." .
                $image->extension;

            if (
                file_exists(
                    base_path() . "/storage/app/public" . $image_old_path,
                )
            ) {
                Storage::move(
                    $old_event_directory_path,
                    $new_event_directory_path,
                );
            }
        }
    }
});

Artisan::command("reload-events-images", function () {
    $events = Event::all();
    $number_of_events = $events->count();
    $event_counter = 0;
    $fail_counter = 0;

    foreach ($events as $event) {
        var_dump(
            "Reloading image for event " .
                $event_counter .
                " out of " .
                $number_of_events,
        );

        $start_gg_id = $event->start_gg_id;
        $apiToken = env("START_GG_API_KEY");
        $endpointUrl = "https://api.start.gg/gql/alpha";

        $query = 'query TournamentQuery($id: ID) {
            tournament(id: $id) {
                images {
                url
                type
                }
            }
        }';

        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer " . $apiToken,
        ];

        $data = [
            "query" => $query,
            "variables" => ["id" => $start_gg_id],
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $endpointUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = json_decode(curl_exec($ch));

        if (curl_errno($ch)) {
            echo "Error: " . curl_error($ch);
        }

        curl_close($ch);

        $data = $response->data ?? null;
        $tournament = $data->tournament ?? null;
        $images = $tournament->images ?? null;

        if ($images) {
            $image = null;
            foreach ($images as $event_image) {
                if ($event_image->type === "profile") {
                    $image = $event_image;
                    break;
                } elseif ($event_image->type === "banner") {
                    $image = $event_image;
                }
            }

            if ($image) {
                $event_directory_path = "/events-images/" . $event->uuid;

                $image_type =
                    $image->type == "profile"
                        ? ImageTypeEnum::EVENT_PROFILE
                        : ImageTypeEnum::EVENT_BANNER;

                $image_file = file_get_contents($image->url);

                Storage::put(
                    $event_directory_path . "/" . $image_type . ".png",
                    $image_file,
                );

                $image_md5 = md5($image_file);

                Image::updateOrCreate(
                    [
                        "parentable_type" => Event::class,
                        "parentable_id" => $event->id,
                        "extension" => "png",
                        "md5" => $image_md5,
                    ],
                    ["origin" => $image->url, "type" => $image_type],
                );
            }
        } else {
            var_dump("Failed to retrieve image");
            var_dump("Response " . json_encode($response));
        }
        $event_counter += 1;
        var_dump(
            "Failed to reload image for event " .
                $fail_counter .
                " out of " .
                $event_counter,
        );
    }
});

Artisan::command("refresh-events-preserve-following", function () {
    $users = User::all();
    $infos = [];
    foreach ($users as $user) {
        foreach ($user->subscribed_events()->get() as $event) {
            $infos[$user->id][] = [
                "created_at" => $event->pivot->created_at,
                "start_gg_id" => $event->start_gg_id,
                "game_id" => $event->game_id,
                "original_attendees" => $event->pivot->original_attendees,
            ];
        }
    }

    Event::query()->delete();

    Artisan::call("import-500-events-all-games");

    foreach ($infos as $user_id => $followed_events_infos) {
        $user = User::find($user_id);
        foreach ($followed_events_infos as $followed_event_infos) {
            $event = Event::where(
                "start_gg_id",
                $followed_event_infos["start_gg_id"],
            )
                ->where("game_id", $followed_event_infos["game_id"])
                ->first();
            if (!$event) {
                continue;
            }
            $user->subscribed_events()->attach($event, [
                "original_attendees" =>
                    $followed_event_infos["original_attendees"],
                "created_at" => $followed_event_infos["created_at"],
            ]);
        }
    }
});
