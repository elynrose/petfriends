<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Notifications\BookingCompleted;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CompletePastBookings implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Find all accepted bookings that ended more than 12 hours ago
        $pastBookings = Booking::where('status', 'accepted')
            ->where('to', '<', Carbon::now()->subHours(12))
            ->get();

        foreach ($pastBookings as $booking) {
            // Use the complete() method which handles credit awarding
            $booking->complete();

            // Send notification to the user
            $booking->user->notify(new BookingCompleted($booking));
        }
    }
} 