@extends('layouts.frontend')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Purchase Credits</h4>
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

                    <div class="text-center mb-4">
                        <h5>Current Credit Balance: {{ auth()->user()->credits }}</h5>
                        <p class="text-muted">Price: ${{ $creditPrice }} per credit</p>
                    </div>

                    <form id="payment-form">
                        <div class="form-group">
                            <label for="credits">Number of Credits to Purchase</label>
                            <input type="number" class="form-control" id="credits" name="credits" min="1" value="1" required>
                            <small class="form-text text-muted">Total: $<span id="total-amount">0</span></small>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary" id="checkout-button">
                                Purchase Credits
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe('{{ env('STRIPE_KEY') }}');
    const checkoutButton = document.getElementById('checkout-button');
    const creditsInput = document.getElementById('credits');
    const totalAmount = document.getElementById('total-amount');
    const creditPrice = {{ $creditPrice }};

    function updateTotal() {
        const credits = parseInt(creditsInput.value) || 0;
        totalAmount.textContent = (credits * creditPrice).toFixed(2);
    }

    creditsInput.addEventListener('input', updateTotal);
    updateTotal();

    checkoutButton.addEventListener('click', function(e) {
        e.preventDefault();
        const credits = parseInt(creditsInput.value);

        if (credits < 1) {
            alert('Please enter a valid number of credits');
            return;
        }

        checkoutButton.disabled = true;
        checkoutButton.textContent = 'Processing...';

        fetch('{{ route('frontend.credits.checkout') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                credits: credits
            })
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
                checkoutButton.textContent = 'Purchase Credits';
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            checkoutButton.disabled = false;
            checkoutButton.textContent = 'Purchase Credits';
        });
    });
</script>
@endsection 