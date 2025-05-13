@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Bookings</h2>
                    @can('create', App\Models\Booking::class)
                        <a href="{{ route('bookings.create') }}" class="btn btn-primary">New Booking</a>
                    @endcan
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Pet</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                    <tr>
                                        <td>{{ $booking->pet->name }}</td>
                                        <td>{{ $booking->start_date->format('Y-m-d H:i') }}</td>
                                        <td>{{ $booking->end_date->format('Y-m-d H:i') }}</td>
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
                                                
                                                @can('delete', $booking)
                                                    <form action="{{ route('bookings.destroy', $booking) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this booking?')">Delete</button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 