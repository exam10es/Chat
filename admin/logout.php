<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/helpers.php';

use Core\Session;

Session::destroy();
redirect('login.php');
