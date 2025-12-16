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
require_once __DIR__ . '/../../src/Like.php';

try {
    Auth::requireAuth();
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['post_id']) || empty($input['post_id'])) {
        throw new Exception('Post ID is required');
    }
    
    $postId = (int)$input['post_id'];
    
    if ($postId <= 0) {
        throw new Exception('Invalid post ID');
    }
    
    $like = new Like();
    $result = $like->toggle(Auth::id(), $postId);
    
    echo json_encode([
        'success' => true,
        'action' => $result['action'],
        'likes_count' => $result['count']
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}