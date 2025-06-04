<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

            // Base query for available pets
            $baseQuery = Pet::with(['user', 'photo'])
                ->where('not_available', false)
                ->where('user_id', '!=', $user->id)
                ->where(function($q) {
                    $q->whereNull('from')
                      ->orWhere('from', '>=', now()->startOfDay());
                });

            // Apply type filter if provided
            if ($request->has('type') && $request->type !== '') {
                $baseQuery->where('type', $request->type);
            }

            // Apply gender filter if provided
            if ($request->has('gender') && $request->gender !== '') {
                $baseQuery->where('gender', $request->gender);
            }

            // Apply age filter if provided
            if ($request->has('age') && $request->age !== '') {
                $baseQuery->where('age', '<=', $request->age);
            }

            // Get the search zip code (from request or user's zip code)
            $searchZipCode = $request->input('zip_code', $user->zip_code);
            $searchRadius = $request->input('radius');

            if ($searchZipCode) {
                // Function to get users within a specific radius
                $getUsersInRadius = function($radius) use ($searchZipCode) {
                    // First get the reference point
                    $referencePoint = User::select('longitude', 'latitude')
                        ->where('zip_code', $searchZipCode)
                        ->first();

                    if (!$referencePoint) {
                        return collect();
                    }

                    return User::select('id')
                        ->whereRaw("
                            ST_Distance_Sphere(
                                point(longitude, latitude),
                                point(?, ?)
                            ) <= ? * 1609.34
                        ", [$referencePoint->longitude, $referencePoint->latitude, $radius])
                        ->pluck('id');
                };

                // If radius is specified in request, use that
                if ($searchRadius) {
                    $usersInRadius = $getUsersInRadius($searchRadius);
                    $query = clone $baseQuery;
                    $query->whereIn('user_id', $usersInRadius);
                    $pets = $query->get();
                } else {
                    // Try 10 miles first
                    $usersInRadius = $getUsersInRadius(10);
                    $query = clone $baseQuery;
                    $query->whereIn('user_id', $usersInRadius);
                    $pets = $query->get();

                    // If no pets found, try 20 miles
                    if ($pets->isEmpty()) {
                        $usersInRadius = $getUsersInRadius(20);
                        $query = clone $baseQuery;
                        $query->whereIn('user_id', $usersInRadius);
                        $pets = $query->get();

                        // If still no pets found, show all available pets
                        if ($pets->isEmpty()) {
                            $pets = $baseQuery->get();
                        }
                    }
                }
            } else {
                // If no zip code available, show all pets
                $pets = $baseQuery->get();
            }
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
