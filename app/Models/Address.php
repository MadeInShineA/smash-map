<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable =[
        'name',
        'latitude',
        'longitude',
        'country_id',
        'continent_id',
        'parentable_type',
        'parentable_id'
    ];


}
