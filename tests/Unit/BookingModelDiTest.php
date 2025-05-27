<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\Pet;
use App\Models\User;
use App\Services\CreditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery; // Ensure Mockery is used if not already via PHPUnit integration
use Mockery\MockInterface;
use Tests\TestCase;

class BookingModelDiTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_complete_method_calls_credit_service_methods()
    {
        // 1. Mock CreditService
        // We need to mock it in the service container because Booking constructor uses app(CreditService::class)
        $creditServiceMock = $this->mock(CreditService::class, function (MockInterface $mock) {
            $mock->shouldReceive('calculateBookingHours')->once()->andReturn(2); // Example hours
            $mock->shouldReceive('deductCreditsForBooking')->once()->andReturn(true);
            $mock->shouldReceive('awardCreditsForBooking')->once()->andReturn(true);
        });

        // 2. Prepare Booking instance and its dependencies
        $petOwner = User::factory()->create();
        $caregiver = User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $petOwner->id]);
        
        // Create booking using factory which will trigger constructor DI
        $booking = Booking::factory()->create([
            'pet_id' => $pet->id,
            'user_id' => $caregiver->id, // Caregiver is the user associated with the booking for award
            'status' => 'accepted', // A status that allows completion
        ]);
        
        // 3. Call the complete method
        $result = $booking->complete();

        // 4. Assertions
        $this->assertTrue($result);
        $this->assertEquals('completed', $booking->status);
        // Mockery assertions (shouldReceive) are checked automatically on tearDown.
    }

    public function test_cancel_method_calls_credit_service_refund()
    {
        // 1. Mock CreditService
        $creditServiceMock = $this->mock(CreditService::class, function (MockInterface $mock) {
            $mock->shouldReceive('calculateBookingHours')->once()->andReturn(2); // Needed by refund
            $mock->shouldReceive('refundCreditsForBooking')->once()->andReturn(true);
        });

        // 2. Prepare Booking instance
        $petOwner = User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $petOwner->id]);
        $booking = Booking::factory()->create([
            'pet_id' => $pet->id,
            'user_id' => User::factory()->create()->id, // Some user
            'status' => 'pending', // A status that allows cancellation
        ]);

        // 3. Call the cancel method
        $result = $booking->cancel();

        // 4. Assertions
        $this->assertTrue($result);
        $this->assertEquals('cancelled', $booking->status);
    }
}

// Note: The Booking factory needs to be available.
// If not already globally available via autoloader, define it conceptually as in RouteRefactoringTest.php
// For these tests, we assume UserFactory, PetFactory, BookingFactory are working.
// The key is mocking CreditService in the container so that when Booking's constructor calls app(CreditService::class),
// it receives our mock.
// $this->mock(CreditService::class, ...) does exactly this.
```

**File 3: `tests/Unit/BookingModelDateTimeTest.php`**
