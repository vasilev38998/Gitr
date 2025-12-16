# Quick Start Guide - Gitr Social Network

This guide will help you get started with the Gitr social network application with full i18n support.

## Prerequisites

- PHP 7.4 or higher
- Web server (Apache, Nginx, or built-in PHP server)
- MySQL 5.7 or higher (for database features)

## Installation & Setup

### 1. Clone the Repository

```bash
git clone <repository-url>
cd gitr
```

### 2. Run with PHP Built-in Server

```bash
cd /path/to/gitr
php -S localhost:8000 -t public
```

Then open your browser and navigate to: `http://localhost:8000`

### 3. (Optional) Database Setup

If you want to use the database features:

```bash
mysql -u root -p < database/migrations/001_initial_schema.sql
```

## File Structure Overview

```
gitr/
├── locales/                 # Translation files
│   ├── ru.php              # Russian translations
│   └── en.php              # English translations
├── src/
│   ├── Localization.php     # Core localization class
│   └── helpers.php          # Helper functions
├── public/
│   ├── index.php            # Home page
│   ├── auth.php             # Login/Register
│   ├── profile.php          # User profile
│   ├── messages.php         # Messaging
│   ├── settings.php         # Settings & language selection
│   └── _api/                # API endpoints
├── database/
│   └── migrations/          # Database schemas
├── README.md                # Main documentation
└── LOCALIZATION.md          # Detailed i18n documentation
```

## Key Features

### 1. **Multi-Language Support**
- Russian (Русский) 🇷🇺
- English (English) 🇬🇧

Switch languages using the flag buttons in the top-right corner of any page.

### 2. **Automatic Language Detection**
The app automatically detects your language preference from:
1. Browser's Accept-Language header
2. Saved cookie (30-day persistence)
3. Session data
4. Default: English

### 3. **Fully Localized Pages**
- **Home Feed** (`/index.php`) - Browse posts, create new posts
- **Auth** (`/auth.php`) - Login and register with error handling
- **Profile** (`/profile.php`) - View and edit user profile
- **Messages** (`/messages.php`) - Send and receive messages
- **Settings** (`/settings.php`) - Manage preferences and language

## Using the Localization System

### In Your PHP Code

```php
<?php
require_once dirname(__DIR__) . '/src/Localization.php';
require_once dirname(__DIR__) . '/src/helpers.php';

// Get a translation
echo trans('auth.login');  // "Вход" or "Login"

// Get translation with replacements
echo trans('likes.likes_count', ['count' => 5]);  // "Нравится 5" or "Liked by 5"

// Change language
set_language('ru');

// Check if language is supported
if (is_language_supported('ru')) {
    echo "Russian is supported!";
}

// Get all supported languages
$langs = get_supported_languages();  // ['ru', 'en']
```

### In HTML Templates

```html
<h1><?php echo trans('feed.home'); ?></h1>
<button><?php echo trans('posts.create_post'); ?></button>
<label><?php echo trans('auth.email'); ?></label>
```

### Language Switcher HTML

```html
<div class="language-switcher">
    <?php foreach (get_supported_languages() as $lang): ?>
        <a href="?lang=<?php echo $lang; ?>" class="<?php echo get_language() === $lang ? 'active' : ''; ?>">
            <?php echo get_language_flag($lang); ?> <?php echo strtoupper($lang); ?>
        </a>
    <?php endforeach; ?>
</div>
```

## Available Translation Keys

### Authentication
- `trans('auth.login')` - Login
- `trans('auth.register')` - Register
- `trans('auth.email')` - Email
- `trans('auth.password')` - Password

### Profile
- `trans('profile.profile')` - Profile
- `trans('profile.edit_profile')` - Edit Profile
- `trans('profile.followers')` - Followers
- `trans('profile.following')` - Following

### Feed
- `trans('feed.home')` - Home
- `trans('feed.whats_on_your_mind')` - What's on your mind?
- `trans('feed.share')` - Share

### Posts
- `trans('posts.create_post')` - Create Post
- `trans('posts.edit')` - Edit
- `trans('posts.delete')` - Delete

