@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
            <div class="text-center py-5">
                            <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                            <h5>{{ $bookings->count() }} requests(s) found</h5>
                       
                        </div>

                <div class="card-body">
                    @if($bookings->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5>No Booking Requests</h5>
                            <p class="text-muted">You haven't received any booking requests for your pets yet.</p>
                        </div>
                    @else
                        <div class="booking-list">
                            @foreach($bookings as $booking)
                                <div class="booking-item mb-4">
                                    <div class="row">
                                        <div class="col-md-3">
                                            @if($booking->pet->photo->isNotEmpty())
                                                <img src="{{ $booking->pet->photo->first()->getUrl() }}" 
                                                     class="img-fluid rounded" 
                                                     alt="{{ $booking->pet->name }}"
                                                     style="height: 200px; object-fit: cover; width: 100%;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                                     style="height: 200px;">
                                                    <i class="fas fa-paw fa-3x text-muted"></i>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="col-md-9">
                                            <div class="booking-content p-3">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h5 class="mb-2">{{ $booking->pet->name }}</h5>
                                                        <span class="badge badge-{{ $booking->status === 'pending' ? 'warning' : 
                                                            ($booking->status === 'accepted' ? 'success' : 
                                                            ($booking->status === 'rejected' ? 'danger' : 
                                                            ($booking->status === 'completed' ? 'info' : 
                                                            ($booking->status === 'new' ? 'success' : 'secondary')
                                                            ))) }}">
                                                            {{ App\Models\Booking::STATUS_SELECT[$booking->status] ?? 'Unknown' }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="booking-details mt-3">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p class="mb-2">
                                                                <i class="fas fa-user text-primary"></i>
                                                                <strong>Requester:</strong> {{ $booking->user->name }}
                                                            </p>
                                                            <p class="mb-2">
                                                                <i class="fas fa-calendar text-primary"></i>
                                                                <strong>From:</strong> {{ \Carbon\Carbon::parse($booking->from)->format('M d, Y') }} {{ $booking->from_time ? \Carbon\Carbon::parse($booking->from_time)->format('H:i') : '' }}
                                                            </p>
                                                            <p class="mb-2">
                                                                <i class="fas fa-calendar text-primary"></i>
                                                                <strong>To:</strong> {{ \Carbon\Carbon::parse($booking->to)->format('M d, Y') }} {{ $booking->to_time ? \Carbon\Carbon::parse($booking->to_time)->format('H:i') : '' }}
                                                            </p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            @if($booking->status === 'pending')
                                                                <div class="d-flex gap-2">
                                                                    <form action="{{ route('frontend.requests.update', $booking->id) }}" 
                                                                          method="POST" 
                                                                          class="flex-grow-1">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <input type="hidden" name="status" value="accepted">
                                                                        <button type="submit" class="btn btn-success w-100">
                                                                            <i class="fas fa-check"></i> Accept
                                                                        </button>
                                                                    </form>
                                                                    <form action="{{ route('frontend.requests.update', $booking->id) }}" 
                                                                          method="POST" 
                                                                          class="flex-grow-1">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <input type="hidden" name="status" value="rejected">
                                                                        <button type="submit" class="btn btn-danger w-100">
                                                                            <i class="fas fa-times"></i> Reject
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            @endif

                                                            @if($booking->status === 'accepted')
                                                                <form action="{{ route('frontend.bookings.complete', $booking->id) }}" 
                                                                      method="POST" 
                                                                      class="mt-2">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button type="submit" 
                                                                            class="btn btn-primary w-100"
                                                                            onclick="return confirm('{{ trans('global.completeBookingConfirmation') }}')">
                                                                        <i class="fas fa-check"></i> {{ trans('global.markAsCompleted') }}
                                                                    </button>
                                                                </form>
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
