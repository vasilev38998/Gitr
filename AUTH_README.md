# Gitr Authentication System

A complete authentication and registration system for the Gitr social network platform.

## Features

- ✅ User registration with validation
- ✅ User login with secure password verification
- ✅ Session management
- ✅ CSRF token protection
- ✅ Secure password hashing using bcrypt
- ✅ Email and username validation
- ✅ Access control checking

## Project Structure

```
project/
├── app/
│   ├── Auth.php                 # Main authentication class
│   ├── models/
│   │   └── User.php            # User model with database operations
│   └── middleware/             # Future middleware directory
├── config/
│   ├── database.php            # Database configuration
│   └── Database.php            # Database connection class (Singleton)
├── database/
│   ├── migrations/
│   │   └── 001_create_users_table.php  # Users table migration
│   └── migrate.php             # Migration runner
├── public/
│   ├── index.php               # Dashboard (requires authentication)
│   └── pages/
│       ├── register.php        # Registration page
│       ├── login.php           # Login page
│       └── logout.php          # Logout handler
└── .gitignore                  # Git ignore file
```

## Setup Instructions

### 1. Database Configuration

Edit `config/database.php` or set environment variables:

```php
$_ENV['DB_HOST'] = 'localhost';
$_ENV['DB_NAME'] = 'gitr';
$_ENV['DB_USER'] = 'root';
$_ENV['DB_PASSWORD'] = '';
```

### 2. Run Migrations

Execute the migration script to create the users table:

```bash
php database/migrate.php up
```

To rollback:

```bash
php database/migrate.php down
```

### 3. Configure Web Server

Make sure the `public/` directory is your document root and configure URL rewriting (if needed).

For Apache (create `.htaccess`):
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?path=$1 [QSA,L]
</IfModule>
```

## Usage

### Registration

Access `/pages/register.php` to create a new account.

**Requirements:**
- Username: 3-100 characters (alphanumeric, hyphens, underscores)
- Email: Valid email format
- Password: Minimum 8 characters

### Login

Access `/pages/login.php` to log in with username and password.

### Dashboard

After login, users are redirected to `/` (dashboard) where they can see their profile information.

### Logout

Access `/pages/logout.php` to end the session and log out.

## API Reference

### Auth Class

```php
use App\Auth;

$auth = new Auth();

// Check if user is authenticated
$auth->isAuthenticated();

// Get authenticated user data
$user = $auth->getAuthenticatedUser();

// Get authenticated user ID
$user_id = $auth->getAuthenticatedUserId();

// Login
$result = $auth->login($username, $password);

// Register
$result = $auth->register($username, $email, $password, $password_confirm);

// Logout
$auth->logout();

// Require authentication (redirect to login if not)
$auth->requireAuthentication();

// CSRF Token Management
$token = $auth->generateCsrfToken();
$is_valid = $auth->validateCsrfToken($token);
$is_verified = $auth->verifyCsrfToken($token);
```

### User Model

```php
use App\Models\User;

$user = new User();

// Create user
$result = $user->create($username, $email, $password);

// Find by username
$user_data = $user->findByUsername($username);

// Find by email
$user_data = $user->findByEmail($email);

// Find by ID
$user_data = $user->findById($id);

// Verify password
$is_valid = $user->verifyPassword($plaintext_password, $hash);
```

## Security Features

### Password Security
- Passwords are hashed using PHP's `password_hash()` with BCRYPT algorithm
- Passwords are verified using `password_verify()` function
- Passwords are never stored in plain text

### CSRF Protection
- Every form includes a unique CSRF token
- Tokens are validated on form submission
- Uses `hash_equals()` for timing-safe comparison

### Input Validation
- Username validation: length, allowed characters
- Email validation: RFC 5322 compliant
- Password requirements: minimum length enforcement

### SQL Injection Prevention
- All database queries use prepared statements
- Parameters are properly bound using parameterized queries

## Database Schema

### users table

```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_username (username),
    KEY idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Error Handling

The system handles various error scenarios:

- Invalid username/email format
- Duplicate username or email
- Password mismatch during registration
- Invalid credentials during login
- CSRF token validation failures
- Database connection errors

## Session Management

- Sessions are automatically started when Auth class is instantiated
- Session data is stored securely in PHP sessions
- User data is stored in `$_SESSION['authenticated_user']`
- CSRF tokens are stored in `$_SESSION['csrf_token']`

## Future Enhancements

- Email verification
- Password reset functionality
- Two-factor authentication
- Social media login integration
- User profile customization
- Account settings management

## License

This authentication system is part of the Gitr social network project.
