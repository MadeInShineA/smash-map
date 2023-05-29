<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends Model
{
    use HasFactory;

    protected $fillable =[
        'name',
        'latitude',
        'longitude',
        'country_id',
    ];

    public function events():HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function users():HasMany
    {
        return $this->hasMany(User::class);
    }


}
