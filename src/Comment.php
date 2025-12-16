<?php

require_once __DIR__ . '/Database.php';

class Comment
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function create($userId, $postId, $content)
    {
        if (empty(trim($content))) {
            throw new Exception('Comment content cannot be empty');
        }
        
        // Verify post exists
        $post = $this->db->fetch("SELECT id FROM posts WHERE id = ?", [$postId]);
        if (!$post) {
            throw new Exception('Post not found');
        }
        
        $sql = "INSERT INTO comments (user_id, post_id, content) VALUES (?, ?, ?)";
        $this->db->query($sql, [$userId, $postId, $content]);
        
        return $this->db->lastInsertId();
    }
    
    public function getByPostId($postId, $limit = 50, $offset = 0)
    {
        $sql = "SELECT c.*, u.username, u.avatar 
                FROM comments c 
                JOIN users u ON c.user_id = u.id 
                WHERE c.post_id = ? 
                ORDER BY c.created_at ASC 
                LIMIT ? OFFSET ?";
        
        return $this->db->fetchAll($sql, [$postId, $limit, $offset]);
    }
    
    public function getById($id)
    {
        $sql = "SELECT c.*, u.username, u.avatar, p.content as post_content
                FROM comments c 
                JOIN users u ON c.user_id = u.id 
                JOIN posts p ON c.post_id = p.id 
                WHERE c.id = ?";
        
        return $this->db->fetch($sql, [$id]);
    }
    
    public function delete($id, $userId)
    {
        // Verify ownership
        $comment = $this->getById($id);
        if (!$comment || $comment['user_id'] != $userId) {
            throw new Exception('You can only delete your own comments');
        }
        
        $sql = "DELETE FROM comments WHERE id = ?";
        $this->db->query($sql, [$id]);
        
        return true;
    }
    
    public function update($id, $userId, $content)
    {
        if (empty(trim($content))) {
            throw new Exception('Comment content cannot be empty');
        }
        
        // Verify ownership
        $comment = $this->getById($id);
        if (!$comment || $comment['user_id'] != $userId) {
            throw new Exception('You can only edit your own comments');
        }
        
        $sql = "UPDATE comments SET content = ?, created_at = CURRENT_TIMESTAMP WHERE id = ?";
        $this->db->query($sql, [$content, $id]);
        
        return true;
    }
    
    public function getCommentsCountForPost($postId)
    {
        $result = $this->db->fetch("SELECT COUNT(*) as count FROM comments WHERE post_id = ?", [$postId]);
        return $result['count'] ?? 0;
    }
}