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
        $petTypes = Pet::TYPE_SELECT;

        return view('frontend.pets.index', compact('pets', 'petTypes'));
    }

    public function create()
    {
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
        $pet = Pet::create($request->all());

        if ($request->input('photo', false)) {
            $pet->addMedia(storage_path('tmp/uploads/' . basename($request->input('photo'))))->toMediaCollection('photo');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $pet->id]);
        }

        return redirect()->route('frontend.pets.index');
    }

    public function edit(Pet $pet)
    {
        if ($pet->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You can only edit your own pets.');
        }

        return view('frontend.pets.edit', compact('pet'));
    }

    public function update(UpdatePetRequest $request, Pet $pet)
    {
        if ($pet->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You can only edit your own pets.');
        }

        $pet->update($request->all());

        if ($request->input('photo', false)) {
            if (!$pet->photo || $request->input('photo') !== $pet->photo->file_name) {
                if ($pet->photo) {
                    $pet->photo->delete();
                }
                $pet->addMedia(storage_path('tmp/uploads/' . basename($request->input('photo'))))->toMediaCollection('photo');
            }
        } elseif ($pet->photo) {
            $pet->photo->delete();
        }

        return redirect()->route('frontend.pets.index');
    }

    public function destroy(Pet $pet)
    {
        if ($pet->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You can only delete your own pets.');
        }

        $pet->delete();

        return redirect()->route('frontend.pets.index');
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