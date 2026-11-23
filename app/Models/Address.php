<?php

namespace App\Models;

use App\Enums\ImageTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\URL;
use function Webmozart\Assert\Tests\StaticAnalysis\length;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "latitude",
        "longitude",
        "country_id",
        "continent_id",
    ];

    /** Places closer than this are the same place on the map (one marker) */
    public const SAME_PLACE_METERS = 5;

    /**
     * The closest address within $meters of the coordinates, or a new one.
     *
     * Never match coordinates with "=": MySQL and PHP round doubles
     * differently (18.953136100000002 vs 18.9531361), so an exact
     * firstOrCreate() never finds the row and creates duplicates, which then
     * stack on the map and hide each other.
     */
    public static function findOrCreateNear(
        float $latitude,
        float $longitude,
        array $values = [],
        float $meters = self::SAME_PLACE_METERS,
    ): self {
        $latitude = round($latitude, 7);
        $longitude = round($longitude, 7);

        $existing = self::findNear($latitude, $longitude, $meters);
        if ($existing) {
            return $existing;
        }

        return self::create(
            ["latitude" => $latitude, "longitude" => $longitude] + $values,
        );
    }

    /** The closest address within $meters of the coordinates */
    public static function findNear(
        float $latitude,
        float $longitude,
        float $meters = self::SAME_PLACE_METERS,
        ?int $exceptId = null,
    ): ?self {
        // Bounding box first (1° of latitude ≈ 111 km), then exact distance
        $latitudeDelta = $meters / 111_000;
        $longitudeDelta =
            $meters / (111_000 * max(cos(deg2rad($latitude)), 0.01));

        $point = new self(["latitude" => $latitude, "longitude" => $longitude]);

        return self::query()
            ->whereBetween("latitude", [
                $latitude - $latitudeDelta,
                $latitude + $latitudeDelta,
            ])
            ->whereBetween("longitude", [
                $longitude - $longitudeDelta,
                $longitude + $longitudeDelta,
            ])
            ->when($exceptId, fn($query) => $query->where("id", "!=", $exceptId))
            ->orderBy("id")
            ->get()
            ->filter(fn(self $address) => $address->distanceToKM($point) * 1000 <= $meters)
            ->sortBy(fn(self $address) => $address->distanceToKM($point))
            ->first();
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function map_users(): HasMany
    {
        return $this->users()->where("is_on_map", true);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    //TODO Fix the icon size (invisible border)
    public function getIconAttribute(): string
    {
        $users = $this->users()->where("is_on_map", true)->get();
        $event_games = $this->events->pluck("game.slug")->unique()->toArray();

        if (sizeof($users) === 0) {
            if (sizeof($event_games) == 1) {
                return URL::to(
                    "/storage/map-icons/" . $event_games[0] . ".png",
                );
            } else {
                return URL::to("/storage/map-icons/events.png");
            }
        } elseif (sizeof($event_games) === 0 && sizeof($users) === 1) {
            $user = $users[0];
            return $user->profile_picture->url;
        } elseif (sizeof($event_games) === 0) {
            return URL::to("/storage/map-icons/users.svg");
        } elseif (sizeof($users) === 1 && sizeof($event_games) === 1) {
            return URL::to(
                "/storage/map-icons/" . $event_games[0] . "-user.png",
            );
        } elseif (sizeof($users) === 1 && sizeof($event_games) > 1) {
            return URL::to("/storage/map-icons/events-user.png");
        } elseif (sizeof($users) > 1 && sizeof($event_games) === 1) {
            return URL::to(
                "/storage/map-icons/" . $event_games[0] . "-users.png",
            );
        } else {
            return URL::to("/storage/map-icons/events-users.png");
        }
    }

    public function distanceToKM(Address $address): float
    {
        $earth_radius = 6371;
        $latitude1 = $this->latitude;
        $longitude1 = $this->longitude;
        $latitude2 = $address->latitude;
        $longitude2 = $address->longitude;

        $latitude1 = deg2rad($latitude1);
        $longitude1 = deg2rad($longitude1);
        $latitude2 = deg2rad($latitude2);
        $longitude2 = deg2rad($longitude2);

        $distance =
            acos(
                sin($latitude1) * sin($latitude2) +
                    cos($latitude1) *
                        cos($latitude2) *
                        cos($longitude2 - $longitude1),
            ) * $earth_radius;

        return $distance;
    }
}
