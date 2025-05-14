<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Notifications\ReviewReminder;
use Carbon\Carbon;

class SendReviewReminders extends Command
{
    protected $signature = 'reviews:send-reminders';
    protected $description = 'Send reminders to users who haven\'t submitted reviews for completed bookings';

    public function handle()
    {
        $yesterday = Carbon::now()->subDay();
        
        // Get completed bookings from yesterday that don't have reviews
        $bookings = Booking::where('status', 'completed')
            ->whereDate('end_time', $yesterday)
            ->whereDoesntHave('review')
            ->with(['user', 'pet'])
            ->get();

        foreach ($bookings as $booking) {
            // Send reminder notification
            $booking->user->notify(new ReviewReminder($booking));
            $this->info("Sent review reminder for booking #{$booking->id}");
        }

        $this->info("Sent {$bookings->count()} review reminders");
    }
} 