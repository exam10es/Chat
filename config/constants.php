<?php

declare(strict_types=1);

define('SYSTEM_NAME', 'نظام إدارة الامتحانات');
define('SYSTEM_VERSION', '1.0.0');
define('ITEMS_PER_PAGE', 20);
define('SESSION_TIMEOUT', 1800);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900);
define('AUTO_SAVE_INTERVAL', 30);
define('EXAM_TIME_LIMIT', 3600);

define('BASE_PATH', dirname(__DIR__));
define('CORE_PATH', BASE_PATH . '/core');
define('ADMIN_PATH', BASE_PATH . '/admin');
define('API_PATH', BASE_PATH . '/api');
define('ASSETS_PATH', BASE_PATH . '/assets');

define('BASE_URL', 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
define('ADMIN_URL', BASE_URL . '/admin');
define('API_URL', BASE_URL . '/api');
define('ASSETS_URL', BASE_URL . '/assets');
