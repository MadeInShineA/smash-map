<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Character extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image_link'
        ];

    public function image(): MorphOne
    {
        return $this->morphOne(Image::class,'parentable');
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
