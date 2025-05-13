<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\SendBookingReminders::class,
        Commands\SendBookingSMSReminders::class,
        Commands\CompletePastBookingsCommand::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Send booking reminders daily at 9 AM
        $schedule->command('bookings:send-reminders')
                ->dailyAt('09:00')
                ->appendOutputTo(storage_path('logs/booking-reminders.log'));

        // Send SMS reminders every minute
        $schedule->command('bookings:send-sms-reminders')
                ->everyMinute()
                ->appendOutputTo(storage_path('logs/booking-sms-reminders.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
