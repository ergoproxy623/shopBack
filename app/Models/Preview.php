<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Preview extends Model
{
    protected $fillable = [
        'preview_url',
    ];
}
