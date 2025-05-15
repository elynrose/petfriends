# Database Documentation

## Overview
This document outlines the database schema, migrations, and relationships for the PetFriends application.

## Database Schema

### Users Table
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    premium BOOLEAN DEFAULT FALSE,
    credits INT DEFAULT 0,
    remember_token VARCHAR(100),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### Pets Table
```sql
CREATE TABLE pets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    type ENUM('cat', 'dog') NOT NULL,
    name VARCHAR(255) NOT NULL,
    age INT NOT NULL,
    gender ENUM('male', 'female') NOT NULL,
    not_available BOOLEAN DEFAULT FALSE,
    from_date DATE NULL,
    from_time TIME NULL,
    to_date DATE NULL,
    to_time TIME NULL,
    featured_until TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### Bookings Table
```sql
CREATE TABLE bookings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pet_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    from_time TIMESTAMP NOT NULL,
    to_time TIMESTAMP NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (pet_id) REFERENCES pets(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### Pet Reviews Table
```sql
CREATE TABLE pet_reviews (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pet_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    rating INT NOT NULL,
    comment TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (pet_id) REFERENCES pets(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### Media Table
```sql
CREATE TABLE media (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT UNSIGNED NOT NULL,
    collection_name VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(255) NOT NULL,
    disk VARCHAR(255) NOT NULL,
    size BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

## Migrations

### Create Users Table
```php
public function up()
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password');
        $table->boolean('premium')->default(false);
        $table->integer('credits')->default(0);
        $table->rememberToken();
        $table->timestamps();
    });
}
```

### Create Pets Table
```php
public function up()
{
    Schema::create('pets', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained();
        $table->enum('type', ['cat', 'dog']);
        $table->string('name');
        $table->integer('age');
        $table->enum('gender', ['male', 'female']);
        $table->boolean('not_available')->default(false);
        $table->date('from_date')->nullable();
        $table->time('from_time')->nullable();
        $table->date('to_date')->nullable();
        $table->time('to_time')->nullable();
        $table->timestamp('featured_until')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });
}
```

### Create Bookings Table
```php
public function up()
{
    Schema::create('bookings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pet_id')->constrained();
        $table->foreignId('user_id')->constrained();
        $table->timestamp('from_time');
        $table->timestamp('to_time');
        $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])
              ->default('pending');
        $table->timestamps();
    });
}
```

## Relationships

### User Model
```php
class User extends Authenticatable
{
    public function pets()
    {
        return $this->hasMany(Pet::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(PetReview::class);
    }
}
```

### Pet Model
```php
class Pet extends Model
{
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(PetReview::class);
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'model');
    }
}
```

## Indexes

### Performance Indexes
```sql
-- Users table indexes
CREATE INDEX users_email_index ON users(email);
CREATE INDEX users_premium_index ON users(premium);

-- Pets table indexes
CREATE INDEX pets_user_id_index ON pets(user_id);
CREATE INDEX pets_type_index ON pets(type);
CREATE INDEX pets_featured_until_index ON pets(featured_until);
CREATE INDEX pets_availability_index ON pets(not_available, from_date, to_date);

-- Bookings table indexes
CREATE INDEX bookings_pet_id_index ON bookings(pet_id);
CREATE INDEX bookings_user_id_index ON bookings(user_id);
CREATE INDEX bookings_status_index ON bookings(status);
CREATE INDEX bookings_time_index ON bookings(from_time, to_time);
```

## Data Integrity

### Foreign Key Constraints
```sql
-- Pets table constraints
ALTER TABLE pets
ADD CONSTRAINT fk_pets_user
FOREIGN KEY (user_id) REFERENCES users(id)
ON DELETE CASCADE;

-- Bookings table constraints
ALTER TABLE bookings
ADD CONSTRAINT fk_bookings_pet
FOREIGN KEY (pet_id) REFERENCES pets(id)
ON DELETE CASCADE;

ALTER TABLE bookings
ADD CONSTRAINT fk_bookings_user
FOREIGN KEY (user_id) REFERENCES users(id)
ON DELETE CASCADE;
```

## Database Maintenance

### Backup Strategy
```bash
# Daily backup
mysqldump -u username -p petfriends > backup_$(date +%Y%m%d).sql

# Weekly backup
mysqldump -u username -p --all-databases > full_backup_$(date +%Y%m%d).sql
```

### Optimization
```sql
-- Analyze table
ANALYZE TABLE pets;
ANALYZE TABLE bookings;

-- Optimize table
OPTIMIZE TABLE pets;
OPTIMIZE TABLE bookings;
```

## Security

### User Permissions
```sql
-- Create application user
CREATE USER 'petfriends_app'@'localhost' IDENTIFIED BY 'password';

-- Grant permissions
GRANT SELECT, INSERT, UPDATE, DELETE ON petfriends.* TO 'petfriends_app'@'localhost';
```

### Data Encryption
```php
// Encrypt sensitive data
$encrypted = encrypt($sensitiveData);

// Decrypt data
$decrypted = decrypt($encrypted);
```

## Monitoring

### Query Logging
```sql
-- Enable slow query log
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 2;
SET GLOBAL slow_query_log_file = '/var/log/mysql/slow-query.log';
```

### Performance Monitoring
```sql
-- Check table status
SHOW TABLE STATUS LIKE 'pets';

-- Check index usage
SHOW INDEX FROM pets;
```

## Troubleshooting

### Common Issues

1. Connection Issues
```sql
-- Check connection status
SHOW STATUS LIKE 'Threads_connected';
SHOW PROCESSLIST;
```

2. Performance Issues
```sql
-- Check slow queries
SHOW VARIABLES LIKE 'slow_query%';
SHOW VARIABLES LIKE 'long_query_time';
```

3. Lock Issues
```sql
-- Check for locks
SHOW OPEN TABLES WHERE In_use > 0;
SHOW PROCESSLIST;
```

## Best Practices

1. Indexing
- Use appropriate indexes for frequently queried columns
- Avoid over-indexing
- Monitor index usage

2. Query Optimization
- Use prepared statements
- Implement proper joins
- Avoid SELECT *
- Use appropriate data types

3. Maintenance
- Regular backups
- Monitor table sizes
- Optimize tables periodically
- Update statistics

4. Security
- Use prepared statements
- Implement proper access control
- Encrypt sensitive data
- Regular security audits 