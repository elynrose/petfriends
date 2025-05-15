<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HomeController
{
    public function index()
    {
        // Check if user is logged in and has required location information
        if (Auth::check()) {
            $user = Auth::user();
            if (empty($user->state) || empty($user->city) || empty($user->zip_code)) {
                return redirect()->route('frontend.profile.index')
                    ->with('warning', 'Please complete your location information (State, City, and Zip Code) before adding or viewing pets.');
            }
        }

        $query = Pet::query()
            ->where('not_available', false)
            ->when(Auth::check(), function($q) {
                return $q->where('user_id', '!=', Auth::id());
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
                $dateFrom = Carbon::parse(request('date_from'))->format('Y-m-d');
                return $q->where(function($q) use ($dateFrom) {
                    $q->whereNull('from')
                      ->orWhere('from', '<=', $dateFrom);
                });
            })
         
            ->when(request('date_to'), function($q) {
                $dateTo = Carbon::parse(request('date_to'))->format('Y-m-d');
                return $q->where(function($q) use ($dateTo) {
                    $q->whereNull('to')
                      ->orWhere('to', '>=', $dateTo);
                });
            });

        // Always exclude current user's pets
        if (Auth::check()) {
            $query->where('user_id', '!=', Auth::id());
        }

        $pets = $query->with(['petReviews', 'user', 'photo'])->get();
        $petTypes = Pet::TYPE_SELECT;

        return view('frontend.home', compact('pets', 'petTypes'));
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
