<?php

// Start session
session_start();

// Include autoloader and helpers
require_once dirname(__DIR__) . '/src/Localization.php';
require_once dirname(__DIR__) . '/src/helpers.php';
require_once dirname(__DIR__) . '/src/Auth.php';

// Redirect to feed if already logged in
if (Auth::check()) {
    redirect('/feed');
}

$action = $_GET['action'] ?? 'login';
$errors = [];
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'login';
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    if (!verify_csrf_token($csrfToken)) {
        $errors[] = trans('errors.validation_failed');
    } else {
        if ($action === 'login') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (empty($email)) {
                $errors[] = trans('errors.required_field');
            }
            if (empty($password)) {
                $errors[] = trans('errors.required_field');
            }
            
            if (empty($errors)) {
                $userId = Auth::attemptLogin($email, $password);
                
                if ($userId !== null) {
                    Auth::setUserId($userId);
                    redirect('/feed');
                } else {
                    $errors[] = trans('errors.invalid_credentials');
                }
            }
        } else if ($action === 'register') {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            if (empty($username)) {
                $errors[] = trans('errors.required_field');
            }
            if (empty($email)) {
                $errors[] = trans('errors.required_field');
            }
            if (empty($password)) {
                $errors[] = trans('errors.required_field');
            }
            if ($password !== $confirm_password) {
                $errors[] = trans('errors.passwords_do_not_match');
            }
            if (!empty($password) && strlen($password) < 6) {
                $errors[] = trans('errors.password_too_short');
            }
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = trans('errors.invalid_email');
            }
            
            if (empty($errors)) {
                try {
                    $userId = Auth::attemptRegister($username, $email, $password);
                    
                    if ($userId !== null) {
                        Auth::setUserId($userId);
                        redirect('/feed');
                    }
                } catch (Exception $e) {
                    $errorMessage = $e->getMessage();
                    if (strpos($errorMessage, 'Email already exists') !== false) {
                        $errors[] = trans('errors.email_already_exists');
                    } else if (strpos($errorMessage, 'Username already exists') !== false) {
                        $errors[] = trans('errors.username_already_exists');
                    } else if (strpos($errorMessage, 'Password too short') !== false) {
                        $errors[] = trans('errors.password_too_short');
                    } else {
                        $errors[] = trans('errors.internal_error');
                    }
                }
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="<?php echo get_language(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo trans('auth.login'); ?> - Gitr</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }
        
        .language-switcher {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
        }
        
        .language-switcher a {
            display: inline-block;
            padding: 8px 12px;
            background-color: white;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            color: #333;
            cursor: pointer;
            opacity: 0.8;
            transition: opacity 0.3s;
        }
        
        .language-switcher a:hover,
        .language-switcher a.active {
            opacity: 1;
        }
        
        .card {
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        
        .logo {
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 30px;
        }
        
        h2 {
            text-align: center;
            margin-bottom: 30px;
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
        
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            font-size: 14px;
            font-family: inherit;
        }
        
        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        button {
            width: 100%;
            padding: 12px;
            background-color: #667eea;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        button:hover {
            background-color: #5568d3;
        }
        
        .toggle-action {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
        
        .toggle-action a {
            color: #667eea;
            text-decoration: none;
            cursor: pointer;
        }
        
        .toggle-action a:hover {
            text-decoration: underline;
        }
        
        .errors {
            background-color: #fee;
            border: 1px solid #fcc;
            color: #c33;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .errors ul {
            list-style: none;
            padding: 0;
        }
        
        .errors li {
            margin-bottom: 5px;
        }
        
        .errors li:last-child {
            margin-bottom: 0;
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
        
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="language-switcher">
        <?php foreach (get_supported_languages() as $lang): ?>
            <a href="?lang=<?php echo $lang; ?>&action=<?php echo $action; ?>" class="<?php echo get_language() === $lang ? 'active' : ''; ?>">
                <?php echo get_language_flag($lang); ?> <?php echo strtoupper($lang); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="container">
        <div class="card">
            <div class="logo">Gitr</div>
            
            <?php if (!empty($errors)): ?>
                <div class="errors">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($message)): ?>
                <div class="success">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form id="login-form" method="POST" class="<?php echo $action === 'login' ? '' : 'hidden'; ?>">
                <h2><?php echo trans('auth.sign_in'); ?></h2>
                <input type="hidden" name="action" value="login">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                
                <div class="form-group">
                    <label><?php echo trans('auth.email'); ?></label>
                    <input type="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label><?php echo trans('auth.password'); ?></label>
                    <input type="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label style="display: flex; align-items: center; font-weight: 400;">
                        <input type="checkbox" name="remember" style="width: auto; margin-right: 8px;">
                        <?php echo trans('auth.remember_me'); ?>
                    </label>
                </div>
                
                <button type="submit"><?php echo trans('auth.sign_in'); ?></button>
                
                <div class="toggle-action">
                    <?php echo trans('auth.dont_have_account'); ?>
                    <a href="#" onclick="toggleForms('register-form', 'login-form'); return false;">
                        <?php echo trans('auth.sign_up'); ?>
                    </a>
                </div>
            </form>

            <!-- Register Form -->
            <form id="register-form" method="POST" class="<?php echo $action === 'register' ? '' : 'hidden'; ?>">
                <h2><?php echo trans('auth.sign_up'); ?></h2>
                <input type="hidden" name="action" value="register">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                
                <div class="form-group">
                    <label><?php echo trans('auth.username'); ?></label>
                    <input type="text" name="username" required>
                </div>
                
                <div class="form-group">
                    <label><?php echo trans('auth.email'); ?></label>
                    <input type="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label><?php echo trans('auth.password'); ?></label>
                    <input type="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label><?php echo trans('auth.confirm_password'); ?></label>
                    <input type="password" name="confirm_password" required>
                </div>
                
                <button type="submit"><?php echo trans('auth.sign_up'); ?></button>
                
                <div class="toggle-action">
                    <?php echo trans('auth.already_have_account'); ?>
                    <a href="#" onclick="toggleForms('login-form', 'register-form'); return false;">
                        <?php echo trans('auth.sign_in'); ?>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleForms(showFormId, hideFormId) {
            document.getElementById(showFormId).classList.remove('hidden');
            document.getElementById(hideFormId).classList.add('hidden');
        }

        // Handle language parameter
        const urlParams = new URLSearchParams(window.location.search);
        const lang = urlParams.get('lang');
        if (lang) {
            // The language is already set through the session
            window.location.href = '?action=' + ('<?php echo $action; ?>');
        }
    </script>
</body>
</html>
