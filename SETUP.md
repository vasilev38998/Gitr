# Gitr Setup Guide

Complete setup instructions for the Gitr authentication system.

## Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache, Nginx, or PHP built-in server)
- Composer (optional, but recommended)

## Installation Steps

### 1. Clone or Download the Project

```bash
git clone <repository-url> gitr
cd gitr
```

### 2. Configure Database Connection

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

Alternatively, set environment variables:

```bash
export DB_HOST=localhost
export DB_NAME=gitr
export DB_USER=root
export DB_PASSWORD=your_password
```

### 3. Create Database

Using MySQL client:

```bash
mysql -u root -p
CREATE DATABASE IF NOT EXISTS gitr CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 4. Run Migrations

```bash
php database/migrate.php up
```

Expected output:
```
Running migrations...
SUCCESS: Users table created successfully
```

### 5. Configure Web Server

#### Using PHP Built-in Server (Development)

```bash
cd public
php -S localhost:8000
```

Then access: `http://localhost:8000`

#### Using Apache

1. Set `DocumentRoot` to the `public/` directory
2. Enable mod_rewrite if needed
3. Create `.htaccess` in the `public/` directory

#### Using Nginx

Configure your Nginx server block to serve from the `public/` directory.

### 6. Test the Installation

1. Navigate to `http://localhost:8000/pages/register.php`
2. Create a new account
3. Log in with your credentials
4. You should see the dashboard

## Troubleshooting

### Database Connection Error

**Problem:** "Database connection failed"

**Solution:**
- Check MySQL is running: `mysql --version`
- Verify credentials in `config/database.php`
- Ensure database exists: `mysql -u root -p -e "SHOW DATABASES;"`

### Migration Error

**Problem:** "Error creating users table"

**Solution:**
- Run migration again: `php database/migrate.php up`
- Check database permissions
- Drop old table if exists: `DROP TABLE IF EXISTS users;`

### Session Error

**Problem:** "Session not working" or "Login not saving"

**Solution:**
- Ensure PHP session directory is writable: `chmod -R 777 /tmp`
- Check `session.save_path` in php.ini
- Restart web server

### CSRF Token Error

**Problem:** "Invalid CSRF token"

**Solution:**
- Clear browser cookies and try again
- Ensure cookies are enabled
- Check session functionality

## Development

### Project Structure

```
gitr/
├── app/               # Application code
├── config/            # Configuration files
├── database/          # Database migrations
├── public/            # Web root directory
│   ├── index.php      # Dashboard
│   └── pages/         # Auth pages
└── docs/              # Documentation
```

### Adding New Features

1. Create model in `app/models/`
2. Create controller in `app/controllers/` (optional)
3. Create view in `public/pages/`
4. Test functionality

### Database Migrations

To create new migrations:

1. Create new file in `database/migrations/`
2. Follow the pattern of `001_create_users_table.php`
3. Implement `up()` and `down()` methods
4. Update `database/migrate.php` to include new migration

## Security Checklist

- [ ] Database credentials stored securely (not in version control)
- [ ] PHP error reporting disabled in production (`display_errors = Off`)
- [ ] HTTPS enabled in production
- [ ] Regular security updates for PHP and MySQL
- [ ] Password hashing using bcrypt (already implemented)
- [ ] CSRF token validation (already implemented)
- [ ] Prepared statements for all queries (already implemented)
- [ ] Session security headers configured

## Production Deployment

### Environment Variables

Create `.env` file (not in version control):

```bash
DB_HOST=production-db-host
DB_NAME=production_db_name
DB_USER=production_user
DB_PASSWORD=secure_password
```

Load environment variables in `config/Database.php`:

```php
'host' => $_ENV['DB_HOST'] ?? 'localhost',
```

### Security Headers

Add to your web server configuration or create `public/.htaccess`:

```apache
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-XSS-Protection "1; mode=block"
</IfModule>
```

### HTTPS

Ensure your site runs over HTTPS in production. Update session configuration in `php.ini`:

```ini
session.cookie_secure = 1
session.cookie_httponly = 1
session.cookie_samesite = Lax
```

## Support

For issues or questions, please refer to `AUTH_README.md` for detailed API documentation.

## License

MIT License - See LICENSE file for details