### Errors
- `trans('errors.required_field')` - This field is required
- `trans('errors.invalid_email')` - Invalid email format
- `trans('errors.password_too_short')` - Password too short

See [LOCALIZATION.md](LOCALIZATION.md) for the complete list of available keys.

## API Endpoints

### Language API

**Get Current Language**
```bash
curl http://localhost:8000/_api/language.php?action=get
```

Response:
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "language": "ru",
        "supported_languages": ["ru", "en"]
    }
}
```

**Set Language**
```bash
curl "http://localhost:8000/_api/language.php?action=set&language=ru"
```

### Authentication API

**Register User**
```bash
curl -X POST http://localhost:8000/_api/auth.php \
  -d "action=register&username=john&email=john@example.com&password=password123&confirm_password=password123"
```

**Login User**
```bash
curl -X POST http://localhost:8000/_api/auth.php \
  -d "action=login&email=john@example.com&password=password123"
```

## Adding Translations

### Editing Existing Translations

Edit the appropriate language file in `locales/`:
- `/locales/ru.php` - Russian translations
- `/locales/en.php` - English translations

### Adding New Keys

1. Open the language file
2. Add a new key in the appropriate section:

```php
'posts' => [
    'create_post' => 'Create Post',
    'my_new_key' => 'My translation',
]
```

3. Use it in your code:
```php
echo trans('posts.my_new_key');
```

## Adding a New Language

1. Create a new file `/locales/[language-code].php`:

```php
<?php
return [
    'auth' => [
        'login' => 'Your translation',
        // ... other keys
    ],
    // ... other sections
];
```

2. Update `/src/Localization.php` - Modify the `SUPPORTED_LANGUAGES` constant:

```php
private const SUPPORTED_LANGUAGES = ['ru', 'en', 'de'];
```

3. Update `/src/helpers.php` - Add language flag and name:

```php
function get_language_flag(string $language): string {
    $flags = [
        'ru' => '🇷🇺',
        'en' => '🇬🇧',
        'de' => '🇩🇪',
    ];
    return $flags[$language] ?? '';
}

function get_language_name(string $language): string {
    $names = [
        'ru' => 'Русский',
        'en' => 'English',
        'de' => 'Deutsch',
    ];
    return $names[$language] ?? $language;
}
```

## Testing the Application

### Test Language Switching
1. Open `http://localhost:8000`
2. Click the language flag in the top-right
3. Verify the interface changes to the selected language
4. Refresh the page - language preference should be saved

### Test Different Pages
- **Home**: `http://localhost:8000`
- **Login/Register**: `http://localhost:8000/auth.php`
- **Profile**: `http://localhost:8000/profile.php`
- **Messages**: `http://localhost:8000/messages.php`
- **Settings**: `http://localhost:8000/settings.php`

### Test API
```bash
# Set language to Russian
curl "http://localhost:8000/_api/language.php?action=set&language=ru"

# Get language info
curl "http://localhost:8000/_api/language.php?action=get"
```

## Troubleshooting

### Language Not Changing
1. Check that sessions are enabled (PHP)
2. Clear browser cookies
3. Check browser console for JavaScript errors
4. Verify the language code is valid (ru, en)

### Translation Key Not Found
1. Check the key spelling and section
2. Look in `/locales/[language].php` to find the exact key
3. Use `trans('key.subkey')` format with dot notation

### PHP Errors
1. Ensure PHP 7.4+ is installed
2. Check file permissions on locales and src directories
3. Verify session directory is writable

## Next Steps

1. Read [LOCALIZATION.md](LOCALIZATION.md) for comprehensive documentation
2. Explore the example pages to understand the structure
3. Modify translations in `/locales/` files
4. Add database integration using the schema in `/database/migrations/`
5. Create your own pages following the existing patterns

## Support

For more detailed information:
- See [LOCALIZATION.md](LOCALIZATION.md) for i18n system details
- See [README.md](README.md) for project overview
- Check `/public/` folder for example implementations

## License

This project is provided as-is for educational and development purposes.
