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
            // Ensure start_time and end_time are populated if they were missed by old records
            // The 'saving' event in Booking model should handle this for new/updated records.
            // However, this command might run on records older than that change.
            if (!$booking->start_time && $booking->from && $booking->from_time) {
                $booking->start_time = Carbon::parse(Carbon::parse($booking->from)->format('Y-m-d') . ' ' . $booking->from_time);
            }
            if (!$booking->end_time && $booking->to && $booking->to_time) {
                $booking->end_time = Carbon::parse(Carbon::parse($booking->to)->format('Y-m-d') . ' ' . $booking->to_time);
            }

            if ($booking->start_time && $booking->end_time && $booking->end_time->lt($booking->start_time)) {
                $this->info("Fixing booking ID: {$booking->id}");
                $this->info("Before: start_time={$booking->start_time->toDateTimeString()}, end_time={$booking->end_time->toDateTimeString()}");
                $this->info("       (Legacy: from={$booking->from} {$booking->from_time}, to={$booking->to} {$booking->to_time})");

                // Swap start_time and end_time
                $tempStartTime = $booking->start_time;
                $booking->start_time = $booking->end_time;
                $booking->end_time = $tempStartTime;

                // Also swap the legacy fields to maintain data consistency for any part of the system
                // that might still read them directly before they are fully deprecated.
                // The Booking's 'saving' event will re-derive start_time/end_time from these,
                // so we need to ensure these are correct for the event to work as expected.
                $tempFromDate = $booking->from;
                $tempFromTime = $booking->from_time;

                $booking->from = $booking->to;
                $booking->from_time = $booking->to_time;
                $booking->to = $tempFromDate;
                $booking->to_time = $tempFromTime;
                
                $booking->save(); // This will trigger the 'saving' event, re-populating start_time and end_time correctly.

                $this->info("After: start_time={$booking->start_time->toDateTimeString()}, end_time={$booking->end_time->toDateTimeString()}");
                $this->info("      (Legacy: from={$booking->from} {$booking->from_time}, to={$booking->to} {$booking->to_time})");
            }
        }

        $this->info('Done!');
    }
} 