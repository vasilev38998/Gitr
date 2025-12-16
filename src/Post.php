<?php

require_once __DIR__ . '/Database.php';

class Post
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function create($userId, $content)
    {
        if (empty(trim($content))) {
            throw new Exception('Post content cannot be empty');
        }
        
        $sql = "INSERT INTO posts (user_id, content) VALUES (?, ?)";
        $this->db->query($sql, [$userId, $content]);
        
        return $this->db->lastInsertId();
    }
    
    public function getById($id)
    {
        $sql = "SELECT p.*, u.username, u.avatar, 
                       (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
                       (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count
                FROM posts p 
                JOIN users u ON p.user_id = u.id 
                WHERE p.id = ?";
        
        return $this->db->fetch($sql, [$id]);
    }
    
    public function getFeed($userId, $limit = 20, $offset = 0)
    {
        $sql = "SELECT p.*, u.username, u.avatar,
                       (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
                       (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count,
                       CASE WHEN l.user_id IS NOT NULL THEN 1 ELSE 0 END as is_liked
                FROM posts p 
                JOIN users u ON p.user_id = u.id 
                LEFT JOIN likes l ON p.id = l.post_id AND l.user_id = ?
                ORDER BY p.created_at DESC 
                LIMIT ? OFFSET ?";
        
        return $this->db->fetchAll($sql, [$userId, $limit, $offset]);
    }
    
    public function getUserPosts($userId, $limit = 20, $offset = 0)
    {
        $sql = "SELECT p.*, u.username, u.avatar,
                       (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
                       (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count,
                       CASE WHEN l.user_id IS NOT NULL THEN 1 ELSE 0 END as is_liked
                FROM posts p 
                JOIN users u ON p.user_id = u.id 
                LEFT JOIN likes l ON p.id = l.post_id AND l.user_id = ?
                WHERE p.user_id = ?
                ORDER BY p.created_at DESC 
                LIMIT ? OFFSET ?";
        
        return $this->db->fetchAll($sql, [$userId, $userId, $limit, $offset]);
    }
    
    public function delete($id, $userId)
    {
        // Verify ownership
        $post = $this->getById($id);
        if (!$post || $post['user_id'] != $userId) {
            throw new Exception('You can only delete your own posts');
        }
        
        $sql = "DELETE FROM posts WHERE id = ?";
        $this->db->query($sql, [$id]);
        
        return true;
    }
    
    public function update($id, $userId, $content)
    {
        if (empty(trim($content))) {
            throw new Exception('Post content cannot be empty');
        }
        
        // Verify ownership
        $post = $this->getById($id);
        if (!$post || $post['user_id'] != $userId) {
            throw new Exception('You can only edit your own posts');
        }
        
        $sql = "UPDATE posts SET content = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $this->db->query($sql, [$content, $id]);
        
        return true;
    }
}