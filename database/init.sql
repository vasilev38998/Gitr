-- Initial database setup

CREATE DATABASE IF NOT EXISTS social_network
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE social_network;

CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    username VARCHAR(64) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_users_email (email),
    UNIQUE KEY uniq_users_username (username)
) ENGINE=InnoDB;
