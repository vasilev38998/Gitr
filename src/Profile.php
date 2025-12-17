<?php

require_once __DIR__ . '/Database.php';

class Profile
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function getById($id)
    {
        $sql = "SELECT id, username, email, bio, avatar, followers_count, following_count, language, created_at 
                FROM users 
                WHERE id = ?";
        
        return $this->db->fetch($sql, [$id]);
    }
    
    public function getByUsername($username)
    {
        $sql = "SELECT id, username, email, bio, avatar, followers_count, following_count, language, created_at 
                FROM users 
                WHERE username = ?";
        
        return $this->db->fetch($sql, [$username]);
    }
    
    public function update($userId, $data)
    {
        $allowedFields = ['bio', 'avatar', 'username', 'email', 'language'];
        $fields = [];
        $values = [];
        
        foreach ($data as $field => $value) {
            if (in_array($field, $allowedFields)) {
                $fields[] = "$field = ?";
                $values[] = $value;
            }
        }
        
        if (empty($fields)) {
            throw new Exception('No valid fields to update');
        }
        
        $values[] = $userId;
        $sql = "UPDATE users SET " . implode(', ', $fields) . ", updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        
        $this->db->query($sql, $values);
        
        return true;
    }
    
    public function updateAvatar($userId, $avatarPath)
    {
        if (empty($avatarPath)) {
            throw new Exception('Avatar path cannot be empty');
        }
        
        $sql = "UPDATE users SET avatar = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $this->db->query($sql, [$avatarPath, $userId]);
        
        return true;
    }
    
    public function getUserStats($userId)
    {
        $sql = "SELECT 
                    (SELECT COUNT(*) FROM posts WHERE user_id = ?) as posts_count,
                    (SELECT COUNT(*) FROM likes WHERE user_id IN (SELECT id FROM posts WHERE user_id = ?)) as total_likes_received,
                    (SELECT COUNT(*) FROM comments WHERE user_id = ?) as comments_count";
        
        return $this->db->fetch($sql, [$userId, $userId, $userId]);
    }
    
    public function searchUsers($query, $limit = 20, $offset = 0)
    {
        $sql = "SELECT id, username, bio, avatar, followers_count, following_count 
                FROM users 
                WHERE username LIKE ? OR bio LIKE ?
                ORDER BY followers_count DESC, username ASC 
                LIMIT ? OFFSET ?";
        
        $searchTerm = '%' . $query . '%';
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $limit, $offset]);
    }
    
    public function getRecentUsers($limit = 10)
    {
        $sql = "SELECT id, username, bio, avatar, followers_count, following_count, created_at 
                FROM users 
                ORDER BY created_at DESC 
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [$limit]);
    }
    
    public function incrementFollowers($userId, $change = 1)
    {
        $sql = "UPDATE users SET followers_count = followers_count + ? WHERE id = ?";
        $this->db->query($sql, [$change, $userId]);
    }
    
    public function incrementFollowing($userId, $change = 1)
    {
        $sql = "UPDATE users SET following_count = following_count + ? WHERE id = ?";
        $this->db->query($sql, [$change, $userId]);
    }
}