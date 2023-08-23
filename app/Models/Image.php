<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\URL;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'parentable_id',
        'type',
        'parentable_type',
        'uuid',
        'md5'
    ];

    /**
     * @return MorphTo
     * Either User, Tournament, Character
     */
    public function parentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getUrlAttribute(){
        if($this->parentable_type === Event::class){
            $directory = 'events-images';
            $subdirectory = 'event-' . $this->parentable->id;
            return URL::to( '/storage/'. $directory . '/'. $subdirectory . '/' . $this->type . '/' . $this->uuid);
        }elseif($this->parentable_type === Character::class){
            $directory = 'characters-images';
            $subdirectory = $this->parentable->game->slug . '/' . $this->parentable->name;
        }elseif ($this->parentable_type === Country::class){
            $directory = 'countries-images';
            $subdirectory = $this->parentable->name;
        }
        return URL::to('/storage/'. $directory . '/'. $subdirectory . '/' . $this->uuid);
    }
}
