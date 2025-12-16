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
    <title><?php echo trans('messages.messages'); ?> - Gitr</title>
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
        
        .messages-grid {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 20px;
        }
        
        .conversations {
            background-color: white;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            overflow: hidden;
        }
        
        .conversation-item {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .conversation-item:last-child {
            border-bottom: none;
        }
        
        .conversation-item:hover,
        .conversation-item.active {
            background-color: #f5f5f5;
        }
        
        .conversation-name {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .conversation-preview {
            font-size: 13px;
            color: #657786;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .chat {
            background-color: white;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
            height: 600px;
        }
        
        .chat-header {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            font-weight: bold;
        }
        
        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .message {
            display: flex;
            margin-bottom: 10px;
        }
        
        .message.own {
            justify-content: flex-end;
        }
        
        .message-bubble {
            max-width: 70%;
            padding: 12px 15px;
            border-radius: 18px;
            background-color: #f0f0f0;
            word-wrap: break-word;
        }
        
        .message.own .message-bubble {
            background-color: #1da1f2;
            color: white;
        }
        
        .message-time {
            font-size: 12px;
            color: #657786;
            margin-top: 5px;
        }
        
        .chat-input {
            padding: 15px;
            border-top: 1px solid #e0e0e0;
            display: flex;
            gap: 10px;
        }
        
        .chat-input input {
            flex: 1;
            padding: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 24px;
            font-family: inherit;
            font-size: 14px;
        }
        
        .chat-input input:focus {
            outline: none;
            border-color: #1da1f2;
            box-shadow: 0 0 0 3px rgba(29, 161, 242, 0.1);
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
        }
        
        button:hover {
            background-color: #1a8cd8;
        }
        
        .no-conversation {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #657786;
            height: 600px;
        }

        @media (max-width: 768px) {
            .messages-grid {
                grid-template-columns: 1fr;
            }
            
            .conversations {
                display: none;
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
        <h2 style="margin-bottom: 20px;"><?php echo trans('messages.messages'); ?></h2>

        <div class="messages-grid">
            <!-- Conversations List -->
            <div class="conversations">
                <div class="conversation-item active">
                    <div class="conversation-name">Alice Johnson</div>
                    <div class="conversation-preview"><?php echo trans('messages.message'); ?> 1</div>
                </div>
                <div class="conversation-item">
                    <div class="conversation-name">Bob Smith</div>
                    <div class="conversation-preview"><?php echo trans('messages.message'); ?> 2</div>
                </div>
                <div class="conversation-item">
                    <div class="conversation-name">Carol White</div>
                    <div class="conversation-preview"><?php echo trans('messages.message'); ?> 3</div>
                </div>
            </div>

            <!-- Chat -->
            <div class="chat">
                <div class="chat-header"><?php echo trans('messages.messages'); ?> - Alice Johnson</div>
                <div class="chat-messages">
                    <div class="message">
                        <div>
                            <div class="message-bubble">Hi! How are you?</div>
                            <div class="message-time">10:30</div>
                        </div>
                    </div>
                    <div class="message own">
                        <div>
                            <div class="message-bubble">I'm doing great, thanks for asking!</div>
                            <div class="message-time">10:32</div>
                        </div>
                    </div>
                    <div class="message">
                        <div>
                            <div class="message-bubble">Want to grab coffee tomorrow?</div>
                            <div class="message-time">10:35</div>
                        </div>
                    </div>
                </div>
                <div class="chat-input">
                    <input type="text" placeholder="<?php echo trans('messages.send_message'); ?>">
                    <button><?php echo trans('messages.send_message'); ?></button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
