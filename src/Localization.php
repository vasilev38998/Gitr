<?php

namespace App;

/**
 * Localization Manager
 * Handles language management and translation functionality
 */
class Localization
{
    /**
     * Default language
     */
    private const DEFAULT_LANGUAGE = 'ru';

    /**
     * Supported languages
     */
    private const SUPPORTED_LANGUAGES = ['ru', 'en'];

    /**
     * Current language
     */
    private string $currentLanguage;

    /**
     * Translation cache
     */
    private array $translations = [];

    /**
     * Session key for language storage
     */
    private const SESSION_KEY = 'user_language';

    /**
     * Cookie key for language storage
     */
    private const COOKIE_KEY = 'language';

    /**
     * Cookie expiration time (30 days)
     */
    private const COOKIE_LIFETIME = 2592000;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->currentLanguage = $this->loadLanguage();
        $this->loadTranslations();
    }

    /**
     * Load language from session/cookie/default
     *
     * @return string
     */
    private function loadLanguage(): string
    {
        // Check session first
        if (isset($_SESSION[self::SESSION_KEY])) {
            $language = $_SESSION[self::SESSION_KEY];
            if (in_array($language, self::SUPPORTED_LANGUAGES)) {
                return $language;
            }
        }

        // Check cookie
        if (isset($_COOKIE[self::COOKIE_KEY])) {
            $language = $_COOKIE[self::COOKIE_KEY];
            if (in_array($language, self::SUPPORTED_LANGUAGES)) {
                $_SESSION[self::SESSION_KEY] = $language;
                return $language;
            }
        }

        // Check Accept-Language header
        $language = $this->detectLanguageFromHeader();
        if (in_array($language, self::SUPPORTED_LANGUAGES)) {
            $_SESSION[self::SESSION_KEY] = $language;
            return $language;
        }

        // Use default
        return self::DEFAULT_LANGUAGE;
    }

    /**
     * Detect language from Accept-Language header
     *
     * @return string
     */
    private function detectLanguageFromHeader(): string
    {
        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return self::DEFAULT_LANGUAGE;
        }

        $languages = [];
        $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'];

        // Parse Accept-Language header
        $parts = explode(',', $acceptLanguage);
        foreach ($parts as $part) {
            $part = trim($part);
            if (preg_match('/^([a-z]{2})(?:-[a-zA-Z]{2})?(?:;q=([0-9.]+))?$/i', $part, $matches)) {
                $lang = strtolower($matches[1]);
                $quality = isset($matches[2]) ? floatval($matches[2]) : 1.0;
                $languages[$lang] = $quality;
            }
        }

        // Sort by quality value
        arsort($languages);

        foreach ($languages as $lang => $quality) {
            if (in_array($lang, self::SUPPORTED_LANGUAGES)) {
                return $lang;
            }
        }

        return self::DEFAULT_LANGUAGE;
    }

    /**
     * Load translations for the current language
     *
     * @return void
     */
    private function loadTranslations(): void
    {
        $filePath = $this->getTranslationFilePath($this->currentLanguage);
        if (file_exists($filePath)) {
            $this->translations = require $filePath;
        }
    }

    /**
     * Get path to translation file
     *
     * @param string $language
     * @return string
     */
    private function getTranslationFilePath(string $language): string
    {
        return dirname(__DIR__) . '/locales/' . $language . '.php';
    }

    /**
     * Set current language
     *
     * @param string $language
     * @return void
     * @throws \InvalidArgumentException
     */
    public function setLanguage(string $language): void
    {
        if (!in_array($language, self::SUPPORTED_LANGUAGES)) {
            throw new \InvalidArgumentException("Language '{$language}' is not supported");
        }

        $this->currentLanguage = $language;
        $_SESSION[self::SESSION_KEY] = $language;
        setcookie(
            self::COOKIE_KEY,
            $language,
            time() + self::COOKIE_LIFETIME,
            '/',
            '',
            false,
            true
        );
        $this->loadTranslations();
    }

    /**
     * Get current language
     *
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->currentLanguage;
    }

    /**
     * Get all supported languages
     *
     * @return array
     */
    public function getSupportedLanguages(): array
    {
        return self::SUPPORTED_LANGUAGES;
    }

    /**
     * Translate a key
     *
     * @param string $key Dot-separated key (e.g., 'auth.login')
     * @param array $replacements Key-value pairs for string replacement
     * @return string
     */
    public function translate(string $key, array $replacements = []): string
    {
        $keys = explode('.', $key);
        $translation = $this->translations;

        foreach ($keys as $k) {
            if (isset($translation[$k])) {
                $translation = $translation[$k];
            } else {
                // Return the key if translation not found
                return $key;
            }
        }

        $result = (string) $translation;

        // Replace placeholders
        foreach ($replacements as $placeholder => $value) {
            $result = str_replace(':' . $placeholder, (string) $value, $result);
        }

        return $result;
    }

    /**
     * Alias for translate()
     *
     * @param string $key
     * @param array $replacements
     * @return string
     */
    public function __invoke(string $key, array $replacements = []): string
    {
        return $this->translate($key, $replacements);
    }

    /**
     * Check if a translation key exists
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        $keys = explode('.', $key);
        $translation = $this->translations;

        foreach ($keys as $k) {
            if (isset($translation[$k])) {
                $translation = $translation[$k];
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Get all translations
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->translations;
    }

    /**
     * Get translations for a specific section
     *
     * @param string $section
     * @return array
     */
    public function getSection(string $section): array
    {
        return $this->translations[$section] ?? [];
    }
}
