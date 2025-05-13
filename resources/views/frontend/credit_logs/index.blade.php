@extends('layouts.frontend')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Credit History</h4>
                </div>

                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Current Balance:</strong> {{ $totalCredits }} credits
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Description</th>
                                    <th>Related Booking</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($creditLogs as $log)
                                    <tr>
                                        <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            @if($log->type === 'add')
                                                <span class="badge bg-success">Added</span>
                                            @elseif($log->type === 'deduct')
                                                <span class="badge bg-danger">Deducted</span>
                                            @else
                                                <span class="badge bg-warning">Refunded</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->type === 'add' || $log->type === 'refund')
                                                <span class="text-success">+{{ $log->amount }}</span>
                                            @else
                                                <span class="text-danger">-{{ $log->amount }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $log->description }}</td>
                                        <td>
                                            @if($log->booking)
                                                <a href="{{ route('frontend.bookings.show', $log->booking->id) }}">
                                                    Booking #{{ $log->booking->id }}
                                                    @if($log->booking->pet)
                                                        - {{ $log->booking->pet->name }}
                                                    @endif
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No credit transactions found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $creditLogs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 