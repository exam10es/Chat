<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/helpers.php';

$specializations = [];
try {
    $stmt = db()->query('SELECT * FROM specializations WHERE is_active = 1 ORDER BY display_order, id');
    $specializations = $stmt->fetchAll();
} catch (Throwable $exception) {
    $specializations = [];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title><?= SYSTEM_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/public.css">
    <link rel="stylesheet" href="../assets/css/mobile.css">
</head>
<body>
<div class="container">
    <h1><?= SYSTEM_NAME; ?></h1>
    <p>اختر التخصص لبدء الامتحان.</p>

    <div class="grid grid-2">
        <?php foreach ($specializations as $spec): ?>
            <div class="card">
                <h3><?= htmlspecialchars($spec['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <p><?= htmlspecialchars($spec['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                <a class="btn" href="specializations.php?id=<?= (int) $spec['id']; ?>">عرض المواد</a>
            </div>
        <?php endforeach; ?>
        <?php if (!$specializations): ?>
            <div class="card">لا توجد تخصصات متاحة.</div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
