<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Video extends Model
{
    const FIRST_BLOCK = '1';
    const SECOND_BLOCK = '2';

    protected $fillable = [
        'name',
        'status',
        'video_url',
        'description',
    ];

    /**
     *  ACCESSORS
     */
    public function getVideoUrlAttribute($value)
    {
        $getPath = Config::get('constants.video_folder.videos.get_path');
        return url($getPath.$value);
    }

    public function getVideoPathAttribute()
    {
        $savePath = Config::get('constants.video_folder.videos.save_path');
        return $savePath . '/' . $this->attributes['video_url'];
    }
}
