<?php

declare(strict_types=1);

$root = dirname(__DIR__);

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

try {
    if ($path === '/' || $path === '') {
        $content = $render('home.php', [
            'db' => $db,
            'dbError' => $dbError,
        ]);

        echo $render('layout.php', [
            'title' => 'Social Network',
            'content' => $content,
        ]);

        return;
    }

    http_response_code(404);
    $content = $render('404.php');

    echo $render('layout.php', [
        'title' => 'Not Found',
        'content' => $content,
    ]);
} catch (Throwable $e) {
    http_response_code(500);

    if (ini_get('display_errors')) {
        echo '<pre>' . htmlspecialchars((string) $e, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</pre>';

        return;
    }

    echo 'Internal Server Error';
}
