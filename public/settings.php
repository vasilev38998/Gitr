<?php

// Start session
session_start();

// Include autoloader and helpers
require_once dirname(__DIR__) . '/src/Localization.php';
require_once dirname(__DIR__) . '/src/helpers.php';
require_once dirname(__DIR__) . '/src/Auth.php';
require_once dirname(__DIR__) . '/src/Database.php';

$message = '';

// Handle language change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['language'])) {
    $language = $_POST['language'];
    if (is_language_supported($language)) {
        set_language($language);
        
        if (Auth::check()) {
            try {
                $db = Database::getInstance();
                $userId = Auth::id();
                $db->query(
                    "UPDATE users SET language = ? WHERE id = ?",
                    [$language, $userId]
                );
            } catch (Exception $e) {
                error_log('Failed to save language preference to database: ' . $e->getMessage());
            }
        }
        
        $message = trans('common.success');
    }
}

?>
<!DOCTYPE html>
<html lang="<?php echo get_language(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo trans('settings.settings'); ?> - Gitr</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #1da1f2;
        }
        
        nav {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        
        nav a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }
        
        nav a:hover {
            color: #1da1f2;
        }
        
        .language-switcher {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .language-switcher a {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            border: 1px solid #e0e0e0;
            color: #333;
            cursor: pointer;
        }
        
        .language-switcher a.active {
            background-color: #1da1f2;
            color: white;
            border-color: #1da1f2;
        }
        
        .language-switcher a:hover {
            background-color: #1da1f2;
            color: white;
            border-color: #1da1f2;
        }
        
        .settings-grid {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 20px;
        }
        
        .sidebar {
            background-color: white;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            overflow: hidden;
        }
        
        .sidebar a {
            display: block;
            padding: 15px;
            text-decoration: none;
            color: #333;
            border-bottom: 1px solid #e0e0e0;
            transition: background-color 0.3s;
        }
        
        .sidebar a:last-child {
            border-bottom: none;
        }
        
        .sidebar a:hover,
        .sidebar a.active {
            background-color: #f5f5f5;
            color: #1da1f2;
        }
        
        .card {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        
        h2 {
            margin-bottom: 20px;
            font-size: 20px;
        }
        
        .section {
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .section h3 {
            font-size: 16px;
            margin-bottom: 15px;
            color: #333;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        select,
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            font-size: 14px;
            font-family: inherit;
        }
        
        select:focus,
        input:focus {
            outline: none;
            border-color: #1da1f2;
            box-shadow: 0 0 0 3px rgba(29, 161, 242, 0.1);
        }
        
        select {
            appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="black" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 10px center;
            padding-right: 40px;
        }
        
        button {
            background-color: #1da1f2;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 24px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        button:hover {
            background-color: #1a8cd8;
        }
        
        button.danger {
            background-color: #e74c3c;
        }
        
        button.danger:hover {
            background-color: #c0392b;
        }
        
        .success {
            background-color: #efe;
            border: 1px solid #cfc;
            color: #3c3;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .language-option {
            display: flex;
            align-items: center;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .language-option:hover {
            background-color: #f5f5f5;
        }

        .language-option input {
            width: auto;
            margin-right: 10px;
        }

        .language-option label {
            margin: 0;
            cursor: pointer;
            display: flex;
            align-items: center;
            width: 100%;
        }

        @media (max-width: 600px) {
            .settings-grid {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                display: flex;
                flex-wrap: wrap;
            }
            
            .sidebar a {
                flex: 1;
                border: 1px solid #e0e0e0;
                border-right: none;
            }
            
            .sidebar a:nth-child(2n) {
                border-right: 1px solid #e0e0e0;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Gitr</div>
        <nav>
            <a href="/"><?php echo trans('feed.home'); ?></a>
            <a href="/profile.php"><?php echo trans('profile.profile'); ?></a>
            <a href="/messages.php"><?php echo trans('messages.messages'); ?></a>
            <a href="/settings.php"><?php echo trans('settings.settings'); ?></a>
        </nav>
        <div class="language-switcher">
            <?php foreach (get_supported_languages() as $lang): ?>
                <a href="?lang=<?php echo $lang; ?>" class="<?php echo get_language() === $lang ? 'active' : ''; ?>">
                    <?php echo get_language_flag($lang); ?> <?php echo strtoupper($lang); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </header>

    <div class="container">
        <div class="settings-grid">
            <!-- Sidebar -->
            <div class="sidebar">
                <a href="#" class="active"><?php echo trans('settings.account_settings'); ?></a>
                <a href="#"><?php echo trans('settings.privacy_settings'); ?></a>
                <a href="#"><?php echo trans('settings.notification_settings'); ?></a>
            </div>

            <!-- Main Content -->
            <div>
                <div class="card">
                    <h2><?php echo trans('settings.account_settings'); ?></h2>
                    
                    <?php if (!empty($message)): ?>
                        <div class="success">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Language Settings -->
                    <div class="section">
                        <h3><?php echo trans('settings.language'); ?></h3>
                        <form method="POST">
                            <div class="form-group">
                                <label><?php echo trans('settings.language'); ?></label>
                                <select name="language" onchange="this.form.submit()">
                                    <?php foreach (get_supported_languages() as $lang): ?>
                                        <option value="<?php echo $lang; ?>" <?php echo get_language() === $lang ? 'selected' : ''; ?>>
                                            <?php echo get_language_flag($lang); ?> <?php echo get_language_name($lang); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </form>
                    </div>

                    <!-- Theme Settings -->
                    <div class="section">
                        <h3><?php echo trans('settings.theme'); ?></h3>
                        <form method="POST">
                            <div class="form-group">
                                <div class="language-option">
                                    <input type="radio" id="light" name="theme" value="light" checked>
                                    <label for="light"><?php echo trans('common.info'); ?> - <?php echo trans('settings.theme'); ?></label>
                                </div>
                                <div class="language-option">
                                    <input type="radio" id="dark" name="theme" value="dark">
                                    <label for="dark"><?php echo trans('common.warning'); ?> - <?php echo trans('settings.theme'); ?></label>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Password Change -->
                    <div class="section">
                        <h3><?php echo trans('settings.change_password'); ?></h3>
                        <form method="POST">
                            <div class="form-group">
                                <label><?php echo trans('auth.password'); ?></label>
                                <input type="password" name="current_password" placeholder="<?php echo trans('auth.password'); ?>">
                            </div>
                            <div class="form-group">
                                <label><?php echo trans('auth.password'); ?> <?php echo trans('settings.change_password'); ?></label>
                                <input type="password" name="new_password" placeholder="<?php echo trans('auth.password'); ?>">
                            </div>
                            <div class="form-group">
                                <label><?php echo trans('auth.confirm_password'); ?></label>
                                <input type="password" name="confirm_password" placeholder="<?php echo trans('auth.confirm_password'); ?>">
                            </div>
                            <button type="submit"><?php echo trans('profile.save'); ?></button>
                        </form>
                    </div>

                    <!-- Danger Zone -->
                    <div class="section">
                        <h3><?php echo trans('common.warning'); ?></h3>
                        <p><?php echo trans('settings.delete_account'); ?></p>
                        <button type="button" class="danger"><?php echo trans('settings.delete_account'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
