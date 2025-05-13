@extends('layouts.frontend')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Write a Review</h4>
                </div>

                <div class="card-body">
                    <div class="mb-4">
                        <h5>Booking Details</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Pet:</strong> {{ $booking->pet->name }}</p>
                                <p><strong>Type:</strong> {{ $booking->pet->type }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Dates:</strong> {{ $booking->from }} to {{ $booking->to }}</p>
                                <p><strong>Times:</strong> {{ $booking->from_time }} - {{ $booking->to_time }}</p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('frontend.pet_reviews.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="booking_id" value="{{ $booking->id }}">

                        <div class="form-group">
                            <label>Rating</label>
                            <div class="rating">
                                @for($i = 5; $i >= 1; $i--)
                                    <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}" required>
                                    <label for="star{{ $i }}"><i class="fas fa-star"></i></label>
                                @endfor
                            </div>
                            @error('rating')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="comment">Your Review</label>
                            <textarea class="form-control @error('comment') is-invalid @enderror" 
                                      name="comment" 
                                      rows="4" 
                                      placeholder="Share your experience...">{{ old('comment') }}</textarea>
                            @error('comment')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                Submit Review
                            </button>
                            <a href="{{ route('frontend.bookings.index') }}" class="btn btn-link">
                                Back to Bookings
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-start;
    }
    .rating input {
        display: none;
    }
    .rating label {
        cursor: pointer;
        font-size: 2em;
        color: #ddd;
        padding: 0 0.1em;
    }
    .rating input:checked ~ label,
    .rating label:hover,
    .rating label:hover ~ label {
        color: #ffd700;
    }
</style>
@endsection 