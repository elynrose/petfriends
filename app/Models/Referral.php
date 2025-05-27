<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Referral extends Model
{
    protected $fillable = [
        'referrer_id',
        'email',
        'token',
        'is_registered',
        'registered_at',
    ];

    protected $casts = [
        'is_registered' => 'boolean',
        'registered_at' => 'datetime',
    ];

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function getReferralLinkAttribute()
    {
        return route('register', ['ref' => $this->token]);
    }

    public static function generateToken()
    {
        return Str::random(32);
    }
} 