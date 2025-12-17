-- Migration to fix users table structure
-- Changes:
-- 1. Rename 'password' to 'password_hash' if needed
-- 2. Remove 'deleted_at' column if it exists

USE social_network;

-- Check if password column exists and rename it to password_hash
SET @col_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'social_network'
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'password'
);

SET @sql = IF(@col_exists > 0,
    'ALTER TABLE users CHANGE COLUMN password password_hash VARCHAR(255) NOT NULL',
    'SELECT "Column password does not exist, no changes needed" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check if password_hash column doesn't exist and add it
SET @col_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'social_network'
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'password_hash'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE users ADD COLUMN password_hash VARCHAR(255) NOT NULL AFTER email',
    'SELECT "Column password_hash already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check if deleted_at column exists and remove it
SET @col_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'social_network'
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'deleted_at'
);

SET @sql = IF(@col_exists > 0,
    'ALTER TABLE users DROP COLUMN deleted_at',
    'SELECT "Column deleted_at does not exist, no changes needed" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Ensure language column exists
SET @col_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'social_network'
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'language'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE users ADD COLUMN language VARCHAR(10) DEFAULT "ru" AFTER avatar_url',
    'SELECT "Column language already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SELECT 'Users table structure updated successfully' AS message;
