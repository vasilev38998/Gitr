<?php

// Start session
session_start();

// Include autoloader and helpers
require_once dirname(__DIR__) . '/src/Localization.php';
require_once dirname(__DIR__) . '/src/helpers.php';

$errors = [];
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $location = $_POST['location'] ?? '';
    $website = $_POST['website'] ?? '';
    
    if (empty($username) || empty($email)) {
        $errors[] = trans('errors.required_field');
    }
    
    if (empty($errors)) {
        $message = trans('posts.post_updated');
    }
}

?>
<!DOCTYPE html>
<html lang="<?php echo get_language(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo trans('profile.profile'); ?> - Gitr</title>
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
        
        .card {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            margin-bottom: 20px;
        }
        
        .profile-header {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .avatar {
            width: 120px;
            height: 120px;
            background-color: #667eea;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
        }
        
        .profile-info h2 {
            margin-bottom: 5px;
        }
        
        .profile-stats {
            display: flex;
            gap: 30px;
            margin-top: 15px;
        }
        
        .stat {
            text-align: center;
        }
        
        .stat-number {
            font-size: 20px;
            font-weight: bold;
            color: #1da1f2;
        }
        
        .stat-label {
            font-size: 13px;
            color: #657786;
        }
        
        h2 {
            margin-bottom: 20px;
            font-size: 20px;
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
        
        input,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            font-size: 14px;
            font-family: inherit;
        }
        
        input:focus,
        textarea:focus {
            outline: none;
            border-color: #1da1f2;
            box-shadow: 0 0 0 3px rgba(29, 161, 242, 0.1);
        }
        
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
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
            margin-right: 10px;
            transition: background-color 0.3s;
        }
        
        button:hover {
            background-color: #1a8cd8;
        }
        
        button.secondary {
            background-color: white;
            color: #1da1f2;
            border: 1px solid #1da1f2;
        }
        
        button.secondary:hover {
            background-color: #f0f8ff;
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
        
        .success {
            background-color: #efe;
            border: 1px solid #cfc;
            color: #3c3;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .profile-header {
                flex-direction: column;
                align-items: center;
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
        <div class="card">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="avatar">👤</div>
                <div class="profile-info">
                    <h2>John Doe</h2>
                    <p>@johndoe</p>
                    <div class="profile-stats">
                        <div class="stat">
                            <div class="stat-number">42</div>
                            <div class="stat-label"><?php echo trans('profile.posts'); ?></div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">123</div>
                            <div class="stat-label"><?php echo trans('profile.followers'); ?></div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">89</div>
                            <div class="stat-label"><?php echo trans('profile.following'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Profile Form -->
        <div class="card">
            <h2><?php echo trans('profile.edit_profile'); ?></h2>
            
            <?php if (!empty($errors)): ?>
                <div class="errors">
                    <?php foreach ($errors as $error): ?>
                        <div><?php echo htmlspecialchars($error); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($message)): ?>
                <div class="success">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label><?php echo trans('auth.first_name'); ?></label>
                        <input type="text" name="username" value="John">
                    </div>
                    <div class="form-group">
                        <label><?php echo trans('auth.last_name'); ?></label>
                        <input type="text" name="username" value="Doe">
                    </div>
                </div>

                <div class="form-group">
                    <label><?php echo trans('auth.email'); ?></label>
                    <input type="email" name="email" value="john@example.com">
                </div>

                <div class="form-group">
                    <label><?php echo trans('profile.bio'); ?></label>
                    <textarea name="bio" placeholder="<?php echo trans('profile.bio'); ?>">Software developer and coffee enthusiast</textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label><?php echo trans('profile.location'); ?></label>
                        <input type="text" name="location" value="San Francisco, CA" placeholder="<?php echo trans('profile.location'); ?>">
                    </div>
                    <div class="form-group">
                        <label><?php echo trans('profile.website'); ?></label>
                        <input type="url" name="website" value="https://example.com" placeholder="<?php echo trans('profile.website'); ?>">
                    </div>
                </div>

                <div>
                    <button type="submit"><?php echo trans('profile.save'); ?></button>
                    <button type="reset" class="secondary"><?php echo trans('profile.cancel'); ?></button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
