<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function create(Request $request)
    {
        $booking = Booking::findOrFail($request->booking);

        // Check if the booking is completed and belongs to the user
        if ($booking->status !== 'completed' || $booking->user_id !== Auth::id()) {
            return redirect()->route('frontend.bookings.index')
                ->with('error', 'You can only review completed bookings that you made.');
        }

        // Check if a review already exists
        if ($booking->review) {
            return redirect()->route('frontend.bookings.index')
                ->with('error', 'You have already reviewed this booking.');
        }

        return view('frontend.pet_reviews.create', compact('booking'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $booking = Booking::findOrFail($request->booking_id);

        // Check if the booking is completed and belongs to the user
        if ($booking->status !== 'completed' || $booking->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You can only review completed bookings that you made.');
        }

        // Check if a review already exists
        if ($booking->review) {
            return redirect()->back()->with('error', 'You have already reviewed this booking.');
        }

        // Create the review
        Review::create([
            'booking_id' => $booking->id,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->route('frontend.bookings.index')
            ->with('success', 'Thank you for your review!');
    }
} 