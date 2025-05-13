@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
               

                <div class="card-body">
                    @if($bookings)
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                            <h5>{{ $pendingBookingsCount }} Upcoming pet date(s) found</h5>
                            <p class="text-muted"></p>
                       
                        </div>
                 
                        <div class="booking-list">
                            @foreach($bookings as $booking)
                                <div class="booking-item mb-4">
                                    <div class="row no-gutters">
                                        <div class="col-md-3">
                                            @if($booking->pet && $booking->pet->photo && $booking->pet->photo->isNotEmpty())
                                                <img src="{{ $booking->pet->photo->first()->getUrl() }}" 
                                                     class="booking-image" 
                                                     alt="{{ $booking->pet->name ?? 'Pet' }}"
                                                     onerror="this.onerror=null; this.src='{{ asset('images/pet-placeholder.jpg') }}';">
                                            @else
                                                <div class="booking-image-placeholder">
                                                    <i class="fas fa-paw fa-3x text-muted"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-9">
                                            <div class="booking-content p-3">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h5 class="mb-2">{{ $booking->pet->name ?? 'Unnamed Pet' }}</h5>
                                                        <span class="badge badge-{{ $booking->status === 'pending' ? 'warning' : 
                                                            ($booking->status === 'approved' ? 'success' : 
                                                            ($booking->status === 'rejected' ? 'danger' : 
                                                            ($booking->status === 'completed' ? 'info' : 
                                                            ($booking->status === 'new' ? 'success' : 'secondary')
                                                            ))) }}">
                                                            {{ App\Models\Booking::STATUS_SELECT[$booking->status] ?? 'Unknown' }}
                                                        </span>
                                                    </div>
                                                    @if($booking->status !== 'completed')
                                                        @can('booking_delete')
                                                            <form action="{{ route('frontend.bookings.destroy', $booking->id) }}" 
                                                                  method="POST" 
                                                                  onsubmit="return confirm('{{ trans('global.cancelBookingConfirmation') }}');" 
                                                                  style="display: inline-block;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                                    <i class="fas fa-cancel"></i> {{ trans('global.cancelBooking') }}
                                                                </button>
                                                            </form>
                                                        @endcan
                                                    @endif
                                                </div>

                                                <div class="booking-details mt-3">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="small text-muted mb-2">
                                                                <i class="fas fa-user"></i> Booked by: {{ $booking->user->name ?? 'Unknown' }}
                                                            </div>
                                                            @if($booking->pet)
                                                                <div class="small text-muted mb-2">
                                                                    <i class="fas fa-paw"></i> Type: {{ $booking->pet->type }}
                                                                </div>
                                                            @endif
                                                            @if($booking->from && $booking->to)
                                                                <div class="small text-muted mb-2">
                                                                    <i class="fas fa-calendar"></i> From: {{ \Carbon\Carbon::parse($booking->from)->format('M d, Y') }} {{ $booking->from_time ? \Carbon\Carbon::parse($booking->from_time)->format('H:i') : '' }}
                                                                </div>
                                                                <div class="small text-muted mb-2">
                                                                    <i class="fas fa-calendar"></i> To: {{ \Carbon\Carbon::parse($booking->to)->format('M d, Y') }} {{ $booking->to_time ? \Carbon\Carbon::parse($booking->to_time)->format('H:i') : '' }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-6">
                                                            @if($booking->status === 'completed')
                                                                @if($booking->review)
                                                                    <div class="alert alert-info mb-0">
                                                                        <strong>Your Review:</strong>
                                                                        <div class="stars">
                                                                            @for($i = 1; $i <= 5; $i++)
                                                                                <i class="fas fa-star {{ $i <= $booking->review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                                            @endfor
                                                                        </div>
                                                                        <p class="mb-0 mt-2">{{ $booking->review->comment }}</p>
                                                                    </div>
                                                                @else
                                                                    <a href="{{ route('frontend.pet_reviews.create', ['booking' => $booking->id]) }}" 
                                                                       class="btn btn-outline-primary btn-sm">
                                                                        <i class="fas fa-star"></i> Write a Review
                                                                    </a>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .booking-item {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .booking-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .booking-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 8px 0 0 8px;
    }
    .booking-image-placeholder {
        width: 100%;
        height: 200px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px 0 0 8px;
    }
    .booking-content {
        height: 100%;
    }
    .badge {
        font-size: 0.9em;
        padding: 0.5em 1em;
    }
    .stars {
        font-size: 1.2em;
    }
    .booking-details {
        border-top: 1px solid #eee;
        padding-top: 1rem;
    }
    @media (max-width: 768px) {
        .booking-image, .booking-image-placeholder {
            border-radius: 8px 8px 0 0;
        }
    }
</style>
@endsection