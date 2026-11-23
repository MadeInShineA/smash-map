<?php

namespace App\Console\Commands;

use App\Enums\GameEnum;
use App\Enums\ImageTypeEnum;
use App\Enums\NotificationTypeEnum;
use App\Models\Address;
use App\Models\Country;
use App\Models\Event;
use App\Models\Game;
use App\Models\Image;
use App\Models\Notification;
use App\Models\Scopes\ShownScope;
use App\Models\User;
use App\Notifications\PushNotification;
use Carbon\Carbon;
use Exception;
use Throwable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/**
 * Imports upcoming parry.gg tournaments, mirroring the start.gg import:
 * one Smash Map event per (tournament, game), using the biggest singles
 * bracket of that game and the tournament's name.
 *
 * Events are keyed by parry_gg_id (the parry.gg tournament id) + game_id.
 */
class ImportParryGgEvents extends Command
{
    protected $signature = "import-parry-gg-events
        {--all : Update every event even if nothing changed on parry.gg}";

    protected $description = "Import upcoming singles events from parry.gg";

    private const ENDPOINT = "https://grpcweb.parry.gg/parrygg.services.TournamentService/GetTournaments";

    /** parry.gg game names → Smash Map game ids */
    private const GAMES = [
        "Super Smash Bros." => GameEnum::SMASH64,
        "Super Smash Bros. 64" => GameEnum::SMASH64,
        "Super Smash Bros. Melee" => GameEnum::MELEE,
        "Super Smash Bros. Brawl" => GameEnum::BRAWL,
        "Project M" => GameEnum::PROJECT_M,
        "Project+" => GameEnum::PROJECT_PLUS,
        "Super Smash Bros. for Wii U" => GameEnum::SMASH4,
        "Super Smash Bros. for Nintendo 3DS and Wii U" => GameEnum::SMASH4,
        "Super Smash Bros. Ultimate" => GameEnum::ULTIMATE,
        "HDR" => GameEnum::HDR,
        "HewDraw Remix" => GameEnum::HDR,
        "Rivals of Aether 2" => GameEnum::RIVALS2,
        "Rivals of Aether II" => GameEnum::RIVALS2,
    ];

    /** Event names containing one of these are never singles main brackets */
    private const NOT_SINGLES =
        "/\b(doubles|dubs|dubers|teams?|crews?|squads?|duos?|trios|2v2s?|3v3s?|4v4s?|amateurs?|ammy|ammies|redemption|side ?brackets?|randoms|randubs|ladder|low ?tiers?|iron ?man|arena|pools only)\b/i";

    /** Event names containing one of these are singles */
    private const SINGLES = "/\b(singles?|1v1s?)\b/i";

    /** Event names that are only the game's name (e.g. "Melee") also count as singles */
    private const GAME_ONLY_NAMES = [
        "smash", "smash 64", "64", "melee", "brawl", "pm", "project m", "p+",
        "project+", "project plus", "smash 4", "wii u", "ultimate", "smash ultimate",
        "ssbu", "hdr", "hewdraw", "hewdraw remix", "rivals", "rivals 2",
        "rivals of aether 2", "rivals of aether ii", "roa2", "roa 2", "roa ii",
    ];

    private int $created = 0;
    private int $updated = 0;
    private int $duplicates = 0;
    private array $skipped = [];

