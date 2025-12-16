<?php

// Start session
session_start();

// Include autoloader and helpers
require_once dirname(__DIR__) . '/src/Localization.php';
require_once dirname(__DIR__) . '/src/helpers.php';

?>
<!DOCTYPE html>
<html lang="<?php echo get_language(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo trans('feed.home'); ?> - Gitr</title>
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
            max-width: 1200px;
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
        
        .main-content {
            display: grid;
            grid-template-columns: 1fr 2fr 1fr;
            gap: 20px;
        }
        
        .card {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        
        .card h2 {
            margin-bottom: 15px;
            font-size: 18px;
        }
        
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-family: inherit;
            font-size: 16px;
            resize: vertical;
            min-height: 100px;
        }
        
        button {
            background-color: #1da1f2;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 24px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            margin-top: 10px;
        }
        
        button:hover {
            background-color: #1a8cd8;
        }
        
        .post {
            border: 1px solid #e0e0e0;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            background-color: white;
        }
        
        .post-author {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .post-content {
            margin-bottom: 10px;
            line-height: 1.5;
        }
        
        .post-meta {
            font-size: 13px;
            color: #657786;
            margin-bottom: 10px;
        }
        
        .post-actions {
            display: flex;
            gap: 20px;
            font-size: 13px;
        }
        
        .post-actions a {
            color: #657786;
            text-decoration: none;
            cursor: pointer;
        }
        
        .post-actions a:hover {
            color: #1da1f2;
        }
        
        .sidebar {
            font-size: 14px;
        }
        
        .sidebar h3 {
            margin-bottom: 15px;
        }
        
        .trending-item {
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .trending-item:last-child {
            border-bottom: none;
        }
        
        @media (max-width: 1024px) {
            .main-content {
                grid-template-columns: 1fr;
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
        <div class="main-content">
            <!-- Sidebar Left -->
            <div class="sidebar">
                <div class="card">
                    <h3><?php echo trans('profile.profile'); ?></h3>
                    <p><?php echo trans('common.loading'); ?></p>
                </div>
            </div>

            <!-- Main Feed -->
            <div>
                <div class="card">
                    <h2><?php echo trans('feed.whats_on_your_mind'); ?></h2>
                    <textarea placeholder="<?php echo trans('feed.whats_on_your_mind'); ?>"></textarea>
                    <button><?php echo trans('feed.share'); ?></button>
                </div>

                <!-- Sample Posts -->
                <div class="post">
                    <div class="post-author">John Doe</div>
                    <div class="post-meta"><?php echo trans('posts.published'); ?> 2 hours ago</div>
                    <div class="post-content">
                        <?php echo trans('feed.whats_on_your_mind'); ?>
                    </div>
                    <div class="post-actions">
                        <a href="#"><?php echo trans('likes.like'); ?></a>
                        <a href="#"><?php echo trans('comments.comments'); ?></a>
                        <a href="#"><?php echo trans('messages.share'); ?></a>
                    </div>
                </div>

                <div class="post">
                    <div class="post-author">Jane Smith</div>
                    <div class="post-meta"><?php echo trans('posts.published'); ?> 5 hours ago</div>
                    <div class="post-content">
                        <?php echo trans('notifications.new_like'); ?>
                    </div>
                    <div class="post-actions">
                        <a href="#"><?php echo trans('likes.like'); ?></a>
                        <a href="#"><?php echo trans('comments.comments'); ?></a>
                        <a href="#"><?php echo trans('messages.share'); ?></a>
                    </div>
                </div>
            </div>

            <!-- Sidebar Right -->
            <div class="sidebar">
                <div class="card">
                    <h3><?php echo trans('search.search'); ?></h3>
                    <input type="text" placeholder="<?php echo trans('search.search'); ?>" style="width: 100%; padding: 8px; border: 1px solid #e0e0e0; border-radius: 4px;">
                </div>
                <div class="card" style="margin-top: 20px;">
                    <h3><?php echo trans('notifications.notifications'); ?></h3>
                    <div class="trending-item"><?php echo trans('notifications.new_follower'); ?></div>
                    <div class="trending-item"><?php echo trans('notifications.new_like'); ?></div>
                    <div class="trending-item"><?php echo trans('notifications.new_comment'); ?></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Handle language switching
        const langButtons = document.querySelectorAll('.language-switcher a');
        langButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                const lang = this.getAttribute('href').split('lang=')[1];
                window.location.href = '?lang=' + lang;
            });
        });

        // Handle language parameter
        const urlParams = new URLSearchParams(window.location.search);
        const lang = urlParams.get('lang');
        if (lang) {
            // Send request to change language
            fetch('/_api/language.php?action=set&language=' + lang)
                .then(response => response.json())
                .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>
