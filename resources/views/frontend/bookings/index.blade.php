@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">My Bookings</h4>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary active" data-filter="all">All</button>
                        <button type="button" class="btn btn-outline-warning" data-filter="pending">Pending</button>
                        <button type="button" class="btn btn-outline-success" data-filter="completed">Completed</button>
                    </div>
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
                    @if($bookings && $bookings->count() > 0)
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Total Bookings</h6>
                                        <h2 class="display-4">{{ $bookings->count() }}</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Pending Bookings</h6>
                                        <h2 class="display-4">{{ $pendingBookingsCount }}</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Completed Bookings</h6>
                                        <h2 class="display-4">{{ $bookings->where('status', 'completed')->count() }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="booking-list">
                            @php
                                $creditService = app(App\Services\CreditService::class);
                            @endphp
                            @foreach($bookings as $booking)
                                <div class="booking-item mb-4" data-status="{{ $booking->status }}">
                                    <div class="row no-gutters">
                                        <div class="col-md-3">
                                            @if($booking->pet && $booking->pet->photo && $booking->pet->photo->isNotEmpty())
                                            <h3 class="mb-3 mt-3">
                                                                {{ $booking->pet->name ?? 'Unnamed Pet' }}
                                                        </h3>
                                                        <a href="{{ route('frontend.pets.show', $booking->pet->id) }}" class="text-decoration-none">
                                                        <img src="{{ $booking->pet->photo->first()->getUrl() }}" 
                                                     class="booking-image" 
                                                     alt="{{ $booking->pet->name ?? 'Pet' }}"
                                                     onerror="this.onerror=null; this.src='{{ asset('images/pet-placeholder.jpg') }}';"></a>
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
                                                       
                                                    
                                                    </div>
                                                    <div class="btn-group">
                                                        @if($booking->status === 'pending')
                                            @can('booking_delete')
                                                                <form action="{{ route('frontend.bookings.destroy', $booking->id) }}" 
                                                                      method="POST" 
                                                                      onsubmit="return confirm('{{ trans('global.cancelBookingConfirmation') }}');" 
                                                                      style="display: inline-block;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                                                        <i class="fas fa-times"></i> Cancel
                                                                    </button>
                                                </form>
                                            @endcan
                                                        @endif
                                                        @if($booking->status === 'accepted')
                                                            <form action="{{ route('frontend.bookings.complete', $booking->id) }}" 
                                                                  method="POST" 
                                                                  class="d-inline">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit" 
                                                                        class="btn btn-outline-success btn-sm"
                                                                        onclick="return confirm('{{ trans('global.completeBookingConfirmation') }}')">
                                                                    <i class="fas fa-check"></i> Complete
                                                                </button>
                                                            </form>
                                                        @endif
                                                        @if($booking->status === 'completed' && !$booking->review)
                                                            <a href="{{ route('frontend.pet-reviews.create', $booking->id) }}" 
                                                               class="btn btn-outline-primary btn-sm">
                                                                <i class="fas fa-star"></i> Review
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="booking-details mt-3">
                                                    <div class="row">
                                                        <div class="col-md-6 col-sm-6">
                                                            <h6 class="text-muted mb-3">Booking Information</h6>
                                                            <div class="small text-muted mb-2">
                                                                <i class="fas fa-user"></i> Booked by: {{ $booking->user->name ?? 'Unknown' }}
                                                            </div>
                                                            @if($booking->pet)
                                                                <div class="small text-muted mb-2">
                                                                    <i class="fas fa-paw"></i> Type: {{ App\Models\Pet::TYPE_SELECT[$booking->pet->type] ?? $booking->pet->type }}
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

                                                           <p class="mt-3"> <span class="badge badge-{{ $booking->status === 'pending' ? 'warning' : 
                                                            ($booking->status === 'approved' ? 'success' : 
                                                            ($booking->status === 'rejected' ? 'danger' : 
                                                            ($booking->status === 'completed' ? 'info' : 
                                                            ($booking->status === 'new' ? 'success' : 'secondary')
                                                            ))) }}">
                                                            {{ App\Models\Booking::STATUS_SELECT[$booking->status] ?? 'Unknown' }}
                                                        </span></p>
                                                        </div>
                                                        <div class="col-md-6 col-sm-6">
                                                            <h6 class="text-muted mb-3">Credits & Duration</h6>
                                                            @if($booking->from && $booking->to)
                                                                <div class="small text-muted mb-2">
                                                                    <i class="fas fa-clock"></i> Duration: {{ $creditService->calculateBookingHours($booking) }} hours
                                                                </div>
                                                                <div class="small text-muted mb-2">
                                                                    @if($booking->status === 'completed')
                                                                        <i class="fas fa-coins"></i> Credits: {{ $creditService->calculateBookingHours($booking) }}
                                                                        <span class="badge badge-success ml-2">Awarded</span>
                                                                    @elseif($booking->status === 'pending')
                                                                        <i class="fas fa-coins"></i> Potential Credits: {{ $creditService->calculateBookingHours($booking) }}
                                                                        <span class="badge badge-warning ml-2">Pending</span>
                                                                        <small class="d-block text-muted mt-1">Credits will be awarded when booking is completed</small>
                                                                    @else
                                                                        <i class="fas fa-coins"></i> Credits: 0
                                                                        <span class="badge badge-secondary ml-2">Not Awarded</span>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                            @if($booking->status === 'completed')
                                                                @if($booking->review)
                                                                    <div class="alert alert-info mb-0 mt-3 shadow-sm">
                                                                        <strong>Your Review:</strong>
                                                                        <div class="stars">
                                                                            @for($i = 1; $i <= 5; $i++)
                                                                                <i class="fas fa-star {{ $i <= $booking->review->score ? 'text-warning' : 'text-muted' }}"></i>
                                                                            @endfor
                                                                        </div>
                                                                        <p class="mb-0 mt-2">{{ $booking->review->comment }}</p>
                                                                    </div>
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
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                            <h5>No bookings found</h5>
                            <p class="text-muted">Start by booking a pet from our available listings</p>
                            <a href="{{ route('frontend.pets.index') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-paw"></i> Browse Pets
                            </a>
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
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .booking-item:hover {
        transform: translateY(-2px);
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
    .display-4 {
        font-size: 2.5rem;
        font-weight: 300;
        line-height: 1.2;
    }
    .bg-light {
        background-color: #f8f9fa !important;
    }
    @media (max-width: 768px) {
        .booking-image, .booking-image-placeholder {
            border-radius: 8px 8px 0 0;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Filter bookings
        $('.btn-group .btn').click(function() {
            $('.btn-group .btn').removeClass('active');
            $(this).addClass('active');
            
            const filter = $(this).data('filter');
            
            if (filter === 'all') {
                $('.booking-item').show();
            } else {
                $('.booking-item').hide();
                $('.booking-item[data-status="' + filter + '"]').show();
            }
        });
    });
</script>
@endsection