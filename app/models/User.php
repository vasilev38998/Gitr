<?php

namespace App\Models;

use App\Config\Database;

class User
{
    private $db;
    private $table = 'users';

    private $id;
    private $username;
    private $email;
    private $password_hash;
    private $created_at;
    private $updated_at;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($username, $email, $password)
    {
        if (!$this->validateUsername($username)) {
            return ['success' => false, 'error' => 'Invalid username format'];
        }

        if (!$this->validateEmail($email)) {
            return ['success' => false, 'error' => 'Invalid email format'];
        }

        if (!$this->validatePassword($password)) {
            return ['success' => false, 'error' => 'Password must be at least 8 characters long'];
        }

        if ($this->userExists($username, $email)) {
            return ['success' => false, 'error' => 'Username or email already exists'];
        }

        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $created_at = date('Y-m-d H:i:s');

        $query = "INSERT INTO {$this->table} (username, email, password_hash, created_at, updated_at) 
                  VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'error' => 'Database error: ' . $this->db->error];
        }

        $stmt->bind_param('sssss', $username, $email, $password_hash, $created_at, $created_at);

        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'User registered successfully', 'user_id' => $this->db->insert_id];
        } else {
            $stmt->close();
            return ['success' => false, 'error' => 'Registration failed: ' . $this->db->error];
        }
    }

    public function findByUsername($username)
    {
        $query = "SELECT * FROM {$this->table} WHERE username = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return null;
        }

        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $stmt->close();
            return $user;
        }

        $stmt->close();
        return null;
    }

    public function findByEmail($email)
    {
        $query = "SELECT * FROM {$this->table} WHERE email = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return null;
        }

        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $stmt->close();
            return $user;
        }

        $stmt->close();
        return null;
    }

    public function findById($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return null;
        }

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $stmt->close();
            return $user;
        }

        $stmt->close();
        return null;
    }

    public function verifyPassword($plainPassword, $hash)
    {
        return password_verify($plainPassword, $hash);
    }

    private function validateUsername($username)
    {
        if (strlen($username) < 3 || strlen($username) > 100) {
            return false;
        }
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            return false;
        }
        return true;
    }

    private function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function validatePassword($password)
    {
        return strlen($password) >= 8;
    }

    private function userExists($username, $email)
    {
        $query = "SELECT id FROM {$this->table} WHERE username = ? OR email = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();

        return $exists;
    }
}
