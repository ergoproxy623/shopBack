<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
class Image extends Model
{
    protected $fillable = [
        'name',
        'image_url'
    ];

    /**
     *  ACCESSORS
     */
    public function getImageUrlAttribute($value)
    {
        $getPath = Config::get('constants.image_folder.images.get_path');
        return url($getPath.$value);
    }

    public function getImagePathAttribute()
    {
        $savePath = Config::get('constants.image_folder.images.save_path');
        return $savePath . '/' . $this->attributes['image_url'];
    }

}
