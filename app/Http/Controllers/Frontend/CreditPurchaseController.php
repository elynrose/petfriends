<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CreditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Notifications\CreditPurchase;

class CreditPurchaseController extends Controller
{
    public function showPurchaseForm()
    {
        $creditPrice = env('CREDIT_PRICE', 12);
        return view('frontend.credits.purchase', compact('creditPrice'));
    }

    public function createCheckoutSession(Request $request)
    {
        $request->validate([
            'credits' => 'required|integer|min:1'
        ]);

        $creditPrice = env('CREDIT_PRICE', 12);
        $amount = $request->credits * $creditPrice * 100; // Convert to cents for Stripe

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'PetFriends Credits',
                        'description' => $request->credits . ' Credits Purchase',
                    ],
                    'unit_amount' => $amount,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('frontend.credits.success') . '?session_id={CHECKOUT_SESSION_ID}&credits=' . $request->credits,
            'cancel_url' => route('frontend.credits.purchase'),
        ]);

        return response()->json(['id' => $session->id]);
    }

    public function handleSuccess(Request $request)
    {
        if (!$request->session_id || !$request->credits) {
            return redirect()->route('frontend.credits.purchase')->with('error', 'Invalid payment session');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));
        $session = Session::retrieve($request->session_id);

        if ($session->payment_status === 'paid') {
            $user = auth()->user();
            $credits = (int)$request->credits;

            // Use the addCredits method to properly handle credit addition and logging
            $user->addCredits(
                $credits,
                'Purchased ' . $credits . ' credits for $' . ($credits * env('CREDIT_PRICE', 12))
            );

            return redirect()->route('frontend.credits.purchase')->with('success', 'Successfully purchased ' . $credits . ' credits!');
        }

        return redirect()->route('frontend.credits.purchase')->with('error', 'Payment was not successful');
    }
} 