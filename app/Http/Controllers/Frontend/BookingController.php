<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyBookingRequest;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Booking;
use App\Models\Pet;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\CreditService;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('booking_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $bookings = Booking::with(['pet', 'user', 'media'])
        ->where('user_id', auth()->id())
        ->orderBy('status', 'desc')
        ->get();

        //Pending bookings count
        $pendingBookingsCount = Booking::where('user_id', auth()->id())
        ->where('status', 'pending')
        ->count();

        return view('frontend.bookings.index', compact('bookings', 'pendingBookingsCount'));
    }

    public function create()
    {
        abort_if(Gate::denies('booking_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('frontend.bookings.create', compact('pets', 'users'));
    }

    public function store(StoreBookingRequest $request)
    {
        $data = $request->all();

        $booking = Booking::where('pet_id', $data['pet_id'])
            ->where('from', '<=', $data['to'])
            ->where('to', '>=', $data['from'])
            ->where('status', '!=', 'completed')
            ->first();

        if ($booking) {
            return redirect()->back()->with('error', 'This pet is already booked for the selected dates.');
        }
        
        $booking = Booking::create($data);

        if ($request->input('photo', false)) {
            $booking->addMedia(storage_path('tmp/uploads/' . basename($request->input('photo'))))->toMediaCollection('photo');
        }

        return redirect()->route('frontend.bookings.index');
    }

    public function edit(Booking $booking)
    {
        abort_if(Gate::denies('booking_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $pets = Pet::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $booking->load('pet', 'user');

        return view('frontend.bookings.edit', compact('booking', 'pets', 'users'));
    }

    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        $booking->update($request->all());

        if (count($booking->photos) > 0) {
            foreach ($booking->photos as $media) {
                if (! in_array($media->file_name, $request->input('photos', []))) {
                    $media->delete();
                }
            }
        }
        $media = $booking->photos->pluck('file_name')->toArray();
        foreach ($request->input('photos', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $booking->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photos');
            }
        }

        return redirect()->route('frontend.bookings.index');
    }

    public function show(Booking $booking)
    {
        abort_if(Gate::denies('booking_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $booking->load('pet', 'user');

        return view('frontend.bookings.show', compact('booking'));
    }

    public function destroy(Booking $booking)
    {
        abort_if(Gate::denies('booking_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Prevent cancellation of completed bookings
        if ($booking->status === 'completed') {
            return redirect()->back()->with('error', 'Completed bookings cannot be cancelled.');
        }

        $booking->delete();

        return back()->with('success', 'Booking has been cancelled successfully.');
    }

    public function massDestroy(MassDestroyBookingRequest $request)
    {
        $bookings = Booking::find(request('ids'));

        foreach ($bookings as $booking) {
            $booking->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('booking_create') && Gate::denies('booking_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Booking();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    public function complete(Booking $booking)
    {
        // Check if the booking is already completed
        if ($booking->status === 'completed') {
            return redirect()->route('frontend.requests.index')
                ->with('error', 'This booking is already completed.');
        }

        // Check if the booking is accepted
        if ($booking->status !== 'accepted') {
            return redirect()->route('frontend.requests.index')
                ->with('error', 'Only accepted bookings can be completed.');
        }

        // Validate booking dates
        $from = Carbon::parse($booking->from . ' ' . $booking->from_time);
        $to = Carbon::parse($booking->to . ' ' . $booking->to_time);

        if ($to->lt($from)) {
            return redirect()->route('frontend.requests.index')
                ->with('error', 'Invalid booking dates: End date/time must be after start date/time.');
        }

        try {
            // Start a database transaction
            DB::beginTransaction();

            // Load necessary relationships
            $booking->load(['pet.user', 'user']);

            // Get credit service instance
            $creditService = app(CreditService::class);
            
            // Calculate hours using the service
            $hours = $creditService->calculateBookingHours($booking);

            // Update booking status
            $booking->status = 'completed';
            $booking->save();

            // Transfer credits from pet owner to caregiver
            // Deduct credits from pet owner
            $booking->pet->user->deductCredits(
                $hours,
                "Credits deducted for {$hours} hours of pet care by {$booking->user->name}",
                $booking
            );

            // Award credits to caregiver
            $booking->user->addCredits(
                $hours,
                "Credits earned for {$hours} hours of pet care for {$booking->pet->name}",
                $booking
            );

            // Set pet as unavailable and clear booking dates
            $pet = $booking->pet;
            $pet->not_available = true;
            $pet->from = null;
            $pet->from_time = null;
            $pet->to = null;
            $pet->to_time = null;
            $pet->save();

            DB::commit();

            return redirect()->route('frontend.requests.index')
                ->with('success', "Booking completed successfully. You provided {$hours} hours of care and earned {$hours} credits.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('frontend.requests.index')
                ->with('error', 'Error completing booking: ' . $e->getMessage());
        }
    }


}
