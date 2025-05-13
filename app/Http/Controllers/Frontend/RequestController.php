<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Notifications\BookingStatusNotification;
use App\Services\CreditService;

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
        try {
            // Load necessary relationships
            $booking->load(['pet.user', 'user']);

            // Check if the user owns the pet
            if ($booking->pet->user_id !== Auth::id()) {
                return redirect()->route('frontend.requests.index')
                    ->with('error', 'You are not authorized to manage this booking request.');
            }

            // Validate the request
            $validated = $request->validate([
                'status' => 'required|in:accepted,rejected',
                'start_time' => 'nullable|date_format:H:i',
                'notes' => 'nullable|string|max:500'
            ]);

            // Check if booking is already processed
            if ($booking->status !== 'pending') {
                return redirect()->route('frontend.requests.index')
                    ->with('error', 'This booking request has already been processed.');
            }

            // For accepted bookings, perform additional checks
            if ($validated['status'] === 'accepted') {
                // Get credit service instance
                $creditService = app(CreditService::class);
                
                // Calculate required credits using the service
                $hours = $creditService->calculateBookingHours($booking);
                $requiredCredits = $hours;

                // Debug logging
                \Log::info('Booking time calculation', [
                    'booking_id' => $booking->id,
                    'from_date' => $booking->from,
                    'from_time' => $booking->from_time,
                    'to_date' => $booking->to,
                    'to_time' => $booking->to_time,
                    'hours' => $hours
                ]);

                // Check if pet owner has enough credits
                if (Auth::user()->credits < $requiredCredits) {
                    return redirect()->route('frontend.requests.index')
                        ->with('error', "You do not have enough credits to award for this booking. Required: {$requiredCredits} credits ({$hours} hours), Available: " . Auth::user()->credits . ' credits');
                }

                // Check if the requested time slot is still available
                if ($this->isTimeSlotBooked($booking)) {
                    return redirect()->route('frontend.requests.index')
                        ->with('error', 'The requested time slot is no longer available.');
                }

                // Update booking with start time if provided
                if (!empty($validated['start_time'])) {
                    $booking->from_time = $validated['start_time'];
                }
            }

            // Update booking status and notes
            $booking->status = $validated['status'];
            if (!empty($validated['notes'])) {
                $booking->notes = $validated['notes'];
            }

            // Save the booking
            $booking->save();

            // Send notification to caregiver
            $this->sendStatusNotification($booking);

            // Log the status change
            \Log::info('Booking status updated', [
                'booking_id' => $booking->id,
                'status' => $booking->status,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('frontend.requests.index')
                ->with('success', 'Booking request has been ' . $validated['status']);

        } catch (\Exception $e) {
            \Log::error('Error updating booking request', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('frontend.requests.index')
                ->with('error', 'An error occurred while processing your request: ' . $e->getMessage());
        }
    }

    /**
     * Check if the time slot is already booked
     */
    private function isTimeSlotBooked(Booking $booking): bool
    {
        return Booking::where('pet_id', $booking->pet_id)
            ->where('status', 'accepted')
            ->where(function ($query) use ($booking) {
                $query->whereBetween('from', [$booking->from, $booking->to])
                    ->orWhereBetween('to', [$booking->from, $booking->to]);
            })
            ->exists();
    }

    /**
     * Send notification to caregiver about booking status
     */
    private function sendStatusNotification(Booking $booking): void
    {
        try {
            if (!$booking->user || !$booking->user->email) {
                \Log::warning('Cannot send notification: User or email not found', [
                    'booking_id' => $booking->id,
                    'user_id' => $booking->user_id
                ]);
                return;
            }

            $message = $booking->status === 'accepted' 
                ? "Your booking request for {$booking->pet->name} has been accepted!"
                : "Your booking request for {$booking->pet->name} has been rejected.";

            // Send notification to caregiver
            $booking->user->notify(new BookingStatusNotification($booking, $message));
            
            \Log::info('Booking notification sent successfully', [
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'status' => $booking->status
            ]);
        } catch (\Exception $e) {
            // Log the notification error but don't stop the booking process
            \Log::warning('Failed to send booking notification', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
        }
    }
} 