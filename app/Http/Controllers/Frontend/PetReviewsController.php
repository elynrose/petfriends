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

class PetReviewsController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('pet_review_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $petReviews = PetReview::with(['pet', 'booking'])->get();

        return view('frontend.petReviews.index', compact('petReviews'));
    }

    public function create()
    {
        abort_if(Gate::denies('pet_review_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $pets = Pet::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $bookings = Booking::pluck('status', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('frontend.petReviews.create', compact('bookings', 'pets'));
    }

    public function store(StorePetReviewRequest $request)
    {
        $petReview = PetReview::create($request->all());

        return redirect()->route('frontend.pet-reviews.index');
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
