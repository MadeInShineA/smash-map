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

    protected $fillable =[
        'name',
        'latitude',
        'longitude',
        'country_id',
        'continent_id'
    ];

    public function events():HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function map_users(): HasMany
    {
        return $this->users()->where('is_on_map', true);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    //TODO Fix the icon size (invisible border)
    public function getIconAttribute(): string
    {
        $users = $this->users()->where('is_on_map', true)->get();
        $event_games = $this->events->pluck('game.slug')->unique()->toArray();


        if(sizeof($users) === 0 ){
            if (sizeof($event_games) == 1){
                return URL::to('/storage/map-icons/' . $event_games[0] . '.png');
            }else{
                return URL::to('/storage/map-icons/events.png');
            }
        }elseif (sizeof($event_games) === 0 && sizeof($users) === 1){
            $user = $users[0];
            return $user->profile_picture->url;
        }elseif(sizeof($event_games) === 0){
            return URL::to('/storage/map-icons/users.svg');
        }elseif (sizeof($users) === 1 && sizeof($event_games) === 1){
            return URL::to('/storage/map-icons/' . $event_games[0] . '-user.png');
        }elseif (sizeof($users) === 1 && sizeof($event_games) > 1){
            return URL::to('/storage/map-icons/events-user.png');
        }elseif (sizeof($users) > 1 && sizeof($event_games) === 1){
            return URL::to('/storage/map-icons/' .$event_games[0] .'-users.png');
        }else{
            return URL::to('/storage/map-icons/events-users.png');
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

        $distance = acos(sin($latitude1) * sin($latitude2) + cos($latitude1) * cos($latitude2) * cos($longitude2 - $longitude1)) * $earth_radius;

        return $distance;
    }
}
