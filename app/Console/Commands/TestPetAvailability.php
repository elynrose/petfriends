<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pet;
use App\Services\PetAvailabilityService;
use Carbon\Carbon;

class TestPetAvailability extends Command
{
    protected $signature = 'test:pet-availability {pet_id}';
    protected $description = 'Test pet availability changes and credit handling';

    public function handle()
    {
        $petId = $this->argument('pet_id');
        $pet = Pet::find($petId);

        if (!$pet) {
            $this->error("Pet with ID {$petId} not found!");
            return 1;
        }

        $service = app(PetAvailabilityService::class);
        $user = $pet->user;

        $this->info("Found pet: {$pet->name}");
        $this->info("Current availability: {$pet->from} {$pet->from_time} to {$pet->to} {$pet->to_time}");
        $this->info("Current credits: {$user->credits}");

        // Helper to calculate hours
        $calcHours = function($from, $fromTime, $to, $toTime) {
            $start = Carbon::parse($from . ' ' . $fromTime);
            $end = Carbon::parse($to . ' ' . $toTime);
            return ceil($end->diffInMinutes($start) / 60);
        };

        // Test 1: Extend availability by 1 hour
        $this->info("\nTest 1: Extending availability by 1 hour");
        $oldHours = $calcHours($pet->from, $pet->from_time, $pet->to, $pet->to_time);
        $newTo = Carbon::parse($pet->to . ' ' . $pet->to_time)->addHour();
        $pet->to = $newTo->format('Y-m-d');
        $pet->to_time = $newTo->format('H:i:s');
        $newHours = $calcHours($pet->from, $pet->from_time, $pet->to, $pet->to_time);
        $pet->save();
        $result = $service->handleCreditChanges($pet, $user, $newHours, $oldHours);
        $this->info("Credits changed: {$result['credits_changed']}");
        $this->info("New user credits: " . $user->fresh()->credits);

        // Test 2: Reduce availability by 1 hour
        $this->info("\nTest 2: Reducing availability by 1 hour");
        $oldHours = $newHours;
        $newTo = Carbon::parse($pet->to . ' ' . $pet->to_time)->subHour();
        $pet->to = $newTo->format('Y-m-d');
        $pet->to_time = $newTo->format('H:i:s');
        $newHours = $calcHours($pet->from, $pet->from_time, $pet->to, $pet->to_time);
        $pet->save();
        $result = $service->handleCreditChanges($pet, $user, $newHours, $oldHours);
        $this->info("Credits changed: {$result['credits_changed']}");
        $this->info("New user credits: " . $user->fresh()->credits);

        // Test 3: Mark as not available
        $this->info("\nTest 3: Marking as not available");
        $pet->not_available = true;
        $pet->save();
        // Refund all credits for the current availability
        $result = $service->handleNotAvailable($pet, $user);
        $this->info("Credits refunded: {$result['credits_changed']}");
        $this->info("New user credits: " . $user->fresh()->credits);

        // Test 4: Mark as available again (8 hours)
        $this->info("\nTest 4: Marking as available again");
        $pet->not_available = false;
        $pet->from = Carbon::now()->addDay()->format('Y-m-d');
        $pet->to = Carbon::now()->addDay()->addHours(8)->format('Y-m-d');
        $pet->from_time = '09:00:00';
        $pet->to_time = '17:00:00';
        $pet->save();
        $newHours = $calcHours($pet->from, $pet->from_time, $pet->to, $pet->to_time);
        $result = $service->handleCreditChanges($pet, $user, $newHours, null);
        $this->info("Credits deducted: {$result['credits_changed']}");
        $this->info("Final user credits: " . $user->fresh()->credits);

        return 0;
    }
} 