<?php

declare(strict_types=1);

/** @var ?App\Database\Database $db */
/** @var ?Throwable $dbError */

// Check if user is authenticated
$isAuthenticated = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$currentUser = null;

if ($isAuthenticated) {
    $currentUser = [
        'id' => $_SESSION['user_id'],
        'email' => $_SESSION['user_email'] ?? 'user@example.com',
        'username' => $_SESSION['user_username'] ?? 'user'
    ];
}

?>
<section class="home-section">
    <?php if (!$isAuthenticated): ?>
        <!-- Guest view -->
        <div class="welcome-section">
            <div class="welcome-content">
                <h1 class="welcome-title"><?= trans('feed.welcome') ?></h1>
                <p class="welcome-subtitle"><?= trans('feed.welcome_subtitle') ?></p>
            </div>
        </div>

        <div class="auth-section">
            <div class="auth-tabs">
                <button class="tab-btn active" data-tab="login"><?= trans('auth.login') ?></button>
                <button class="tab-btn" data-tab="register"><?= trans('auth.register') ?></button>
            </div>

            <!-- Login Form -->
            <div class="auth-form active" id="login-form">
                <form method="POST" action="/auth.php">
                    <input type="hidden" name="action" value="login">
                    <div class="form-group">
                        <label for="login-email"><?= trans('auth.email') ?></label>
                        <input type="email" id="login-email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="login-password"><?= trans('auth.password') ?></label>
                        <input type="password" id="login-password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary"><?= trans('auth.sign_in') ?></button>
                </form>
            </div>

            <!-- Register Form -->
            <div class="auth-form" id="register-form">
                <form method="POST" action="/auth.php">
                    <input type="hidden" name="action" value="register">
                    <div class="form-group">
                        <label for="register-username"><?= trans('auth.username') ?></label>
                        <input type="text" id="register-username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="register-email"><?= trans('auth.email') ?></label>
                        <input type="email" id="register-email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="register-password"><?= trans('auth.password') ?></label>
                        <input type="password" id="register-password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="register-confirm-password"><?= trans('auth.confirm_password') ?></label>
                        <input type="password" id="register-confirm-password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary"><?= trans('auth.sign_up') ?></button>
                </form>
            </div>
        </div>

    <?php else: ?>
        <!-- Authenticated user view -->
        <div class="user-header">
            <div class="user-info">
                <h1><?= trans('feed.home') ?></h1>
                <p><?= trans('feed.welcome_user', ['username' => $currentUser['username']]) ?></p>
            </div>
            <div class="user-actions">
                <a href="/feed.php" class="btn btn-primary"><?= trans('feed.feed') ?></a>
                <a href="/profile.php" class="btn btn-secondary"><?= trans('nav.profile') ?></a>
                <a href="/messages.php" class="btn btn-secondary"><?= trans('nav.messages') ?></a>
                <a href="/settings.php" class="btn btn-secondary"><?= trans('nav.settings') ?></a>
                <a href="/logout.php" class="btn btn-danger"><?= trans('auth.logout') ?></a>
            </div>
        </div>

        <!-- Create Post Section -->
        <div class="create-post-section">
            <form id="create-post-form">
                <div class="form-group">
                    <textarea 
                        name="content" 
                        placeholder="<?= trans('feed.whats_on_your_mind') ?>" 
                        required
                        rows="3"
                    ></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><?= trans('posts.create_post') ?></button>
                </div>
            </form>
        </div>

        <!-- Posts Feed -->
        <div class="posts-feed" id="posts-feed">
            <div class="loading"><?= trans('common.loading') ?></div>
        </div>

        <script>
            // Load posts feed
            loadPosts();
            
            // Auto-load posts every 30 seconds
            setInterval(loadPosts, 30000);
        </script>

    <?php endif; ?>
</section>

<!-- Language Switcher -->
<div class="language-switcher">
    <?php foreach (get_supported_languages() as $lang): ?>
        <a href="?lang=<?= $lang ?>" 
           class="lang-btn <?= get_language() === $lang ? 'active' : '' ?>"
           title="<?= get_language_name($lang) ?>">
            <?= get_language_flag($lang) ?>
        </a>
    <?php endforeach; ?>
</div>

<script>
// Handle language switching
document.querySelectorAll('.lang-btn').forEach(btn => {
    btn.addEventListener('click', async function(e) {
        e.preventDefault();
        
        const language = this.href.split('lang=')[1];
        
        try {
            const response = await fetch('/_api/language.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'set',
                    language: language
                })
            });
            
            if (response.ok) {
                window.location.reload();
            }
        } catch (error) {
            console.error('Language switch failed:', error);
            window.location.reload(); // Fallback to direct navigation
        }
    });
});

// Tab switching for auth forms
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tab = this.dataset.tab;
        
        // Update active tab button
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        // Update active form
        document.querySelectorAll('.auth-form').forEach(form => form.classList.remove('active'));
        document.getElementById(tab + '-form').classList.add('active');
    });
});

