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
require_once __DIR__ . '/../../src/Post.php';
require_once __DIR__ . '/../../src/Like.php';
require_once __DIR__ . '/../../src/Comment.php';

try {
    Auth::requireAuth();
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['content']) || empty(trim($input['content']))) {
        throw new Exception('Content is required');
    }
    
    $post = new Post();
    $postId = $post->create(Auth::id(), trim($input['content']));
    
    // Get the created post with user data
    $createdPost = $post->getById($postId);
    
    echo json_encode([
        'success' => true,
        'message' => 'Post created successfully',
        'post' => $createdPost
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}