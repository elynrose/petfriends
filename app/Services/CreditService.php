<?php

namespace App\Services;

use App\Models\User;
use App\Models\Booking;
use App\Models\Pet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreditService
{
    /**
     * Calculate hours based on booking duration
     *
     * @param Booking $booking
     * @return int
     */
    public function calculateBookingHours(Booking $booking): int
    {
        $start = Carbon::parse($booking->from . ' ' . $booking->from_time);
        $end = Carbon::parse($booking->to . ' ' . $booking->to_time);
        
        // Calculate hours, rounding up to the nearest hour
        $hours = ceil($end->diffInMinutes($start) / 60);
        
        // Ensure minimum of 1 credit
        return max(1, (int) $hours);
    }

    /**
     * Calculate credits based on booking duration
     *
     * @param Booking $booking
     * @return int
     */
    public function calculateCreditsFromBooking(Booking $booking): int
    {
        return $this->calculateBookingHours($booking);
    }

    /**
     * Award credits to a user for a completed booking
     *
     * @param User $user
     * @param Booking $booking
     * @return bool
     */
    public function awardCreditsForBooking(User $user, Booking $booking): bool
    {
        try {
            DB::beginTransaction();
            
            $credits = $this->calculateCreditsFromBooking($booking);
            $result = $user->addCredits(
                $credits,
                "Credits earned from completed booking #{$booking->id} for {$booking->pet->name}",
                $booking
            );
            
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error awarding credits for booking', [
                'booking_id' => $booking->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Check if a user has enough credits for a booking
     *
     * @param User $user
     * @param Booking $booking
     * @return bool
     */
    public function hasEnoughCreditsForBooking(User $user, Booking $booking): bool
    {
        $requiredCredits = $this->calculateCreditsFromBooking($booking);
        return $user->hasEnoughCredits($requiredCredits);
    }

    /**
     * Deduct credits from a user for a booking
     *
     * @param User $user
     * @param Booking $booking
     * @return bool
     */
    public function deductCreditsForBooking(User $user, Booking $booking): bool
    {
        try {
            DB::beginTransaction();
            
            $requiredCredits = $this->calculateCreditsFromBooking($booking);
            $result = $user->deductCredits(
                $requiredCredits,
                "Credits deducted for booking #{$booking->id}",
                $booking
            );
            
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deducting credits for booking', [
                'booking_id' => $booking->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Calculate hours based on pet availability
     *
     * @param Pet $pet
     * @return int
     */
    public function calculatePetAvailabilityHours(Pet $pet): int
    {
        if (!$pet->from || !$pet->to || !$pet->from_time || !$pet->to_time) {
            return 0;
        }

        try {
            $start = Carbon::parse($pet->from . ' ' . $pet->from_time);
            $end = Carbon::parse($pet->to . ' ' . $pet->to_time);
            
            // Validate time range
            if ($end->lte($start)) {
                return 0;
            }
            
            // Calculate hours, rounding up to the nearest hour
            $hours = ceil($end->diffInMinutes($start) / 60);
            
            // Ensure minimum of 1 credit and maximum of 24 hours
            return max(1, min(24, (int) $hours));
        } catch (\Exception $e) {
            \Log::error('Error calculating pet availability hours', [
                'pet_id' => $pet->id,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Refund credits to a user
     *
     * @param User $user
     * @param Booking $booking
     * @return bool
     */
    public function refundCreditsForBooking(User $user, Booking $booking): bool
    {
        try {
            DB::beginTransaction();
            
            $credits = $this->calculateCreditsFromBooking($booking);
            $result = $user->refundCredits(
                $credits,
                "Credits refunded for cancelled booking #{$booking->id}",
                $booking
            );
            
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error refunding credits for booking', [
                'booking_id' => $booking->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
