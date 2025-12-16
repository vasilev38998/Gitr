<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/migrations/001_create_users_table.php';

use Database\Migrations\CreateUsersTable;

if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line');
}

$action = $argv[1] ?? 'up';

if ($action === 'up') {
    echo "Running migrations...\n";
    $result = CreateUsersTable::up();
    echo ($result['success'] ? 'SUCCESS: ' : 'ERROR: ') . $result['message'] . "\n";
} elseif ($action === 'down') {
    echo "Rolling back migrations...\n";
    $result = CreateUsersTable::down();
    echo ($result['success'] ? 'SUCCESS: ' : 'ERROR: ') . $result['message'] . "\n";
} else {
    echo "Unknown action: $action\n";
    echo "Available actions: up, down\n";
}
