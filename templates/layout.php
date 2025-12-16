<?php

declare(strict_types=1);

/** @var string $title */
/** @var string $content */

?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header class="container">
    <h1><?= htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h1>
</header>
<main class="container">
    <?= $content ?>
</main>
<script src="/assets/js/app.js" defer></script>
</body>
</html>
