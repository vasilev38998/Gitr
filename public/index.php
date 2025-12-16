<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../app/Auth.php';
require_once __DIR__ . '/../app/models/User.php';

use App\Auth;

$auth = new Auth();

// Simple router to handle different paths
$path = isset($_GET['path']) ? trim($_GET['path'], '/') : '';

// Route authentication pages
if ($path === 'register' || $path === 'pages/register') {
    require __DIR__ . '/pages/register.php';
    exit();
}

if ($path === 'login' || $path === 'pages/login') {
    require __DIR__ . '/pages/login.php';
    exit();
}

if ($path === 'logout' || $path === 'pages/logout') {
    require __DIR__ . '/pages/logout.php';
    exit();
}

// Default route - Dashboard (protected)
if (!$auth->isAuthenticated()) {
    header('Location: /login');
    exit();
}

$user = $auth->getAuthenticatedUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gitr</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
        }

        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        h1 {
            font-size: 24px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-details {
            text-align: right;
        }

        .user-details p {
            font-size: 14px;
            margin: 2px 0;
        }

        .username {
            font-weight: 600;
            font-size: 16px;
        }

        .logout-btn {
            padding: 10px 20px;
            background: white;
            color: #667eea;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .welcome-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .welcome-section h2 {
            color: #333;
            margin-bottom: 15px;
        }

        .welcome-section p {
            color: #666;
            line-height: 1.6;
        }

        .user-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .user-card h3 {
            color: #667eea;
            margin-bottom: 15px;
        }

        .user-info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .user-info-item:last-child {
            border-bottom: none;
        }

        .label {
            color: #666;
            font-weight: 500;
        }

        .value {
            color: #333;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <h1>Gitr</h1>
            <div class="user-info">
                <div class="user-details">
                    <p class="username"><?php echo htmlspecialchars($user['username']); ?></p>
                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
                <a href="/logout" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="welcome-section">
            <h2>Welcome to Gitr!</h2>
            <p>You have successfully logged in. This is your dashboard where you can manage your profile and interact with the platform.</p>
        </div>

        <div class="user-card">
            <h3>Your Profile</h3>
            <div class="user-info-item">
                <span class="label">Username:</span>
                <span class="value"><?php echo htmlspecialchars($user['username']); ?></span>
            </div>
            <div class="user-info-item">
                <span class="label">Email:</span>
                <span class="value"><?php echo htmlspecialchars($user['email']); ?></span>
            </div>
            <div class="user-info-item">
                <span class="label">Member Since:</span>
                <span class="value"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></span>
            </div>
            <div class="user-info-item">
                <span class="label">Last Updated:</span>
                <span class="value"><?php echo date('F j, Y \a\t H:i', strtotime($user['updated_at'])); ?></span>
            </div>
        </div>
    </div>
</body>
</html>