    public function handle(): int
    {
        $apiKey = config("services.parry_gg.key");
        if (!$apiKey) {
            $this->error("PARRY_GG_API_KEY is not set");
            return self::FAILURE;
        }

        $games = Game::whereIn("id", array_unique(self::GAMES))->get()->keyBy("id");
        $cursor = null;
        $fetched = 0;

        do {
            try {
                $response = Http::timeout(30)
                    ->retry(3, 1000, throw: false)
                    ->withHeaders(["X-API-KEY" => $apiKey])
                    ->post(self::ENDPOINT, [
                        "paginationRequest" => $cursor
                            ? ["cursor" => ["id" => $cursor]]
                            : (object) [],
                    ]);
            } catch (Throwable $e) {
                Log::error("Parry.gg API unreachable: " . $e->getMessage());
                $this->error("Parry.gg API unreachable: " . $e->getMessage());
                return self::FAILURE;
            }

            if (!$response->successful()) {
                Log::error("Parry.gg API error: HTTP " . $response->status() . " " . $response->body());
                $this->error("Parry.gg API error: HTTP " . $response->status());
                return self::FAILURE;
            }

            $data = $response->object();
            $tournaments = $data->tournaments ?? [];
            $fetched += count($tournaments);

            foreach ($tournaments as $tournament) {
                try {
                    $this->importTournament($tournament, $games);
                } catch (Throwable $e) {
                    Log::error("Parry.gg tournament " . ($tournament->id ?? "?") . " failed: " . $e->getMessage());
                    $this->skip("error");
                }
            }

            $hasMore = $data->paginationResponse->hasMore ?? false;
            $cursor = $data->paginationResponse->nextCursor->id ?? null;
        } while ($hasMore && $cursor);

        $summary = "Parry.gg import: {$fetched} tournaments fetched, {$this->created} created, {$this->updated} updated, {$this->duplicates} start.gg duplicates";
        Log::info($summary, ["skipped" => $this->skipped]);
        $this->info($summary);
        if ($this->output->isVerbose()) {
            arsort($this->skipped);
            foreach ($this->skipped as $reason => $count) {
                $this->line("  skipped ({$reason}): {$count}");
            }
        }

        return self::SUCCESS;
    }

    private function skip(string $reason): void
    {
        $this->skipped[$reason] = ($this->skipped[$reason] ?? 0) + 1;
    }

    private function importTournament(object $tournament, $games): void
    {
        if (empty($tournament->events)) {
            $this->skip("no events");
            return;
        }
        if (($tournament->visibilityLevel ?? null) && !str_ends_with($tournament->visibilityLevel, "PUBLIC")) {
            $this->skip("not public");
            $this->hideTournament($tournament->id);
            return;
        }
        if (preg_match("/\btest\b/i", $tournament->name ?? "")) {
            $this->skip("test tournament");
            $this->hideTournament($tournament->id);
            return;
        }

        // Per game: the biggest event named as singles ("Melee Singles", "1v1", "Melee"),
        // otherwise the biggest event that is not doubles / teams / a side bracket
        // (organizers often name the main bracket after the tournament)
        $picked = ["singles" => [], "fallback" => []];
        foreach ($tournament->events as $event) {
            $gameId = self::GAMES[$event->game->name ?? ""] ?? null;
            if (!$gameId) {
                $this->skip("not a Smash Map game");
                continue;
            }
            if ($this->isNotSingles($event)) {
                $this->skip("not singles");
                continue;
            }
            $target = $this->isNamedSingles($event) ? "singles" : "fallback";
            $current = $picked[$target][$gameId] ?? null;
            if (!$current || ($event->entrantCount ?? 0) > ($current->entrantCount ?? 0)) {
                $picked[$target][$gameId] = $event;
            }
        }

        foreach ($picked["singles"] + $picked["fallback"] as $gameId => $event) {
            $this->importEvent($tournament, $event, $games[$gameId] ?? null);
        }
    }

    /** Hide events already imported from a tournament that should not be shown anymore */
    private function hideTournament(string $parryId): void
    {
        Event::withoutGlobalScope(ShownScope::class)
            ->where("parry_gg_id", $parryId)
            ->where("show", true)
            ->update(["show" => false]);
    }

    /** Doubles, teams or a side bracket (amateur, redemption, ...) */
    private function isNotSingles(object $event): bool
    {
        return ($event->entrantSize ?? 1) > 1 ||
            preg_match(self::NOT_SINGLES, $this->eventName($event)) === 1;
    }

    /** "Melee Singles", "1v1", or just the game name ("Melee", "Main Bracket") */
    private function isNamedSingles(object $event): bool
    {
        $name = $this->eventName($event);
        if (preg_match(self::SINGLES, $name)) {
            return true;
        }
        $bare = Str::lower(trim(preg_replace("/\s+/", " ", preg_replace("/\b(bracket|main|tournament|event)\b/i", "", $name))));
        return $bare === "" || in_array($bare, self::GAME_ONLY_NAMES, true);
    }

    private function eventName(object $event): string
    {
        return trim(preg_replace("/\s+/", " ", $event->name ?? ""));
    }

