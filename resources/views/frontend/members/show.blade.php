@extends('layouts.frontend')
@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-4">
            <!-- Member Profile Card -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    @if($user->profile_photo_path)
                        <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-secondary mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 150px; height: 150px;">
                            <i class="fas fa-user fa-3x text-white"></i>
                        </div>
                    @endif
                    <h3 class="card-title">{{ $user->name }}</h3>
                    <p class="text-muted">Member since {{ $user->created_at->format('F Y') }}</p>
                    <div class="d-flex justify-content-center mb-3">
                        <div class="text-center mx-3">
                            <h4>{{ $totalHours }}</h4>
                            <small class="text-muted">Hours of Care</small>
                        </div>
                        <div class="text-center mx-3">
                            <h4>{{ $pets->count() }}</h4>
                            <small class="text-muted">Pets</small>
                        </div>
                        <div class="text-center mx-3">
                            <h4>{{ $reviews->count() }}</h4>
                            <small class="text-muted">Reviews</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Contact Information</h5>
                </div>
                <div class="card-body">
                    <p><i class="fas fa-envelope mr-2"></i> {{ $user->email }}</p>
                    @if($user->phone_number)
                        <p><i class="fas fa-phone mr-2"></i> {{ $user->phone_number }}</p>
                    @endif
                    @if($user->address)
                        <p><i class="fas fa-map-marker-alt mr-2"></i> {{ $user->address }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Pets Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Pets</h5>
                </div>
                <div class="card-body">
                    @if($pets->count() > 0)
                        <div class="row">
                            @foreach($pets as $pet)
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100">
                                        @if($pet->getFirstMedia('pet_photos'))
                                            <img src="{{ $pet->getFirstMedia('pet_photos')->getUrl() }}" class="card-img-top" alt="{{ $pet->name }}" style="height: 200px; object-fit: cover;">
                                        @else
                                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                                <i class="fas fa-paw fa-3x text-muted"></i>
                                            </div>
                                        @endif
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                @if($pet->user->profile_photo_path)
                                                    <img src="{{ $pet->user->profile_photo_url }}" alt="{{ $pet->user->name }}" class="rounded-circle mr-2" style="width: 32px; height: 32px; object-fit: cover;">
                                                @else
                                                    <div class="rounded-circle bg-secondary mr-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                @endif
                                                <a href="{{ route('frontend.members.show', $pet->user) }}" class="text-dark">
                                                    {{ $pet->user->name }}
                                                </a>
                                            </div>
                                            <h5 class="card-title">{{ $pet->name }}</h5>
                                            <p class="card-text">
                                                <small class="text-muted">{{ ucfirst($pet->type) }} • {{ ucfirst($pet->gender) }}</small>
                                            </p>
                                            <a href="{{ route('frontend.pets.show', $pet) }}" class="btn btn-outline-primary btn-sm">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No pets listed yet.</p>
                    @endif
                </div>
            </div>

            <!-- Reviews Section -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Reviews</h5>
                </div>
                <div class="card-body">
                    @if($reviews->count() > 0)
                        @foreach($reviews as $review)
                            <div class="review mb-4 pb-4 border-bottom">
                                <div class="d-flex align-items-center mb-2">
                                    @if($review->reviewer->profile_photo_path)
                                        <img src="{{ $review->reviewer->profile_photo_url }}" alt="{{ $review->reviewer->name }}" class="rounded-circle mr-3" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-secondary mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h6 class="mb-0">
                                            <a href="{{ route('frontend.members.show', $review->reviewer) }}" class="text-dark">
                                                {{ $review->reviewer->name }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">{{ $review->created_at->format('F j, Y') }}</small>
                                    </div>
                                </div>
                                <div class="rating mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                </div>
                                <p class="mb-0">{{ $review->comment }}</p>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted mb-0">No reviews yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 