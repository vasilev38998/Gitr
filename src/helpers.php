<?php

/**
 * Translation helper functions
 */

use App\Localization;

/**
 * Global localization instance
 */
if (!isset($GLOBALS['localization'])) {
    $GLOBALS['localization'] = new Localization();
}

/**
 * Get translation
 *
 * @param string $key Dot-separated key (e.g., 'auth.login')
 * @param array $replacements Key-value pairs for string replacement
 * @return string
 */
function trans(string $key, array $replacements = []): string
{
    return $GLOBALS['localization']->translate($key, $replacements);
}

/**
 * Get translation or return key if not found
 *
 * @param string $key
 * @param array $replacements
 * @return string
 */
function translate(string $key, array $replacements = []): string
{
    return trans($key, $replacements);
}

/**
 * Get localization instance
 *
 * @return Localization
 */
function localization(): Localization
{
    return $GLOBALS['localization'];
}

/**
 * Get current language
 *
 * @return string
 */
function get_language(): string
{
    return localization()->getLanguage();
}

/**
 * Set language
 *
 * @param string $language
 * @return void
 */
function set_language(string $language): void
{
    localization()->setLanguage($language);
}

/**
 * Get all supported languages
 *
 * @return array
 */
function get_supported_languages(): array
{
    return localization()->getSupportedLanguages();
}

/**
 * Check if language exists
 *
 * @param string $language
 * @return bool
 */
function is_language_supported(string $language): bool
{
    return in_array($language, get_supported_languages());
}

/**
 * Get language flag emoji
 *
 * @param string $language
 * @return string
 */
function get_language_flag(string $language): string
{
    $flags = [
        'ru' => '🇷🇺',
        'en' => '🇬🇧',
    ];
    return $flags[$language] ?? '';
}

/**
 * Get language name
 *
 * @param string $language
 * @return string
 */
function get_language_name(string $language): string
{
    $names = [
        'ru' => 'Русский',
        'en' => 'English',
    ];
    return $names[$language] ?? $language;
}

/**
 * Generate CSRF token
 *
 * @return string
 */
function generate_csrf_token(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 *
 * @param string|null $token
 * @return bool
 */
function verify_csrf_token(?string $token): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Redirect to a URL
 *
 * @param string $url
 * @return void
 */
function redirect(string $url): void
{
    header("Location: $url");
    exit;
}