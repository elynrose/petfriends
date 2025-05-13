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

        $pet->update($request->all());

        if (count($pet->photo) > 0) {
            foreach ($pet->photo as $media) {
                if (! in_array($media->file_name, $request->input('photo', []))) {
                    $media->delete();
                }
            }
        }
        $media = $pet->photo->pluck('file_name')->toArray();
        foreach ($request->input('photo', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $pet->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photo');
            }
        }

        return redirect()->route('frontend.pets.index')
            ->with('message', trans('global.pet_updated'));
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