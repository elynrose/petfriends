<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Booking $booking)
    {
        return $user->id === $booking->user_id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Booking $booking)
    {
        return $user->id === $booking->user_id;
    }

    public function delete(User $user, Booking $booking)
    {
        return $user->id === $booking->user_id;
    }

    public function restore(User $user, Booking $booking)
    {
        return $user->id === $booking->user_id;
    }

    public function forceDelete(User $user, Booking $booking)
    {
        return $user->id === $booking->user_id;
    }

    public function complete(User $user, Booking $booking)
    {
        return $user->id === $booking->user_id;
    }
} 