<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../../src/Database.php';
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Profile.php';

try {
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'GET':
            // Check authentication status
            if (Auth::check()) {
                echo json_encode([
                    'authenticated' => true,
                    'user' => Auth::user()
                ]);
            } else {
                echo json_encode([
                    'authenticated' => false
                ]);
            }
            break;
            
        case 'POST':
            // Login
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['email']) || !isset($input['password'])) {
                throw new Exception('Email and password are required');
            }
            
            $email = trim($input['email']);
            $password = $input['password'];
            
            // Simple authentication (in real app, use proper password hashing)
            $db = Database::getInstance();
            $user = $db->fetch(
                "SELECT id, username, email, password_hash FROM users WHERE email = ?",
                [$email]
            );
            
            if (!$user || !password_verify($password, $user['password_hash'])) {
                throw new Exception('Invalid email or password');
            }
            
            Auth::login($user['id']);
            
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email']
                ]
            ]);
            break;
            
        case 'DELETE':
            // Logout
            Auth::logout();
            echo json_encode([
                'success' => true,
                'message' => 'Logout successful'
            ]);
            break;
            
        default:
            throw new Exception('Method not allowed');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}