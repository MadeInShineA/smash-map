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

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    //TODO Fix the icon size (invisible border)
    public function getIconAttribute(): string
    {
        $users = $this->users()->get();
        $events = $this->events()->get();

        if(sizeof($users) === 0 ){
            $event_games = $this->events->pluck('game.slug')->unique()->toArray();
            if (sizeof($event_games) == 1){
                return URL::to('/storage/map-icons/' . $event_games[0] . '.png');
            }else{
                return URL::to('/storage/map-icons/events.png');
            }
        }elseif (sizeof($events) === 0 && sizeof($users) === 1){
            $user = $users[0];
            return $user->profile_picture;
        }elseif(sizeof($events) === 0){
            return URL::to('/storage/map-icons/users.svg');
        }elseif (sizeof($users) === 1){
            $event_games = $this->events->pluck('game.slug')->unique()->toArray();
            return URL::to('/storage/map-icons/' . $event_games[0] . '-user.png');
        }else{
            $event_games = $this->events->pluck('game.slug')->unique()->toArray();
            return URL::to('/storage/map-icons/' . $event_games[0] . '-users.png');
        }
    }
}
