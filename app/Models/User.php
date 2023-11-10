<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\ImageTypeEnum;
use App\Http\Resources\Character\CharacterResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'address_id',
        'discord',
        'twitter',
        'description',
        'color',
        'distance_notifications',
        'attendees_notifications',
        'time_notifications',
        'is_admin',
        'is_subscribed'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    //TODO Add the profile picture url inside the data returned by login / register
    protected $appends = [
        'profile_picture'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function address():BelongsTo
    {
        return $this->BelongsTo(Address::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'parentable');
    }

    public function subscribed_events(): BelongsToMany
    {
        return  $this->belongsToMany(Event::class, 'relation_event_user', 'user_id', 'event_id');
    }

    public function characters(): BelongsToMany
    {
        return  $this->belongsToMany(Character::class, 'relation_user_character', 'user_id', 'character_id');
    }

    public function games(): BelongsToMany
    {
        return  $this->belongsToMany(Game::class, 'relation_user_game', 'user_id', 'game_id');
    }
    public function getProfilePictureAttribute(): string
    {
        $user = User::where('id', $this->id)->first();
        return $user->images->where('type', ImageTypeEnum::USER_PROFILE)->first()->url;
    }

    public function getGamesCharactersArrayAttribute(): array
    {
        $games = $this->games;
        $event_games_array = [];
        foreach ($games as $game) {
            $event_games_array[$game->name] = [
                'name' => $game->name,
                'color' => $game->color,
                'characters' => CharacterResource::collection($this->characters()->where('game_id', $game->id)->get())
            ];

        }
        return $event_games_array;
    }
}
