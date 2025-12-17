-- Migration to add/update language support in users table
-- Ensures language column exists with correct specifications:
-- - Type: VARCHAR(10) for future language code support
-- - Default: 'ru' (Russian as default language)

USE social_network;

-- Check if language column exists
SET @col_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'social_network'
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'language'
);

-- If column doesn't exist, add it
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE users ADD COLUMN language VARCHAR(10) DEFAULT ''ru'' AFTER avatar_url',
    'SELECT "Column language already exists, checking specifications..." AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check current column specifications
SET @col_type = (
    SELECT COLUMN_TYPE
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'social_network'
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'language'
);

SET @col_default = (
    SELECT COLUMN_DEFAULT
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'social_network'
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'language'
);

-- If column exists but needs to be modified (wrong type or default)
SET @needs_modification = (
    SELECT IF(
        @col_exists > 0 AND (
            @col_type != 'varchar(10)' OR 
            (@col_default IS NOT NULL AND @col_default != 'ru')
        ), 
        1, 
        0
    )
);

SET @sql = IF(@needs_modification = 1,
    'ALTER TABLE users MODIFY COLUMN language VARCHAR(10) DEFAULT ''ru''',
    'SELECT "Column language has correct specifications" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Update existing users with 'en' default to 'ru' if needed (optional, comment out if you want to keep existing preferences)
-- UPDATE users SET language = 'ru' WHERE language = 'en';

SELECT 'Language column setup completed successfully' AS message;
