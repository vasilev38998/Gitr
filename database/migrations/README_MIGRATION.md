# Database Structure Fix - Migration Guide

## Problem Description

The application code was using incorrect field names that didn't match the database schema:

1. **Field Name Mismatch**: Code was using `password` instead of `password_hash`
2. **Soft Delete Check**: Code was checking for `deleted_at` column which doesn't exist in the database
3. **Inconsistent Schema**: Different migration files had conflicting definitions

## Changes Made

### 1. Code Changes

#### `/src/Auth.php`
- **Line 66**: Changed `SELECT id, password FROM users WHERE email = ? AND deleted_at IS NULL` to `SELECT id, password_hash FROM users WHERE email = ?`
- **Line 74**: Changed `password_verify($password, $user['password'])` to `password_verify($password, $user['password_hash'])`
- **Line 95**: Removed `AND deleted_at IS NULL` from email existence check
- **Line 104**: Removed `AND deleted_at IS NULL` from username existence check
- **Line 120**: Changed `INSERT INTO users (username, email, password, ...)` to `INSERT INTO users (username, email, password_hash, ...)`
- **Line 42-45**: Added `Auth::login()` method as an alias for `setUserId()` for API compatibility

### 2. Database Migration Changes

#### `/database/migrations/001_initial_schema.sql`
- **Line 9**: Changed `password VARCHAR(255)` to `password_hash VARCHAR(255)`
- **Line 20**: Removed `deleted_at TIMESTAMP NULL` from users table
- **Line 33**: Removed `deleted_at TIMESTAMP NULL` from posts table
- **Line 47**: Removed `deleted_at TIMESTAMP NULL` from comments table

#### `/database/migrations/002_fix_users_table_structure.sql` (NEW)
Created a migration script to fix existing databases:
- Renames `password` column to `password_hash` if it exists
- Removes `deleted_at` column if it exists
- Ensures `language` column exists for i18n support

### 3. Reference Files

#### `/database/init.sql`
- Already correct with `password_hash` field
- No changes needed

## How to Apply the Migration

### For New Installations

Simply run the updated migration:

```bash
mysql -u root -p social_network < database/migrations/001_initial_schema.sql
```

Or use init.sql:

```bash
mysql -u root -p < database/init.sql
```

### For Existing Databases

Run the fix migration script:

```bash
mysql -u root -p social_network < database/migrations/002_fix_users_table_structure.sql
```

This will:
1. Rename `password` to `password_hash` (if needed)
2. Remove `deleted_at` column (if exists)
3. Add `language` column (if missing)
4. Preserve all existing data

## Verification

After applying the migration, verify the structure:

```sql
USE social_network;
DESCRIBE users;
```

Expected output should include:
- `id` - Primary key
- `username` - Unique, NOT NULL
- `email` - Unique, NOT NULL
- `password_hash` - NOT NULL (not `password`)
- `language` - VARCHAR(5), DEFAULT 'en'
- `created_at` - TIMESTAMP
- `updated_at` - TIMESTAMP
- NO `deleted_at` column

## Testing

After migration, test the following:

1. **Registration**: Create a new user
   - Navigate to `/auth.php?action=register`
   - Fill in username, email, password
   - Should succeed without errors

2. **Login**: Log in with the new user
   - Navigate to `/auth.php`
   - Enter email and password
   - Should successfully log in

3. **Password Verification**: Ensure passwords are properly hashed
   ```sql
   SELECT username, password_hash FROM users LIMIT 1;
   ```
   The `password_hash` should start with `$2y$` (bcrypt)

## Rollback (If Needed)

If you need to rollback (NOT RECOMMENDED as it breaks the application):

```sql
-- Rename password_hash back to password
ALTER TABLE users CHANGE COLUMN password_hash password VARCHAR(255) NOT NULL;

-- Add deleted_at column back
ALTER TABLE users ADD COLUMN deleted_at TIMESTAMP NULL;
```

**Note**: This will break the application code. Only use for emergency database recovery.

## Summary

All references to incorrect field names have been fixed in both code and migrations. The database structure is now consistent with the application code, and authentication (login/register) should work correctly.
