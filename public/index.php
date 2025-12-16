<?php

declare(strict_types=1);

$root = dirname(__DIR__);

// Start session
session_start();

// Include autoloader and localization
$autoload = $root . '/vendor/autoload.php';
if (is_file($autoload)) {
    require $autoload;
} else {
    spl_autoload_register(static function (string $class) use ($root): void {
        $prefix = 'App\\';
        if (!str_starts_with($class, $prefix)) {
            return;
        }

        $relative = substr($class, strlen($prefix));
        $path = $root . '/src/' . str_replace('\\', '/', $relative) . '.php';

        if (is_file($path)) {
            require $path;
        }
    });
}

// Initialize localization
require_once $root . '/src/Localization.php';
require_once $root . '/src/helpers.php';

// Handle language parameter
if (isset($_GET['lang'])) {
    try {
        set_language($_GET['lang']);
        // Remove lang parameter from URL and redirect
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $query = $_GET;
        unset($query['lang']);
        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }
        header('Location: ' . $url);
        exit;
    } catch (\InvalidArgumentException $e) {
        // Invalid language, ignore
    }
}

$databaseConfig = require $root . '/config/database.php';

$db = null;
$dbError = null;

try {
    $db = new App\Database\Database($databaseConfig);
} catch (Throwable $e) {
    $dbError = $e;
}

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

$render = static function (string $template, array $params = []) use ($root): string {
    $templatePath = $root . '/templates/' . ltrim($template, '/');

    if (!is_file($templatePath)) {
        throw new RuntimeException(sprintf('Template not found: %s', $templatePath));
    }

    extract($params, EXTR_SKIP);

    ob_start();
    require $templatePath;

    return (string) ob_get_clean();
};

// Check if user is authenticated
$isAuthenticated = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);

try {
    switch ($path) {
        case '/':
        case '':
            // Home page
            $content = $render('home.php', [
                'db' => $db,
                'dbError' => $dbError,
            ]);

            echo $render('layout.php', [
                'title' => trans('feed.home'),
                'content' => $content,
            ]);
            break;

        case '/login':
            if ($isAuthenticated) {
                header('Location: /feed');
                exit;
            }
            // Redirect to login page
            header('Location: /login.php');
            exit;
            break;

        case '/register':
            if ($isAuthenticated) {
                header('Location: /feed');
                exit;
            }
            // Redirect to register page
            header('Location: /pages/register.php');
            exit;
            break;

        case '/feed':
            if (!$isAuthenticated) {
                header('Location: /login');
                exit;
            }
            // Include feed functionality in home page
            $content = $render('home.php', [
                'db' => $db,
                'dbError' => $dbError,
            ]);

            echo $render('layout.php', [
                'title' => trans('feed.feed'),
                'content' => $content,
            ]);
            break;

        case '/profile':
            if (!$isAuthenticated) {
                header('Location: /login');
                exit;
            }
            // Redirect to profile page
            header('Location: /profile.php');
            exit;
            break;

        case '/messages':
            if (!$isAuthenticated) {
                header('Location: /login');
                exit;
            }
            // Redirect to messages page
            header('Location: /messages.php');
            exit;
            break;

        case '/settings':
            if (!$isAuthenticated) {
                header('Location: /login');
                exit;
            }
            // Redirect to settings page
            header('Location: /settings.php');
            exit;
            break;

        case '/logout':
            // Handle logout
            session_destroy();
            header('Location: /');
            exit;
            break;

        default:
            // Handle 404
            http_response_code(404);
            $content = $render('404.php');

            echo $render('layout.php', [
                'title' => 'Not Found',
                'content' => $content,
            ]);
            break;
    }
} catch (Throwable $e) {
    http_response_code(500);

    if (ini_get('display_errors')) {
        echo '<pre>' . htmlspecialchars((string) $e, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</pre>';

        return;
    }

    echo 'Internal Server Error';
}
