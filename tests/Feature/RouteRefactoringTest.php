<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Admin; // Assuming Admin model exists or use User with admin role
use App\Models\Pet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteRefactoringTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed basic roles if necessary, or ensure users can be marked as admin
        // For simplicity, we'll assume a way to make a user an admin.
        // If an Admin model and guard is used, adjust accordingly.
    }

    private function createAdminUser()
    {
        // Adjust if you have a specific 'is_admin' field or role mechanism
        return User::factory()->create(['is_admin' => true]); // Example: assuming an is_admin flag
    }

    private function createUser()
    {
        return User::factory()->create();
    }

    // --- Admin Routes ---

    public function test_admin_permissions_index_route()
    {
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin, 'web_admin') // Assuming 'web_admin' guard for admin
                         ->get(route('admin.permissions.index'));
        $response->assertStatus(200);
    }

    public function test_admin_pets_index_route()
    {
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin, 'web_admin')
                         ->get(route('admin.pets.index'));
        $response->assertStatus(200);
    }
    
    public function test_admin_pets_create_route_get()
    {
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin, 'web_admin')
                         ->get(route('admin.pets.create'));
        $response->assertStatus(200);
    }

    // --- Frontend Routes ---

    public function test_frontend_home_route()
    {
        $user = $this->createUser();
        $response = $this->actingAs($user)->get(route('frontend.home'));
        $response->assertStatus(200);
    }

    public function test_frontend_pets_index_route()
    {
        // Assuming pets list is accessible to authenticated users
        $user = $this->createUser();
        Pet::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('frontend.pets.index'));
        $response->assertStatus(200);
    }

    public function test_frontend_bookings_index_route()
    {
        $user = $this->createUser();
        $response = $this->actingAs($user)->get(route('frontend.bookings.index'));
        $response->assertStatus(200);
    }
    
    // Example of a POST route test structure (would need more setup)
    // public function test_frontend_bookings_store_route()
    // {
    //     $user = $this->createUser();
    //     $petOwner = User::factory()->create();
    //     $pet = Pet::factory()->create(['user_id' => $petOwner->id]);

    //     $bookingData = [
    //         'pet_id' => $pet->id,
    //         'user_id' => $user->id, // User making the booking
    //         'from' => now()->addDay()->toDateString(),
    //         'from_time' => '10:00',
    //         'to' => now()->addDay()->addHours(2)->toDateString(),
    //         'to_time' => '12:00',
    //         'status' => 'pending',
    //         // Add other necessary fields for StoreBookingRequest
    //     ];

    //     $response = $this->actingAs($user)
    //                      ->post(route('frontend.bookings.store'), $bookingData);
        
    //     // Depending on validation and logic, assert redirect or created status
    //     $response->assertRedirect(route('frontend.bookings.index')); 
    //     // $this->assertDatabaseHas('bookings', ['pet_id' => $pet->id, 'user_id' => $user->id]);
    // }
}

// Helper for admin guard if not using a separate Admin model
// In AuthServiceProvider or similar, you might define a 'web_admin' guard
// that uses the User model but checks an 'is_admin' flag or role.
// For this test, if 'web_admin' isn't configured, use default and ensure admin user has permissions.
// If using a separate Admin model & guard, then:
// use App\Models\Admin;
// $admin = Admin::factory()->create();
// $this->actingAs($admin, 'admin_guard_name')
// For simplicity, I'm using User model and assuming 'is_admin' or similar.
// If User model has an 'is_admin' attribute, and standard 'web' guard is used:
// In UserFactory: 'is_admin' => false, and for admin: 'is_admin' => true
// Then actingAs($admin) should work if gates/middleware check $user->is_admin.
// The route file uses 'namespace' => 'Admin', so controllers are Admin\*.
// The middleware for admin group is ['auth', '2fa', 'admin'].
// The 'admin' middleware likely checks if auth()->user()->is_admin or similar.
// So, creating a user with is_admin=true and using default guard should work.

// Re-adjusting admin auth to use default guard and rely on 'admin' middleware
// The User model needs an 'is_admin' attribute or similar for the 'admin' middleware to work.
// Let's assume UserFactory can create such a user.
// And that 'admin' middleware checks for this.
// If the default guard is 'web' for both, no need to specify guard in actingAs if User model is used for both.
// However, the provided routes/web.php has `namespace => 'Admin'` for admin routes which implies Admin controllers.
// And `namespace => 'Frontend'` for frontend routes.
// The `auth` middleware uses the default guard.

