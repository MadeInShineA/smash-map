<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Continent extends Model
{
    use HasFactory;

    public function countries(): HasMany
    {
        return $this->hasMany(Country::class);
    }
}
