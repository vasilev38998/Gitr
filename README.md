# Gitr - Social Network with i18n Support

Gitr is a social network application with complete internationalization (i18n) support for Russian and English languages.

## Features

- 🌍 **Multi-language Support** - Full support for Russian (RU) and English (EN)
- 🎨 **Modern UI** - Clean and responsive user interface
- 👤 **User Profiles** - User profiles with customizable information
- 📝 **Posts & Comments** - Create, edit, and delete posts with comments
- 💬 **Messaging** - Direct messaging between users
- 🔔 **Notifications** - Real-time notifications for user interactions
- ⚙️ **Settings** - Comprehensive user settings including language preferences

## Project Structure

```
Gitr/
├── locales/                  # Translation files
│   ├── ru.php               # Russian translations
│   └── en.php               # English translations
├── src/
│   ├── Localization.php      # Localization class
│   └── helpers.php           # Helper functions for i18n
├── public/
│   ├── index.php             # Home page
│   ├── auth.php              # Login/Register page
│   ├── profile.php           # User profile page
│   ├── messages.php          # Messaging page
│   ├── settings.php          # Settings page
│   └── _api/
│       ├── language.php      # Language API endpoint
│       └── auth.php          # Authentication API
└── LOCALIZATION.md           # Complete i18n documentation
```

## Getting Started

### Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd gitr
```

2. Create a `.gitignore` file (if not exists)

3. Install dependencies (if using Composer or package managers)

### Running the Application

1. Start a PHP development server:
```bash
php -S localhost:8000 -t public
```

2. Open your browser and navigate to:
```
http://localhost:8000
```

### Language Switching

The application automatically detects your language based on:
1. Browser's Accept-Language header
2. Stored cookie preference
3. Session language selection

You can manually switch languages using the language switcher available in the top-right corner of each page.

## Localization System

The application uses a robust i18n system with the following capabilities:

- **Multi-language files** - Separate translation files for each language
- **Dot-notation keys** - Easy-to-use translation key system (e.g., `trans('auth.login')`)
- **Variable replacement** - Support for dynamic content in translations
- **Session & Cookie storage** - Persistent language preference
- **Browser detection** - Automatic language selection based on browser settings

For detailed information about the localization system, see [LOCALIZATION.md](LOCALIZATION.md)

## Available Pages

- **Home** (`/index.php`) - Main feed with posts
- **Login/Register** (`/auth.php`) - User authentication
- **Profile** (`/profile.php`) - User profile and edit options
- **Messages** (`/messages.php`) - Direct messaging interface
- **Settings** (`/settings.php`) - User settings and preferences

## Translation Examples

### In PHP Templates
```php
<h1><?php echo trans('feed.home'); ?></h1>
<button><?php echo trans('posts.create_post'); ?></button>
```

### With Variable Replacement
```php
<?php echo trans('likes.likes_count', ['count' => 42]); ?>
<!-- Output: "Liked by 42" or "Нравится 42" depending on language -->
```

### API Integration
```javascript
fetch('/_api/language.php?action=set&language=ru')
    .then(response => response.json())
    .then(data => console.log(data.message));
```

## API Endpoints

### Language Management
- `GET /_api/language.php?action=get` - Get current language info
- `GET /_api/language.php?action=set&language=ru` - Set language

### Authentication
- `POST /_api/auth.php` - User registration and login
  - Parameters: `action`, `email`, `password`, `username` (for registration)

## Translation Keys

The system includes comprehensive translations for:

- **Auth** - Login, registration, password reset
- **Profile** - User profile information and editing
- **Feed** - Timeline and post creation
- **Posts** - Post management operations
- **Comments** - Comment functionality
- **Likes** - Like interactions
- **Errors** - Error messages and validation
- **Navigation** - Menu items
- **Messages** - Messaging interface
- **Settings** - User settings
- **Search** - Search functionality
- **Notifications** - User notifications

## Adding New Languages

To add support for a new language:

1. Create a new file in `locales/[language_code].php`
2. Add translations following the existing structure
3. Update the `Localization.php` class to include the new language in `SUPPORTED_LANGUAGES`
4. Add language flag and name in `src/helpers.php`

See [LOCALIZATION.md](LOCALIZATION.md) for detailed instructions.

## Browser Support

- Chrome/Chromium (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## License

This project is provided as-is for educational and development purposes.

## Support

For issues, questions, or contributions, please refer to the [LOCALIZATION.md](LOCALIZATION.md) documentation or create an issue in the repository.
