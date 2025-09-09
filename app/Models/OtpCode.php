<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    protected $fillable = [
        'channel',       // sms or email
        'recipient',     // phone number or email address
        'code',          // OTP value
        'expires_at',    // when OTP expires
        'attempts',      // failed attempts
        'used_at',       // timestamp when OTP was used
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at'    => 'datetime',
    ];
}
