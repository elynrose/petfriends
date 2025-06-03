<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pet_available',
        'booking_requested',
        'booking_accepted',
        'booking_rejected',
        'booking_completed',
        'new_message',
        'email_notifications',
    ];

    protected $casts = [
        'pet_available' => 'boolean',
        'booking_requested' => 'boolean',
        'booking_accepted' => 'boolean',
        'booking_rejected' => 'boolean',
        'booking_completed' => 'boolean',
        'new_message' => 'boolean',
        'email_notifications' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getDefaults(): array
    {
        return [
            'pet_available' => true,
            'booking_requested' => true,
            'booking_accepted' => true,
            'booking_rejected' => true,
            'booking_completed' => true,
            'new_message' => true,
            'email_notifications' => true,
        ];
    }
}
