# Implementation Guide - Localizing Your Pages

This guide shows how to implement the localization system in your own pages and integrate it with your application logic.

## Table of Contents

1. [Basic Page Setup](#basic-page-setup)
2. [Form Localization](#form-localization)
3. [Error Message Handling](#error-message-handling)
4. [API Response Localization](#api-response-localization)
5. [Database Integration](#database-integration)
6. [Advanced Features](#advanced-features)

## Basic Page Setup

### Step 1: Include Required Files

Every page that needs localization must start with:

```php
<?php
session_start();

// Include localization system
require_once dirname(__DIR__) . '/src/Localization.php';
require_once dirname(__DIR__) . '/src/helpers.php';

// Your page logic here
?>
```

### Step 2: Set HTML Language Attribute

In your HTML document:

```html
<!DOCTYPE html>
<html lang="<?php echo get_language(); ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo trans('feed.home'); ?></title>
</head>
<body>
    <!-- Your content -->
</body>
</html>
```

### Step 3: Add Language Switcher

Include this in your header/navigation:

```html
<div class="language-switcher">
    <?php foreach (get_supported_languages() as $lang): ?>
        <a href="?lang=<?php echo $lang; ?>" 
           class="<?php echo get_language() === $lang ? 'active' : ''; ?>">
            <?php echo get_language_flag($lang); ?>
            <?php echo get_language_name($lang); ?>
        </a>
    <?php endforeach; ?>
</div>
```

## Form Localization

### Localizing Form Labels and Placeholders

```html
<form method="POST">
    <div class="form-group">
        <label><?php echo trans('auth.email'); ?></label>
        <input type="email" 
               name="email" 
               placeholder="<?php echo trans('auth.email'); ?>"
               required>
    </div>

    <div class="form-group">
        <label><?php echo trans('auth.password'); ?></label>
        <input type="password" 
               name="password" 
               placeholder="<?php echo trans('auth.password'); ?>"
               required>
    </div>

    <button type="submit">
        <?php echo trans('auth.sign_in'); ?>
    </button>
</form>
```

### Displaying Form Validation Errors

```php
<?php
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    
    if (empty($email)) {
        $errors['email'] = trans('errors.required_field');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = trans('errors.invalid_email');
    }
}
?>

<!-- In HTML -->
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $field => $message): ?>
            <div><?php echo htmlspecialchars($message); ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
```

## Error Message Handling

### Server-Side Error Messages

```php
<?php
try {
    // Validate input
    if (empty($email)) {
        throw new \Exception(trans('errors.required_field'));
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new \Exception(trans('errors.invalid_email'));
    }
    
    // Process request
    // ...
    
} catch (\Exception $e) {
    $error = $e->getMessage();
    // Display error using trans() for localized messages
}
?>
```

### Client-Side Error Display

```html
<?php if (!empty($error)): ?>
    <div class="alert alert-danger" role="alert">
        <strong><?php echo trans('errors.error'); ?>:</strong>
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>
```

## API Response Localization

### Sending Localized JSON Responses

```php
<?php
// File: /public/_api/create-post.php

session_start();

require_once dirname(__DIR__) . '/src/Localization.php';
require_once dirname(__DIR__) . '/src/helpers.php';

header('Content-Type: application/json; charset=utf-8');

$response = [
    'success' => false,
    'message' => '',
    'errors' => [],
    'data' => [],
];

try {
    // Validate post content
    $content = $_POST['content'] ?? '';
    
    if (empty($content)) {
        throw new \Exception(trans('errors.required_field'));
    }
    
    if (strlen($content) > 5000) {
        throw new \Exception(trans('posts.post_content')); // Custom message
    }
    
    // Simulate database save
    $post_id = rand(1000, 9999);
    
    $response['success'] = true;
    $response['message'] = trans('posts.post_created');
    $response['data'] = [
        'post_id' => $post_id,
        'content' => $content,
        'created_at' => date('Y-m-d H:i:s'),
    ];
    
} catch (\Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>
```

### Consuming API with Localized Messages

```javascript
async function createPost(content) {
    const formData = new FormData();
    formData.append('content', content);
    
    try {
        const response = await fetch('/_api/create-post.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Show localized success message
            showNotification(data.message, 'success');
            // data.message is already in the user's language
        } else {
            // Show localized error message
            showError(data.message);
            // data.message is already in the user's language
        }
    } catch (error) {
        showError('Network error');
    }
}
```

## Database Integration

### Storing User Language Preference

When a user changes their language, store it in the database:

```php
<?php
// Assume we have a PDO connection: $pdo

if ($_POST['action'] === 'change-language') {
    $new_language = $_POST['language'];
    
    if (!is_language_supported($new_language)) {
        throw new \Exception(trans('errors.validation_failed'));
    }
    
    // Update database
    $stmt = $pdo->prepare('UPDATE users SET language = ? WHERE id = ?');
    $stmt->execute([$new_language, $user_id]);
    
    // Update session
    set_language($new_language);
    
    $response['success'] = true;
    $response['message'] = trans('common.success');
}
?>
```

### Loading User's Language on Login

```php
<?php
// After successful login, load user's language preference

$stmt = $pdo->prepare('SELECT language FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && is_language_supported($user['language'])) {
    set_language($user['language']);
}
?>
```

### Storing Localized Data

For content that needs translation (posts, comments):

```php
<?php
// Create a post (no translation needed for user content)
$stmt = $pdo->prepare(
    'INSERT INTO posts (user_id, content, language) VALUES (?, ?, ?)'
);
$stmt->execute([$user_id, $content, get_language()]);

// But UI strings are localized server-side
$response['message'] = trans('posts.post_created');
?>
```

## Advanced Features

### Dynamic Translations from Database

If you want to manage translations in the database:

```php
<?php
function get_dynamic_translation($key, $replacements = []) {
    global $pdo;
    
    $keys = explode('.', $key);
    $section = $keys[0];
    $key_name = $keys[1] ?? '';
    
    $stmt = $pdo->prepare(
        'SELECT value FROM translations WHERE language = ? AND section = ? AND key_path = ?'
    );
    $stmt->execute([get_language(), $section, $key_name]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $translation = $result['value'] ?? $key;
    
    // Replace variables
    foreach ($replacements as $placeholder => $value) {
        $translation = str_replace(':' . $placeholder, $value, $translation);
    }
    
    return $translation;
}

// Usage
echo get_dynamic_translation('posts.create_post');
?>
```

### Pluralization Support

Add pluralization to your translations:

```php
<?php
function trans_plural($key, $count, $replacements = []) {
    $translation = trans($key, $replacements);
    
    // Simple English pluralization
    if (get_language() === 'en' && $count !== 1) {
        // Add 's' for simple pluralization
        // More complex logic would be needed for real implementation
    }
    
    // Russian pluralization (more complex)
    if (get_language() === 'ru') {
        // Russian has different rules for plural forms
        // This would require more sophisticated logic
    }
    
    return $translation;
}

// Usage
echo trans_plural('notifications.new_like', 5);
?>
```

### Language-Specific Date/Time Formatting

```php
<?php
function format_date_localized($date, $format = 'short') {
    $language = get_language();
    
    $timestamp = strtotime($date);
    
    if ($language === 'ru') {
        $months_ru = [
            'January' => 'Января',
            'February' => 'Февраля',
            // ... etc
        ];
        // Format Russian date
        return date('d F Y', $timestamp); // Use russian months
    }
    
    // English format
    if ($format === 'short') {
        return date('M d, Y', $timestamp);
    }
    return date('F d, Y', $timestamp);
}

// Usage
echo format_date_localized($post['created_at']);
?>
```

### Real-Time Language Switching with JavaScript

```javascript
class LocalizationManager {
    constructor() {
        this.currentLanguage = document.documentElement.lang;
        this.setupListeners();
    }
    
    setupListeners() {
        document.querySelectorAll('.language-switcher a').forEach(link => {
            link.addEventListener('click', (e) => {
                const language = new URL(link.href).searchParams.get('lang');
                this.changeLanguage(language);
            });
        });
    }
    
    async changeLanguage(language) {
        try {
            const response = await fetch(`/_api/language.php?action=set&language=${language}`);
            const data = await response.json();
            
            if (data.success) {
                // Reload page with new language
                window.location.href = window.location.pathname + '?lang=' + language;
            }
        } catch (error) {
            console.error('Failed to change language:', error);
        }
    }
    
    getCurrentLanguage() {
        return this.currentLanguage;
    }
}

// Initialize
const localization = new LocalizationManager();
```

## Best Practices Checklist

- ✅ Always include the localization files at the start of your PHP script
- ✅ Use `trans()` helper function instead of direct array access
- ✅ Use dot notation for keys: `trans('section.key')`
- ✅ Localize all user-facing text, including error messages
- ✅ Use placeholders for dynamic content: `:variable`
- ✅ Always set the `lang` attribute on the HTML element
- ✅ Include a language switcher on every page
- ✅ Return localized messages from API endpoints
- ✅ Store user's language preference in the database
- ✅ Test all pages in both languages
- ✅ Handle missing translations gracefully (return the key)
- ✅ Use `htmlspecialchars()` when displaying user input

## Common Patterns

### Pattern 1: Login Page
See `/public/auth.php` for a complete implementation

### Pattern 2: User Settings with Language Selection
See `/public/settings.php` for a complete implementation

### Pattern 3: API Endpoint with Localized Responses
See `/public/_api/auth.php` for a complete implementation

### Pattern 4: Main Feed with Interactions
See `/public/index.php` for a complete implementation

## Troubleshooting Implementation

### Issue: Translations not showing
- Check that the key exists in `/locales/[language].php`
- Verify key spelling and case sensitivity
- Use correct dot notation: `'auth.login'` not `'auth:login'`

### Issue: Language not persisting
- Check that `session_start()` is called
- Verify cookies are enabled in browser
- Check that `/src/helpers.php` is properly included

### Issue: API responses in wrong language
- Ensure `session_start()` is called before including helpers
- Verify the language is being set correctly
- Check Accept-Language header handling

### Issue: HTML layout breaks with long translations
- Use flexible CSS (flexbox, grid)
- Avoid hardcoded text widths
- Test with both long (Russian) and short (English) text

## Next Steps

1. Review the example implementations in `/public/`
2. Create your own pages following the patterns
3. Add your specific translation keys to `/locales/`
4. Integrate with your database
5. Test thoroughly in both languages

For more information, see [LOCALIZATION.md](LOCALIZATION.md) and [QUICKSTART.md](QUICKSTART.md)
