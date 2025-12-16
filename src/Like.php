<?php

require_once __DIR__ . '/Database.php';

class Like
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function toggle($userId, $postId)
    {
        // Check if already liked
        $existingLike = $this->db->fetch(
            "SELECT id FROM likes WHERE user_id = ? AND post_id = ?",
            [$userId, $postId]
        );
        
        if ($existingLike) {
            // Unlike
            $this->db->query("DELETE FROM likes WHERE user_id = ? AND post_id = ?", [$userId, $postId]);
            $this->updatePostLikesCount($postId, -1);
            return ['action' => 'unliked', 'count' => $this->getLikesCount($postId)];
        } else {
            // Like
            try {
                $this->db->query("INSERT INTO likes (user_id, post_id) VALUES (?, ?)", [$userId, $postId]);
                $this->updatePostLikesCount($postId, 1);
                return ['action' => 'liked', 'count' => $this->getLikesCount($postId)];
            } catch (Exception $e) {
                // Handle duplicate like (race condition)
                return ['action' => 'already_liked', 'count' => $this->getLikesCount($postId)];
            }
        }
    }
    
    public function getLikesCount($postId)
    {
        $result = $this->db->fetch("SELECT COUNT(*) as count FROM likes WHERE post_id = ?", [$postId]);
        return $result['count'] ?? 0;
    }
    
    public function isLikedByUser($userId, $postId)
    {
        $result = $this->db->fetch(
            "SELECT id FROM likes WHERE user_id = ? AND post_id = ?",
            [$userId, $postId]
        );
        return !empty($result);
    }
    
    private function updatePostLikesCount($postId, $change)
    {
        // This would typically be updated automatically by trigger or we maintain it separately
        // For now, we'll just update it directly
        $sql = "UPDATE posts SET updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $this->db->query($sql, [$postId]);
    }
    
    public function getLikesForPost($postId, $limit = 10, $offset = 0)
    {
        $sql = "SELECT l.*, u.username, u.avatar 
                FROM likes l 
                JOIN users u ON l.user_id = u.id 
                WHERE l.post_id = ? 
                ORDER BY l.created_at DESC 
                LIMIT ? OFFSET ?";
        
        return $this->db->fetchAll($sql, [$postId, $limit, $offset]);
    }
}