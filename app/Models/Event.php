<?php

namespace App\Models;

use App\Enums\ImageTypeEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\Request;



class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_gg_id',
        'start_gg_updated_at',
        'game_id',
        'address_id',
        'image_id',
        'creator_id',
        'is_online',
        'name',
        'start_date_time',
        'end_date_time',
        'attendees',
        'link',
        'timezone',
    ];

    public function game():BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function address():BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'parentable')->orderByRaw("FIELD(type ,'".ImageTypeEnum::EVENT_PROFILE . "', '" . ImageTypeEnum::EVENT_BANNER . "') ASC");
    }

    public function notifications(): BelongsTo
    {
        return $this->belongsTo(Notification::class);
    }

    ##TODO Check if the date time from the start gg api are in UTC or in the timezone of the event
    public function getTimezoneStartDateTimeAttribute() :string
    {
        return Carbon::parse($this->start_date_time, $this->timezone)->timezone($this->address?->country?->timezone)->format('d-m-Y H:i:s');
    }

    public function getTimezoneEndDateTimeAttribute(): string
    {
        $start_date = Carbon::parse($this->start_date_time, $this->timezone)->format('Y-m-d');

        $end_date = Carbon::parse($this->end_date_time, $this->timezone)->format('Y-m-d');

        if($start_date === $end_date){
            return Carbon::parse($this->end_date_time, $this->timezone)->format('H:i:s');
        }
        return Carbon::parse($this->end_date_time, $this->timezone)->format('d-m-Y H:i:s');
    }

    public function getTimezoneLabelAttribute()
    {
        if($this->address?->country?->timezone){
            return $this->address?->country?->timezone;
        }
        return $this->timezone;
    }

    #FIXME Find a way to access the logged in user inside this funciton
    public function user_subscribed(Request $request): bool
    {
        $user = $request->user('sanctum');
        if($user){
            return $user->subscribed_events->contains($this->id);
        }
        return false;
    }

    public function scopeCountries($query, array $countries)
    {
        return $query->whereHas('address.country', function ($query) use ($countries) {
            $query->whereIn('code', $countries);
        });
    }

    public function scopeContinents($query, array $continents)
    {
        return $query->whereHas('address.country.continent', function ($query) use ($continents) {
            $query->whereIn('code', $continents);
        });
    }

}
