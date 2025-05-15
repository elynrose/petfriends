# PetFriends - Pet Sitting & Boarding Platform

## Overview
PetFriends is a Laravel-based platform that connects pet owners with pet sitters. The platform allows users to list their pets, manage availability, and handle bookings for pet sitting services.

## Features

### Core Features
- Pet Management System
- Booking System
- Premium User Features
- Credit System
- Media Management (Photos)

### User Types
- Regular Users
- Premium Users (with additional features)

## Technical Stack
- Laravel Framework
- MySQL Database
- Spatie Media Library
- Carbon Date/Time Library
- Bootstrap (Frontend)

## Installation

1. Clone the repository:
```bash
git clone [repository-url]
```

2. Install dependencies:
```bash
composer install
```

3. Copy environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Configure database in .env file:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=petfriends
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. Run migrations:
```bash
php artisan migrate
```

7. Start the development server:
```bash
php artisan serve
```

## System Architecture

### Models

#### Pet Model
- Represents pets in the system
- Attributes:
  - type (Cat/Dog)
  - name
  - age
  - gender
  - not_available (boolean)
  - from (availability start)
  - to (availability end)
  - featured_until (for premium features)
- Relationships:
  - belongsTo User
  - hasMany Bookings
  - hasMany PetReviews
  - morphMany Media (photos)

#### User Model
- Represents pet owners/users
- Features:
  - Premium status
  - Credit management
  - Location information

#### Booking Model
- Manages pet sitting bookings
- Attributes:
  - status (pending, completed, etc.)
  - from (start date/time)
  - to (end date/time)

### Controllers

#### Frontend Controllers
- HomeController: Manages home page and featured pets
- PetsController: Handles pet CRUD operations
- BookingController: Manages booking operations
- PetController: Additional pet-related functionality

#### Admin Controllers
- Admin/PetsController: Admin pet management
- Admin/HomeController: Admin dashboard

### Services

#### PetAvailabilityService
- Manages pet availability
- Handles credit calculations
- Validates booking times

#### CreditService
- Manages user credits
- Handles credit transactions
- Validates credit availability

## Features in Detail

### Pet Management
1. Create Pet
   - Required fields: name, type, age, gender
   - Optional: photos, notes
   - Location information required

2. Edit Pet
   - Update pet information
   - Manage availability
   - Upload/remove photos
   - Feature pet (premium users)

3. Delete Pet
   - Soft delete implementation
   - Authorization check

### Availability System
1. Set Availability
   - Start date and time
   - End date and time
   - Credit requirement
   - Time restrictions (6 AM - 10 PM)

2. Availability Rules
   - Must be future dates
   - End time after start time
   - Minimum 1-hour duration
   - Within allowed hours

### Premium Features
1. Pet Featuring
   - 1-hour featured listing
   - Premium user requirement
   - Home page visibility

2. Premium User Benefits
   - Featured pet capability
   - Additional features (to be implemented)

### Booking System
1. Create Booking
   - Date/time selection
   - Conflict checking
   - Status tracking

2. Booking Management
   - View bookings
   - Update status
   - Cancel bookings

### Credit System
1. Credit Management
   - Credit balance
   - Credit transactions
   - Credit validation

2. Credit Usage
   - Setting availability
   - Premium features
   - Refund handling

## Security Features

### Authorization
- Pet ownership verification
- Premium feature access control
- Admin access control

### Validation
- Form request validation
- Credit balance validation
- Location information validation

### Data Protection
- Soft deletes
- Secure file uploads
- Input sanitization

## UI/UX Features

### Responsive Design
- Mobile-friendly interface
- Bootstrap framework
- Custom styling

### User Interface
- Photo galleries
- Date/time pickers
- Form validation
- Success/error messages

### Media Management
- Photo upload
- Image optimization
- Thumbnail generation

## Business Rules

### Pet Management
- Location information required
- Photo upload capability
- Availability management

### Premium Features
- Premium user verification
- Featured pet duration (1 hour)
- Availability requirement

### Booking Rules
- Time restrictions (6 AM - 10 PM)
- Conflict prevention
- Credit requirements

## Development Guidelines

### Code Style
- PSR-12 standards
- Laravel best practices
- Clean code principles

### Testing
- Unit tests
- Feature tests
- Integration tests

### Documentation
- Code documentation
- API documentation
- User guides

## Deployment

### Requirements
- PHP 8.0+
- MySQL 5.7+
- Composer
- Node.js & NPM

### Production Setup
1. Configure environment
2. Run migrations
3. Set up storage links
4. Configure web server
5. Set up SSL certificate

## Support

For support, please contact:
- Email: [support-email]
- Documentation: [documentation-url]
- Issue Tracker: [issue-tracker-url]

## License

This project is licensed under the [License Name] - see the LICENSE file for details.
