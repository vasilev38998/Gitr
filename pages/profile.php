<?php
require_once __DIR__ . '/../src/Auth.php';
require_once __DIR__ . '/../src/Profile.php';
require_once __DIR__ . '/../src/Post.php';

if (!Auth::check()) {
    header('Location: /login.php');
    exit;
}

$currentUser = Auth::user();
$profile = new Profile();
$post = new Post();

// Get user's posts
$userPosts = $post->getUserPosts($currentUser['id'], 10);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - <?= htmlspecialchars($currentUser['username']) ?> - Gitr</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="/">Gitr</a>
            </div>
            <div class="nav-menu">
                <a href="/feed.php">Feed</a>
                <a href="/profile.php" class="active">Profile</a>
                <a href="/post/create.php">Create Post</a>
                <a href="#" onclick="logout()">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container">
        <div class="profile-header">
            <div class="profile-avatar">
                <?php if ($currentUser['avatar']): ?>
                    <img src="<?= htmlspecialchars($currentUser['avatar']) ?>" alt="Avatar">
                <?php else: ?>
                    <div class="avatar-placeholder"><?= strtoupper(substr($currentUser['username'], 0, 2)) ?></div>
                <?php endif; ?>
            </div>
            <div class="profile-info">
                <h1><?= htmlspecialchars($currentUser['username']) ?></h1>
                <?php if ($currentUser['bio']): ?>
                    <p class="bio"><?= nl2br(htmlspecialchars($currentUser['bio'])) ?></p>
                <?php endif; ?>
                <div class="profile-stats">
                    <div class="stat">
                        <span class="stat-number"><?= $currentUser['followers_count'] ?></span>
                        <span class="stat-label">Followers</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number"><?= $currentUser['following_count'] ?></span>
                        <span class="stat-label">Following</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number"><?= count($userPosts) ?></span>
                        <span class="stat-label">Posts</span>
                    </div>
                </div>
            </div>
            <div class="profile-actions">
                <a href="/profile/edit.php" class="btn btn-secondary">Edit Profile</a>
            </div>
        </div>

        <section class="user-posts">
            <h2>My Posts</h2>
            <div id="posts-list">
                <?php foreach ($userPosts as $postData): ?>
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
                                <span class="post-date"><?= date('M j, Y', strtotime($postData['created_at'])) ?></span>
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
                
                <?php if (empty($userPosts)): ?>
                    <div class="empty-state">
                        <p>You haven't posted anything yet.</p>
                        <a href="/post/create.php" class="btn btn-primary">Create Your First Post</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script src="/js/app.js"></script>
</body>
</html>