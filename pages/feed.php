<?php
require_once __DIR__ . '/../src/Auth.php';
require_once __DIR__ . '/../src/Post.php';

if (!Auth::check()) {
    header('Location: /login.php');
    exit;
}

$currentUser = Auth::user();
$post = new Post();

// Get feed posts
$feedPosts = $post->getFeed($currentUser['id'], 20);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feed - Gitr</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="/">Gitr</a>
            </div>
            <div class="nav-menu">
                <a href="/feed.php" class="active">Feed</a>
                <a href="/profile.php">Profile</a>
                <a href="/post/create.php">Create Post</a>
                <a href="#" onclick="logout()">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container">
        <section class="create-post">
            <div class="create-post-form">
                <h3>What's on your mind?</h3>
                <form id="create-post-form">
                    <textarea id="post-content" placeholder="Share your thoughts..." rows="3" required></textarea>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Post</button>
                    </div>
                </form>
            </div>
        </section>

        <section class="feed">
            <h2>Your Feed</h2>
            <div id="posts-list">
                <?php foreach ($feedPosts as $postData): ?>
                    <div class="post-card">
                        <div class="post-header">
                            <div class="post-author-avatar">
                                <?php if ($postData['avatar']): ?>
                                    <img src="<?= htmlspecialchars($postData['avatar']) ?>" alt="Avatar">
                                <?php else: ?>
                                    <div class="avatar-placeholder"><?= strtoupper(substr($postData['username'], 0, 2)) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="post-author-info">
                                <span class="post-author"><?= htmlspecialchars($postData['username']) ?></span>
                                <span class="post-date"><?= date('M j, Y g:i A', strtotime($postData['created_at'])) ?></span>
                            </div>
                        </div>
                        <div class="post-content">
                            <?= nl2br(htmlspecialchars($postData['content'])) ?>
                        </div>
                        <div class="post-actions">
                            <button class="action-btn like-btn <?= $postData['is_liked'] ? 'liked' : '' ?>" 
                                    onclick="toggleLike(<?= $postData['id'] ?>)">
                                <span class="heart">♥</span>
                                <span class="count"><?= $postData['likes_count'] ?></span>
                            </button>
                            <a href="/post/view.php?id=<?= $postData['id'] ?>" class="action-btn comment-btn">
                                <span>💬</span>
                                <span class="count"><?= $postData['comments_count'] ?></span>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <?php if (empty($feedPosts)): ?>
                    <div class="empty-state">
                        <p>Your feed is empty. Start following people or create your first post!</p>
                        <a href="/post/create.php" class="btn btn-primary">Create Post</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script src="/js/app.js"></script>
    <script>
        // Handle post creation
        document.getElementById('create-post-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const content = document.getElementById('post-content').value.trim();
            if (!content) return;
            
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
                    document.getElementById('post-content').value = '';
                    location.reload(); // Reload to show new post
                } else {
                    alert('Error: ' + result.error);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to create post');
            }
        });
    </script>
</body>
</html>