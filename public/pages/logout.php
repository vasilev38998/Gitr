<?php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../app/Auth.php';
require_once __DIR__ . '/../../app/models/User.php';

use App\Auth;

$auth = new Auth();

if ($auth->isAuthenticated()) {
    $auth->logout();
}

header('Location: /login?logout=1');
exit();
