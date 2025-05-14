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
use App\Services\PetAvailabilityService;
use Illuminate\Support\Facades\DB;

class PetsController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        $query = Pet::query()
            ->where('not_available', false)
            ->when(Auth::check(), function($q) {
                return $q->where('user_id', '=', Auth::id());
            })
            ->when(request('type'), function($q) {
                return $q->where('type', request('type'));
            })
            ->when(request('zip_code'), function($q) {
                return $q->whereHas('user', function($q) {
                    $q->where('zip_code', 'like', '%' . request('zip_code') . '%');
                });
            })
            ->when(request('date_from'), function($q) {
                return $q->where(function($q) {
                    $q->whereNull('from')
                      ->orWhere('from', '<=', request('date_from'));
                });
            })
            ->when(request('date_to'), function($q) {
                return $q->where(function($q) {
                    $q->whereNull('to')
                      ->orWhere('to', '>=', request('date_to'));
                });
            })
            ->withCount(['bookings' => function($query) {
                $query->where('status', 'pending');
            }]);

        $pets = $query->get();
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

        // Create the pet first
        $pet = Pet::create($request->all());

        // If setting availability (not_available is false), check credits
        if (!$request->input('not_available')) {
            $user = Auth::user();
            $start = Carbon::parse($request->input('from') . ' ' . $request->input('from_time'));
            $end = Carbon::parse($request->input('to') . ' ' . $request->input('to_time'));
            
            // Calculate required credits (hours rounded up)
            $requiredCredits = ceil($end->diffInMinutes($start) / 60);
            
            if (!$user->hasEnoughCredits($requiredCredits)) {
                // Delete the pet if user doesn't have enough credits
                $pet->delete();
                return redirect()->back()
                    ->with('error', "You need {$requiredCredits} credits to set this availability period. You currently have {$user->credits} credits.");
            }
            
            // Use PetAvailabilityService to handle credit changes
            $petAvailabilityService = app(PetAvailabilityService::class);
            $result = $petAvailabilityService->handleCreditChanges($pet, $user, $requiredCredits);
            
            if (!$result['success']) {
                // Delete the pet if credit handling fails
                $pet->delete();
                return redirect()->back()
                    ->with('error', $result['message']);
            }
        }

        // Handle media uploads
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
        \Log::info('UPDATE METHOD STARTED', [
            'pet_id' => $pet->id,
            'request_data' => $request->all(),
            'user_id' => auth()->id(),
            'user_credits' => auth()->user()->credits
        ]);

        // Check if user is authorized to edit this pet
        if ($pet->user_id !== Auth::user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $user = Auth::user();
        $service = app(PetAvailabilityService::class);

        // Save old values for credit calculation
        $oldFrom = $pet->from;
        $oldFromTime = $pet->from_time;
        $oldTo = $pet->to;
        $oldToTime = $pet->to_time;
        $oldNotAvailable = $pet->not_available;

        \Log::info('OLD VALUES', [
            'old_from' => $oldFrom,
            'old_from_time' => $oldFromTime,
            'old_to' => $oldTo,
            'old_to_time' => $oldToTime,
            'old_not_available' => $oldNotAvailable
        ]);

        // Calculate hours helper
        $calcHours = function($from, $fromTime, $to, $toTime) {
            if (!$from || !$fromTime || !$to || !$toTime) return 0;
            $start = Carbon::parse($from . ' ' . $fromTime);
            $end = Carbon::parse($to . ' ' . $toTime);
            return ceil($end->diffInMinutes($start) / 60);
        };

        // If setting availability (not_available is false), validate time range
        if (!$request->input('not_available')) {
            $start = Carbon::parse($request->input('from') . ' ' . $request->input('from_time'));
            $end = Carbon::parse($request->input('to') . ' ' . $request->input('to_time'));
            
            $errors = $service->validateTimeRange($start, $end);
            if (!empty($errors)) {
                return redirect()->back()
                    ->withErrors(['availability' => $errors])
                    ->withInput();
            }
        }

        try {
            DB::beginTransaction();

            // Handle credit changes first
            if ($request->input('not_available')) {
                \Log::info('PET IS BEING MARKED AS NOT AVAILABLE');
                
                // If just marked as not available, refund credits
                if (!$oldNotAvailable) {
                    $result = $service->handleNotAvailable($pet, $user);
                    \Log::info('NOT AVAILABLE RESULT', [
                        'result' => $result,
                        'user_credits_after' => $user->credits
                    ]);

                    if (!$result['success']) {
                        throw new \Exception($result['message']);
                    }
                }
            } else {
                \Log::info('PET IS BEING MADE AVAILABLE');
                
                // Calculate old and new hours
                $oldHours = $calcHours($oldFrom, $oldFromTime, $oldTo, $oldToTime);
                $newHours = $calcHours($request->input('from'), $request->input('from_time'), 
                                     $request->input('to'), $request->input('to_time'));

                \Log::info('HOURS CALCULATED', [
                    'old_hours' => $oldHours,
                    'new_hours' => $newHours
                ]);

                // If previously not available, treat as new availability
                if ($oldNotAvailable) {
                    $result = $service->handleCreditChanges($pet, $user, $newHours, null);
                } else {
                    $result = $service->handleCreditChanges($pet, $user, $newHours, $oldHours);
                }

                \Log::info('CREDIT CHANGES RESULT', [
                    'result' => $result,
                    'user_credits_after' => $user->credits
                ]);

                if (!$result['success']) {
                    throw new \Exception($result['message']);
                }
            }

            // Now update the pet with new values
            $pet->fill($request->all());
            $pet->save();

            // Handle media uploads
            if (count($pet->photo) > 0) {
                foreach ($pet->photo as $media) {
                    if (!in_array($media->file_name, $request->input('photo', []))) {
                        $media->delete();
                    }
                }
            }
            $media = $pet->photo->pluck('file_name')->toArray();
            foreach ($request->input('photo', []) as $file) {
                if (count($media) === 0 || !in_array($file, $media)) {
                    $pet->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photo');
                }
            }

            DB::commit();

            \Log::info('UPDATE COMPLETED SUCCESSFULLY', [
                'pet_id' => $pet->id,
                'user_id' => auth()->id(),
                'final_credits' => $user->credits
            ]);

            return redirect()->route('frontend.pets.index')
                ->with('message', trans('global.pet_updated'));

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('UPDATE FAILED', [
                'pet_id' => $pet->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
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