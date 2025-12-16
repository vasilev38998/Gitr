<?php

class Auth
{
    public static function check()
    {
        session_start();
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
        session_start();
        return $_SESSION['user_id'] ?? null;
    }
    
    public static function login($userId)
    {
        session_start();
        $_SESSION['user_id'] = $userId;
    }
    
    public static function logout()
    {
        session_start();
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
}