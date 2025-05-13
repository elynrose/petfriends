@extends('layouts.frontend')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Write a Review</h4>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="card-body">
                    <div class="mb-4">
                        <h5>Booking Details</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Pet:</strong> {{ $booking->pet->name }}</p>
                                <p><strong>Type:</strong> {{ App\Models\Pet::TYPE_SELECT[$booking->pet->type] ?? $booking->pet->type }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Dates:</strong> {{ \Carbon\Carbon::parse($booking->from)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($booking->to)->format('M d, Y') }}</p>
                                <p><strong>Times:</strong> {{ $booking->from_time ? \Carbon\Carbon::parse($booking->from_time)->format('H:i') : 'N/A' }} - {{ $booking->to_time ? \Carbon\Carbon::parse($booking->to_time)->format('H:i') : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('frontend.pet-reviews.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="booking_id" value="{{ $booking->id }}">

                        <div class="form-group">
                            <label for="score">Rating*</label>
                            <div class="rating">
                                @for($i = 5; $i >= 1; $i--)
                                    <input type="radio" name="score" value="{{ $i }}" id="star{{ $i }}" {{ old('score') == $i ? 'checked' : '' }} required>
                                    <label for="star{{ $i }}"><i class="fas fa-star"></i></label>
                                @endfor
                            </div>
                            @error('score')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="comment">Your Review</label>
                            <textarea class="form-control @error('comment') is-invalid @enderror" 
                                      name="comment" 
                                      id="comment"
                                      rows="4" 
                                      placeholder="Share your experience...">{{ old('comment') }}</textarea>
                            @error('comment')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Submit Review
                            </button>
                            <a href="{{ route('frontend.bookings.index') }}" class="btn btn-link">
                                <i class="fas fa-arrow-left"></i> Back to Bookings
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
        transition: color 0.2s;
    }
    .rating input:checked ~ label,
    .rating label:hover,
    .rating label:hover ~ label {
        color: #ffd700;
    }
    .card {
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #eee;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .btn-primary {
        padding: 0.5rem 1.5rem;
    }
    .btn-link {
        color: #6c757d;
        text-decoration: none;
    }
    .btn-link:hover {
        color: #495057;
        text-decoration: underline;
    }
</style>
@endsection 