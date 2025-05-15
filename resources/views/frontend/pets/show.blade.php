@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ $pet->name }}</h4>
                    @if($pendingRequestsCount > 0)
                        <div class="position-relative">
                            <span class="badge badge-danger notification-badge">{{ $pendingRequestsCount }}</span>
                        </div>
                    @endif
                    <a class="btn btn-default" href="{{ route('frontend.pets.index') }}">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Photo Carousel -->
                        <div class="col-md-6 mb-4">
                            @if($pet->photo && $pet->photo->first())
                                <div id="petCarousel" class="carousel slide" data-ride="carousel">
                                    @can('pet_delete')
                                        <div class="position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                                            <form action="{{ route('frontend.pets.destroy', $pet->id) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('{{ trans('global.areYouSure') }}');" 
                                                  style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endcan
                                    <div class="carousel-inner">
                                        @foreach($pet->photo as $key => $media)
                                            <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
                                                <img src="{{ $media->getUrl() }}" 
                                                     class="d-block w-100" 
                                                     alt="{{ $pet->name }}"
                                                     style="height: 400px; object-fit: cover;">
                                                <div class="carousel-caption d-none d-md-block">
                                                    <p>{{ $media->file_name }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($pet->photo->count() > 1)
                                        <a class="carousel-control-prev" href="#petCarousel" role="button" data-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="sr-only">Previous</span>
                                        </a>
                                        <a class="carousel-control-next" href="#petCarousel" role="button" data-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="sr-only">Next</span>
                                        </a>
                                    @endif
                                </div>
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center position-relative" 
                                     style="height: 400px;">
                                    @can('pet_delete')
                                        <div class="position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                                            <form action="{{ route('frontend.pets.destroy', $pet->id) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('{{ trans('global.areYouSure') }}');" 
                                                  style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endcan
                                    <i class="fas fa-paw fa-5x text-muted"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Pet Details -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="mb-4">
                                        <h3 class="card-title">Pet Information</h3>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-2">
                                                    <strong><i class="fas fa-paw mr-2"></i>Type:</strong>
                                        {{ App\Models\Pet::TYPE_SELECT[$pet->type] ?? '' }}
                                                </p>
                                                <p class="mb-2">
                                                    <strong><i class="fas fa-birthday-cake mr-2"></i>Age:</strong>
                                        {{ $pet->age }}
                                                </p>
                                                <p class="mb-2">
                                                    <strong><i class="fas fa-venus-mars mr-2"></i>Gender:</strong>
                                        {{ App\Models\Pet::GENDER_SELECT[$pet->gender] ?? '' }}
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                            <p class="mb-2">
                                                    <strong>@if($pet->not_available==true)<i class="fa fa-stop-circle text-danger"> </i> {{ trans('cruds.pet.fields.not_available') }} @else <i class="fa fa-check-circle text-success"></i> {{ trans('cruds.pet.fields.available') }} @endif </strong> 
                                                </p>
                                                <p class="mb-2">
                                                    <strong><i class="fas fa-calendar-alt mr-2"></i>Available From:</strong><br>
                                                    @if($pet->from)
                                                        {{ \Carbon\Carbon::parse($pet->from)->format('M d, Y') }}
                                                        @if($pet->from_time)
                                                            {{ \Carbon\Carbon::parse($pet->from_time)->format('H:i') }}
                                                        @endif
                                                    @else
                                                        Not specified
                                                    @endif
                                                </p>
                                                <p class="mb-2">
                                                    <strong><i class="fas fa-calendar-alt mr-2"></i>Available To:</strong><br>
                                                    @if($pet->to)
                                                        {{ \Carbon\Carbon::parse($pet->to)->format('M d, Y') }}
                                                        @if($pet->to_time)
                                                            {{ \Carbon\Carbon::parse($pet->to_time)->format('H:i') }}
                                                        @endif
                                                    @else
                                                        Not specified
                                                    @endif
                                                </p>
                                             
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Hours and Credits Summary -->
                                    <div class="mb-4">
                                        <h5 class="card-title">Usage Statistics</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="card bg-light mb-3">
                                                    <div class="card-body text-center">
                                                        <h6 class="card-title">Total Hours</h6>
                                                        <h3 class="display-4">{{ $pet->bookings->where('completed', true)->sum('hours') ?? 0 }}</h3>
                                                        <p class="text-muted small">Completed Bookings</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card bg-light mb-3">
                                                    <div class="card-body text-center">
                                                        <h6 class="card-title">Total Credits</h6>
                                                        <h3 class="display-4">{{ $pet->bookings->where('completed', true)->sum('credits') ?? 0 }}</h3>
                                                        <p class="text-muted small">Used Credits</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if($pet->notes)
                                        <div class="mb-4">
                                            <h5 class="card-title">Additional Notes</h5>
                                            <p class="card-text">{{ $pet->notes }}</p>
                                        </div>
                                    @endif

                                    @if(!$pet->not_available && $pet->user_id !== Auth::user()->id)
                                        <div class="mb-4">
                                            <h5 class="card-title">Booking Terms & Conditions</h5>
                                            <div class="terms-conditions">
                                                <p class="small text-muted">By proceeding with the booking, you agree to the following terms:</p>
                                                <ul class="small text-muted">
                                                    <li>You must be at least 18 years old to book a pet.</li>
                                                    <li>You agree to provide a safe and suitable environment for the pet during the booking period.</li>
                                                    <li>You will not leave the pet unattended for extended periods.</li>
                                                    <li>You agree to follow all care instructions provided by the pet owner.</li>
                                                    <li>You will not transfer the pet to another person without the owner's consent.</li>
                                                    <li>You agree to cover any veterinary expenses resulting from your negligence.</li>
                                                    <li>You will return the pet in the same condition as received.</li>
                                                    <li>The pet owner reserves the right to cancel the booking if they believe the pet's welfare is at risk.</li>
                                                    <li>You agree to maintain regular communication with the pet owner during the booking period.</li>
                                                    <li>Any damage to property or injury to the pet must be reported immediately.</li>
                                                </ul>
                                                <div class="alert alert-info small">
                                                    <i class="fas fa-info-circle mr-2"></i>
                                                    Please ensure you can meet all these requirements before proceeding with the booking.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-center mt-4">
                                            <form method="POST" action="{{ route("frontend.bookings.store") }}" enctype="multipart/form-data" id="booking-form">
                                                @method('POST')
                                                @csrf
                                                <div class="form-group">
                                                    <input type="hidden" name="status" value="pending">
                                                    <input type="hidden" name="from" value="{{ $pet->from }}">
                                                    <input type="hidden" name="to" value="{{ $pet->to }}">
                                                    <input type="hidden" name="from_time" value="{{ $pet->from_time ? \Carbon\Carbon::parse($pet->from_time)->format('H:i') : '' }}">
                                                    <input type="hidden" name="to_time" value="{{ $pet->to_time ? \Carbon\Carbon::parse($pet->to_time)->format('H:i') : '' }}">
                                                    <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                                                    <input type="hidden" name="pet_id" value="{{ $pet->id }}">
                                                    @if(Auth::user()->id !== $pet->user_id)
                                                        <button class="btn btn-primary btn-lg" type="submit">
                                                            {{ trans('global.book') }}
                                                        </button>
                                                    @endif
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Bookings -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Recent Bookings</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Hours</th>
                                            <th>Credits</th>
                                            <th>Status</th>
                                </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pet->bookings as $booking)
                                            <tr>
                                                <td>{{ $booking->from ? \Carbon\Carbon::parse($booking->from . ' ' . $booking->from_time)->format('Y-m-d H:i') : 'N/A' }}</td>
                                                <td>{{ $booking->to ? \Carbon\Carbon::parse($booking->to . ' ' . $booking->to_time)->format('Y-m-d H:i') : 'N/A' }}</td>
                                                <td>{{ $booking->hours ?? 0 }}</td>
                                                <td>{{ $booking->credits ?? 0 }}</td>
                                                <td>
                                                    @if($booking->completed)
                                                        <span class="badge bg-success">Completed</span>
                                                    @else
                                                        <span class="badge bg-warning">Pending</span>
                                                    @endif
                                    </td>
                                </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No bookings found for this pet.</td>
                                </tr>
                                        @endforelse
                            </tbody>
                        </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    @if(auth()->user()->canUseChat())
                        <a href="{{ route('frontend.chats.create', ['pet_id' => $pet->id]) }}" class="btn btn-primary">
                            <i class="fas fa-comments"></i> Chat with Owner
                        </a>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-crown"></i> Chat is a Premium feature. 
                            <a href="{{ route('frontend.subscription.index') }}" class="alert-link">Upgrade to Premium</a> to start chatting!
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
    .carousel-item img {
        border-radius: 8px;
    }
    .carousel-control-prev,
    .carousel-control-next {
        width: 5%;
        opacity: 0.8;
    }
    .badge {
        font-size: 0.9em;
        padding: 0.5em 1em;
    }
    .card {
        border: none;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    .card-header {
        background-color: #fff;
        border-bottom: 1px solid rgba(0,0,0,0.1);
    }
    .notification-badge {
        position: absolute;
        top: -10px;
        right: -10px;
        background-color: #dc3545;
        color: white;
        border-radius: 50%;
        padding: 0.25em 0.6em;
        font-size: 0.75em;
        font-weight: bold;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .display-4 {
        font-size: 2.5rem;
        font-weight: 300;
        line-height: 1.2;
    }
    .bg-light {
        background-color: #f8f9fa !important;
    }
</style>
@endsection

<script>
    $(document).ready(function() {
        // Format time to 24-hour format
        function formatTimeTo24Hour(time) {
            if (!time) return '';
            
            // If already in 24-hour format, return as is
            if (/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/.test(time)) {
                return time;
            }
            
            // Convert from 12-hour to 24-hour format
            let [timePart, modifier] = time.split(' ');
            let [hours, minutes] = timePart.split(':');
            
            if (hours === '12') {
                hours = '00';
            }
            
            if (modifier === 'PM') {
                hours = parseInt(hours, 10) + 12;
            }
            
            return `${hours.toString().padStart(2, '0')}:${minutes}`;
        }

        // Format time before form submission
        $('#booking-form').on('submit', function(e) {
            let fromTime = $('input[name="from_time"]').val();
            let toTime = $('input[name="to_time"]').val();
            
            // Format times to 24-hour format
            $('input[name="from_time"]').val(formatTimeTo24Hour(fromTime));
            $('input[name="to_time"]').val(formatTimeTo24Hour(toTime));
        });

        // Initialize datepickers
        $('#from').datepicker({
            format: 'yyyy-mm-dd',
            startDate: new Date(),
            autoclose: true
        });

        $('#to').datepicker({
            format: 'yyyy-mm-dd',
            startDate: new Date(),
            autoclose: true
        });

        // Initialize timepickers with 24-hour format
        $('#from_time').timepicker({
            format: 'HH:mm',
            showMeridian: false,
            minuteStep: 30
        });

        $('#to_time').timepicker({
            format: 'HH:mm',
            showMeridian: false,
            minuteStep: 30
        });
    });
</script>