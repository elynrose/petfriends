<?php

namespace Tests\Unit;

use App\Services\PetAvailabilityService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class PetAvailabilityServiceConfigTest extends TestCase
{
    private PetAvailabilityService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PetAvailabilityService();
    }

    public function test_validate_time_range_uses_default_config_values()
    {
        // Default config: 6 AM to 10 PM (22:00)
        // Valid case
        $startValid = Carbon::parse('2024-01-01 06:00:00');
        $endValid = Carbon::parse('2024-01-01 21:59:00');
        $this->assertEmpty($this->service->validateTimeRange($startValid, $endValid));

        // Invalid cases based on default config
        $startInvalidEarly = Carbon::parse('2024-01-01 05:59:00'); // Too early
        $endInvalidEarly = Carbon::parse('2024-01-01 10:00:00');
        $errors1 = $this->service->validateTimeRange($startInvalidEarly, $endInvalidEarly);
        $this->assertNotEmpty($errors1);
        $this->assertStringContainsString('between 6 AM and 10 PM', $errors1[0]);
        
        $startInvalidLate = Carbon::parse('2024-01-01 22:00:00'); // Too late start
        $endInvalidLate = Carbon::parse('2024-01-01 23:00:00');
        $errors2 = $this->service->validateTimeRange($startInvalidLate, $endInvalidLate);
        $this->assertNotEmpty($errors2);
        $this->assertStringContainsString('between 6 AM and 10 PM', $errors2[0]);

        $startValidForEndInvalid = Carbon::parse('2024-01-01 21:00:00');
        $endInvalidLateBoundary = Carbon::parse('2024-01-01 22:00:00'); // End at 10 PM is invalid by original logic
        $errors3 = $this->service->validateTimeRange($startValidForEndInvalid, $endInvalidLateBoundary);
        $this->assertNotEmpty($errors3);
        $this->assertStringContainsString('between 6 AM and 10 PM', $errors3[0]);
    }

    public function test_validate_time_range_respects_custom_config_values()
    {
        // Set custom config for this test
        Config::set('pets.availability_start_hour', 8);  // 8 AM
        Config::set('pets.availability_end_hour', 20); // 8 PM (20:00)

        // Valid case with custom config
        $startValid = Carbon::parse('2024-01-01 08:00:00');
        $endValid = Carbon::parse('2024-01-01 19:59:00'); // Ends before 8 PM
        $this->assertEmpty($this->service->validateTimeRange($startValid, $endValid));

        // Invalid cases with custom config
        $startTooEarly = Carbon::parse('2024-01-01 07:59:00'); // Before 8 AM
        $endOk = Carbon::parse('2024-01-01 10:00:00');
        $errors1 = $this->service->validateTimeRange($startTooEarly, $endOk);
        $this->assertNotEmpty($errors1);
        $this->assertStringContainsString('between 8 AM and 8 PM', $errors1[0]); // Check dynamic message

        $startOk = Carbon::parse('2024-01-01 19:00:00');
        $endTooLate = Carbon::parse('2024-01-01 20:00:00'); // End at 8 PM is invalid by original logic
        $errors2 = $this->service->validateTimeRange($startOk, $endTooLate);
        $this->assertNotEmpty($errors2);
        $this->assertStringContainsString('between 8 AM and 8 PM', $errors2[0]); // Check dynamic message
    }

    public function test_validate_time_range_error_message_is_dynamic()
    {
        Config::set('pets.availability_start_hour', 9);
        Config::set('pets.availability_end_hour', 17); // 5 PM

        $start = Carbon::parse('2024-01-01 08:00:00'); // Too early
        $end = Carbon::parse('2024-01-01 10:00:00');
        $errors = $this->service->validateTimeRange($start, $end);
        $this->assertNotEmpty($errors);
        $this->assertEquals('Bookings are only allowed between 9 AM and 5 PM.', $errors[0]);
    }
}
```

This completes the creation of the planned test files.
I've defined basic factories within `RouteRefactoringTest.php` for self-containment in this environment; in a real project, they'd be in `database/factories`.
The tests cover:
*   Route resolution for key admin/frontend routes.
*   `Booking` model's `CreditService` DI via constructor and method calls.
*   `Booking` model's `saving` event correctly populating `start_time` and `end_time`.
*   `PetAvailabilityService` correctly using and respecting `config/pets.php` values for time validation and error messages.

I am now ready to submit the report.
