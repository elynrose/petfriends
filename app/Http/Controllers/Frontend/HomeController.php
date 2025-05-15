<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HomeController
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userLocation = null;
        $pets = collect();
        $featuredPets = collect();
        $petTypes = Pet::TYPE_SELECT;

        if ($user) {
            $userLocation = $user->location;
            
            // Get featured pets with detailed debugging
            $featuredQuery = Pet::with(['user', 'photo'])
                ->where('featured_until', '>', now())
                ->where('not_available', false)
                ->take(4);

            \Log::info('Featured Pets Query SQL:', [
                'sql' => $featuredQuery->toSql(),
                'bindings' => $featuredQuery->getBindings(),
                'now' => now()->toDateTimeString()
            ]);

            $featuredPets = $featuredQuery->get();

            \Log::info('Featured Pets Results:', [
                'count' => $featuredPets->count(),
                'pets' => $featuredPets->map(function($pet) {
                    return [
                        'id' => $pet->id,
                        'name' => $pet->name,
                        'featured_until' => $pet->featured_until,
                        'not_available' => $pet->not_available,
                        'user_id' => $pet->user_id,
                        'current_user_id' => Auth::id(),
                        'time_diff' => now()->diffInMinutes($pet->featured_until)
                    ];
                })
            ]);

            // Get available pets
            $query = Pet::with(['user', 'photo'])
                ->where('not_available', false)
                ->where('user_id', '!=', $user->id);

            if ($request->has('type') && $request->type !== '') {
                $query->where('type', $request->type);
            }

            if ($request->has('gender') && $request->gender !== '') {
                $query->where('gender', $request->gender);
            }

            if ($request->has('age') && $request->age !== '') {
                $query->where('age', '<=', $request->age);
            }

            $pets = $query->get();
        }

        return view('frontend.home', compact('pets', 'featuredPets', 'userLocation', 'petTypes'));
    }

    public function checkIfUserStateCityAndZipIsFilled()
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        return !empty($user->state) && !empty($user->city) && !empty($user->zip_code);
    }
}
