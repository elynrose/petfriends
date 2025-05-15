# Services Documentation

## Overview
The services layer in PetFriends handles business logic and complex operations. This document outlines the available services and their usage.

## Available Services

### 1. PetAvailabilityService

Handles pet availability management and credit calculations.

```php
namespace App\Services;

class PetAvailabilityService
{
    public function handleCreditChanges(Pet $pet, User $user, int $newHours, ?int $oldHours = null)
    {
        // Calculate credit changes
        $creditChange = $newHours - ($oldHours ?? 0);
        
        if ($creditChange > 0) {
            // Check if user has enough credits
            if (!$user->hasEnoughCredits($creditChange)) {
                return [
                    'success' => false,
                    'message' => "Insufficient credits. Required: {$creditChange}, Available: {$user->credits}"
                ];
            }
            
            // Deduct credits
            $user->deductCredits($creditChange);
        } elseif ($creditChange < 0) {
            // Refund credits
            $user->refundCredits(abs($creditChange));
        }
        
        return ['success' => true];
    }

    public function handleNotAvailable(Pet $pet, User $user)
    {
        if (!$pet->not_available) {
            // Calculate refund
            $start = Carbon::parse($pet->from . ' ' . $pet->from_time);
            $end = Carbon::parse($pet->to . ' ' . $pet->to_time);
            $hours = ceil($end->diffInMinutes($start) / 60);
            
            // Refund credits
            $user->refundCredits($hours);
        }
        
        return ['success' => true];
    }
}
```

### 2. CreditService

Manages user credits and transactions.

```php
namespace App\Services;

class CreditService
{
    public function calculatePetAvailabilityHours(Pet $pet)
    {
        if ($pet->not_available) {
            return 0;
        }

        $start = Carbon::parse($pet->from . ' ' . $pet->from_time);
        $end = Carbon::parse($pet->to . ' ' . $pet->to_time);
        
        return ceil($end->diffInMinutes($start) / 60);
    }

    public function validateCreditTransaction(User $user, int $amount)
    {
        if ($amount < 0 && !$user->hasEnoughCredits(abs($amount))) {
            return false;
        }
        
        return true;
    }
}
```

### 3. BookingService

Handles booking operations and validations.

```php
namespace App\Services;

class BookingService
{
    public function validateBookingTime(Pet $pet, Carbon $from, Carbon $to)
    {
        // Check if time is within allowed hours
        if ($from->hour < 6 || $from->hour >= 22 || 
            $to->hour < 6 || $to->hour >= 22) {
            return [
                'success' => false,
                'message' => 'Bookings are only allowed between 6 AM and 10 PM.'
            ];
        }

        // Check for conflicts
        $conflict = Booking::where('pet_id', $pet->id)
            ->where('from', '<=', $to)
            ->where('to', '>=', $from)
            ->where('status', '!=', 'completed')
            ->first();

        if ($conflict) {
            return [
                'success' => false,
                'message' => 'This pet is already booked for the selected time.'
            ];
        }

        return ['success' => true];
    }
}
```

## Service Registration

Register services in `app/Providers/AppServiceProvider.php`:

```php
public function register()
{
    $this->app->singleton(PetAvailabilityService::class);
    $this->app->singleton(CreditService::class);
    $this->app->singleton(BookingService::class);
}
```

## Usage Examples

### In Controllers

```php
class PetsController extends Controller
{
    protected $petAvailabilityService;
    protected $creditService;

    public function __construct(
        PetAvailabilityService $petAvailabilityService,
        CreditService $creditService
    ) {
        $this->petAvailabilityService = $petAvailabilityService;
        $this->creditService = $creditService;
    }

    public function update(Request $request, Pet $pet)
    {
        // Calculate hours
        $hours = $this->creditService->calculatePetAvailabilityHours($pet);
        
        // Handle availability changes
        $result = $this->petAvailabilityService->handleCreditChanges(
            $pet,
            auth()->user(),
            $hours
        );

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }
    }
}
```

