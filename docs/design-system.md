# Dolce Design System

## Overview
Dolce's design system focuses on creating a modern, clean, and user-friendly experience that reflects the warmth and care associated with pet services.

## Brand Identity

### Colors
```scss
// Primary Colors
$primary: #FF6B6B;      // Warm coral - Main brand color
$secondary: #4ECDC4;    // Turquoise - Accent color
$accent: #FFE66D;       // Soft yellow - Highlight color

// Neutral Colors
$white: #FFFFFF;
$light-gray: #F7F7F7;
$medium-gray: #E0E0E0;
$dark-gray: #666666;
$black: #333333;

// Semantic Colors
$success: #2ECC71;
$warning: #F1C40F;
$error: #E74C3C;
$info: #3498DB;
```

### Typography
```scss
// Font Families
$font-primary: 'Poppins', sans-serif;    // Main font
$font-secondary: 'Inter', sans-serif;    // Secondary font

// Font Sizes
$text-xs: 0.75rem;    // 12px
$text-sm: 0.875rem;   // 14px
$text-base: 1rem;     // 16px
$text-lg: 1.125rem;   // 18px
$text-xl: 1.25rem;    // 20px
$text-2xl: 1.5rem;    // 24px
$text-3xl: 1.875rem;  // 30px
$text-4xl: 2.25rem;   // 36px

// Font Weights
$font-light: 300;
$font-regular: 400;
$font-medium: 500;
$font-semibold: 600;
$font-bold: 700;
```

### Spacing
```scss
$spacing-1: 0.25rem;   // 4px
$spacing-2: 0.5rem;    // 8px
$spacing-3: 0.75rem;   // 12px
$spacing-4: 1rem;      // 16px
$spacing-6: 1.5rem;    // 24px
$spacing-8: 2rem;      // 32px
$spacing-12: 3rem;     // 48px
$spacing-16: 4rem;     // 64px
```

### Border Radius
```scss
$radius-sm: 0.25rem;   // 4px
$radius-md: 0.5rem;    // 8px
$radius-lg: 1rem;      // 16px
$radius-full: 9999px;  // Full rounded
```

### Shadows
```scss
$shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
$shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
$shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
$shadow-xl: 0 20px 25px rgba(0, 0, 0, 0.15);
```

## Components

### Buttons
```scss
.btn {
  padding: $spacing-3 $spacing-6;
  border-radius: $radius-md;
  font-weight: $font-medium;
  transition: all 0.2s ease;
  
  &-primary {
    background-color: $primary;
    color: $white;
    &:hover {
      background-color: darken($primary, 10%);
    }
  }
  
  &-secondary {
    background-color: $secondary;
    color: $white;
    &:hover {
      background-color: darken($secondary, 10%);
    }
  }
  
  &-outline {
    border: 2px solid $primary;
    color: $primary;
    &:hover {
      background-color: $primary;
      color: $white;
    }
  }
}
```

### Cards
```scss
.card {
  background-color: $white;
  border-radius: $radius-lg;
  box-shadow: $shadow-md;
  padding: $spacing-6;
  transition: transform 0.2s ease;
  
  &:hover {
    transform: translateY(-4px);
    box-shadow: $shadow-lg;
  }
  
  &-header {
    margin-bottom: $spacing-4;
  }
  
  &-body {
    color: $dark-gray;
  }
}
```

### Forms
```scss
.form-control {
  padding: $spacing-3;
  border: 2px solid $medium-gray;
  border-radius: $radius-md;
  transition: border-color 0.2s ease;
  
  &:focus {
    border-color: $primary;
    outline: none;
  }
  
  &::placeholder {
    color: $dark-gray;
  }
}
```

### Navigation
```scss
.navbar {
  background-color: $white;
  box-shadow: $shadow-sm;
  padding: $spacing-4;
  
  &-brand {
    font-size: $text-xl;
    font-weight: $font-bold;
    color: $primary;
  }
  
  &-nav {
    .nav-link {
      color: $dark-gray;
      font-weight: $font-medium;
      padding: $spacing-2 $spacing-4;
      
      &:hover {
        color: $primary;
      }
    }
  }
}
```

## Layout

### Grid System
```scss
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 $spacing-4;
}

.grid {
  display: grid;
  gap: $spacing-6;
  
  &-cols-1 { grid-template-columns: repeat(1, 1fr); }
  &-cols-2 { grid-template-columns: repeat(2, 1fr); }
  &-cols-3 { grid-template-columns: repeat(3, 1fr); }
  &-cols-4 { grid-template-columns: repeat(4, 1fr); }
}
```

### Responsive Breakpoints
```scss
$breakpoints: (
  'sm': 640px,
  'md': 768px,
  'lg': 1024px,
  'xl': 1280px,
  '2xl': 1536px
);
```

## Animations
```scss
// Transitions
$transition-fast: 0.2s ease;
$transition-normal: 0.3s ease;
$transition-slow: 0.5s ease;

// Keyframes
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideUp {
  from { transform: translateY(20px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}
```

## Icons and Images
```scss
// Icon Sizes
$icon-sm: 1rem;
$icon-md: 1.5rem;
$icon-lg: 2rem;

// Image Styles
.img-rounded {
  border-radius: $radius-lg;
}

.img-circle {
  border-radius: $radius-full;
}
```

## Accessibility
```scss
// Focus States
:focus {
  outline: 3px solid rgba($primary, 0.5);
  outline-offset: 2px;
}

// High Contrast Mode
@media (prefers-contrast: high) {
  :root {
    --primary: #FF0000;
    --secondary: #00FF00;
  }
}
```

## Dark Mode
```scss
@media (prefers-color-scheme: dark) {
  :root {
    --background: #1A1A1A;
    --text: #FFFFFF;
    --card-bg: #2D2D2D;
  }
  
  .card {
    background-color: var(--card-bg);
  }
}
```

## Implementation Guidelines

### 1. Component Usage
- Use semantic HTML elements
- Maintain consistent spacing using the spacing scale
- Follow the color system for visual hierarchy
- Implement responsive design using the breakpoint system

### 2. Performance
- Optimize images and assets
- Use lazy loading for images
- Implement code splitting
- Minimize CSS and JavaScript

### 3. Accessibility
- Maintain proper contrast ratios
- Use semantic HTML
- Implement ARIA labels
- Ensure keyboard navigation
- Test with screen readers

### 4. Responsive Design
- Mobile-first approach
- Use fluid typography
- Implement responsive images
- Test across all breakpoints

### 5. Animation Guidelines
- Keep animations subtle and purposeful
- Use appropriate timing
- Consider reduced motion preferences
- Test performance impact 