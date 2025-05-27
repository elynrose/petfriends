<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;

class MarkExpiredBookings extends Command
{
    protected $signature = 'bookings:mark-expired';
    protected $description = 'Mark pending bookings as expired if they have passed their end time';

    public function handle()
    {
        $expiredBookings = Booking::expired()->get();
        
        foreach ($expiredBookings as $booking) {
            $booking->update([
                'status' => 'expired'
            ]);
            
            $this->info("Marked booking #{$booking->id} as expired");
        }

        $this->info("Processed {$expiredBookings->count()} expired bookings");
    }
} 