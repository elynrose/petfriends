@extends('layouts.frontend')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Notifications</h4>
                    <div>
                        <a href="{{ route('frontend.notifications.preferences.edit') }}" class="btn btn-sm btn-outline-secondary me-2">
                            <i class="fas fa-cog"></i> Preferences
                        </a>
                        @if($notifications->isNotEmpty())
                            <form action="{{ route('frontend.notifications.mark-all-read') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                    Mark All as Read
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    @forelse($notifications as $notification)
                        <div class="notification-item mb-3 p-3 border rounded">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="mb-1">
                                        <a href="{{ route('frontend.pets.show', $notification->pet) }}" class="text-dark">
                                            {{ $notification->pet->name }}
                                        </a>
                                        is available again!
                                    </h5>
                                    <p class="text-muted mb-0">
                                        <small>
                                            <i class="fas fa-clock"></i>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </small>
                                    </p>
                                </div>
                                <form action="{{ route('frontend.notifications.mark-read', $notification) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                                        Mark as Read
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-bell fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No new notifications</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .notification-item {
        transition: background-color 0.2s;
    }
    .notification-item:hover {
        background-color: #f8f9fa;
    }
</style>
@endsection 