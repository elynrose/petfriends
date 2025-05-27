<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\Pet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingModelDateTimeTest extends TestCase
{
    use RefreshDatabase;

    public function test_saving_event_populates_start_time_and_end_time()
    {
        // 1. Prepare data
        $fromDate = '2024-08-15';
        $fromTime = '10:00:00';
        $toDate = '2024-08-15';
        $toTime = '12:30:00';

        $expectedStartTime = Carbon::parse($fromDate . ' ' . $fromTime);
        $expectedEndTime = Carbon::parse($toDate . ' ' . $toTime);

        // 2. Create (but don't save yet) a Booking instance
        // We'll use create() which calls save(), to trigger the 'saving' event.
        $booking = Booking::create([
            'pet_id' => Pet::factory()->create()->id,
            'user_id' => User::factory()->create()->id,
            'from' => $fromDate,
            'from_time' => $fromTime,
            'to' => $toDate,
            'to_time' => $toTime,
            'status' => 'pending',
        ]);

        // 3. Retrieve the booking to ensure data is loaded from DB or fresh instance
        $savedBooking = Booking::find($booking->id);

        // 4. Assertions
        $this->assertInstanceOf(Carbon::class, $savedBooking->start_time);
        $this->assertInstanceOf(Carbon::class, $savedBooking->end_time);
        $this->assertEquals($expectedStartTime->toDateTimeString(), $savedBooking->start_time->toDateTimeString());
        $this->assertEquals($expectedEndTime->toDateTimeString(), $savedBooking->end_time->toDateTimeString());
    }

    public function test_start_time_is_null_if_from_or_from_time_is_missing()
    {
        // Test with from_time missing
        $booking1 = Booking::create([
            'pet_id' => Pet::factory()->create()->id,
            'user_id' => User::factory()->create()->id,
            'from' => '2024-08-16',
            // 'from_time' => null, // Missing
            'to' => '2024-08-16',
            'to_time' => '14:00:00',
            'status' => 'pending',
        ]);
        $this->assertNull(Booking::find($booking1->id)->start_time);
        $this->assertNotNull(Booking::find($booking1->id)->end_time); // end_time should still be populated

        // Test with from missing (though 'from' is usually required by validation)
        // Model 'saving' event checks 'isset($model->from)'
        // If 'from' is null, Carbon::parse(null) would error.
        // The saving event logic is: if (isset($model->from) && isset($model->from_time))
        // So if 'from' is null, start_time won't be set.
        $booking2 = Booking::create([
            'pet_id' => Pet::factory()->create()->id,
            'user_id' => User::factory()->create()->id,
            // 'from' => null, // Missing
            'from_time' => '10:00:00',
            'to' => '2024-08-17',
            'to_time' => '15:00:00',
            'status' => 'pending',
        ]);
        $this->assertNull(Booking::find($booking2->id)->start_time);
    }

    public function test_end_time_is_null_if_to_or_to_time_is_missing()
    {
        // Test with to_time missing
        $booking1 = Booking::create([
            'pet_id' => Pet::factory()->create()->id,
            'user_id' => User::factory()->create()->id,
            'from' => '2024-08-18',
            'from_time' => '10:00:00',
            'to' => '2024-08-18',
            // 'to_time' => null, // Missing
            'status' => 'pending',
        ]);
        $this->assertNull(Booking::find($booking1->id)->end_time);
        $this->assertNotNull(Booking::find($booking1->id)->start_time);

        // Test with to missing
        $booking2 = Booking::create([
            'pet_id' => Pet::factory()->create()->id,
            'user_id' => User::factory()->create()->id,
            'from' => '2024-08-19',
            'from_time' => '10:00:00',
            // 'to' => null, // Missing
            'to_time' => '16:00:00',
            'status' => 'pending',
        ]);
        $this->assertNull(Booking::find($booking2->id)->end_time);
    }
}
```

**File 4: `tests/Unit/PetAvailabilityServiceConfigTest.php`**
