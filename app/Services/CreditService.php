<?php

namespace App\Services;

use App\Models\User;
use App\Models\Booking;
use App\Models\Pet;
use Carbon\Carbon;

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
        $credits = $this->calculateCreditsFromBooking($booking);
        $user->addCredits($credits, "Credits earned from completed booking for {$booking->pet->name}", $booking);
        
        return true;
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
        $requiredCredits = $this->calculateCreditsFromBooking($booking);
        return $user->deductCredits($requiredCredits);
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

        $start = Carbon::parse($pet->from . ' ' . $pet->from_time);
        $end = Carbon::parse($pet->to . ' ' . $pet->to_time);
        
        // Calculate hours, rounding up to the nearest hour
        $hours = ceil($end->diffInMinutes($start) / 60);
        
        // Ensure minimum of 1 credit
        return max(1, (int) $hours);
    }
}