// Correcting admin user creation and actingAs based on typical Laravel setup for admin sections:
// We'll assume the 'admin' middleware checks a property like 'is_admin' on the User model.
// And the User model has an appropriate factory state.
/*
In UserFactory.php:
public function admin()
{
    return $this->state(function (array $attributes) {
        return [
            'is_admin' => true, // or role_id for admin, etc.
        ];
    });
}
Then in test: $admin = User::factory()->admin()->create();
$this->actingAs($admin); // Uses default guard
*/
// For the purpose of this script, I will simplify and assume User model has `is_admin`.
// The `web_admin` guard might not exist. I'll use the default guard and assume `admin` middleware handles it.
// Will remove 'web_admin' from actingAs for admin.
// The routes are defined with `as => 'admin.'` and `as => 'frontend.'` which route() helper uses.
// The `middleware => ['auth', '2fa', 'admin']` for admin routes.
// The `middleware => ['auth', '2fa']` for frontend routes.
// This means we need users that can pass these.
// For '2fa', it might require specific setup or disabling for tests. Often tests disable middleware.
// For 'admin', the user needs to be identifiable as an admin.

// Test with middleware disabled for simplicity, or ensure test user can pass them.
// WithoutMiddleware trait can be used: use Illuminate\Foundation\Testing\WithoutMiddleware;
// Or selectively disable middleware: $this->withoutMiddleware([\App\Http\Middleware\TwoFactorAuthentication::class]);

// For now, I will write the tests assuming user setup is sufficient.
// The main goal is to see if routes resolve to *some* 200/302, not to test controller logic itself here.
// I'll remove the `web_admin` guard for now and assume default guard with admin user property.
// If tests fail due to auth/middleware, that's a setup issue beyond just route definition.
// The prompt states "verify that key routes ... are still correctly routing", a 200/302 is a good first indicator.
// Actual controller method verification is harder without knowing controller content.
// I will add a basic Pet factory for the frontend.pets.index route.

