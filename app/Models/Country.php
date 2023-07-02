<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Country extends Model
{
    use HasFactory;

    protected $fillable =[
        'hex'
    ];

    public function image(): MorphOne
    {
        return $this->morphOne(Image::class,'parentable');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function continent(): BelongsTo
    {
        return $this->belongsTo(Continent::class);
    }

    public function scopeContinents($query, array $continents)
    {
        return $query->whereHas('continent', function ($query) use ($continents) {
            $query->whereIn('code', $continents);
        });
    }
}
