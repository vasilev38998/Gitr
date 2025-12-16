<?php

declare(strict_types=1);

/** @var string $title */
/** @var string $content */

// Set the HTML language attribute based on current language
$htmlLang = get_language();

?><!doctype html>
<html lang="<?= htmlspecialchars($htmlLang, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    
    <!-- Meta tags for better SEO and social sharing -->
    <meta name="description" content="<?= htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?> - Gitr Social Network">
    <meta name="author" content="Gitr">
    
    <!-- Theme color for mobile browsers -->
    <meta name="theme-color" content="#2563eb">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.ico">
</head>
<body>
    <header class="main-header">
        <nav class="nav-container">
            <div class="nav-brand">
                <h1><?= htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h1>
            </div>
            <div class="nav-menu">
                <!-- Additional navigation items can be added here -->
            </div>
        </nav>
    </header>
    
    <main class="main-container">
        <?= $content ?>
    </main>
    
    <footer class="main-footer">
        <div class="container">
            <p>© <?= date('Y') ?> Gitr Social Network. <?= trans('common.rights_reserved', ['app' => 'Gitr']) ?: 'All rights reserved.' ?></p>
        </div>
    </footer>
    
    <script src="/assets/js/app.js" defer></script>
    
    <!-- Dark mode detection and application -->
    <script>
        // Apply dark mode based on user preference
        if (localStorage.getItem('theme') === 'dark' || 
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.body.classList.add('dark-mode');
        }
    </script>
</body>
</html>

<style>
.main-header {
    background: #ffffff;
    border-bottom: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    z-index: 100;
}

.nav-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 4rem;
}

.nav-brand h1 {
    margin: 0;
    font-size: 1.5rem;
    color: #2563eb;
    font-weight: 700;
}

.nav-menu {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.main-container {
    min-height: calc(100vh - 8rem);
    padding: 2rem 1rem;
}

.main-footer {
    background: #f8f9fa;
    border-top: 1px solid #e5e7eb;
    padding: 2rem 0;
    text-align: center;
    color: #6b7280;
    margin-top: auto;
}

.main-footer p {
    margin: 0;
    font-size: 0.875rem;
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .main-header {
        background: #1f2937;
        border-bottom-color: #374151;
    }
    
    .nav-brand h1 {
        color: #3b82f6;
    }
    
    .main-footer {
        background: #1f2937;
        border-top-color: #374151;
        color: #9ca3af;
    }
    
    .dark-mode body {
        background: #111827;
        color: #f9fafb;
    }
    
    .dark-mode .main-container {
        background: #111827;
    }
}

/* Responsive design */
@media (max-width: 768px) {
    .nav-container {
        padding: 0 0.5rem;
    }
    
    .nav-brand h1 {
        font-size: 1.25rem;
    }
    
    .main-container {
        padding: 1rem 0.5rem;
    }
}
</style>
