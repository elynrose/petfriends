@extends('layouts.frontend')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 font-weight-bold mb-4">Find Your Pet's Perfect Companion</h1>
                <p class="lead mb-4">Connect with trusted pet sitters in your area. Safe, reliable, and loving care for your furry friends.</p>
                        @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-light btn-custom">Get Started</a>
                        @endif
            </div>
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1548199973-03cce0bbc87b?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                     alt="Happy pets" class="img-fluid rounded-lg shadow-lg">
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Why Choose PetFriends?</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card feature-card h-100">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-shield-alt feature-icon"></i>
                        <h4 class="card-title">Trusted Care</h4>
                        <p class="card-text">All our pet sitters are verified and background-checked to ensure your pet's safety.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card feature-card h-100">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-clock feature-icon"></i>
                        <h4 class="card-title">Flexible Hours</h4>
                        <p class="card-text">Book pet sitting services for any duration, from a few hours to several days.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card feature-card h-100">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-star feature-icon"></i>
                        <h4 class="card-title">Verified Reviews</h4>
                        <p class="card-text">Read authentic reviews from other pet owners to find the perfect match.</p>
                    </div>
                </div>
            </div>
        </div>
                                </div>
</section>

<!-- Stats Section -->
<section class="stats-section py-5 mb-5" style="background-color: #FF6F61;">
    <div class="container justify-content-center py-5">
        <div class="row">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-number"><h1 class="text-center text-white">1000+</h1></div>
                    <p class="text-center text-white">Happy Pets</p>
                            </div>
                                </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-number"><h1 class="text-center text-white">500+</h1></div>
                    <p class="text-center text-white">Trusted Sitters</p>
                            </div>
                                </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-number"><h1 class="text-center text-white">98%</h1>   </div>
                    <p class="text-center text-white">Satisfaction Rate</p>
                            </div>
                                </div>
                            </div>
                        </div>
</section>

<!-- CTA Section -->
<section class="cta-section py-5 mb-5">
    <div class="container text-center">
        <h2 class="mb-4">Ready to Find Your Pet's Perfect Match?</h2>
        <p class="lead mb-4">Join our community of pet lovers today and experience the best pet sitting service.</p>
        @if (Route::has('register'))
            <a href="{{ route('register') }}" class="btn btn-light btn-primary btn-lg">Sign Up Now</a>
        @endif
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials-section py-5">
    <div class="container justify-content-center">
        <h2 class="text-center mb-5">What Our Clients Say</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="testimonial-card"> 
                    <div class="testimonial-image">
                        <img src="https://images.unsplash.com/photo-1548199973-03cce0bbc87b?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Testimonial Image" class="img-fluid rounded-circle">
                    </div>
                    <div class="testimonial-content mt-3">
                        <p>"I couldn't be happier with the service I received from PetFriends. My dog absolutely adores their sitter!"</p>
                        <p class="testimonial-author text-muted">- Sarah M.</p>
                    </div>
                </div>
                        </div>
            <div class="col-md-4 mb-4">
                <div class="testimonial-card">
                    <div class="testimonial-image">
                        <img src="https://images.unsplash.com/photo-1548199973-03cce0bbc87b?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Testimonial Image" class="img-fluid rounded-circle">
                    </div>
                    <div class="testimonial-content mt-3">
                        <p>"I couldn't be happier with the service I received from PetFriends. My dog absolutely adores their sitter!"</p>
                        <p class="testimonial-author text-muted">- Sarah M.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="testimonial-card">
                    <div class="testimonial-image">
                        <img src="https://images.unsplash.com/photo-1548199973-03cce0bbc87b?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Testimonial Image" class="img-fluid rounded-circle">
                    </div>
                    <div class="testimonial-content mt-3">
                        <p>"I couldn't be happier with the service I received from PetFriends. My dog absolutely adores their sitter!"</p>
                        <p class="testimonial-author text-muted">- Sarah M.</p>
                    </div>
                </div>  
            </div>
        </div>
    </div>
</section>

<!-- Footer Section -->
<footer class="footer py-5 mt-5" style="background-color: #999999;">
    <div class="container justify-content-center">
        <div class="row">
            <p class="text-center text-white">Copyright &copy; 2025 PetFriends. All rights reserved.</p>
        </div>
    </div>          
</footer>

@push('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, #FF6F61 0%, #FF8E53 100%);
        color: white;
        padding: 100px 0;
        position: relative;
        overflow: hidden;
    }
    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('https://images.unsplash.com/photo-1450778869180-41d0601e046e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80') center/cover;
        opacity: 0.1;
    }
    .feature-card {
        border: none;
        border-radius: 15px;
        transition: transform 0.3s ease;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .feature-card:hover {
        transform: translateY(-5px);
    }
    .feature-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: #FF6F61;
    }
    .cta-section {
        background: #8EC6C5;
        color: white;
        padding: 80px 0;
    }
    .btn-custom {
        padding: 12px 30px;
        border-radius: 30px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .btn-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    .stats-section {
        background: #f8f9fa;
        padding: 60px 0;
    }
    .stat-card {
        text-align: center;
        padding: 30px;
    }
    .stat-number {
        font-size: 2.5rem;
        font-weight: 600;
        color: #FF6F61;
    }
</style>
@endpush
@endsection