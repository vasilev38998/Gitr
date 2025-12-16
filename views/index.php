<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gitr - Social Network</title>
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
        <div class="hero">
            <h1>Welcome to Gitr</h1>
            <p>Connect with friends and share your thoughts</p>
            <div class="hero-buttons">
                <a href="/feed.php" class="btn btn-primary">Go to Feed</a>
                <a href="/post/create.php" class="btn btn-secondary">Create Post</a>
            </div>
        </div>

        <section class="recent-posts">
            <h2>Recent Posts</h2>
            <div id="recent-posts-list" class="posts-grid">
                <!-- Posts will be loaded here via JavaScript -->
            </div>
        </section>
    </main>

    <script src="/js/app.js"></script>
    <script>
        // Load recent posts on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadRecentPosts();
        });
    </script>
</body>
</html>