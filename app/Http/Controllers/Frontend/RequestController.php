<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RequestController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['pet', 'user'])
            ->whereHas('pet', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('frontend.requests.index', compact('bookings'));
    }

    public function update(Request $request, Booking $booking)
    {

        /*User cannot accept a booking if the to date is in the past
        if (Carbon::parse($booking->to)->isPast()) {
            return redirect()->route('frontend.requests.index')
                ->with('error', 'You cannot accept a booking that has already started.');
        }
        */

        // Check if the user owns the pet
        if ($booking->pet->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Validate the status
        $request->validate([
            'status' => 'required|in:accepted,rejected'
        ]);

        // Update the booking status
        $booking->status = $request->status;

        /* If accepting the booking, update the start time to current time
        if ($request->status === 'accepted') {
            $booking->from = Carbon::now()->format('Y-m-d');
            $booking->from_time = Carbon::now()->format('H:i');
        }
        */

        $booking->save();

        return redirect()->route('frontend.requests.index')
            ->with('message', 'Booking request has been ' . $request->status);
    }
} 