<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;
use App\Notifications\BookingSMSReminderNotification;

class SendBookingSMSReminders extends Command
{
    protected $signature = 'bookings:send-sms-reminders';
    protected $description = 'Send SMS reminders for bookings that start in 1 hour';

    public function handle()
    {
        $now = Carbon::now();
        $oneHourFromNow = $now->copy()->addHour();
        
        $bookings = Booking::with(['user', 'pet.owner'])
            ->where('from', $oneHourFromNow->format('Y-m-d'))
            ->where('from_time', $oneHourFromNow->format('H:i'))
            ->where('status', 'approved')
            ->whereHas('user', function($query) {
                $query->where('sms_notifications', true);
            })
            ->get();

        foreach ($bookings as $booking) {
            try {
                $booking->user->notify(new BookingSMSReminderNotification($booking));
                $this->info("SMS reminder sent for booking #{$booking->id}");
            } catch (\Exception $e) {
                $this->error("Failed to send SMS for booking #{$booking->id}: {$e->getMessage()}");
            }
        }

        $this->info('All SMS reminders have been sent!');
    }
} 