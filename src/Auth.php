<?php

require_once __DIR__ . '/Database.php';

class Auth
{
    public static function check()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    public static function user()
    {
        if (!self::check()) {
            return null;
        }
        
        require_once __DIR__ . '/Profile.php';
        $profile = new Profile();
        return $profile->getById($_SESSION['user_id']);
    }
    
    public static function id()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user_id'] ?? null;
    }
    
    public static function setUserId($userId)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = $userId;
    }
    
    public static function login($userId)
    {
        self::setUserId($userId);
    }
    
    public static function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['user_id']);
        session_destroy();
    }
    
    public static function requireAuth()
    {
        if (!self::check()) {
            http_response_code(401);
            echo json_encode(['error' => 'Authentication required']);
            exit;
        }
    }
    
    public static function attemptLogin(string $email, string $password): ?int
    {
        try {
            $db = Database::getInstance();
            
            $user = $db->fetch(
                "SELECT id, password_hash, language FROM users WHERE email = ?",
                [$email]
            );
            
            if (!$user) {
                return null;
            }
            
            if (!password_verify($password, $user['password_hash'])) {
                return null;
            }
            
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user_language'] = $user['language'] ?? 'ru';
            
            return (int) $user['id'];
        } catch (Exception $e) {
            error_log('Login error: ' . $e->getMessage());
            return null;
        }
    }
    
    public static function attemptRegister(string $username, string $email, string $password): ?int
    {
        try {
            $db = Database::getInstance();
            
            if (strlen($password) < 6) {
                throw new Exception('Password too short');
            }
            
            $existingUser = $db->fetch(
                "SELECT id FROM users WHERE email = ?",
                [$email]
            );
            
            if ($existingUser) {
                throw new Exception('Email already exists');
            }
            
            $existingUsername = $db->fetch(
                "SELECT id FROM users WHERE username = ?",
                [$username]
            );
            
            if ($existingUsername) {
                throw new Exception('Username already exists');
            }
            
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            
            $language = 'ru';
            if (function_exists('get_language')) {
                $language = get_language();
            }
            
            $db->query(
                "INSERT INTO users (username, email, password_hash, language, created_at) VALUES (?, ?, ?, ?, NOW())",
                [$username, $email, $passwordHash, $language]
            );
            
            return (int) $db->lastInsertId();
        } catch (Exception $e) {
            error_log('Registration error: ' . $e->getMessage());
            throw $e;
        }
    }
}