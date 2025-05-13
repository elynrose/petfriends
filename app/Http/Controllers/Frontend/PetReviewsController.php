<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyPetReviewRequest;
use App\Http\Requests\StorePetReviewRequest;
use App\Http\Requests\UpdatePetReviewRequest;
use App\Models\Booking;
use App\Models\Pet;
use App\Models\PetReview;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class PetReviewsController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('pet_review_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $petReviews = PetReview::with(['pet', 'booking'])->get();

        return view('frontend.petReviews.index', compact('petReviews'));
    }

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
            'score' => 'required|integer|min:1|max:5',
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

        try {
            // Create the review
            PetReview::create([
                'booking_id' => $booking->id,
                'user_id' => Auth::id(),
                'pet_id' => $booking->pet_id,
                'score' => (int) $request->score,
                'comment' => $request->comment,
            ]);

            return redirect()->route('frontend.bookings.index')
                ->with('success', 'Thank you for your review!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating review: ' . $e->getMessage());
        }
    }

    public function edit(PetReview $petReview)
    {
        abort_if(Gate::denies('pet_review_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $pets = Pet::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $bookings = Booking::pluck('status', 'id')->prepend(trans('global.pleaseSelect'), '');

        $petReview->load('pet', 'booking');

        return view('frontend.petReviews.edit', compact('bookings', 'petReview', 'pets'));
    }

    public function update(UpdatePetReviewRequest $request, PetReview $petReview)
    {
        $petReview->update($request->all());

        return redirect()->route('frontend.pet-reviews.index');
    }

    public function show(PetReview $petReview)
    {
        abort_if(Gate::denies('pet_review_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $petReview->load('pet', 'booking');

        return view('frontend.petReviews.show', compact('petReview'));
    }

    public function destroy(PetReview $petReview)
    {
        abort_if(Gate::denies('pet_review_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $petReview->delete();

        return back();
    }

    public function massDestroy(MassDestroyPetReviewRequest $request)
    {
        $petReviews = PetReview::find(request('ids'));

        foreach ($petReviews as $petReview) {
            $petReview->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
