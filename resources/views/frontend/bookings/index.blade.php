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
                    <h4 class="mb-0">My Bookings</h4>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary active" data-filter="all">All</button>
                        <button type="button" class="btn btn-outline-warning" data-filter="pending">Pending</button>
                        <button type="button" class="btn btn-outline-success" data-filter="accepted">Accepted</button>
                        <button type="button" class="btn btn-outline-danger" data-filter="rejected">Rejected</button>
                        <button type="button" class="btn btn-outline-info" data-filter="completed">Completed</button>
                    </div>
                </div>

                <div class="card-body">
                    @if($bookings->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                            <h5>No Bookings</h5>
                            <p class="text-muted">You haven't made any bookings yet.</p>
                            <a href="{{ route('frontend.pets.index') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-paw"></i> Find a Pet
                            </a>
                        </div>
                    @else
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Total Bookings</h6>
                                        <h2 class="display-4">{{ $bookings->count() }}</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Pending</h6>
                                        <h2 class="display-4">{{ $bookings->where('status', 'pending')->count() }}</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Accepted</h6>
                                        <h2 class="display-4">{{ $bookings->where('status', 'accepted')->count() }}</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Completed</h6>
                                        <h2 class="display-4">{{ $bookings->where('status', 'completed')->count() }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="booking-list">
                            @foreach($bookings as $booking)
                                <div class="booking-item mb-4" data-status="{{ $booking->status }}">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    @if($booking->pet && $booking->pet->photo && $booking->pet->photo->isNotEmpty())
                                                        <div class="position-relative">
                                                            <img src="{{ $booking->pet->photo->first()->getUrl() }}" 
                                                                 class="img-fluid rounded" 
                                                                 alt="{{ $booking->pet->name ?? 'Pet' }}"
                                                                 onerror="this.onerror=null; this.src='{{ asset('images/pet-placeholder.jpg') }}';">
                                                            @if($booking->pet->user->photo)
                                                                <img src="{{ $booking->pet->user->photo->getUrl() }}" 
                                                                     class="rounded-circle position-absolute" 
                                                                     style="width: 50px; height: 50px; bottom: -25px; right: 10px; border: 3px solid white;">
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="bg-light rounded p-4 text-center">
                                                            <i class="fas fa-paw fa-3x text-muted"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-md-9">
                                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                                        <div>
                                                            <div class="d-flex align-items-center mb-2">
                                                                @if($booking->pet->user->profile_photo_path)
                                                                    <img src="{{ $booking->pet->user->profile_photo_url }}" 
                                                                         alt="{{ $booking->pet->user->name }}" 
                                                                         class="rounded-circle mr-3" 
                                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                                @else
                                                                    <div class="rounded-circle bg-secondary mr-3 d-flex align-items-center justify-content-center" 
                                                                         style="width: 40px; height: 40px;">
                                                                        <i class="fas fa-user text-white"></i>
                                                                    </div>
                                                                @endif
                                                                <div>
                                                                    <h5 class="mb-0">
                                                                        <a href="{{ route('frontend.members.show', $booking->pet->user) }}" class="text-dark">
                                                                            {{ $booking->pet->user->name }}
                                                                        </a>
                                                                    </h5>
                                                                    <small class="text-muted">Pet Owner</small>
                                                                </div>
                                                            </div>
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
                                                                <form action="{{ route('frontend.bookings.destroy', $booking->id) }}" 
                                                                      method="POST" 
                                                                      class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to cancel this booking?')">
                                                                        <i class="fas fa-times"></i> Cancel
                                                                    </button>
                                                                </form>
                                                            @elseif($booking->status === 'accepted')
                                                                <a href="{{ route('frontend.bookings.show', $booking->id) }}" class="btn btn-outline-primary btn-sm">
                                                                    <i class="fas fa-comments"></i> Chat
                                                                </a>
                                                            @elseif($booking->status === 'completed' && !$booking->review)
                                                                <a href="{{ route('frontend.pet-reviews.create', ['booking' => $booking->id]) }}" class="btn btn-outline-success btn-sm">
                                                                    <i class="fas fa-star"></i> Review
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="booking-details mt-3">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <h6 class="text-muted mb-3">Booking Information</h6>
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
                                                                <h6 class="text-muted mb-3">Booking Status</h6>
                                                                @if($booking->status === 'completed')
                                                                    <div class="small text-muted mb-2">
                                                                        <i class="fas fa-check-circle text-success"></i> Booking completed
                                                                        @if($booking->review)
                                                                            <span class="badge badge-success ml-2">Reviewed</span>
                                                                        @else
                                                                            <span class="badge badge-warning ml-2">Review Pending</span>
                                                                        @endif
                                                                    </div>
                                                                @elseif($booking->status === 'rejected')
                                                                    <div class="small text-muted mb-2">
                                                                        <i class="fas fa-times-circle text-danger"></i> Booking rejected
                                                                        @if($booking->notes)
                                                                            <div class="mt-1">
                                                                                <small class="text-muted">Reason: {{ $booking->notes }}</small>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                @else
                                                                    <div class="small text-muted mb-2">
                                                                        <i class="fas fa-clock text-{{ $booking->status === 'pending' ? 'warning' : 'success' }}"></i>
                                                                        {{ $booking->status === 'pending' ? 'Waiting for owner\'s response' : 'Booking accepted' }}
                                                                    </div>
                                                                @endif
                                                            </div>
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

@if($bookings->hasPages())
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        {{-- Previous Page Link --}}
                        @if($bookings->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="fas fa-chevron-left"></i>
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $bookings->previousPageUrl() }}" rel="prev">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach($bookings->getUrlRange(1, $bookings->lastPage()) as $page => $url)
                            @if($page == $bookings->currentPage())
                                <li class="page-item active">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if($bookings->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $bookings->nextPageUrl() }}" rel="next">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="fas fa-chevron-right"></i>
                                </span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
    </div>
@endif

@endsection

@section('styles')
<style>
.booking-item {
    transition: all 0.3s ease;
}

.booking-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.btn-group .btn {
    margin: 0 2px;
}

.badge {
    font-size: 0.85em;
    padding: 0.5em 0.8em;
}

.booking-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
}

.booking-image-placeholder {
    width: 100%;
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f8f9fa;
    border-radius: 8px;
}

.booking-content {
    background-color: #fff;
    border-radius: 8px;
}

.booking-details {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
}

.btn-group .btn {
    padding: 0.375rem 0.75rem;
}

.btn-group .btn i {
    margin-right: 0.25rem;
}

/* Pagination Styles */
.pagination {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

.pagination .page-item .page-link {
    color: #6c757d;
    border: 1px solid #dee2e6;
    padding: 0.5rem 1rem;
    margin: 0 0.25rem;
    border-radius: 0.25rem;
    transition: all 0.3s ease;
}

.pagination .page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.pagination .page-item .page-link:hover {
    background-color: #e9ecef;
    border-color: #dee2e6;
    color: #007bff;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
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