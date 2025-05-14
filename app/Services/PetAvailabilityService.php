<?php

namespace App\Services;

use App\Models\Pet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PetAvailabilityService
{
    const MIN_HOURS = 1;
    const MAX_HOURS = 24;
    const START_TIME = '06:00';
    const END_TIME = '22:00';
    const MIN_DURATION = 60; // minutes

    /**
     * Calculate hours between two dates
     */
    public function calculateHours($start, $end): int
    {
        return ceil($end->diffInMinutes($start) / 60);
    }

    /**
     * Validate time range
     */
    public function validateTimeRange($start, $end): array
    {
        $errors = [];

        if ($start->isPast()) {
            $errors[] = 'Start date and time must be in the future.';
        }

        if ($end->isPast()) {
            $errors[] = 'End date and time must be in the future.';
        }

        if ($end->lte($start)) {
            $errors[] = 'End date and time must be after start date and time.';
        }

        $durationInHours = $this->calculateHours($start, $end);
        if ($durationInHours < self::MIN_HOURS) {
            $errors[] = "Booking duration must be at least " . self::MIN_HOURS . " hour.";
        }

        if ($durationInHours > self::MAX_HOURS) {
            $errors[] = "Booking duration cannot exceed " . self::MAX_HOURS . " hours.";
        }

        $startHour = $start->hour;
        $endHour = $end->hour;
        if ($startHour < 6 || $startHour >= 22 || $endHour < 6 || $endHour >= 22) {
            $errors[] = 'Bookings are only allowed between 6 AM and 10 PM.';
        }

        return $errors;
    }

    /**
     * Handle credit changes for pet availability
     */
    public function handleCreditChanges(Pet $pet, User $user, $newHours, $oldHours = null): array
    {
        $result = [
            'success' => true,
            'message' => '',
            'credits_changed' => 0
        ];

        try {
            DB::beginTransaction();

            if ($oldHours !== null) {
                // Updating existing availability
                $hourDifference = $newHours - $oldHours;

                if ($hourDifference > 0) {
                    // Extending availability
                    if (!$user->hasEnoughCredits($hourDifference)) {
                        throw new \Exception("You need {$hourDifference} additional credits to extend availability for {$pet->name}. You currently have {$user->credits} credits.");
                    }
                    $user->deductCredits($hourDifference, "Extended availability for {$pet->name} by {$hourDifference} hours");
                    $result['message'] = "Successfully extended availability and deducted {$hourDifference} credits.";
                    $result['credits_changed'] = $hourDifference;
                } elseif ($hourDifference < 0) {
                    // Reducing availability
                    $refundAmount = abs($hourDifference);
                    $user->refundCredits($refundAmount, "Reduced availability for {$pet->name} by {$refundAmount} hours");
                    $result['message'] = "Successfully reduced availability and refunded {$refundAmount} credits.";
                    $result['credits_changed'] = -$refundAmount;
                } else {
                    $result['message'] = "Availability period updated with no change in credits.";
                }
            } else {
                // New availability
                if (!$user->hasEnoughCredits($newHours)) {
                    throw new \Exception("You need {$newHours} credits to make {$pet->name} available for this duration. You currently have {$user->credits} credits.");
                }
                $user->deductCredits($newHours, "New availability period for {$pet->name} ({$newHours} hours)");
                $result['message'] = "Successfully made {$pet->name} available and deducted {$newHours} credits.";
                $result['credits_changed'] = $newHours;
            }

            DB::commit();

            // Log the availability change
            Log::channel('pet_availability')->info('Pet availability updated', [
                'pet_id' => $pet->id,
                'pet_name' => $pet->name,
                'old_hours' => $oldHours,
                'new_hours' => $newHours,
                'credits_changed' => $result['credits_changed'],
                'user_id' => $user->id,
                'user_credits' => $user->credits
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            $result['success'] = false;
            $result['message'] = $e->getMessage();
            
            // Log the error
            Log::channel('pet_availability')->error('Pet availability update failed', [
                'pet_id' => $pet->id,
                'pet_name' => $pet->name,
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);
        }

        return $result;
    }

    /**
     * Handle marking pet as not available
     */
    public function handleNotAvailable(Pet $pet, User $user): array
    {
        $result = [
            'success' => true,
            'message' => '',
            'credits_changed' => 0
        ];

        try {
            DB::beginTransaction();

            // Calculate hours to refund
            $start = Carbon::parse($pet->from . ' ' . $pet->from_time);
            $end = Carbon::parse($pet->to . ' ' . $pet->to_time);
            $hours = ceil($end->diffInMinutes($start) / 60);

            if ($hours > 0) {
                $user->refundCredits($hours, "Pet {$pet->name} marked as not available");
                $result['credits_changed'] = $hours;
                $result['message'] = "Successfully refunded {$hours} credits.";
            } else {
                $result['message'] = "No credits to refund.";
            }

            DB::commit();

            // Log the refund
            Log::channel('pet_availability')->info('Pet marked as not available, credits refunded', [
                'pet_id' => $pet->id,
                'pet_name' => $pet->name,
                'credits_refunded' => $result['credits_changed'],
                'user_id' => $user->id,
                'user_credits' => $user->credits
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            $result['success'] = false;
            $result['message'] = $e->getMessage();
            $result['credits_changed'] = 0;

            // Log the error
            Log::channel('pet_availability')->error('Failed to refund credits when marking pet as not available', [
                'pet_id' => $pet->id,
                'pet_name' => $pet->name,
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);
        }

        return $result;
    }
} 