### In Jobs

```php
class BookingReminderJob implements ShouldQueue
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function handle()
    {
        // Use booking service in job
    }
}
```

## Best Practices

### 1. Service Design
- Keep services focused and single-purpose
- Use dependency injection
- Implement proper error handling
- Follow SOLID principles

### 2. Error Handling
```php
try {
    $result = $service->handleOperation();
} catch (ServiceException $e) {
    Log::error('Service operation failed', [
        'error' => $e->getMessage(),
        'context' => $e->getContext()
    ]);
    
    return [
        'success' => false,
        'message' => $e->getMessage()
    ];
}
```

### 3. Logging
```php
Log::info('Service operation completed', [
    'operation' => 'credit_calculation',
    'user_id' => $user->id,
    'amount' => $amount
]);
```

## Testing Services

### Unit Tests
```php
class PetAvailabilityServiceTest extends TestCase
{
    protected $service;
    protected $user;
    protected $pet;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->service = app(PetAvailabilityService::class);
        $this->user = User::factory()->create(['credits' => 100]);
        $this->pet = Pet::factory()->create();
    }

    public function test_handle_credit_changes()
    {
        $result = $this->service->handleCreditChanges(
            $this->pet,
            $this->user,
            5
        );

        $this->assertTrue($result['success']);
        $this->assertEquals(95, $this->user->fresh()->credits);
    }
}
```

### Integration Tests
```php
class BookingServiceTest extends TestCase
{
    public function test_validate_booking_time()
    {
        $service = app(BookingService::class);
        $pet = Pet::factory()->create();
        
        $from = now()->setHour(7);
        $to = now()->setHour(9);
        
        $result = $service->validateBookingTime($pet, $from, $to);
        
        $this->assertTrue($result['success']);
    }
}
```

## Service Events

### Available Events
- `PetAvailabilityChanged`
- `CreditsUpdated`
- `BookingCreated`
- `BookingStatusChanged`

### Event Listeners
```php
protected $listen = [
    'App\Events\PetAvailabilityChanged' => [
        'App\Listeners\UpdatePetAvailability',
    ],
    'App\Events\CreditsUpdated' => [
        'App\Listeners\LogCreditTransaction',
    ],
];
```

## Service Middleware

### Rate Limiting
```php
class ServiceRateLimiter
{
    public function handle($request, Closure $next)
    {
        if (RateLimiter::tooManyAttempts('service:' . $request->ip(), 60)) {
            throw new ServiceException('Too many requests');
        }
        
        return $next($request);
    }
}
```

## Service Caching

### Cache Implementation
```php
class CachedPetService
{
    protected $cache;
    protected $petService;

    public function __construct(Cache $cache, PetService $petService)
    {
        $this->cache = $cache;
        $this->petService = $petService;
    }

    public function getPet($id)
    {
        return $this->cache->remember("pet:{$id}", 3600, function () use ($id) {
            return $this->petService->getPet($id);
        });
    }
}
```

## Service Monitoring

### Health Checks
```php
class ServiceHealthCheck
{
    public function check()
    {
        return [
            'status' => 'healthy',
            'services' => [
                'pet_availability' => $this->checkPetAvailabilityService(),
                'credits' => $this->checkCreditService(),
                'bookings' => $this->checkBookingService()
            ]
        ];
    }
}
```

## Service Documentation

### PHPDoc Example
```php
/**
 * Handles pet availability and credit calculations
 *
 * @param Pet $pet The pet to handle
 * @param User $user The user making the request
 * @param int $newHours The new availability hours
 * @param int|null $oldHours The previous availability hours
 * @return array{success: bool, message?: string}
 * @throws ServiceException When credit validation fails
 */
public function handleCreditChanges(Pet $pet, User $user, int $newHours, ?int $oldHours = null)
{
    // Implementation
}
``` 