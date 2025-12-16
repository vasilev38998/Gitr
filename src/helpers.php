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
