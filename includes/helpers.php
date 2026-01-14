<?php

declare(strict_types=1);

require_once BASE_PATH . '/config/constants.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/includes/security.php';
require_once CORE_PATH . '/Session.php';
require_once CORE_PATH . '/Response.php';
require_once CORE_PATH . '/Validator.php';
require_once CORE_PATH . '/Database.php';

use Core\Session;
use Core\Database;
use PDO;
use RuntimeException;

Session::start();
Session::enforceTimeout(SESSION_TIMEOUT);
Session::bindClient($_SERVER['REMOTE_ADDR'] ?? 'unknown', $_SERVER['HTTP_USER_AGENT'] ?? 'unknown');
apply_security_headers();

$configPath = BASE_PATH . '/config/config.php';
if (file_exists($configPath)) {
    $config = require $configPath;
}

function db(): PDO
{
    global $config;
    if (!$config) {
        throw new RuntimeException('Config not loaded.');
    }
    return Database::getInstance($config['db'])->getConnection();
}
