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
    public function handleCreditChanges(Pet $pet, User $user, int $newHours, int $oldHours): array
    {
        $result = [
            'success' => true,
            'message' => '',
            'credits_changed' => 0
        ];

        try {
            DB::beginTransaction();

            if ($oldHours > 0) {
                // Existing availability being modified
                $hourDifference = $newHours - $oldHours;

                if ($hourDifference > 0) {
                    // Extending availability
                    if (!$user->hasEnoughCredits($hourDifference)) {
                        throw new \Exception("You need {$hourDifference} additional credits to extend this availability period. You currently have {$user->credits} credits.");
                    }
                    $user->deductCredits($hourDifference, "Extended availability period for {$pet->name} by {$hourDifference} hours");
                    $result['message'] = "Successfully extended availability period for {$pet->name} by {$hourDifference} hours. {$hourDifference} credits have been deducted from your account.";
                    $result['credits_changed'] = $hourDifference;
                } elseif ($hourDifference < 0) {
                    // Reducing availability
                    $refundAmount = abs($hourDifference);
                    $user->refundCredits($refundAmount, "Reduced availability period for {$pet->name} by {$refundAmount} hours");
                    $result['message'] = "Successfully reduced availability period for {$pet->name} by {$refundAmount} hours. {$refundAmount} credits have been refunded to your account.";
                    $result['credits_changed'] = -$refundAmount;
                }
            } else {
                // New availability
                if (!$user->hasEnoughCredits($newHours)) {
                    throw new \Exception("You need {$newHours} credits to make {$pet->name} available for this duration. You currently have {$user->credits} credits.");
                }
                $user->deductCredits($newHours, "New availability period for {$pet->name} ({$newHours} hours)");
                $result['message'] = "Successfully made {$pet->name} available for {$newHours} hours. {$newHours} credits have been deducted from your account.";
                $result['credits_changed'] = $newHours;
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            $result['success'] = false;
            $result['message'] = $e->getMessage();
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

            if (!$pet->not_available) {
                $oldStart = \Carbon\Carbon::parse($pet->from . ' ' . $pet->from_time);
                $oldEnd = \Carbon\Carbon::parse($pet->to . ' ' . $pet->to_time);
                $oldHours = ceil($oldEnd->diffInMinutes($oldStart) / 60);
                
                $user->refundCredits($oldHours, "Pet {$pet->name} marked as not available");
                $result['message'] = "Successfully marked {$pet->name} as not available. {$oldHours} credits have been refunded to your account for the cancelled availability period from " . 
                    $oldStart->format('F j, Y g:i A') . " to " . $oldEnd->format('F j, Y g:i A') . ".";
                $result['credits_changed'] = -$oldHours;
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            $result['success'] = false;
            $result['message'] = $e->getMessage();
        }

        return $result;
    }
} 