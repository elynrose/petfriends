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
            
            // Get featured pets
            $featuredQuery = Pet::with(['user', 'photo'])
                ->where('featured_until', '>', now())
                ->where('not_available', false)
                ->where(function($q) {
                    $q->whereNull('from')
                      ->orWhere('from', '>=', now()->startOfDay());
                })
                ->take(4);

            $featuredPets = $featuredQuery->get();

            // Get available pets
            $query = Pet::with(['user', 'photo'])
                ->where('not_available', false)
                ->where('user_id', '!=', $user->id)
                ->where(function($q) {
                    $q->whereNull('from')
                      ->orWhere('from', '>=', now()->startOfDay());
                });

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
