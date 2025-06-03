<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Notifications\ReferralInvitation;
use App\Notifications\ReferralCreditAwarded;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use App\Models\User;

class ReferralController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Check if user has a referral token
        if (!$user->referral_token) {
            do {
                $token = Str::random(8);
            } while (User::where('referral_token', $token)->exists());
            
            $user->referral_token = $token;
            $user->save();
        }
        
        // Debug the token and URL
        \Log::info('Referral token for user ' . $user->id . ': ' . $user->referral_token);
        $baseUrl = config('app.url');
        $referralLink = $baseUrl . '/register?ref=' . $user->referral_token;
        \Log::info('Generated referral link: ' . $referralLink);
        
        $referrals = Referral::where('referrer_id', $user->id)
            ->latest()
            ->get();
        
        return view('frontend.referrals.index', compact('referralLink', 'referrals'));
    }

    public function invite(Request $request)
    {
        $user = auth()->user();
        
        // Generate referral token if user doesn't have one
        if (!$user->referral_token) {
            do {
                $token = Str::random(8);
            } while (User::where('referral_token', $token)->exists());
            
            $user->update(['referral_token' => $token]);
        }

        $request->validate([
            'email' => 'required|email|unique:users,email|unique:referrals,email'
        ]);

        $referral = Referral::create([
            'referrer_id' => $user->id,
            'email' => $request->email,
            'token' => $user->referral_token,
        ]);

        $invitationLink = url('/register') . '?ref=' . $user->referral_token;

        Notification::route('mail', $request->email)
            ->notify(new ReferralInvitation($referral, $invitationLink));

        return back()->with('success', 'Referral invitation sent successfully!');
    }
} 