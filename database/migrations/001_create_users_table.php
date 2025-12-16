<?php

namespace Database\Migrations;

use App\Config\Database;

class CreateUsersTable
{
    public static function up()
    {
        $db = Database::getInstance()->getConnection();

        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            username VARCHAR(100) UNIQUE NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY idx_username (username),
            KEY idx_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        if (!$db->query($sql)) {
            return ['success' => false, 'error' => 'Error creating users table: ' . $db->error];
        }

        return ['success' => true, 'message' => 'Users table created successfully'];
    }

    public static function down()
    {
        $db = Database::getInstance()->getConnection();

        $sql = "DROP TABLE IF EXISTS users";

        if (!$db->query($sql)) {
            return ['success' => false, 'error' => 'Error dropping users table: ' . $db->error];
        }

        return ['success' => true, 'message' => 'Users table dropped successfully'];
    }
}