// Handle post creation
document.getElementById('create-post-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const content = formData.get('content');
    
    if (!content.trim()) {
        return;
    }
    
    try {
        const response = await fetch('/api/posts.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ content })
        });
        
        const result = await response.json();
        
        if (result.success) {
            this.reset();
            loadPosts();
        }
    } catch (error) {
        console.error('Post creation failed:', error);
    }
});

// Load posts function
async function loadPosts() {
    try {
        const response = await fetch('/api/posts.php');
        const result = await response.json();
        
        const postsFeed = document.getElementById('posts-feed');
        if (result.success && result.data.length > 0) {
            postsFeed.innerHTML = result.data.map(post => `
                <div class="post">
                    <div class="post-header">
                        <div class="post-author">${post.username}</div>
                        <div class="post-time">${new Date(post.created_at).toLocaleString()}</div>
                    </div>
                    <div class="post-content">${post.content}</div>
                    <div class="post-actions">
                        <button class="btn btn-like" data-post-id="${post.id}">
                            ❤️ ${post.likes_count || 0}
                        </button>
                        <button class="btn btn-comment">💬 <?= trans('comments.comments') ?></button>
                    </div>
                </div>
            `).join('');
        } else {
            postsFeed.innerHTML = '<div class="no-posts"><?= trans('feed.no_posts') ?></div>';
        }
    } catch (error) {
        console.error('Failed to load posts:', error);
        document.getElementById('posts-feed').innerHTML = 
            '<div class="error"><?= trans('errors.internal_error') ?></div>';
    }
}
</script>

<style>
.home-section {
    position: relative;
    min-height: 100vh;
}

.welcome-section {
    text-align: center;
    padding: 4rem 2rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    margin-bottom: 2rem;
}

.welcome-title {
    font-size: 3rem;
    margin-bottom: 1rem;
    font-weight: 700;
}

.welcome-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
}

.auth-section {
    max-width: 400px;
    margin: 0 auto;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.auth-tabs {
    display: flex;
    background: #f8f9fa;
}

.tab-btn {
    flex: 1;
    padding: 1rem;
    border: none;
    background: transparent;
    cursor: pointer;
    font-size: 1rem;
    transition: background-color 0.2s;
}

.tab-btn.active {
    background: white;
    color: #2563eb;
}

.auth-form {
    display: none;
    padding: 2rem;
}

.auth-form.active {
    display: block;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #374151;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 1rem;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.user-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding: 2rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.user-info h1 {
    margin: 0;
    color: #1f2937;
}

.user-info p {
    margin: 0.5rem 0 0 0;
    color: #6b7280;
}

.user-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.create-post-section {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 2rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.posts-feed {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.post {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.post:last-child {
    border-bottom: none;
}

.post-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.post-author {
    font-weight: 600;
    color: #1f2937;
}

.post-time {
    font-size: 0.875rem;
    color: #6b7280;
}

.post-content {
    margin-bottom: 1rem;
    line-height: 1.5;
    white-space: pre-wrap;
}

.post-actions {
    display: flex;
    gap: 1rem;
}

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.btn-primary {
    background: #2563eb;
    color: white;
}

.btn-primary:hover {
    background: #1d4ed8;
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.btn-danger {
    background: #dc2626;
    color: white;
}

.btn-danger:hover {
    background: #b91c1c;
}

.btn-like,
.btn-comment {
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    font-size: 0.875rem;
}

.btn-like:hover,
.btn-comment:hover {
    color: #2563eb;
}

.language-switcher {
    position: fixed;
    top: 1rem;
    right: 1rem;
    display: flex;
    gap: 0.5rem;
    z-index: 1000;
}

.lang-btn {
    padding: 0.5rem;
    border: 2px solid transparent;
    border-radius: 6px;
    background: white;
    text-decoration: none;
    font-size: 1.2rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: all 0.2s;
}

.lang-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.lang-btn.active {
    border-color: #2563eb;
    background: #eff6ff;
}

.loading {
    text-align: center;
    padding: 2rem;
    color: #6b7280;
}

.no-posts {
    text-align: center;
    padding: 2rem;
    color: #6b7280;
}

.error {
    text-align: center;
    padding: 2rem;
    color: #dc2626;
}

/* Dark theme support */
@media (prefers-color-scheme: dark) {
    body {
        background: #111827;
        color: #f9fafb;
    }
    
    main {
        background: #1f2937;
        border-color: #374151;
    }
    
    .auth-section,
    .user-header,
    .create-post-section,
    .posts-feed {
        background: #1f2937;
        color: #f9fafb;
    }
    
    .form-group input,
    .form-group textarea {
        background: #374151;
        border-color: #4b5563;
        color: #f9fafb;
    }
    
    .tab-btn {
        color: #9ca3af;
    }
    
    .tab-btn.active {
        background: #1f2937;
        color: #3b82f6;
    }
    
    .post {
        border-bottom-color: #374151;
    }
    
    .post-author {
        color: #f9fafb;
    }
    
    .lang-btn {
        background: #1f2937;
        color: #f9fafb;
    }
}
</style>
