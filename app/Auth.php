<?php

namespace App;

use App\Models\User;

class Auth
{
    private $user;
    private const SESSION_USER_KEY = 'authenticated_user';
    private const CSRF_TOKEN_KEY = 'csrf_token';

    public function __construct()
    {
        $this->user = new User();
        $this->startSession();
    }

    private function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function register($username, $email, $password, $password_confirm)
    {
        if ($password !== $password_confirm) {
            return ['success' => false, 'error' => 'Passwords do not match'];
        }

        $result = $this->user->create($username, $email, $password);
        return $result;
    }

    public function login($username, $password)
    {
        $user = $this->user->findByUsername($username);

        if (!$user) {
            return ['success' => false, 'error' => 'Invalid username or password'];
        }

        if (!$this->user->verifyPassword($password, $user['password_hash'])) {
            return ['success' => false, 'error' => 'Invalid username or password'];
        }

        $this->setAuthenticatedUser($user);
        return ['success' => true, 'message' => 'Login successful'];
    }

    public function logout()
    {
        unset($_SESSION[self::SESSION_USER_KEY]);
        session_destroy();
        return true;
    }

    public function isAuthenticated()
    {
        return isset($_SESSION[self::SESSION_USER_KEY]);
    }

    public function getAuthenticatedUser()
    {
        if ($this->isAuthenticated()) {
            return $_SESSION[self::SESSION_USER_KEY];
        }
        return null;
    }

    public function getAuthenticatedUserId()
    {
        $user = $this->getAuthenticatedUser();
        return $user ? $user['id'] : null;
    }

    public function requireAuthentication()
    {
        if (!$this->isAuthenticated()) {
            header('Location: /login.php');
            exit();
        }
    }

    private function setAuthenticatedUser($user)
    {
        unset($user['password_hash']);
        $_SESSION[self::SESSION_USER_KEY] = $user;
    }

    public function generateCsrfToken()
    {
        if (!isset($_SESSION[self::CSRF_TOKEN_KEY])) {
            $_SESSION[self::CSRF_TOKEN_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::CSRF_TOKEN_KEY];
    }

    public function verifyCsrfToken($token)
    {
        if (!isset($_SESSION[self::CSRF_TOKEN_KEY])) {
            return false;
        }
        return hash_equals($_SESSION[self::CSRF_TOKEN_KEY], $token);
    }

    public function validateCsrfToken($token)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return true;
        }

        if (!$this->verifyCsrfToken($token)) {
            return false;
        }

        return true;
    }
}
