# Gitr Authentication System - Quick Start Guide

Get the authentication system up and running in minutes.

## 1. Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache, Nginx, or PHP built-in server)

## 2. Configure Database

Edit `config/database.php` with your database credentials:

```php
return [
    'host' => 'localhost',
    'port' => 3306,
    'database' => 'gitr',
    'user' => 'root',
    'password' => 'your_password',
    'charset' => 'utf8mb4',
];
```

## 3. Create Database

```bash
mysql -u root -p
CREATE DATABASE IF NOT EXISTS gitr CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

## 4. Run Migrations

```bash
php database/migrate.php up
```

## 5. Start Development Server

```bash
cd public
php -S localhost:8000
```

## 6. Access the Application

Open your browser and navigate to:
- **Register:** http://localhost:8000/register
- **Login:** http://localhost:8000/login
- **Dashboard:** http://localhost:8000/ (after login)

## Testing the System

### Create a Test Account

1. Go to http://localhost:8000/register
2. Fill in the form:
   - Username: `testuser`
   - Email: `test@example.com`
   - Password: `password123`
   - Confirm Password: `password123`
3. Click Register

### Login

1. Go to http://localhost:8000/login
2. Enter:
   - Username: `testuser`
   - Password: `password123`
3. Click Login

### View Dashboard

After logging in, you'll see your profile information on the dashboard.

### Logout

Click the "Logout" button to end your session.

## Features Implemented

✅ User Registration
- Username validation (3-100 characters, alphanumeric + hyphens/underscores)
- Email validation
- Password strength requirement (minimum 8 characters)
- Password confirmation

✅ User Login
- Username-based authentication
- Secure password verification
- Session management

✅ Security
- CSRF token protection on all forms
- Password hashing with bcrypt
- Prepared SQL statements (prevents SQL injection)
- Session-based authentication

✅ User Dashboard
- Display authenticated user information
- Show member since and last updated dates
- Quick logout option

## Directory Structure

```
gitr/
├── public/              # Web-accessible files
│   ├── index.php        # Main router and dashboard
│   ├── .htaccess        # Apache rewrite rules
│   └── pages/
│       ├── register.php # Registration page
│       ├── login.php    # Login page
│       └── logout.php   # Logout handler
├── app/
│   ├── Auth.php         # Authentication class
│   └── models/
│       └── User.php     # User model
├── config/
│   ├── Database.php     # Database connection (Singleton)
│   └── database.php     # Database configuration
├── database/
│   ├── migrations/
│   │   └── 001_create_users_table.php
│   └── migrate.php      # Migration runner
├── autoload.php         # PSR-4 autoloader
├── composer.json        # Project dependencies
└── .gitignore          # Git ignore rules
```

## Troubleshooting

### Database Connection Error

**Error:** "Database connection failed"

**Solution:**
1. Check if MySQL is running
2. Verify credentials in `config/database.php`
3. Ensure the database was created

### Session Not Working

**Error:** "Session not working" or login not saving

**Solution:**
1. Ensure PHP session directory is writable
2. Check `/tmp` directory has proper permissions
3. Restart your web server

### CSRF Token Error

**Error:** "Invalid CSRF token"

**Solution:**
1. Clear your browser cookies
2. Try again from a fresh browser session
3. Ensure cookies are enabled

## Next Steps

Now that the authentication system is working, you can:

1. Create additional models and pages
2. Implement user profile editing
3. Add password reset functionality
4. Integrate with social features

For detailed API documentation, see `AUTH_README.md`.

For complete setup instructions, see `SETUP.md`.
