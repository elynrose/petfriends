<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyPetRequest;
use App\Http\Requests\StorePetRequest;
use App\Http\Requests\UpdatePetRequest;
use App\Models\Pet;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PetController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        $query = Pet::where('user_id', '=', Auth::id());

        $pets = $query->get();
        
        // Debug the bookings count
        foreach($pets as $pet) {
            \Log::info("Pet {$pet->name} has {$pet->bookings_count} pending bookings");
        }
        
        $petTypes = Pet::TYPE_SELECT;

        return view('frontend.pets.index', compact('pets', 'petTypes'));
    }

    public function create()
    {
        // Check if user has required location information
        if (Auth::check()) {
            $user = Auth::user();
            if (empty($user->state) || empty($user->city) || empty($user->zip_code)) {
                return redirect()->route('frontend.profile.edit')
                    ->with('warning', 'Please complete your location information (State, City, and Zip Code) before adding a pet.');
            }
        }

        return view('frontend.pets.create');
    }

    public function store(StorePetRequest $request)
    {
        // Check if user has required location information
        if (Auth::check()) {
            $user = Auth::user();
            if (empty($user->state) || empty($user->city) || empty($user->zip_code)) {
                return redirect()->route('frontend.profile.index')
                    ->with('warning', 'Please complete your location information (State, City, and Zip Code) before adding a pet.');
            }
        }

        // If setting availability (not_available is false), check credits
        if (!$request->input('not_available')) {
            $user = Auth::user();
            $start = Carbon::parse($request->input('from') . ' ' . $request->input('from_time'));
            $end = Carbon::parse($request->input('to') . ' ' . $request->input('to_time'));
            
            // Calculate required credits (hours rounded up)
            $requiredCredits = ceil($end->diffInMinutes($start) / 60);
            
            if (!$user->hasEnoughCredits($requiredCredits)) {
                return redirect()->back()
                    ->with('error', "You need {$requiredCredits} credits to set this availability period. You currently have {$user->credits} credits.");
            }
            
            // Deduct credits
            $user->deductCredits($requiredCredits);
        }

        $pet = Pet::create($request->all());

        foreach ($request->input('photo', []) as $file) {
            $pet->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photo');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $pet->id]);
        }

        return redirect()->route('frontend.pets.index')
            ->with('message', trans('global.pet_created'));
    }

    public function edit(Pet $pet)
    {
        // Check if user is authorized to edit this pet
        if ($pet->user_id !== Auth::user()->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('frontend.pets.edit', compact('pet'));
    }

    public function update(UpdatePetRequest $request, Pet $pet)
    {
        // Check if user is authorized to edit this pet
        if ($pet->user_id !== Auth::user()->id) {
            abort(403, 'Unauthorized action.');
        }

        // Validate date range
        $start = Carbon::parse($request->input('from') . ' ' . $request->input('from_time'));
        $end = Carbon::parse($request->input('to') . ' ' . $request->input('to_time'));

        if ($start->isPast() || $end->isPast()) {
            return redirect()->back()
                ->with('error', 'Booking dates must be in the future.');
        }

        if ($end->lte($start)) {
            return redirect()->back()
                ->with('error', 'End date and time must be after start date and time.');
        }

        // Validate booking duration
        $durationInHours = ceil($end->diffInMinutes($start) / 60);
        $minDuration = 1; // Minimum 1 hour

        if ($durationInHours < $minDuration) {
            return redirect()->back()
                ->with('error', "Booking duration must be at least {$minDuration} hour.");
        }

        // Validate time of day (e.g., no bookings between 10 PM and 6 AM)
        $startHour = $start->hour;
        $endHour = $end->hour;
        if ($startHour < 6 || $startHour >= 22 || $endHour < 6 || $endHour >= 22) {
            return redirect()->back()
                ->with('error', 'Bookings are only allowed between 6 AM and 10 PM.');
        }

        // Handle existing pending booking update
        $existingBooking = $pet->bookings()
            ->where('status', 'pending')
            ->where('from', $request->input('from'))
            ->where('to', $request->input('to'))
            ->first();

        // Check for overlapping bookings
        $overlappingBooking = $pet->bookings()
            ->where('status', '!=', 'rejected')
            ->where('status', '!=', 'completed')
            ->where(function ($query) use ($start, $end) {
                $query->where(function ($q) use ($start, $end) {
                    $q->where('from', '<=', $end->format('Y-m-d'))
                      ->where('to', '>=', $start->format('Y-m-d'));
                });
            })
            ->where('id', '!=', $request->input('booking_id', 0))
            ->where('id', '!=', $existingBooking?->id) // Exclude the current booking being updated
            ->first();

        if ($overlappingBooking) {
            return redirect()->back()
                ->with('error', 'This time period overlaps with an existing booking.');
        }

        // Handle credit calculations
        if (!$request->input('not_available')) {
            $user = Auth::user();
            $newHours = ceil($end->diffInMinutes($start) / 60);

            // If pet was previously available, calculate the difference
            if (!$pet->not_available) {
                $oldStart = Carbon::parse($pet->from . ' ' . $pet->from_time);
                $oldEnd = Carbon::parse($pet->to . ' ' . $pet->to_time);
                $oldHours = ceil($oldEnd->diffInMinutes($oldStart) / 60);
                $hourDifference = $newHours - $oldHours;

                if ($hourDifference > 0) {
                    // Extending availability - need more credits
                    if (!$user->hasEnoughCredits($hourDifference)) {
                        return redirect()->back()
                            ->with('error', "You need {$hourDifference} additional credits to extend availability for {$pet->name}. You currently have {$user->credits} credits.");
                    }
                    $user->deductCredits($hourDifference, "Extended availability for {$pet->name} by {$hourDifference} hours");
                    $message = "Successfully extended availability and deducted {$hourDifference} credits.";
                } elseif ($hourDifference < 0) {
                    // Reducing availability - refund credits
                    $refundAmount = abs($hourDifference);
                    $user->refundCredits($refundAmount, "Reduced availability for {$pet->name} by {$refundAmount} hours");
                    $message = "Successfully reduced availability and refunded {$refundAmount} credits.";
                } else {
                    $message = "Availability period updated with no change in credits.";
                }
            } else {
                // Pet was not available before - charge full duration
                if (!$user->hasEnoughCredits($newHours)) {
                    return redirect()->back()
                        ->with('error', "You need {$newHours} credits to make {$pet->name} available for this duration. You currently have {$user->credits} credits.");
                }
                $user->deductCredits($newHours, "New availability period for {$pet->name} ({$newHours} hours)");
                $message = "Successfully made {$pet->name} available and deducted {$newHours} credits.";
            }
        } else {
            // If pet is being marked as not available and was previously available
            if (!$pet->not_available) {
                $oldStart = Carbon::parse($pet->from . ' ' . $pet->from_time);
                $oldEnd = Carbon::parse($pet->to . ' ' . $pet->to_time);
                $oldHours = ceil($oldEnd->diffInMinutes($oldStart) / 60);
                
                // Refund credits
                $user = Auth::user();
                $user->refundCredits($oldHours, "Marked {$pet->name} as not available, refunding {$oldHours} hours of availability");
                $message = "Successfully marked {$pet->name} as not available and refunded {$oldHours} credits.";
            } else {
                $message = "Pet status updated.";
            }
        }

        // Update pet information
        $pet->update([
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'age' => $request->input('age'),
            'gender' => $request->input('gender'),
            'not_available' => $request->input('not_available'),
            'from' => $request->input('from'),
            'from_time' => $request->input('from_time'),
            'to' => $request->input('to'),
            'to_time' => $request->input('to_time')
        ]);

        // Handle media updates more efficiently
        $this->handleMediaUpdates($pet, $request);

        return redirect()->route('frontend.pets.index')
            ->with('message', $message ?? trans('global.pet_updated'));
    }

    /**
     * Handle media updates for the pet
     *
     * @param Pet $pet
     * @param Request $request
     * @return void
     */
    private function handleMediaUpdates(Pet $pet, Request $request): void
    {
        $currentPhotos = $pet->photo->pluck('file_name')->toArray();
        $newPhotos = $request->input('photo', []);
        
        // Delete removed photos
        $pet->photo->each(function ($media) use ($newPhotos) {
            if (!in_array($media->file_name, $newPhotos)) {
                $media->delete();
            }
        });

        // Add new photos
        foreach ($newPhotos as $file) {
            if (!in_array($file, $currentPhotos)) {
                $pet->addMedia(storage_path('tmp/uploads/' . basename($file)))
                    ->toMediaCollection('photo');
            }
        }
    }

    public function show(Pet $pet)
    {
        $pendingRequestsCount = $pet->bookings()
            ->where('status', 'pending')
            ->count();

        return view('frontend.pets.show', compact('pet', 'pendingRequestsCount'));
    }

    public function destroy(Pet $pet)
    {
        if ($pet->user_id !== Auth::user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $pet->delete();

        return back();
    }

    public function massDestroy(MassDestroyPetRequest $request)
    {
        $pets = Pet::find(request('ids'));

        foreach ($pets as $pet) {
            if ($pet->user_id === Auth::user()->id) {
                $pet->delete();
            }
        }

        return response(null, 204);
    }

    public function storeMedia(Request $request)
    {
        $path = storage_path('tmp/uploads');
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file = $request->file('file');
        $name = uniqid() . '_' . trim($file->getClientOriginalName());
        $file->move($path, $name);

        return response()->json([
            'name' => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    public function storeCKEditorImages(Request $request)
    {
        $model = new Pet();
        $model->id = $request->input('crud_id', 0);
        $model->exists = true;
        $media = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], 201);
    }
} 