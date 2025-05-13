<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;

class FixBookingDates extends Command
{
    protected $signature = 'bookings:fix-dates';
    protected $description = 'Fix booking dates where end date is before start date';

    public function handle()
    {
        $bookings = Booking::all();

        foreach ($bookings as $booking) {
            $from = Carbon::parse($booking->from . ' ' . $booking->from_time);
            $to = Carbon::parse($booking->to . ' ' . $booking->to_time);

            if ($to->lt($from)) {
                $this->info("Fixing booking ID: {$booking->id}");
                $this->info("Before: from={$booking->from} {$booking->from_time}, to={$booking->to} {$booking->to_time}");

                // Swap the dates
                $tempFrom = $booking->from;
                $tempFromTime = $booking->from_time;
                $booking->from = $booking->to;
                $booking->from_time = $booking->to_time;
                $booking->to = $tempFrom;
                $booking->to_time = $tempFromTime;
                $booking->save();

                $this->info("After: from={$booking->from} {$booking->from_time}, to={$booking->to} {$booking->to_time}");
            }
        }

        $this->info('Done!');
    }
} 