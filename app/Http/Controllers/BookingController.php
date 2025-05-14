<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\CreditService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected $creditService;

    public function __construct(CreditService $creditService)
    {
        $this->creditService = $creditService;
    }

    public function index()
    {
        $this->authorize('viewAny', Booking::class);
        $bookings = Booking::where('user_id', auth()->user()->id)->get();
        return view('bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);
        return view('bookings.show', compact('booking'));
    }

    public function create()
    {
        $this->authorize('create', Booking::class);
        return view('bookings.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Booking::class);
        // ... existing store logic ...
    }

    public function edit(Booking $booking)
    {
        $this->authorize('update', $booking);
        return view('bookings.edit', compact('booking'));
    }

    public function update(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);
        // ... existing update logic ...
    }

    public function destroy(Booking $booking)
    {
        $this->authorize('delete', $booking);
        // ... existing destroy logic ...
    }

    public function restore(Booking $booking)
    {
        $this->authorize('restore', $booking);
        // ... existing restore logic ...
    }

    public function forceDelete(Booking $booking)
    {
        $this->authorize('forceDelete', $booking);
        // ... existing forceDelete logic ...
    }

    public function complete(Booking $booking)
    {
        $this->authorize('complete', $booking);
        // ... existing complete logic ...
    }
} 