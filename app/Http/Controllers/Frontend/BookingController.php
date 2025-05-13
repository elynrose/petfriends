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

class BookingController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('booking_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $bookings = Booking::with(['pet', 'user', 'media'])
        ->where('user_id', auth()->id())
        ->get();

        return view('frontend.bookings.index', compact('bookings'));
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
        
        //Add the from date  and time to the data array, it should be the current date and time

        $data['from'] = Carbon::now()->format('Y-m-d');
        $data['from_time'] = Carbon::now()->format('H:i');
        
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
            return redirect()->route('frontend.bookings.index')
                ->with('error', 'This booking is already completed.');
        }

        // Use the complete() method which handles credit awarding
        if ($booking->complete()) {
            return redirect()->route('frontend.bookings.index')
                ->with('success', 'Booking completed successfully. Credits have been awarded.');
        }

        return redirect()->route('frontend.bookings.index')
            ->with('error', 'Failed to complete booking.');
    }


}
