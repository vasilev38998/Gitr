<?php

// Start session
session_start();

// Include autoloader and helpers
require_once dirname(__DIR__) . '/../src/Localization.php';
require_once dirname(__DIR__) . '/../src/helpers.php';
require_once dirname(__DIR__) . '/../src/Auth.php';
require_once dirname(__DIR__) . '/../src/Database.php';

// Set response header to JSON
header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? '';
$response = [
    'success' => false,
    'message' => '',
    'data' => [],
];

try {
    if ($action === 'set') {
        $language = $_GET['language'] ?? '';
        
        if (empty($language)) {
            throw new \Exception(trans('errors.required_field'));
        }
        
        if (!is_language_supported($language)) {
            throw new \Exception(trans('errors.validation_failed'));
        }
        
        set_language($language);
        
        if (Auth::check()) {
            try {
                $db = Database::getInstance();
                $userId = Auth::id();
                $db->query(
                    "UPDATE users SET language = ? WHERE id = ?",
                    [$language, $userId]
                );
            } catch (\Exception $e) {
                error_log('Failed to save language preference to database: ' . $e->getMessage());
            }
        }
        
        $response['success'] = true;
        $response['message'] = trans('common.success');
        $response['data'] = [
            'language' => get_language(),
            'flag' => get_language_flag($language),
        ];
    } else if ($action === 'get') {
        $response['success'] = true;
        $response['message'] = trans('common.success');
        $response['data'] = [
            'language' => get_language(),
            'supported_languages' => get_supported_languages(),
        ];
    } else {
        throw new \Exception(trans('errors.invalid_credentials'));
    }
} catch (\Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
