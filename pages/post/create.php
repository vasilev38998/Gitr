<?php
require_once __DIR__ . '/../src/Auth.php';
require_once __DIR__ . '/../src/Post.php';

if (!Auth::check()) {
    header('Location: /login.php');
    exit;
}

$currentUser = Auth::user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $content = trim($_POST['content'] ?? '');
        
        if (empty($content)) {
            $error = 'Post content cannot be empty';
        } else {
            $post = new Post();
            $postId = $post->create($currentUser['id'], $content);
            
            header('Location: /post/view.php?id=' . $postId);
            exit;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post - Gitr</title>
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
                <a href="/profile.php">Profile</a>
                <a href="/post/create.php" class="active">Create Post</a>
                <a href="#" onclick="logout()">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container">
        <div class="page-header">
            <h1>Create New Post</h1>
            <p>Share your thoughts with the world</p>
        </div>

        <div class="create-post-form-container">
            <form method="POST" class="create-post-form">
                <?php if (isset($error)): ?>
                    <div class="error-message"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="content">What's on your mind?</label>
                    <textarea id="content" name="content" rows="6" placeholder="Write your post here..." required><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
                </div>
                
                <div class="form-actions">
                    <a href="/feed.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Post</button>
                </div>
            </form>
        </div>

        <div class="post-preview">
            <h3>Preview</h3>
            <div class="post-card">
                <div class="post-header">
                    <div class="post-author-avatar">
                        <?php if ($currentUser['avatar']): ?>
                            <img src="<?= htmlspecialchars($currentUser['avatar']) ?>" alt="Avatar">
                        <?php else: ?>
                            <div class="avatar-placeholder"><?= strtoupper(substr($currentUser['username'], 0, 2)) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="post-author-info">
                        <span class="post-author"><?= htmlspecialchars($currentUser['username']) ?></span>
                        <span class="post-date">will be posted now</span>
                    </div>
                </div>
                <div class="post-content" id="preview-content">
                    Your post preview will appear here...
                </div>
            </div>
        </div>
    </main>

    <script src="/js/app.js"></script>
    <script>
        // Live preview
        const contentTextarea = document.getElementById('content');
        const previewContent = document.getElementById('preview-content');
        
        contentTextarea.addEventListener('input', function() {
            const content = this.value.trim();
            if (content) {
                // Simple text preview (no HTML for security)
                previewContent.textContent = content;
            } else {
                previewContent.textContent = 'Your post preview will appear here...';
            }
        });
    </script>
</body>
</html>