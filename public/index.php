<?php
require_once __DIR__ . '/../src/Auth.php';

if (Auth::check()) {
    header('Location: /feed.php');
} else {
    header('Location: /login.php');
}
exit;