    /** parry.gg sends unset dates as 1970-01-01 */
    private function date(?string $value): ?Carbon
    {
        if (!$value) {
            return null;
        }
        $date = Carbon::parse($value)->utc();
        return $date->year < 2000 ? null : $date;
    }

    private function importEvent(object $tournament, object $event, ?Game $game): void
    {
        if (!$game) {
            $this->skip("game missing in DB");
            return;
        }

        $start = $this->date($event->startDate ?? null) ?? $this->date($tournament->startDate ?? null);
        if (!$start) {
            $this->skip("no start date");
            return;
        }
        $end = $this->date($tournament->endDate ?? null);
        if (!$end || $end < $start) {
            // No end date on parry.gg: assume a one-day event
            $end = $start->copy()->addDay();
        }
        if ($end < Carbon::now("UTC")) {
            $this->skip("past");
            return;
        }

        $parryId = $tournament->id;
        $updatedAt = Carbon::parse($tournament->updatedAt ?? "now")->utc();
        $eventUpdatedAt = Carbon::parse($event->updatedAt ?? "now")->utc();
        if ($eventUpdatedAt > $updatedAt) {
            $updatedAt = $eventUpdatedAt;
        }
        $attendees = (int) ($event->entrantCount ?? 0);
        $isOnline = ($event->locationType ?? $tournament->locationType ?? null) === "LOCATION_TYPE_ONLINE";
        $address = $isOnline ? null : $this->address($tournament);

        $instance = Event::withoutGlobalScope(ShownScope::class)
            ->where("parry_gg_id", $parryId)
            ->where("game_id", $game->id)
            ->first();

        // Same tournament also shown from start.gg: keep the start.gg one,
        // unless the start.gg page only points to parry.gg
        $duplicate = $this->findStartGgDuplicate($tournament->name, $game->id, $start, $isOnline, $address);
        if ($duplicate) {
            $this->duplicates++;
            if (preg_match("/parry/i", $duplicate->name)) {
                $duplicate->update(["show" => false]);
                Log::info("start.gg event hidden (hosted on parry.gg): " . $duplicate->name);
            } else {
                if ($instance && $instance->show) {
                    $instance->update(["show" => false]);
                    Log::info("Parry.gg event hidden (duplicate of start.gg): " . $instance->name);
                }
                return;
            }
        }

        // No shown start.gg duplicate anymore (e.g. the start.gg event was
        // hidden): show the parry.gg event again
        if ($instance && !$instance->show) {
            $instance->update(["show" => true]);
            Log::info("Parry.gg event shown again: " . $instance->name);
        }

        if ($instance && $instance->show) {
            $this->sendFollowerNotifications($instance, $attendees);
        }

        $shouldImport = $this->option("all") ||
            !$instance ||
            ($instance->show &&
                ($instance->parry_gg_updated_at < $updatedAt->format("Y-m-d H:i:s") ||
                    $instance->attendees < $attendees));
        if (!$shouldImport) {
            $this->skip("unchanged");
            return;
        }

        $timezone = "+00:00";
        if (!empty($tournament->timeZone)) {
            try {
                $timezone = $start->copy()->setTimezone($tournament->timeZone)->format("P");
            } catch (Exception $e) {
                // keep UTC
            }
        }

        $attributes = [
            "parry_gg_updated_at" => $updatedAt->format("Y-m-d H:i:s"),
            "game_id" => $game->id,
            "is_online" => $isOnline,
            "name" => trim($tournament->name),
            "timezone" => $timezone,
            "start_date_time" => $start->format("Y-m-d H:i:s"),
            "end_date_time" => $end->format("Y-m-d H:i:s"),
            "attendees" => $attendees,
            "link" => $this->link($tournament),
        ];
        if ($address) {
            $attributes["address_id"] = $address->id;
        }

        if ($instance) {
            $instance->update($attributes);
            $this->updated++;
            Log::info("Parry.gg event updated: " . $instance->name);
        } else {
            $instance = Event::create($attributes + [
                "uuid" => Str::uuid()->toString(),
                "parry_gg_id" => $parryId,
            ]);
            $this->created++;
            Log::info("Parry.gg event created: " . $instance->name);
        }

        $this->storeImage($tournament, $instance);

        if ($instance->wasRecentlyCreated && $address) {
            $this->sendDistanceNotifications($instance, $address);
        }
    }

