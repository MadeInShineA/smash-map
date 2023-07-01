<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Country extends Model
{
    use HasFactory;

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function continent(): BelongsTo
    {
        return $this->belongsTo(Continent::class);
    }

    public function scopeContinent($query, String $continent)
    {
        return $query->whereHas('continent', function ($query) use ($continent) {
            $query->where('code', $continent);
        });
    }
}
