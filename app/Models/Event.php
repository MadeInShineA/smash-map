<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_gg_id',
        'start_gg_updated_at',
        'address_id',
        'image_id',
        'creator_id',
        'is_online',
        'name',
        'start_date_time',
        'end_date_time',
        'link'
    ];

    public function address():BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'parentable');
    }
}
