<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pet;
use App\Models\Review;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function show(User $user)
    {
        // Get total hours of care provided
        $totalHours = $user->bookings()
            ->where('status', 'completed')
            ->get()
            ->sum(function ($booking) {
                $from = \Carbon\Carbon::parse($booking->from . ' ' . $booking->from_time);
                $to = \Carbon\Carbon::parse($booking->to . ' ' . $booking->to_time);
                return $from->diffInHours($to);
            });

        // Get pets owned by the user
        $pets = $user->pets()
            ->with(['media'])
            ->get();

        // Get reviews received
        $reviews = Review::where('reviewer_id', $user->id)
            ->orWhere('reviewed_id', $user->id)
            ->with(['reviewer', 'reviewed'])
            ->latest()
            ->get();

        return view('frontend.members.show', compact('user', 'totalHours', 'pets', 'reviews'));
    }
} 