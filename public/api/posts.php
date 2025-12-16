<?php

// Start session
session_start();

// Include localization helpers
require_once dirname(__DIR__) . '/src/Localization.php';
require_once dirname(__DIR__) . '/src/helpers.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Simple mock data for demonstration
$mockPosts = [
    [
        'id' => 1,
        'content' => 'Добро пожаловать в Gitr! 🎉',
        'username' => 'admin',
        'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours')),
        'likes_count' => 5
    ],
    [
        'id' => 2,
        'content' => 'Это демо-пост для тестирования функционала.',
        'username' => 'user1',
        'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour')),
        'likes_count' => 2
    ],
    [
        'id' => 3,
        'content' => 'Отличная социальная сеть для общения!',
        'username' => 'user2',
        'created_at' => date('Y-m-d H:i:s', strtotime('-30 minutes')),
        'likes_count' => 1
    ]
];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Return all posts
        echo json_encode([
            'success' => true,
            'data' => $mockPosts,
            'message' => trans('common.success')
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Check if user is authenticated
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            throw new Exception(trans('errors.unauthorized'));
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['content']) || empty(trim($input['content']))) {
            throw new Exception(trans('errors.required_field'));
        }
        
        // Create new post
        $newPost = [
            'id' => count($mockPosts) + 1,
            'content' => trim($input['content']),
            'username' => $_SESSION['user_username'] ?? 'user',
            'created_at' => date('Y-m-d H:i:s'),
            'likes_count' => 0
        ];
        
        echo json_encode([
            'success' => true,
            'message' => trans('posts.post_created'),
            'data' => $newPost
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Method not allowed
    http_response_code(405);
    throw new Exception(trans('errors.validation_failed'));
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'message' => trans('errors.internal_error')
    ], JSON_UNESCAPED_UNICODE);
}