<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\User;

class SubscriptionController extends Controller
{
    public function showSubscriptionPage()
    {
        $user = auth()->user();
        $isSubscribed = $user->is_premium;
        $subscriptionEndsAt = $user->subscription_ends_at;
        
        return view('frontend.subscription.index', compact('isSubscribed', 'subscriptionEndsAt'));
    }

    public function createCheckoutSession()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'PetFriends Premium',
                        'description' => 'Premium subscription for PetFriends',
                    ],
                    'unit_amount' => 999, // $9.99
                    'recurring' => [
                        'interval' => 'month',
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => route('frontend.subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('frontend.subscription.index'),
        ]);

        return response()->json(['id' => $session->id]);
    }

    public function handleSuccess(Request $request)
    {
        if (!$request->session_id) {
            return redirect()->route('frontend.subscription.index')->with('error', 'Invalid subscription session');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));
        $session = Session::retrieve($request->session_id);

        if ($session->subscription) {
            $user = auth()->user();
            $user->is_premium = true;
            $user->stripe_subscription_id = $session->subscription;
            $user->subscription_ends_at = null; // Active subscription
            $user->save();

            return redirect()->route('frontend.subscription.index')
                ->with('success', 'Successfully subscribed to Premium!');
        }

        return redirect()->route('frontend.subscription.index')
            ->with('error', 'Subscription was not successful');
    }

    public function cancel()
    {
        $user = auth()->user();
        
        if ($user->stripe_subscription_id) {
            Stripe::setApiKey(env('STRIPE_SECRET'));
            
            try {
                $subscription = \Stripe\Subscription::retrieve($user->stripe_subscription_id);
                $subscription->cancel();
                
                $user->is_premium = false;
                $user->stripe_subscription_id = null;
                $user->subscription_ends_at = now();
                $user->save();

                return redirect()->route('frontend.subscription.index')
                    ->with('success', 'Your subscription has been cancelled.');
            } catch (\Exception $e) {
                return redirect()->route('frontend.subscription.index')
                    ->with('error', 'Failed to cancel subscription. Please try again.');
            }
        }

        return redirect()->route('frontend.subscription.index')
            ->with('error', 'No active subscription found.');
    }
} 