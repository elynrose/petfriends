@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
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

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Booking Requests</h4>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary active" data-filter="all">All</button>
                        <button type="button" class="btn btn-outline-warning" data-filter="pending">Pending</button>
                        <button type="button" class="btn btn-outline-success" data-filter="accepted">Accepted</button>
                        <button type="button" class="btn btn-outline-danger" data-filter="rejected">Rejected</button>
                    </div>
                </div>

                <div class="card-body">
                    @if($bookings->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5>No Booking Requests</h5>
                            <p class="text-muted">You haven't received any booking requests for your pets yet.</p>
                        </div>
                    @else
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Total Requests</h6>
                                        <h2 class="display-4">{{ $bookings->count() }}</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Pending Requests</h6>
                                        <h2 class="display-4">{{ $bookings->where('status', 'pending')->count() }}</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Completed Requests</h6>
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
                                                        <h5 class="mb-2">
                                                            <a href="{{ route('frontend.pets.show', $booking->pet->id) }}" class="text-decoration-none">
                                                                {{ $booking->pet->name ?? 'Unnamed Pet' }}
                                                            </a>
                                                        </h5>
                                                        <span class="badge badge-{{ $booking->status === 'pending' ? 'warning' : 
                                                            ($booking->status === 'accepted' ? 'success' : 
                                                            ($booking->status === 'rejected' ? 'danger' : 
                                                            ($booking->status === 'completed' ? 'info' : 
                                                            ($booking->status === 'new' ? 'success' : 'secondary')
                                                            ))) }}">
                                                            {{ App\Models\Booking::STATUS_SELECT[$booking->status] ?? 'Unknown' }}
                                                        </span>
                                                    </div>
                                                    <div class="btn-group">
                                                        @if($booking->status === 'pending')
                                                            <form action="{{ route('frontend.requests.update', $booking->id) }}" 
                                                                  method="POST" 
                                                                  class="d-inline">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="status" value="accepted">
                                                                <button type="submit" class="btn btn-outline-success btn-sm" onclick="return confirm('Are you sure you want to accept this booking?')">
                                                                    <i class="fas fa-check"></i> Accept
                                                                </button>
                                                            </form>
                                                            <form action="{{ route('frontend.requests.update', $booking->id) }}" 
                                                                  method="POST" 
                                                                  class="d-inline">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="status" value="rejected">
                                                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to reject this booking?')">
                                                                    <i class="fas fa-times"></i> Reject
                                                                </button>
                                                            </form>
                                                        @endif
                                                        @if($booking->status === 'accepted')
                                                            <form action="{{ route('frontend.bookings.complete', $booking->id) }}" 
                                                                  method="POST" 
                                                                  class="d-inline">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit" 
                                                                        class="btn btn-outline-primary btn-sm"
                                                                        onclick="return confirm('{{ trans('global.completeBookingConfirmation') }}')">
                                                                    <i class="fas fa-check"></i> {{ trans('global.markAsCompleted') }}
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="booking-details mt-3">
                                                    <div class="row">
                                                        <div class="col-md-6">
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
                                                        </div>
                                                        <div class="col-md-6">
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
