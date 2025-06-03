@extends('layouts.frontend')

@section('content')
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PetFriends - Pet Sitting Community</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #FF6F61;
            --secondary-color: #4ECDC4;
            --dark-color: #2C3E50;
            --light-color: #F7F9FC;
        }

        body {
            font-family: 'Nunito', sans-serif;
            color: var(--dark-color);
        }

        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.pexels.com/photos/1404819/pexels-photo-1404819.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2');
            background-size: cover;
            background-position: center;
            min-height: 75vh;
            display: flex;
            align-items: center;
            color: white;
        }

        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .stats-section {
            background-color: var(--primary-color);
            color: white;
            padding: 4rem 0;
        }

        .testimonial-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
        }

        .cta-section {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.pexels.com/photos/1643457/pexels-photo-1643457.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 6rem 0;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: darken(var(--primary-color), 10%);
            border-color: darken(var(--primary-color), 10%);
        }

        .btn-outline-light:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .footer {
            background-color: var(--dark-color);
            color: white;
            padding: 4rem 0 2rem;
        }

        .social-links a {
            color: white;
            font-size: 1.5rem;
            margin-right: 1rem;
        }

        .social-links a:hover {
            color: var(--primary-color);
        }

        .feature-card {
            text-align: center;
            padding: 2rem;
            border-radius: 10px;
            background: white;
            height: 100%;
        }

        .feature-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .testimonial-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .testimonial-card img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }

        .rating {
            color: #FFD700;
            margin-bottom: 1rem;
        }

        .credit-system {
            background: var(--light-color);
            padding: 2rem;
            border-radius: 10px;
            margin-top: 2rem;
        }

        .credit-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Trusted Pet Sitting Community</h1>
                    <p class="lead mb-4">Join our community of pet lovers. Earn credits by caring for pets and use them for your own pet's care when you're away.</p>
                    <div class="d-flex gap-3">
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Join Now</a>
                        <a href="#how-it-works" class="btn btn-outline-light btn-lg">How It Works</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="how-it-works" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">How PetFriends Works</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <img src="https://images.pexels.com/photos/1643456/pexels-photo-1643456.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2" alt="Earn Credits" class="img-fluid">
                        <h3 class="h5 mb-3">Earn Credits</h3>
                        <p>Care for other members' pets and earn credits that you can use for your own pet's care.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <img src="https://images.pexels.com/photos/1643455/pexels-photo-1643455.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2" alt="Use Credits" class="img-fluid">
                        <h3 class="h5 mb-3">Use Credits</h3>
                        <p>When you need pet care, use your earned credits to request care from trusted community members.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <img src="https://images.pexels.com/photos/1643454/pexels-photo-1643454.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2" alt="Build Trust" class="img-fluid">
                        <h3 class="h5 mb-3">Build Trust</h3>
                        <p>Connect with verified pet lovers in your area and build lasting relationships.</p>
                    </div>
                </div>
            </div>

            <!-- Credit System Section -->
            <div class="credit-system text-center mt-5">
                <h3 class="mb-4">Our Credit System</h3>
                <div class="row">
                    <div class="col-md-4">
                        <i class="fas fa-coins credit-icon"></i>
                        <h4>Earn Credits</h4>
                        <p>Care for pets, refer friends, or purchase credits directly.</p>
                    </div>
                    <div class="col-md-4">
                        <i class="fas fa-exchange-alt credit-icon"></i>
                        <h4>Exchange Credits</h4>
                        <p>Use your credits to request pet care when you need it.</p>
                    </div>
                    <div class="col-md-4">
                        <i class="fas fa-star credit-icon"></i>
                        <h4>Build Reputation</h4>
                        <p>Earn reviews and build your reputation in the community.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4">
                    <i class="fas fa-paw fa-3x mb-3"></i>
                    <h3 class="h2">1000+</h3>
                    <p>Pets Cared For</p>
                </div>
                <div class="col-md-4">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <h3 class="h2">500+</h3>
                    <p>Active Members</p>
                </div>
                <div class="col-md-4">
                    <i class="fas fa-coins fa-3x mb-3"></i>
                    <h3 class="h2">5000+</h3>
                    <p>Credits Exchanged</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section text-center">
        <div class="container">
            <h2 class="display-4 mb-4">Ready to Join Our Pet Sitting Community?</h2>
            <p class="lead mb-4">Start earning credits today and give your pet the care they deserve.</p>
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Join Now</a>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">What Our Members Say</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="mb-3">"The credit system is brilliant! I've earned enough credits to cover my pet's care while I'm on vacation."</p>
                        <div class="d-flex align-items-center">
                            <img src="https://images.pexels.com/photos/774909/pexels-photo-774909.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2" alt="User" class="me-3">
                            <div>
                                <h5 class="mb-0">Sarah Johnson</h5>
                                <small>Dog Owner</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="mb-3">"I love caring for other pets and earning credits. The community is so supportive and trustworthy."</p>
                        <div class="d-flex align-items-center">
                            <img src="https://images.pexels.com/photos/1222271/pexels-photo-1222271.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2" alt="User" class="me-3">
                            <div>
                                <h5 class="mb-0">Mike Thompson</h5>
                                <small>Pet Sitter</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="mb-3">"The referral program is amazing! I've earned extra credits by inviting my friends to join the community."</p>
                        <div class="d-flex align-items-center">
                            <img src="https://images.pexels.com/photos/1239291/pexels-photo-1239291.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2" alt="User" class="me-3">
                            <div>
                                <h5 class="mb-0">Emily Davis</h5>
                                <small>Community Member</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5>PetFriends</h5>
                    <p>Your trusted pet sitting community since 2024.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white">How It Works</a></li>
                        <li><a href="#" class="text-white">Credit System</a></li>
                        <li><a href="#" class="text-white">Safety & Trust</a></li>
                        <li><a href="#" class="text-white">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Newsletter</h5>
                    <p>Subscribe for pet care tips and community updates.</p>
                    <form>
                        <div class="input-group mb-3">
                            <input type="email" class="form-control" placeholder="Your email">
                            <button class="btn btn-primary" type="submit">Subscribe</button>
                        </div>
                    </form>
                </div>
            </div>
            <hr class="mt-4 mb-4">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; 2024 PetFriends. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-white me-3">Privacy Policy</a>
                    <a href="#" class="text-white">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
@endsection