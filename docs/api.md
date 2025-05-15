# API Documentation

## Overview
The PetFriends API provides endpoints for managing pets, bookings, and user interactions. This document outlines available endpoints, authentication, and usage.

## Authentication

### API Token Authentication
All API requests require an API token in the header:
```
Authorization: Bearer {api_token}
```

### Obtaining API Token
```http
POST /api/auth/token
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password"
}
```

Response:
```json
{
    "token": "api_token_here",
    "expires_at": "2024-12-31 23:59:59"
}
```

## Endpoints

### Pets

#### List Pets
```http
GET /api/pets
```

Query Parameters:
- `type` (optional): Filter by pet type (cat/dog)
- `available` (optional): Filter by availability
- `page` (optional): Page number for pagination

Response:
```json
{
    "data": [
        {
            "id": 1,
            "name": "Buddy",
            "type": "Dog",
            "age": 3,
            "gender": "Male",
            "available": true,
            "featured_until": null
        }
    ],
    "meta": {
        "current_page": 1,
        "total": 50,
        "per_page": 15
    }
}
```

#### Get Pet Details
```http
GET /api/pets/{id}
```

Response:
```json
{
    "id": 1,
    "name": "Buddy",
    "type": "Dog",
    "age": 3,
    "gender": "Male",
    "available": true,
    "featured_until": null,
    "photos": [
        {
            "id": 1,
            "url": "https://example.com/photos/buddy.jpg",
            "thumbnail": "https://example.com/photos/buddy-thumb.jpg"
        }
    ],
    "owner": {
        "id": 1,
        "name": "John Doe",
        "rating": 4.5
    }
}
```

#### Create Pet
```http
POST /api/pets
Content-Type: application/json

{
    "name": "Buddy",
    "type": "Dog",
    "age": 3,
    "gender": "Male",
    "photos": ["photo1.jpg", "photo2.jpg"]
}
```

#### Update Pet
```http
PUT /api/pets/{id}
Content-Type: application/json

{
    "name": "Buddy",
    "type": "Dog",
    "age": 4,
    "gender": "Male"
}
```

#### Delete Pet
```http
DELETE /api/pets/{id}
```

### Bookings

#### List Bookings
```http
GET /api/bookings
```

Query Parameters:
- `status` (optional): Filter by booking status
- `from` (optional): Filter by start date
- `to` (optional): Filter by end date

Response:
```json
{
    "data": [
        {
            "id": 1,
            "pet_id": 1,
            "from": "2024-01-01 10:00:00",
            "to": "2024-01-02 10:00:00",
            "status": "pending"
        }
    ]
}
```

#### Create Booking
```http
POST /api/bookings
Content-Type: application/json

{
    "pet_id": 1,
    "from": "2024-01-01 10:00:00",
    "to": "2024-01-02 10:00:00"
}
```

#### Update Booking Status
```http
PUT /api/bookings/{id}/status
Content-Type: application/json

{
    "status": "confirmed"
}
```

### Users

#### Get User Profile
```http
GET /api/users/profile
```

Response:
```json
{
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "premium": true,
    "credits": 100,
    "rating": 4.5
}
```

#### Update User Profile
```http
PUT /api/users/profile
Content-Type: application/json

{
    "name": "John Doe",
    "phone": "1234567890"
}
```

## Error Handling

### Error Response Format
```json
{
    "error": {
        "code": "VALIDATION_ERROR",
        "message": "The given data was invalid.",
        "details": {
            "name": ["The name field is required."]
        }
    }
}
```

### Common Error Codes
- `VALIDATION_ERROR`: Input validation failed
- `UNAUTHORIZED`: Authentication required
- `FORBIDDEN`: Insufficient permissions
- `NOT_FOUND`: Resource not found
- `CONFLICT`: Resource conflict

## Rate Limiting

### Limits
- 60 requests per minute for authenticated users
- 30 requests per minute for unauthenticated users

### Headers
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1612345678
```

## Webhooks

### Available Events
- `pet.created`
- `pet.updated`
- `booking.created`
- `booking.updated`
- `booking.completed`

### Webhook Configuration
```http
POST /api/webhooks
Content-Type: application/json

{
    "url": "https://your-domain.com/webhook",
    "events": ["pet.created", "booking.created"]
}
```

### Webhook Payload
```json
{
    "event": "pet.created",
    "timestamp": "2024-01-01T12:00:00Z",
    "data": {
        "id": 1,
        "name": "Buddy"
    }
}
```

## Best Practices

### Request Headers
- Always include `Accept: application/json`
- Use `Content-Type: application/json` for POST/PUT requests
- Include API token in `Authorization` header

### Response Handling
- Check status codes
- Handle rate limiting
- Implement proper error handling
- Cache responses when appropriate

### Security
- Use HTTPS only
- Implement proper authentication
- Validate all input
- Sanitize all output

## SDK Examples

### PHP
```php
$client = new PetFriendsClient('api_token');

// List pets
$pets = $client->pets()->list([
    'type' => 'dog',
    'available' => true
]);

// Create booking
$booking = $client->bookings()->create([
    'pet_id' => 1,
    'from' => '2024-01-01 10:00:00',
    'to' => '2024-01-02 10:00:00'
]);
```

### JavaScript
```javascript
const client = new PetFriendsClient('api_token');

// List pets
const pets = await client.pets.list({
    type: 'dog',
    available: true
});

// Create booking
const booking = await client.bookings.create({
    pet_id: 1,
    from: '2024-01-01 10:00:00',
    to: '2024-01-02 10:00:00'
});
```

## Versioning

### Current Version
- API Version: v1
- Base URL: `https://api.petfriends.com/v1`

### Version Header
```
Accept: application/vnd.petfriends.v1+json
```

## Support

For API support:
- Email: api@petfriends.com
- Documentation: https://api.petfriends.com/docs
- Status Page: https://status.petfriends.com 