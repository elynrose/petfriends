<?php

namespace App\Console\Commands;

use App\Jobs\CompletePastBookings;
use Illuminate\Console\Command;

class CompletePastBookingsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:complete-past';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Complete bookings that ended more than 12 hours ago';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to process past bookings...');
        
        CompletePastBookings::dispatch();
        
        $this->info('Job dispatched successfully!');
    }
} 