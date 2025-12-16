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

try {
    Auth::requireAuth();
    
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Method not allowed');
    }
    
    $limit = (int)($_GET['limit'] ?? 20);
    $offset = (int)($_GET['offset'] ?? 0);
    
    // Validate pagination
    $limit = max(1, min(100, $limit)); // Max 100 posts per request
    $offset = max(0, $offset);
    
    $post = new Post();
    $posts = $post->getFeed(Auth::id(), $limit, $offset);
    
    echo json_encode([
        'success' => true,
        'posts' => $posts,
        'pagination' => [
            'limit' => $limit,
            'offset' => $offset,
            'has_more' => count($posts) === $limit
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}