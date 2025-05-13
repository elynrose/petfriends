@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>{{ $pet->name }}'s Details</h2>
                    <div class="btn-group">
                        @can('update', $pet)
                            <a href="{{ route('pets.edit', $pet) }}" class="btn btn-primary">Edit Pet</a>
                        @endcan
                        @can('delete', $pet)
                            <form action="{{ route('pets.destroy', $pet) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this pet?')">Delete Pet</button>
                            </form>
                        @endcan
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Pet Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Pet Information</h4>
                                </div>
                                <div class="card-body">
                                    <table class="table">
                                        <tr>
                                            <th>Name:</th>
                                            <td>{{ $pet->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Type:</th>
                                            <td>{{ $pet->type }}</td>
                                        </tr>
                                        <tr>
                                            <th>Age:</th>
                                            <td>{{ $pet->age }}</td>
                                        </tr>
                                        <tr>
                                            <th>Gender:</th>
                                            <td>{{ $pet->gender }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status:</th>
                                            <td>
                                                @if($pet->not_available)
                                                    <span class="badge bg-danger">Not Available</span>
                                                @else
                                                    <span class="badge bg-success">Available</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Hours and Credits Summary -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>Hours & Credits Summary</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card bg-light mb-3">
                                                <div class="card-body text-center">
                                                    <h5 class="card-title">Total Hours</h5>
                                                    <h2 class="display-4">{{ $pet->bookings->where('completed', true)->sum('hours') }}</h2>
                                                    <p class="text-muted">Completed Bookings</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-light mb-3">
                                                <div class="card-body text-center">
                                                    <h5 class="card-title">Total Credits</h5>
                                                    <h2 class="display-4">{{ $pet->bookings->where('completed', true)->sum('credits') }}</h2>
                                                    <p class="text-muted">Used Credits</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Bookings -->
                    <div class="card mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>Recent Bookings</h4>
                            @can('create', App\Models\Booking::class)
                                <a href="{{ route('bookings.create', ['pet_id' => $pet->id]) }}" class="btn btn-primary">New Booking</a>
                            @endcan
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
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pet->bookings->sortByDesc('start_date') as $booking)
                                            <tr>
                                                <td>{{ $booking->start_date->format('Y-m-d H:i') }}</td>
                                                <td>{{ $booking->end_date->format('Y-m-d H:i') }}</td>
                                                <td>{{ $booking->hours }}</td>
                                                <td>{{ $booking->credits }}</td>
                                                <td>
                                                    @if($booking->completed)
                                                        <span class="badge bg-success">Completed</span>
                                                    @else
                                                        <span class="badge bg-warning">Pending</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        @can('view', $booking)
                                                            <a href="{{ route('bookings.show', $booking) }}" class="btn btn-info btn-sm">View</a>
                                                        @endcan
                                                        
                                                        @can('update', $booking)
                                                            <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-primary btn-sm">Edit</a>
                                                        @endcan
                                                        
                                                        @can('complete', $booking)
                                                            @if(!$booking->completed)
                                                                <form action="{{ route('bookings.complete', $booking) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit" class="btn btn-success btn-sm">Complete</button>
                                                                </form>
                                                            @endif
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No bookings found for this pet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 