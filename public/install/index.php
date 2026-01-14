<?php

declare(strict_types=1);

$installer = __DIR__ . '/../../install/index.php';

if (!file_exists($installer)) {
    http_response_code(404);
    echo 'Installer not found.';
    exit;
}

require $installer;
