<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePetAvailabilityRequest;
use App\Models\Pet;
use App\Services\PetAvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PetController extends Controller
{
    protected $petAvailabilityService;

    public function __construct(PetAvailabilityService $petAvailabilityService)
    {
        $this->petAvailabilityService = $petAvailabilityService;
    }

    public function index()
    {
        $this->authorize('viewAny', Pet::class);
        $pets = Pet::all();
        return view('pets.index', compact('pets'));
    }

    public function show(Pet $pet)
    {
        $this->authorize('view', $pet);
        return view('pets.show', compact('pet'));
    }

    public function create()
    {
        $this->authorize('create', Pet::class);
        return view('pets.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Pet::class);
        // ... existing store logic ...
    }

    public function edit(Pet $pet)
    {
        $this->authorize('update', $pet);
        return view('pets.edit', compact('pet'));
    }

    public function update(UpdatePetAvailabilityRequest $request, Pet $pet)
    {
        $this->authorize('update', $pet);
        // ... existing update logic ...
    }

    public function destroy(Pet $pet)
    {
        $this->authorize('delete', $pet);
        // ... existing destroy logic ...
    }

    public function restore(Pet $pet)
    {
        $this->authorize('restore', $pet);
        // ... existing restore logic ...
    }

    public function forceDelete(Pet $pet)
    {
        $this->authorize('forceDelete', $pet);
        // ... existing forceDelete logic ...
    }
} 