<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyPetRequest;
use App\Http\Requests\StorePetRequest;
use App\Http\Requests\UpdatePetRequest;
use App\Models\Pet;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class PetsController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('pet_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $pets = Pet::with(['media'])->get();

        return view('admin.pets.index', compact('pets'));
    }

    public function create()
    {
        abort_if(Gate::denies('pet_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.pets.create');
    }

    public function store(StorePetRequest $request)
    {
        $pet = Pet::create($request->all());

        foreach ($request->input('photo', []) as $file) {
            $pet->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photo');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $pet->id]);
        }

        return redirect()->route('admin.pets.index');
    }

    public function edit(Pet $pet)
    {
        abort_if(Gate::denies('pet_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.pets.edit', compact('pet'));
    }

    public function update(UpdatePetRequest $request, Pet $pet)
    {
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

        return redirect()->route('admin.pets.index');
    }

    public function show(Pet $pet)
    {
        abort_if(Gate::denies('pet_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.pets.show', compact('pet'));
    }

    public function destroy(Pet $pet)
    {
        abort_if(Gate::denies('pet_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $pet->delete();

        return back();
    }

    public function massDestroy(MassDestroyPetRequest $request)
    {
        $pets = Pet::find(request('ids'));

        foreach ($pets as $pet) {
            $pet->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('pet_create') && Gate::denies('pet_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Pet();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