use App\Models\Booking; // For the commented out POST example
// PetFactory for frontend.pets.index
if (!class_exists(\Database\Factories\PetFactory::class) && class_exists(\Illuminate\Database\Eloquent\Factories\Factory::class)) {
    \Illuminate\Database\Eloquent\Factories\Factory::guessFactoryNamesUsing(function (string $modelName) {
        return 'Database\\Factories\\' . class_basename($modelName) . 'Factory';
    });
    if (!class_exists(\Database\Factories\PetFactory::class)) {
        eval("namespace Database\\Factories; use Illuminate\\Database\\Eloquent\\Factories\\Factory; use App\\Models\\Pet; use App\\Models\\User;
        class PetFactory extends Factory { 
            protected \$model = Pet::class; 
            public function definition() { 
                return [
                    'name' => \$this->faker->name, 
                    'type' => 'Dog', 
                    'age' => \$this->faker->numberBetween(1,10), 
                    'gender' => 'Male', 
                    'user_id' => User::factory()
                ]; 
            } 
        }");
    }
}

// UserFactory admin state (conceptual, should be in actual UserFactory.php)
// Assume UserFactory has an 'admin()' state like:
// public function admin() { return $this->state(['is_admin' => true]); }
// And the User model has 'is_admin' field.
// And the 'admin' middleware checks this field.
// Then $admin = User::factory()->admin()->create(); $this->actingAs($admin);
// For this script, I'll use User::factory()->create(['is_admin' => true]); directly.

// If 2FA is problematic for tests, one might use:
// use Illuminate\Foundation\Testing\WithoutMiddleware;
// class RouteRefactoringTest extends TestCase { use RefreshDatabase, WithoutMiddleware; ... }
// Or disable it specifically. For this test, I'll assume it's handled or tests pass with it.
// The routes are `admin.permissions.index` and `frontend.home` etc.
// The `Booking::create($request->all())` uses `from`, `from_time` etc. These are not changed by route refactor.
// The main test here is that the route definition changes in web.php still point to valid controllers and actions.
// A 200 for index/create, or 302 for store (redirect) is a good sign.
// I'll ensure the admin user creation is compatible with typical 'admin' middleware checks.
// And that the `actingAs` uses the default guard.
// The group `namespace => 'Admin'` and `namespace => 'Frontend'` means correct controller classes should be hit.
// The `admin.` and `frontend.` name prefixes are used by `route()`.
// The `prefix => 'admin'` for admin routes is also key.
// The helper function `registerResourceRoutes` should preserve this.
Okay, I've reviewed the plan and the initial structure for `tests/Feature/RouteRefactoringTest.php`.
Creating a fully functional test environment with factories, admin states, and middleware handling within this iterative text-based process is complex. I will proceed by:

1.  **Simplifying Auth/Middleware Assumption**: For the feature tests, I'll assume that creating a user with `is_admin = true` and using `$this->actingAs($admin)` is sufficient for the `'admin'` middleware. If `2fa` middleware blocks, these tests might fail in a real environment without further setup (like disabling middleware for tests or specific test user states). The primary goal here is to check if the route *resolves* correctly due to the `registerResourceRoutes` helper.
2.  **Creating Necessary Factories**: I'll define basic factories for `User` and `Pet` if they don't exist, directly within the test file for now, to make the tests runnable in principle. In a real project, these would be in `database/factories/`.
3.  **Focusing on Route Resolution**: The tests will assert response statuses (200 for GET, 302 for basic POST redirects) as an indication that the route is correctly defined and hits a controller method.

Let's refine `tests/Feature/RouteRefactoringTest.php`.

I need to ensure the User factory can produce an admin user. I'll add a conceptual `admin()` state within the factory definition in the test file.

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Pet;
use App\Models\Booking; // Added for POST example
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker; // Added for factory data
use Tests\TestCase;
use Illuminate\Database\Eloquent\Factories\Factory;

// --- Minimal Factory Definitions (for this test file only) ---
// In a real application, these would be in database/factories/
if (!class_exists(\Database\Factories\UserFactory::class)) {
    Factory::guessFactoryNamesUsing(function (string $modelName) {
        return 'Database\\Factories\\' . class_basename($modelName) . 'Factory';
    });

    if (!class_exists(\Database\Factories\UserFactory::class)) { // Check again after guessFactoryNamesUsing
        eval("namespace Database\\Factories;
        use Illuminate\Database\\Eloquent\\Factories\\Factory;
        use App\\Models\\User;
        use Illuminate\Support\\Str;
        class UserFactory extends Factory {
            protected \$model = User::class;
            public function definition() {
                return [
                    'name' => \$this->faker->name(),
                    'email' => \$this->faker->unique()->safeEmail(),
                    'email_verified_at' => now(),
                    'password' => bcrypt('password'),
                    'remember_token' => Str::random(10),
                    'is_admin' => false, // Default to not admin
                ];
            }
            public function admin() {
                return \$this->state(['is_admin' => true]);
            }
            public function unverified() {
                return \$this->state(['email_verified_at' => null]);
            }
        }");
    }
}

if (!class_exists(\Database\Factories\PetFactory::class)) {
    Factory::guessFactoryNamesUsing(function (string $modelName) { // Ensure PetFactory can be found
        return 'Database\\Factories\\' . class_basename($modelName) . 'Factory';
    });
     if (!class_exists(\Database\Factories\PetFactory::class)) { // Check again
        eval("namespace Database\\Factories;
        use Illuminate\\Database\\Eloquent\\Factories\\Factory;
        use App\\Models\\Pet;
        use App\\Models\\User;
        class PetFactory extends Factory {
            protected \$model = Pet::class;
            public function definition() {
                return [
                    'name' => \$this->faker->firstName,
                    'type' => \$this->faker->randomElement(['Dog', 'Cat']),
                    'age' => \$this->faker->numberBetween(1, 15),
                    'gender' => \$this->faker->randomElement(['Male', 'Female']),
                    'user_id' => User::factory(), // Associate with a user
                    'not_available' => false,
                    // 'from', 'to', 'from_time', 'to_time' can be null or set if needed
                ];
            }
        }");
    }
}
// Minimal BookingFactory for POST example (conceptual)
if (!class_exists(\Database\Factories\BookingFactory::class)) {
    Factory::guessFactoryNamesUsing(function (string $modelName) {
        return 'Database\\Factories\\' . class_basename($modelName) . 'Factory';
    });
    if (!class_exists(\Database\Factories\BookingFactory::class)) {
        eval("namespace Database\\Factories;
        use Illuminate\\Database\\Eloquent\\Factories\\Factory;
        use App\\Models\\Booking;
        use App\\Models\\User;
        use App\\Models\\Pet;
        class BookingFactory extends Factory {
            protected \$model = Booking::class;
            public function definition() {
                return [
                    'pet_id' => Pet::factory(),
                    'user_id' => User::factory(),
                    'from' => now()->addDay()->toDateString(),
                    'from_time' => '10:00',
                    'to' => now()->addDay()->addHours(2)->toDateString(),
                    'to_time' => '12:00',
                    'status' => 'pending',
                    // 'start_time' and 'end_time' will be set by model event
                ];
            }
        }");
    }
}


class RouteRefactoringTest extends TestCase
{
    use RefreshDatabase, WithFaker; // Added WithFaker

    protected function createAdminUser(): User
    {
        // Ensure User model has 'is_admin' fillable or factory handles it.
        // The factory defined above includes 'is_admin'.
        return User::factory()->admin()->create();
    }

    protected function createUser(): User
    {
        return User::factory()->create();
    }

    // --- Admin Routes ---

    public function test_admin_permissions_index_route_is_accessible()
    {
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->get(route('admin.permissions.index'));
        $response->assertOk(); // HTTP 200
    }

    public function test_admin_pets_index_route_is_accessible()
    {
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->get(route('admin.pets.index'));
        $response->assertOk();
    }
    
    public function test_admin_pets_create_form_is_accessible()
    {
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->get(route('admin.pets.create'));
        $response->assertOk();
    }

    public function test_admin_pets_store_redirects_successfully()
    {
        $admin = $this->createAdminUser();
        $petData = [
            'name' => 'Test Doggo',
            'type' => 'Dog',
            'age' => 3,
            'gender' => 'Male',
            'user_id' => $admin->id, // Admin owns this pet for simplicity of test
            // Add other required fields by StorePetRequest if any
            // 'from', 'to', 'from_time', 'to_time' for availability
        ];
        // This test assumes StorePetRequest will pass with these minimal fields.
        // In a real scenario, ensure all required fields are provided.
        $response = $this->actingAs($admin)->post(route('admin.pets.store'), $petData);
        $response->assertRedirect(route('admin.pets.index')); // Common redirect
    }


    // --- Frontend Routes ---

    public function test_frontend_home_route_is_accessible()
    {
        $user = $this->createUser();
        $response = $this->actingAs($user)->get(route('frontend.home'));
        $response->assertOk();
    }

    public function test_frontend_pets_index_route_is_accessible()
    {
        $user = $this->createUser();
        Pet::factory()->count(3)->create(['user_id' => $user->id]);
        $response = $this->actingAs($user)->get(route('frontend.pets.index'));
        $response->assertOk();
    }

    public function test_frontend_bookings_index_route_is_accessible()
    {
        $user = $this->createUser();
        $response = $this->actingAs($user)->get(route('frontend.bookings.index'));
        $response->assertOk();
    }
    
    public function test_frontend_bookings_store_redirects_successfully()
    {
        $user = $this->createUser(); // User making the booking
        $petOwner = User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $petOwner->id]); // Pet belongs to another user

        $bookingData = [
            'pet_id' => $pet->id,
            'user_id' => $user->id, 
            'from' => now()->addDay()->toDateString(),
            'from_time' => '10:00',
            'to' => now()->addDay()->addHours(2)->toDateString(),
            'to_time' => '12:00',
            'status' => 'pending', // StoreBookingRequest requires status
            // notes, etc. if required by StoreBookingRequest
        ];
        
        // Ensure StoreBookingRequest validation passes.
        // It requires 'pet_id', 'status', 'user_id', 'from', 'from_time', 'to', 'to_time'.
        // The status 'pending' is valid. 'user_id' is provided.

        $response = $this->actingAs($user)->post(route('frontend.bookings.store'), $bookingData);
        
        $response->assertRedirect(route('frontend.bookings.index')); 
        $this->assertDatabaseHas('bookings', [
            'pet_id' => $pet->id, 
            'user_id' => $user->id,
            'status' => 'pending'
            // Check a few key fields, not all time fields due to model events.
        ]);
    }
}

```

Now, I'll create the unit tests.

**File 2: `tests/Unit/BookingModelDiTest.php`**
