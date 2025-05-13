<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Credit::class);
        $credits = Credit::all();
        return view('credits.index', compact('credits'));
    }

    public function show(Credit $credit)
    {
        $this->authorize('view', $credit);
        return view('credits.show', compact('credit'));
    }

    public function create()
    {
        $this->authorize('create', Credit::class);
        return view('credits.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Credit::class);
        // ... existing store logic ...
    }

    public function edit(Credit $credit)
    {
        $this->authorize('update', $credit);
        return view('credits.edit', compact('credit'));
    }

    public function update(Request $request, Credit $credit)
    {
        $this->authorize('update', $credit);
        // ... existing update logic ...
    }

    public function destroy(Credit $credit)
    {
        $this->authorize('delete', $credit);
        // ... existing destroy logic ...
    }

    public function restore(Credit $credit)
    {
        $this->authorize('restore', $credit);
        // ... existing restore logic ...
    }

    public function forceDelete(Credit $credit)
    {
        $this->authorize('forceDelete', $credit);
        // ... existing forceDelete logic ...
    }
} 