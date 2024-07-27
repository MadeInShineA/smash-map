<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\URL;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'game_id',
        'user_id',
        'type',
        'attendees',
        'message',
        'image_url',
        'seen'
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function getImageUrlLabelAttribute(): string
    {
        $url = $this->image_url;
        $url = strstr($url, '/storage');

        if(file_exists(public_path($url))){
            return $this->image_url;
        }

        return URL::to('/storage/map-icons/' . $this->game->slug . '.png');

        }
}
