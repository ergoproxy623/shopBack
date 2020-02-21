<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    const PAY_PROCESS = '0';
    const PAY_SUCCESS = '1';
    const PAY_FAIL = '2';
    const FIRST_BLOCK = '1';
    const SECOND_BLOCK = '2';

    protected $fillable = [
        'user_id',
        'status',
        'pay_status',
        'payment_system',
    ];

    public function user()
    {
        return $this->belongsTo(Role::class);
    }
}
