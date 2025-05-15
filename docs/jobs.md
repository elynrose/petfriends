# Jobs Documentation

## Overview
The jobs system in PetFriends handles background processing and scheduled tasks. This document outlines the available jobs and their configurations.

## Available Jobs

### 1. FeaturedPetExpirationJob
Handles the expiration of featured pets.

```php
namespace App\Jobs;

class FeaturedPetExpirationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // Expire featured pets
        Pet::where('featured_until', '<', now())
           ->update(['featured_until' => null]);
    }
}
```

### 2. BookingReminderJob
Sends reminders for upcoming bookings.

```php
namespace App\Jobs;

class BookingReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // Send reminders for bookings starting in 24 hours
        Booking::where('from', '>', now())
               ->where('from', '<=', now()->addDay())
               ->where('status', 'pending')
               ->each(function ($booking) {
                   // Send reminder notification
               });
    }
}
```

## Job Scheduling

### Kernel Configuration
Configure jobs in `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Expire featured pets every minute
    $schedule->job(new FeaturedPetExpirationJob)->everyMinute();
    
    // Send booking reminders daily at 9 AM
    $schedule->job(new BookingReminderJob)->dailyAt('09:00');
}
```

## Queue Configuration

### Queue Driver
Configure in `.env`:
```
QUEUE_CONNECTION=database
```

### Queue Tables
Run migrations:
```bash
php artisan queue:table
php artisan migrate
```

### Queue Worker
Start queue worker:
```bash
php artisan queue:work
```

## Job Priorities

1. High Priority
   - Booking confirmations
   - Payment processing
   - Critical notifications

2. Medium Priority
   - Booking reminders
   - Featured pet expiration
   - Regular notifications

3. Low Priority
   - Analytics processing
   - Report generation
   - Cleanup tasks

## Error Handling

### Retry Configuration
```php
public $tries = 3;
public $maxExceptions = 3;
public $backoff = [60, 180, 360]; // Retry after 1, 3, and 6 minutes
```

### Failed Jobs
Monitor failed jobs:
```bash
php artisan queue:failed
```

Retry failed jobs:
```bash
php artisan queue:retry all
```

## Monitoring

### Queue Status
Check queue status:
```bash
php artisan queue:monitor
```

### Job Events
Listen for job events in `EventServiceProvider`:
```php
protected $listen = [
    'Illuminate\Queue\Events\JobFailed' => [
        'App\Listeners\LogFailedJob',
    ],
];
```

## Best Practices

1. Job Design
   - Keep jobs focused and single-purpose
   - Handle exceptions appropriately
   - Implement proper logging
   - Use appropriate queue priorities

2. Performance
   - Optimize database queries
   - Use chunking for large datasets
   - Implement proper indexing
   - Monitor memory usage

3. Security
   - Validate input data
   - Implement proper authorization
   - Handle sensitive data appropriately
   - Log security-related events

## Testing Jobs

### Unit Testing
```php
public function test_featured_pet_expiration()
{
    $pet = Pet::factory()->create([
        'featured_until' => now()->subMinute()
    ]);

    $job = new FeaturedPetExpirationJob();
    $job->handle();

    $this->assertNull($pet->fresh()->featured_until);
}
```

### Integration Testing
```php
public function test_booking_reminder_sends_notification()
{
    $booking = Booking::factory()->create([
        'from' => now()->addDay(),
        'status' => 'pending'
    ]);

    $job = new BookingReminderJob();
    $job->handle();

    $this->assertNotificationSent($booking->user, BookingReminder::class);
}
```

## Deployment Considerations

1. Queue Worker
   - Use supervisor for process management
   - Configure appropriate number of workers
   - Monitor worker health

2. Server Requirements
   - Adequate memory allocation
   - Proper PHP configuration
   - Database connection limits

3. Monitoring
   - Set up queue monitoring
   - Configure error notifications
   - Monitor job processing times

## Troubleshooting

### Common Issues

1. Jobs Not Processing
   - Check queue worker status
   - Verify queue configuration
   - Check for failed jobs

2. Memory Issues
   - Monitor memory usage
   - Implement chunking
   - Optimize queries

3. Performance Problems
   - Check database indexes
   - Optimize job logic
   - Monitor processing times

### Debugging Tools

1. Queue Monitor
```bash
php artisan queue:monitor
```

2. Failed Jobs
```bash
php artisan queue:failed
```

3. Queue Size
```bash
php artisan queue:size
``` 