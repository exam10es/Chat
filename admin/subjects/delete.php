<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/helpers.php';

if (!is_admin()) {
    redirect('../login.php');
}

$id = (int) ($_GET['id'] ?? 0);
if ($id) {
    $stmt = db()->prepare('DELETE FROM subjects WHERE id = :id');
    $stmt->execute([':id' => $id]);
}

redirect('index.php');
