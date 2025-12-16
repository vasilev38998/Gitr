<?php

declare(strict_types=1);

/** @var ?App\Database\Database $db */
/** @var ?Throwable $dbError */

$dbStatus = 'not connected';

if ($db !== null) {
    try {
        $row = $db->fetchOne('SELECT 1 AS ok');
        $dbStatus = ($row !== null && (int) $row['ok'] === 1) ? 'connected' : 'connected (unexpected response)';
    } catch (Throwable $e) {
        $dbStatus = 'connected (query failed)';
        $dbError = $dbError ?? $e;
    }
}

?>
<section>
    <p>Base project structure is ready. Next step: authentication.</p>

    <h2>Database status</h2>
    <p><strong><?= htmlspecialchars($dbStatus, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></strong></p>

    <?php if ($dbError !== null && ini_get('display_errors')): ?>
        <pre><?= htmlspecialchars((string) $dbError, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></pre>
    <?php endif; ?>
</section>
