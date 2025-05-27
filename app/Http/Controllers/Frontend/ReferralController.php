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
        
        // Generate referral token if user doesn't have one
        if (!$user->referral_token) {
            do {
                $token = Str::random(8);
            } while (User::where('referral_token', $token)->exists());
            
            $user->update(['referral_token' => $token]);
        }

        $referrals = $user->referrals()->latest()->get();
        $referralLink = url('/register') . '?ref=' . $user->referral_token;
        
        return view('frontend.referrals.index', compact('referrals', 'referralLink'));
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