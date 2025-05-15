# Frontend Documentation

## Overview
The frontend of PetFriends is built using Laravel Blade templates, Bootstrap for styling, and JavaScript for interactivity. This document outlines the frontend architecture, components, and best practices.

## Directory Structure

```
resources/
├── views/
│   ├── frontend/
│   │   ├── pets/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   ├── edit.blade.php
│   │   │   └── show.blade.php
│   │   ├── bookings/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   └── show.blade.php
│   │   └── layouts/
│   │       ├── app.blade.php
│   │       └── guest.blade.php
│   └── components/
│       ├── pet-card.blade.php
│       ├── booking-form.blade.php
│       └── review-form.blade.php
├── js/
│   ├── app.js
│   └── components/
│       ├── PetForm.js
│       └── BookingCalendar.js
└── scss/
    ├── app.scss
    └── components/
        ├── _pet-card.scss
        └── _booking-form.scss
```

## Components

### 1. Pet Card Component
```php
{{-- resources/views/components/pet-card.blade.php --}}
<div class="pet-card">
    <div class="pet-image">
        <img src="{{ $pet->getFirstMediaUrl('photos') }}" alt="{{ $pet->name }}">
        @if($pet->featured_until)
            <span class="featured-badge">Featured</span>
        @endif
    </div>
    <div class="pet-info">
        <h3>{{ $pet->name }}</h3>
        <p>Type: {{ ucfirst($pet->type) }}</p>
        <p>Age: {{ $pet->age }} years</p>
        <p>Gender: {{ ucfirst($pet->gender) }}</p>
        @if(!$pet->not_available)
            <p class="availability">
                Available: {{ $pet->from_time }} - {{ $pet->to_time }}
            </p>
        @endif
    </div>
</div>
```

### 2. Booking Form Component
```php
{{-- resources/views/components/booking-form.blade.php --}}
<form action="{{ route('bookings.store') }}" method="POST" class="booking-form">
    @csrf
    <input type="hidden" name="pet_id" value="{{ $pet->id }}">
    
    <div class="form-group">
        <label for="from_time">From</label>
        <input type="datetime-local" 
               name="from_time" 
               id="from_time" 
               class="form-control @error('from_time') is-invalid @enderror"
               required>
        @error('from_time')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="to_time">To</label>
        <input type="datetime-local" 
               name="to_time" 
               id="to_time" 
               class="form-control @error('to_time') is-invalid @enderror"
               required>
        @error('to_time')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">Book Now</button>
</form>
```

## Layouts

### 1. Main Layout
```php
{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'PetFriends') }}</title>
    
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <!-- Navigation content -->
    </nav>

    <main class="py-4">
        @yield('content')
    </main>

    <footer class="footer mt-auto py-3 bg-light">
        <!-- Footer content -->
    </footer>
</body>
</html>
```

## JavaScript Components

### 1. Pet Form Handler
```javascript
// resources/js/components/PetForm.js
export default class PetForm {
    constructor(form) {
        this.form = form;
        this.initializeValidation();
        this.initializeImageUpload();
    }

    initializeValidation() {
        this.form.addEventListener('submit', (e) => {
            if (!this.validateForm()) {
                e.preventDefault();
            }
        });
    }

    validateForm() {
        // Form validation logic
        return true;
    }

    initializeImageUpload() {
        const input = this.form.querySelector('input[type="file"]');
        if (input) {
            input.addEventListener('change', this.handleImageUpload.bind(this));
        }
    }

    handleImageUpload(e) {
        // Image upload handling
    }
}
```

### 2. Booking Calendar
```javascript
// resources/js/components/BookingCalendar.js
export default class BookingCalendar {
    constructor(element) {
        this.element = element;
        this.initializeCalendar();
    }

    initializeCalendar() {
        // Calendar initialization
    }

    handleDateSelect(date) {
        // Date selection handling
    }

    checkAvailability(date) {
        // Availability checking
    }
}
```

## Styling

### 1. Main Styles
```scss
// resources/scss/app.scss
@import 'variables';
@import '~bootstrap/scss/bootstrap';
@import 'components/pet-card';
@import 'components/booking-form';

// Custom styles
.pet-card {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    
    .pet-image {
        position: relative;
        
        img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .featured-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: $primary;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
        }
    }
    
    .pet-info {
        padding: 1rem;
        
        h3 {
            margin-bottom: 0.5rem;
        }
        
        .availability {
            color: $success;
            font-weight: bold;
        }
    }
}
```

## Best Practices

### 1. Component Design
- Keep components focused and reusable
- Use proper naming conventions
- Implement proper error handling
- Follow accessibility guidelines

### 2. JavaScript
- Use ES6+ features
- Implement proper error handling
- Use async/await for asynchronous operations
- Follow the module pattern

### 3. Styling
- Use BEM naming convention
- Implement responsive design
- Use CSS variables for theming
- Follow accessibility guidelines

### 4. Performance
- Optimize images
- Implement lazy loading
- Use proper caching
- Minimize HTTP requests

## Accessibility

### 1. ARIA Labels
```html
<button aria-label="Book this pet">
    <i class="fas fa-calendar"></i>
</button>
```

### 2. Keyboard Navigation
```javascript
document.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && e.target.matches('.pet-card')) {
        e.target.click();
    }
});
```

## Testing

### 1. Component Testing
```javascript
// tests/Feature/PetCardTest.php
public function test_pet_card_displays_correctly()
{
    $pet = Pet::factory()->create();
    
    $response = $this->get(route('pets.show', $pet));
    
    $response->assertSee($pet->name)
             ->assertSee($pet->type)
             ->assertSee($pet->age);
}
```

### 2. JavaScript Testing
```javascript
// tests/Javascript/PetForm.test.js
describe('PetForm', () => {
    it('validates form correctly', () => {
        const form = document.createElement('form');
        const petForm = new PetForm(form);
        
        expect(petForm.validateForm()).toBe(true);
    });
});
```

## Deployment

### 1. Asset Compilation
```bash
# Compile assets
npm run production

# Watch for changes
npm run watch
```

### 2. Cache Busting
```php
// In blade templates
<link href="{{ mix('css/app.css') }}" rel="stylesheet">
<script src="{{ mix('js/app.js') }}" defer></script>
```

## Troubleshooting

### 1. Common Issues

1. JavaScript Errors
```javascript
window.onerror = function(msg, url, line) {
    console.error(`Error: ${msg}\nURL: ${url}\nLine: ${line}`);
};
```

2. CSS Conflicts
```scss
// Use specific selectors
.pet-card {
    &__image {
        // Styles
    }
    
    &__info {
        // Styles
    }
}
```

3. Performance Issues
```javascript
// Implement debouncing
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
```

## Security

### 1. CSRF Protection
```php
// In forms
@csrf
```

### 2. XSS Prevention
```php
// In blade templates
{{ $variable }}
```

### 3. Input Validation
```javascript
function sanitizeInput(input) {
    return input.replace(/[<>]/g, '');
}
```

## Monitoring

### 1. Error Tracking
```javascript
window.onerror = function(msg, url, line) {
    // Send to error tracking service
    errorTrackingService.capture({
        message: msg,
        url: url,
        line: line
    });
};
```

### 2. Performance Monitoring
```javascript
// Measure page load time
window.addEventListener('load', () => {
    const timing = window.performance.timing;
    const loadTime = timing.loadEventEnd - timing.navigationStart;
    console.log(`Page load time: ${loadTime}ms`);
});
``` 