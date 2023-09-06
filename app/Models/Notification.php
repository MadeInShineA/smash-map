<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'type',
        'content'
    ];

    public function event(): HasOne
    {
        return $this->hasOne(Event::class);
    }
}
