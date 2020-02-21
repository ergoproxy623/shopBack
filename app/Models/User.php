<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Config;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\JWTAuth;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    const BUY_ANYTHING = '0';
    const BUY_ONE_BLOCK = '1';
    const BUY_TWO_BLOCK = '3';

    protected $fillable = [
        'name',
        'last_name',
        'role_id',
        'status',
        'telephone',
        'email',
        'social_id',
        'avatar_url',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    public static function boot()
    {
        parent::boot();
        static::creating(function ($table) {
                $table->role_id = 1;
                $table->status = 0;
        });
    }

    public function createUser($data)
    {
        $user = new User();
        $user->name = $data['name'];
        $user->last_name = $data['last_name'];
        $user->email = $data['email'] ?? null;
        $user->avatar_url = $data['avatar_url'];
        if (isset($data['social_id'])) {
            $user->social_id = $data['social_id'];
        }
        $user->save();
        return $user;
    }

    /**
     *  ACCESSORS
     */
    public function getAvatarUrlAttribute($value)
    {
        if($this->social_id) {
            $photo =$this->attributes['avatar_url'];
            $str = strpos($photo, 'http');
            if($str !== false) {
                return $this->attributes['avatar_url'];
            }
        }
        if (!$this->attributes['avatar_url']) {
            return $this->attributes['avatar_url'];
        }
        $getPath = Config::get('constants.image_folder.avatars.get_path');
        return url($getPath.$value);
    }

    public function getAvatarPathAttribute()
    {
        $savePath = Config::get('constants.image_folder.avatars.save_path');
        return $savePath . '/' . $this->attributes['avatar_url'];
    }

    /**
     * RELATIONS
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function purchase()
    {
        return $this->hasMany(Purchase::class)
            ->where('purchases.pay_status', '=', Purchase::PAY_SUCCESS);
    }
    /**
     * JWT auto-generated methods
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
