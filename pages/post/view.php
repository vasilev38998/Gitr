<?php
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Post.php';
require_once __DIR__ . '/../../src/Comment.php';

if (!Auth::check()) {
    header('Location: /login.php');
    exit;
}

$currentUser = Auth::user();
$postId = (int)($_GET['id'] ?? 0);

if ($postId <= 0) {
    header('Location: /feed.php');
    exit;
}

$post = new Post();
$postData = $post->getById($postId);

if (!$postData) {
    header('Location: /feed.php');
    exit;
}

$comment = new Comment();
$comments = $comment->getByPostId($postId);

// Handle new comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $content = trim($_POST['comment_content'] ?? '');
        
        if (empty($content)) {
            $commentError = 'Comment content cannot be empty';
        } else {
            $commentId = $comment->create($currentUser['id'], $postId, $content);
            header('Location: /post/view.php?id=' . $postId . '#comment-' . $commentId);
            exit;
        }
    } catch (Exception $e) {
        $commentError = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($postData['username']) ?>'s Post - Gitr</title>
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
                <a href="/post/create.php">Create Post</a>
                <a href="#" onclick="logout()">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container">
        <div class="post-view">
            <div class="post-full">
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
                        <span class="post-date"><?= date('F j, Y \a\t g:i A', strtotime($postData['created_at'])) ?></span>
                        <?php if ($postData['updated_at'] !== $postData['created_at']): ?>
                            <span class="post-edited">(edited)</span>
                        <?php endif; ?>
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
                        <span class="label">Like</span>
                    </button>
                    <div class="action-btn comment-btn">
                        <span>💬</span>
                        <span class="count"><?= $postData['comments_count'] ?></span>
                        <span class="label">Comments</span>
                    </div>
                </div>
            </div>

            <div class="add-comment">
                <h3>Add a comment</h3>
                <form method="POST" class="comment-form">
                    <?php if (isset($commentError)): ?>
                        <div class="error-message"><?= htmlspecialchars($commentError) ?></div>
                    <?php endif; ?>
                    
                    <div class="comment-input-group">
                        <div class="comment-avatar">
                            <?php if ($currentUser['avatar']): ?>
                                <img src="<?= htmlspecialchars($currentUser['avatar']) ?>" alt="Your Avatar">
                            <?php else: ?>
                                <div class="avatar-placeholder"><?= strtoupper(substr($currentUser['username'], 0, 2)) ?></div>
                            <?php endif; ?>
                        </div>
                        <textarea name="comment_content" placeholder="Write a comment..." rows="2" required><?= htmlspecialchars($_POST['comment_content'] ?? '') ?></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Comment</button>
                    </div>
                </form>
            </div>

            <div class="comments-section">
                <h3>Comments (<?= count($comments) ?>)</h3>
                <div class="comments-list">
                    <?php foreach ($comments as $commentData): ?>
                        <div class="comment" id="comment-<?= $commentData['id'] ?>">
                            <div class="comment-avatar">
                                <?php if ($commentData['avatar']): ?>
                                    <img src="<?= htmlspecialchars($commentData['avatar']) ?>" alt="Avatar">
                                <?php else: ?>
                                    <div class="avatar-placeholder"><?= strtoupper(substr($commentData['username'], 0, 2)) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="comment-content">
                                <div class="comment-header">
                                    <span class="comment-author"><?= htmlspecialchars($commentData['username']) ?></span>
                                    <span class="comment-date"><?= date('M j, Y g:i A', strtotime($commentData['created_at'])) ?></span>
                                </div>
                                <div class="comment-text">
                                    <?= nl2br(htmlspecialchars($commentData['content'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if (empty($comments)): ?>
                        <div class="empty-state">
                            <p>No comments yet. Be the first to comment!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="/js/app.js"></script>
</body>
</html>