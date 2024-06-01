<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'parentable_id',
        'type',
        'parentable_type',
        'uuid',
        'md5',
        'extension'
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
            $directory_path = '/storage/events-images/' . Str::slug($this->parentable->name) . '/' . $this->type . '.' . $this->extension;
        }elseif($this->parentable_type === Character::class){
            $directory_path = '/storage/characters-images/' . $this->parentable->game->slug . '/' . Str::slug($this->parentable->name) . '.' . $this->extension;
        }elseif ($this->parentable_type === Country::class){
            $directory_path = '/storage/countries-images/' . $this->parentable->code . '.' . $this->extension;
        }elseif ($this->parentable_type === User::class) {
            $directory_path = '/storage/users-images/' . $this->parentable->uuid . '/' . $this->type . '.' . $this->extension;
            $timestamp = '?time=' . $this->updated_at->timestamp;
            return URL::to(urlencode($directory_path)) . $timestamp;
        }
        return URL::to(urlencode($directory_path));
    }
}