    private function link(object $tournament): ?string
    {
        $slug = null;
        foreach ($tournament->slugs ?? [] as $s) {
            if (($s->type ?? null) === "SLUG_TYPE_PRIMARY") {
                $slug = $s->slug;
                break;
            }
        }
        $slug ??= $tournament->slugs[0]->slug ?? null;
        if (!$slug) {
            return "https://parry.gg/";
        }
        return "https://parry.gg/{$slug}";
    }

    private function address(object $tournament): ?Address
    {
        $addr = $tournament->address ?? null;
        if (!$addr || empty($addr->latitude) || empty($addr->longitude)) {
            return null;
        }

        $country = !empty($addr->countryCode)
            ? Country::where("code", $addr->countryCode)->first()
            : null;
        if (!$country) {
            Log::error("Country not found: " . ($addr->countryCode ?? "null") . " for Parry.gg tournament: " . $tournament->name);
        }

        return Address::findOrCreateNear(
            $addr->latitude,
            $addr->longitude,
            [
                "name" => $addr->formattedAddress ?? $tournament->venueAddress ?? null,
                "country_id" => $country?->id,
            ],
        );
    }

    /**
     * A shown start.gg event of the same game, starting within a day, that is
     * either at (almost) the same place or has (almost) the same name.
     * Hidden start.gg events are ignored, so hiding one shows the parry.gg event.
     */
    private function findStartGgDuplicate(string $name, int $gameId, Carbon $start, bool $isOnline, ?Address $address): ?Event
    {
        $candidates = Event::query()
            ->with("address")
            ->whereNotNull("start_gg_id")
            ->where("game_id", $gameId)
            ->whereBetween("start_date_time", [
                $start->copy()->subDay()->format("Y-m-d H:i:s"),
                $start->copy()->addDay()->format("Y-m-d H:i:s"),
            ])
            ->get();

        $normalize = fn(string $s) => preg_replace("/[^a-z0-9]/", "", Str::lower($s));
        // "Melee Monday #513" and "Melee Monday #62" are different events
        $numbers = fn(string $s) => preg_match_all("/\d+/", $s, $m) ? $m[0] : [];
        $rawName = $name;
        $name = $normalize($name);

        foreach ($candidates as $candidate) {
            $other = $normalize($candidate->name);
            similar_text($name, $other, $percent);
            $sameNumbers = $numbers($rawName) === $numbers($candidate->name);
            if ($name === $other || ($percent >= 85 && $sameNumbers)) {
                return $candidate;
            }
            if (!$isOnline && $address && $candidate->address && $address->distanceToKM($candidate->address) < 0.5) {
                return $candidate;
            }
        }
        return null;
    }

    private function storeImage(object $tournament, Event $instance): void
    {
        $image = null;
        foreach ($tournament->images ?? [] as $candidate) {
            if ($candidate->type === "IMAGE_TYPE_PROFILE") {
                $image = $candidate;
                break;
            }
            if ($candidate->type === "IMAGE_TYPE_BANNER") {
                $image = $candidate;
            }
        }
        if (!$image) {
            return;
        }

        $response = Http::timeout(20)->get($image->url);
        if (!$response->successful()) {
            Log::error("Parry.gg image couldn't be retrieved: {$image->url} for event: {$instance->name}");
            return;
        }
        $file = $response->body();
        $md5 = md5($file);
        if (!$this->option("all") && $instance->image?->md5 === $md5) {
            return;
        }

        $type = $image->type === "IMAGE_TYPE_PROFILE"
            ? ImageTypeEnum::EVENT_PROFILE
            : ImageTypeEnum::EVENT_BANNER;

        if (!Storage::put("/events-images/{$instance->uuid}/{$type}.png", $file)) {
            Log::error("Parry.gg image couldn't be stored: {$image->url} for event: {$instance->name}");
            return;
        }

        Image::updateOrCreate(
            [
                "parentable_type" => Event::class,
                "parentable_id" => $instance->id,
                "extension" => "png",
            ],
            ["origin" => $image->url, "type" => $type, "md5" => $md5],
        );
        $instance->unsetRelation("image");
    }

    private function imageUrl(Event $event): string
    {
        $event->load("image");
        return $event->image?->url
            ?? URL::to("/storage/map-icons/" . $event->game->slug . ".png");
    }

