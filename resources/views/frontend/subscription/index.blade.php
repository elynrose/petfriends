@extends('layouts.frontend')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">PetFriends Premium</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($isSubscribed)
                        <div class="text-center mb-4">
                            <h5 class="text-success">You are a Premium Member! 🎉</h5>
                            @if($subscriptionEndsAt)
                                <p class="text-muted">Your subscription will end on {{ $subscriptionEndsAt->format('F j, Y') }}</p>
                            @endif
                            <form action="{{ route('frontend.subscription.cancel') }}" method="POST" class="mt-3">
                                @csrf
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel your subscription?')">
                                    Cancel Subscription
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="text-center mb-4">
                            <h5>Upgrade to Premium</h5>
                            <p class="text-muted">$9.99/month - Cancel anytime</p>
                        </div>

                        <div class="premium-features mb-4">
                            <h6>Premium Features:</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">✓ Unlimited Pet Listings</li>
                                <li class="mb-2">✓ Chat with Other Pet Owners</li>
                                <li class="mb-2">✓ SMS Notifications</li>
                                <li class="mb-2">✓ Priority Support</li>
                            </ul>
                        </div>

                        <div class="text-center">
                            <button id="checkout-button" class="btn btn-primary btn-lg">
                                Subscribe Now
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if(!$isSubscribed)
<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe('{{ env('STRIPE_KEY') }}');
    const checkoutButton = document.getElementById('checkout-button');

    checkoutButton.addEventListener('click', function () {
        checkoutButton.disabled = true;
        checkoutButton.textContent = 'Processing...';

        fetch('{{ route('frontend.subscription.checkout') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(session) {
            return stripe.redirectToCheckout({ sessionId: session.id });
        })
        .then(function(result) {
            if (result.error) {
                alert(result.error.message);
                checkoutButton.disabled = false;
                checkoutButton.textContent = 'Subscribe Now';
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
            checkoutButton.disabled = false;
            checkoutButton.textContent = 'Subscribe Now';
        });
    });
</script>
@endif
@endsection 