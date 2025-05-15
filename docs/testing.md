# Testing Documentation

## Overview
This document outlines the testing strategy, test cases, and best practices for the PetFriends application.

## Testing Strategy

### 1. Test Types

#### Unit Tests
- Test individual components in isolation
- Focus on business logic
- Mock dependencies
- Fast execution

#### Feature Tests
- Test complete features
- Include multiple components
- Test user interactions
- Database interactions

#### Integration Tests
- Test component interactions
- External service integration
- API endpoints
- Database operations

#### Browser Tests
- End-to-end testing
- User interface testing
- JavaScript functionality
- Real browser testing

## Test Structure

### 1. Unit Tests
```php
// tests/Unit/PetTest.php
class PetTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_feature_pet()
    {
        $pet = Pet::factory()->create();
        $user = User::factory()->create(['premium' => true]);

        $pet->feature();

        $this->assertNotNull($pet->featured_until);
        $this->assertTrue($pet->featured_until->isFuture());
    }

    public function test_cannot_feature_pet_without_premium()
    {
        $pet = Pet::factory()->create();
        $user = User::factory()->create(['premium' => false]);

        $this->expectException(PremiumRequiredException::class);
        $pet->feature();
    }
}
```

### 2. Feature Tests
```php
// tests/Feature/PetManagementTest.php
class PetManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_pet()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('pets.store'), [
                'name' => 'Buddy',
                'type' => 'dog',
                'age' => 3,
                'gender' => 'male'
            ]);

        $response->assertRedirect(route('pets.index'));
        $this->assertDatabaseHas('pets', [
            'name' => 'Buddy',
            'user_id' => $user->id
        ]);
    }

    public function test_user_cannot_create_pet_without_authentication()
    {
        $response = $this->post(route('pets.store'), [
            'name' => 'Buddy',
            'type' => 'dog',
            'age' => 3,
            'gender' => 'male'
        ]);

        $response->assertRedirect(route('login'));
    }
}
```

### 3. Integration Tests
```php
// tests/Integration/BookingServiceTest.php
class BookingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_service_validates_time()
    {
        $pet = Pet::factory()->create();
        $service = app(BookingService::class);

        $result = $service->validateBookingTime(
            $pet,
            now()->setHour(5),
            now()->setHour(7)
        );

        $this->assertFalse($result['success']);
        $this->assertEquals(
            'Bookings are only allowed between 6 AM and 10 PM.',
            $result['message']
        );
    }
}
```

### 4. Browser Tests
```php
// tests/Browser/PetBookingTest.php
class PetBookingTest extends DuskTestCase
{
    public function test_user_can_book_pet()
    {
        $user = User::factory()->create();
        $pet = Pet::factory()->create();

        $this->browse(function (Browser $browser) use ($user, $pet) {
            $browser->loginAs($user)
                   ->visit(route('pets.show', $pet))
                   ->type('from_time', now()->addDay()->format('Y-m-d H:i'))
                   ->type('to_time', now()->addDays(2)->format('Y-m-d H:i'))
                   ->press('Book Now')
                   ->assertPathIs('/bookings')
                   ->assertSee('Booking confirmed');
        });
    }
}
```

## Test Data

### 1. Factories
```php
// database/factories/PetFactory.php
class PetFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['cat', 'dog']),
            'name' => $this->faker->name,
            'age' => $this->faker->numberBetween(1, 15),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'not_available' => false,
            'from_time' => '09:00:00',
            'to_time' => '17:00:00'
        ];
    }
}
```

### 2. Seeders
```php
// database/seeders/TestDataSeeder.php
class TestDataSeeder extends Seeder
{
    public function run()
    {
        // Create test users
        User::factory()->count(5)->create();
        
        // Create test pets
        Pet::factory()->count(10)->create();
        
        // Create test bookings
        Booking::factory()->count(20)->create();
    }
}
```

## Test Coverage

### 1. Coverage Configuration
```xml
<!-- phpunit.xml -->
<coverage>
    <include>
        <directory suffix=".php">./app</directory>
    </include>
    <exclude>
        <directory>./app/Console</directory>
        <directory>./app/Exceptions</directory>
    </exclude>
</coverage>
```

