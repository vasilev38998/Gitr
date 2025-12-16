<?php

// Start session
session_start();

// Include autoloader and helpers
require_once dirname(__DIR__) . '/../src/Localization.php';
require_once dirname(__DIR__) . '/../src/helpers.php';

// Set response header to JSON
header('Content-Type: application/json; charset=utf-8');

$action = $_POST['action'] ?? '';
$response = [
    'success' => false,
    'message' => '',
    'errors' => [],
    'data' => [],
];

try {
    if ($action === 'register') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Validation
        if (empty($username)) {
            $response['errors']['username'] = trans('errors.required_field');
        }
        
        if (empty($email)) {
            $response['errors']['email'] = trans('errors.required_field');
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['errors']['email'] = trans('errors.invalid_email');
        }
        
        if (empty($password)) {
            $response['errors']['password'] = trans('errors.required_field');
        } else if (strlen($password) < 6) {
            $response['errors']['password'] = trans('errors.password_too_short');
        }
        
        if ($password !== $confirm_password) {
            $response['errors']['confirm_password'] = trans('errors.passwords_do_not_match');
        }
        
        if (!empty($response['errors'])) {
            $response['message'] = trans('errors.validation_failed');
            throw new \Exception($response['message']);
        }
        
        // Simulate database check
        $existing_users = ['john@example.com', 'jane@example.com'];
        
        if (in_array($email, $existing_users)) {
            $response['errors']['email'] = trans('errors.email_already_exists');
            throw new \Exception(trans('errors.email_already_exists'));
        }
        
        // Simulate successful registration
        $response['success'] = true;
        $response['message'] = trans('auth.sign_up');
        $response['data'] = [
            'user_id' => rand(1000, 9999),
            'username' => $username,
            'email' => $email,
        ];
        
    } else if ($action === 'login') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Validation
        if (empty($email)) {
            $response['errors']['email'] = trans('errors.required_field');
        }
        
        if (empty($password)) {
            $response['errors']['password'] = trans('errors.required_field');
        }
        
        if (!empty($response['errors'])) {
            $response['message'] = trans('errors.validation_failed');
            throw new \Exception($response['message']);
        }
        
        // Simulate successful login
        $response['success'] = true;
        $response['message'] = trans('auth.sign_in');
        $response['data'] = [
            'user_id' => rand(1000, 9999),
            'email' => $email,
            'token' => bin2hex(random_bytes(16)),
        ];
        
    } else {
        throw new \Exception(trans('errors.invalid_credentials'));
    }
    
} catch (\Exception $e) {
    $response['success'] = false;
    if (empty($response['message'])) {
        $response['message'] = $e->getMessage();
    }
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
