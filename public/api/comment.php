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
require_once __DIR__ . '/../../src/Comment.php';

try {
    Auth::requireAuth();
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['post_id']) || empty($input['post_id'])) {
        throw new Exception('Post ID is required');
    }
    
    if (!isset($input['content']) || empty(trim($input['content']))) {
        throw new Exception('Comment content is required');
    }
    
    $postId = (int)$input['post_id'];
    $content = trim($input['content']);
    
    if ($postId <= 0) {
        throw new Exception('Invalid post ID');
    }
    
    $comment = new Comment();
    $commentId = $comment->create(Auth::id(), $postId, $content);
    
    // Get the created comment with user data
    $createdComment = $comment->getById($commentId);
    
    echo json_encode([
        'success' => true,
        'message' => 'Comment created successfully',
        'comment' => $createdComment
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}