### 2. Coverage Reports
```bash
# Generate coverage report
php artisan test --coverage-html coverage/

# Generate coverage report for specific test
php artisan test --coverage-html coverage/ tests/Unit/PetTest.php
```

## Mocking

### 1. Service Mocks
```php
public function test_booking_service_uses_credit_service()
{
    $creditService = $this->mock(CreditService::class);
    $creditService->shouldReceive('validateCreditTransaction')
        ->once()
        ->andReturn(true);

    $bookingService = new BookingService($creditService);
    $result = $bookingService->createBooking($pet, $user, $from, $to);

    $this->assertTrue($result['success']);
}
```

### 2. Event Mocks
```php
public function test_booking_creates_event()
{
    Event::fake();

    $booking = Booking::factory()->create();

    Event::assertDispatched(BookingCreated::class, function ($event) use ($booking) {
        return $event->booking->id === $booking->id;
    });
}
```

## Test Environment

### 1. Environment Configuration
```php
// phpunit.xml
<php>
    <env name="APP_ENV" value="testing"/>
    <env name="DB_CONNECTION" value="sqlite"/>
    <env name="DB_DATABASE" value=":memory:"/>
</php>
```

### 2. Test Database
```php
// config/database.php
'testing' => [
    'driver' => 'sqlite',
    'database' => ':memory:',
    'prefix' => '',
],
```

## Best Practices

### 1. Test Organization
- Group tests by feature
- Use descriptive test names
- Follow AAA pattern (Arrange, Act, Assert)
- Keep tests independent

### 2. Test Data
- Use factories for test data
- Keep test data minimal
- Use meaningful test data
- Clean up test data

### 3. Assertions
- Use specific assertions
- Test edge cases
- Test error conditions
- Test success conditions

### 4. Performance
- Keep tests fast
- Use in-memory database
- Mock external services
- Use appropriate test types

## Continuous Integration

### 1. GitHub Actions
```yaml
# .github/workflows/tests.yml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.1'
        
    - name: Install Dependencies
      run: composer install -q --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist
        
    - name: Execute tests
      run: php artisan test
```

### 2. Test Reports
```bash
# Generate JUnit report
php artisan test --log-junit junit.xml

# Generate coverage report
php artisan test --coverage-clover coverage.xml
```

## Troubleshooting

### 1. Common Issues

1. Database Issues
```php
// Reset database between tests
use RefreshDatabase;

// Use transactions
use DatabaseTransactions;
```

2. Time Issues
```php
// Freeze time
$this->freezeTime();

// Travel to specific time
$this->travelTo(now()->addDay());
```

3. Cache Issues
```php
// Clear cache before test
Cache::flush();

// Mock cache
Cache::shouldReceive('get')->andReturn(null);
```

## Security Testing

### 1. Authentication Tests
```php
public function test_unauthorized_user_cannot_access_pet()
{
    $pet = Pet::factory()->create();
    
    $response = $this->get(route('pets.edit', $pet));
    
    $response->assertRedirect(route('login'));
}
```

### 2. Authorization Tests
```php
public function test_user_cannot_edit_other_users_pet()
{
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $pet = Pet::factory()->create(['user_id' => $otherUser->id]);
    
    $response = $this->actingAs($user)
        ->put(route('pets.update', $pet), [
            'name' => 'New Name'
        ]);
    
    $response->assertStatus(403);
}
```

## Performance Testing

### 1. Load Testing
```php
public function test_pet_listing_performance()
{
    $start = microtime(true);
    
    $response = $this->get(route('pets.index'));
    
    $end = microtime(true);
    $time = $end - $start;
    
    $this->assertLessThan(1.0, $time);
}
```

### 2. Memory Testing
```php
public function test_pet_search_memory_usage()
{
    $startMemory = memory_get_usage();
    
    $response = $this->get(route('pets.search', ['q' => 'test']));
    
    $endMemory = memory_get_usage();
    $memoryUsed = $endMemory - $startMemory;
    
    $this->assertLessThan(10 * 1024 * 1024, $memoryUsed); // 10MB
}
``` 