    private function eventLink(Event $event): string
    {
        return '<a href="' . $event->link . '"target="blank">' . $event->name . "</a>";
    }

    private function notify(User $user, Event $event, string $type, string $message, array $extra = []): void
    {
        $notification = Notification::create([
            "event_id" => $event->id,
            "game_id" => $event->game_id,
            "user_id" => $user->id,
            "message" => $message,
            "type" => $type,
            "image_url" => $this->imageUrl($event),
        ] + $extra);
        $user->notify(new PushNotification($notification));
    }

    /** Same rules as the start.gg import (attendees and time thresholds) */
    private function sendFollowerNotifications(Event $event, int $attendees): void
    {
        foreach ($event->subscribed_users()->get() as $user) {
            if ($user->has_attendees_notifications) {
                $whenSubscribed = $user->pivot->original_attendees;
                $last = $user->notifications()
                    ->where("event_id", $event->id)
                    ->where("type", NotificationTypeEnum::ATTENDEES)
                    ->orderBy("created_at", "desc")
                    ->first();

                foreach ($user->attendees_notifications_thresholds as $threshold) {
                    if ($whenSubscribed >= $threshold) {
                        continue;
                    }
                    if ($last && $last->attendees >= $threshold) {
                        continue;
                    }
                    $send = $attendees >= $threshold &&
                        (!$last || $attendees > $last->attendees);
                    if ($send) {
                        Log::info("User: {$user->username} notified for event: {$event->name} User old attendees: {$event->attendees} User new attendees: {$attendees} User attendees notifications thresholds: {$threshold}");
                        $this->notify(
                            $user,
                            $event,
                            NotificationTypeEnum::ATTENDEES,
                            "The Event: " . $this->eventLink($event) . " has reached {$attendees} attendees!",
                            ["attendees" => $attendees],
                        );
                        break;
                    }
                }
            }

            if ($user->has_time_notifications) {
                $eventDate = Carbon::parse($event->start_date_time);
                $daysUntil = $eventDate->diffInDays(Carbon::now());
                $daysWhenSubscribed = $eventDate->diffInDays(Carbon::parse($user->pivot->created_at));
                $last = $user->notifications()
                    ->where("event_id", $event->id)
                    ->where("type", NotificationTypeEnum::TIME)
                    ->orderBy("created_at", "desc")
                    ->first();

                foreach ($user->time_notifications_thresholds as $threshold) {
                    if ($daysWhenSubscribed <= $threshold) {
                        continue;
                    }
                    $daysWhenNotified = $last
                        ? $eventDate->diffInDays(Carbon::parse($last->created_at))
                        : null;
                    if ($last && $daysWhenNotified <= $threshold) {
                        continue;
                    }
                    $send = $daysUntil <= $threshold &&
                        (!$last || $daysUntil < $daysWhenNotified);
                    if ($send) {
                        Log::info("User: {$user->username} notified for event: {$event->name} User days until event: {$daysUntil} User time notifications thresholds: {$threshold}");
                        $when = match (true) {
                            $daysUntil === 0 => "starts in less than 24 hours!",
                            $daysUntil === 1 => "starts in 1 day!",
                            default => "starts in {$daysUntil} days!",
                        };
                        $this->notify(
                            $user,
                            $event,
                            NotificationTypeEnum::TIME,
                            "The Event: " . $this->eventLink($event) . " {$when}",
                        );
                        break;
                    }
                }
            }
        }
    }

    private function sendDistanceNotifications(Event $event, Address $address): void
    {
        foreach (User::where("has_distance_notifications", true)->get() as $user) {
            if (!$user->address || !$user->games->contains($event->game_id)) {
                continue;
            }
            $distance = $address->distanceToKM($user->address);
            if ($distance > $user->distance_notifications_radius) {
                continue;
            }
            Log::info("User: {$user->username} notified for event: {$event->name} User to event distance: " . round($distance) . " User distance notifications radius: {$user->distance_notifications_radius}");
            $this->notify(
                $user,
                $event,
                NotificationTypeEnum::DISTANCE,
                "The Event: " . $this->eventLink($event) . " is happening near you, it's only " . round($distance) . " kilometers away!",
            );
        }
    }
}
