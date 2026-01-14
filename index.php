<?php

declare(strict_types=1);

require_once __DIR__ . '/config/constants.php';

if (!file_exists(__DIR__ . '/config/config.php')) {
    header('Location: /install/index.php');
    exit;
}

header('Location: /public/index.php');
exit;
