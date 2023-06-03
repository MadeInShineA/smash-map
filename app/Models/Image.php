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
        if($this->parentable_type === 'App\Models\Event'){
            $directory = 'events-images';
            $subdirectory = 'event-' . $this->parentable->id;
        }elseif($this->parentable_type === 'App\Models\Character'){
            $directory = 'characters-images';
            $subdirectory = $this->parentable->name;
        }
        return URL::to('/') . '/storage/'. $directory . '/'. $subdirectory . '/' . $this->type . '/' . $this->uuid;

    }
}
