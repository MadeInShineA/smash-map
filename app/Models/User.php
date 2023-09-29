<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'name',
        'email',
        'password',
        'address_id',
        'main',
        'color',
        'discord',
        'twitter',
        'description',
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
    public function getProfilePictureAttribute()
    {
        return $this->morphMany(Image::class, 'parentable')->where('type', 'profile')->first()?->url_attribute();
    }
}
