<?php

namespace App\Services;

use App\Models\Pet;
use App\Models\PetNotification;
use Illuminate\Support\Collection;

class PetNotificationService
{
    public function notifyPreviousCaretakers(Pet $pet): void
    {
        // Get all users who have completed bookings for this pet
        $previousCaretakers = $pet->bookings()
            ->where('completed', true)
            ->where('user_id', '!=', $pet->user_id)
            ->with('user.notificationPreferences')
            ->get()
            ->pluck('user')
            ->unique('id');

        // Create notifications for each previous caretaker who has enabled the preference
        foreach ($previousCaretakers as $user) {
            $preferences = $user->getNotificationPreferences();
            
            if ($preferences->pet_available) {
                PetNotification::updateOrCreate(
                    [
                        'pet_id' => $pet->id,
                        'user_id' => $user->id,
                        'is_read' => false,
                    ],
                    [
                        'is_read' => false,
                        'read_at' => null,
                    ]
                );

                // Send email notification if enabled
                if ($preferences->email_notifications) {
                    // TODO: Implement email notification
                }
            }
        }
    }

    public function getUserNotifications(int $userId): Collection
    {
        return PetNotification::with('pet')
            ->where('user_id', $userId)
            ->where('is_read', false)
            ->latest()
            ->get();
    }

    public function markAllAsRead(int $userId): void
    {
        PetNotification::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    public function markAsRead(PetNotification $notification): void
    {
        $notification->markAsRead();
    }
} 