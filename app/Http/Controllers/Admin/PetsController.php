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
use App\Services\PetAvailabilityService;
use Illuminate\Support\Facades\DB;

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
        \Log::info('UPDATE METHOD STARTED', [
            'pet_id' => $pet->id,
            'request_data' => $request->all(),
            'user_id' => auth()->id(),
            'user_credits' => auth()->user()->credits
        ]);

        try {
            DB::beginTransaction();

            $oldHours = null;
            if (!$pet->not_available) {
                $start = \Carbon\Carbon::parse($pet->from . ' ' . $pet->from_time);
                $end = \Carbon\Carbon::parse($pet->to . ' ' . $pet->to_time);
                $oldHours = ceil($end->diffInMinutes($start) / 60);
                
                \Log::info('OLD HOURS CALCULATED', [
                    'old_hours' => $oldHours,
                    'from' => $pet->from,
                    'from_time' => $pet->from_time,
                    'to' => $pet->to,
                    'to_time' => $pet->to_time
                ]);
            }

            // Handle credit changes if availability is being modified
            if (!$request->input('not_available')) {
                \Log::info('PET IS BEING MADE AVAILABLE');
                
                $start = \Carbon\Carbon::parse($request->input('from') . ' ' . $request->input('from_time'));
                $end = \Carbon\Carbon::parse($request->input('to') . ' ' . $request->input('to_time'));
                $newHours = ceil($end->diffInMinutes($start) / 60);

                \Log::info('NEW HOURS CALCULATED', [
                    'new_hours' => $newHours,
                    'from' => $request->input('from'),
                    'from_time' => $request->input('from_time'),
                    'to' => $request->input('to'),
                    'to_time' => $request->input('to_time')
                ]);

                $availabilityService = new PetAvailabilityService();
                $result = $availabilityService->handleCreditChanges($pet, auth()->user(), $newHours, $oldHours);

                \Log::info('CREDIT CHANGES RESULT', [
                    'result' => $result,
                    'user_credits_after' => auth()->user()->credits
                ]);

                if (!$result['success']) {
                    throw new \Exception($result['message']);
                }
            } elseif (!$pet->not_available) {
                \Log::info('PET IS BEING MARKED AS NOT AVAILABLE');
                
                $availabilityService = new PetAvailabilityService();
                $result = $availabilityService->handleNotAvailable($pet, auth()->user());

                \Log::info('NOT AVAILABLE RESULT', [
                    'result' => $result,
                    'user_credits_after' => auth()->user()->credits
                ]);

                if (!$result['success']) {
                    throw new \Exception($result['message']);
                }
            }

            // Now update the pet with new values
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

            DB::commit();

            \Log::info('UPDATE COMPLETED SUCCESSFULLY', [
                'pet_id' => $pet->id,
                'user_id' => auth()->id(),
                'final_credits' => auth()->user()->credits
            ]);

            return redirect()->route('admin.pets.index')
                ->with('message', 'Pet updated successfully');

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
