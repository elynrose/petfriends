<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;
use App\Notifications\BookingReminderNotification;

class SendBookingReminders extends Command
{
    protected $signature = 'bookings:send-reminders';
    protected $description = 'Send reminder emails for bookings that are due in 24 hours';

    public function handle()
    {
        $tomorrow = Carbon::tomorrow();
        
        $bookings = Booking::with(['user', 'pet'])
            ->where('from', $tomorrow->format('Y-m-d'))
            ->where('status', 'approved')
            ->get();

        foreach ($bookings as $booking) {
            $booking->user->notify(new BookingReminderNotification($booking));
            $this->info("Reminder sent for booking #{$booking->id}");
        }

        $this->info('All reminders have been sent!');
    }
} 