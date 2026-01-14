<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/helpers.php';

$specId = (int) ($_GET['id'] ?? 0);
$specialization = null;
$subjects = [];

if ($specId) {
    $stmt = db()->prepare('SELECT * FROM specializations WHERE id = :id AND is_active = 1');
    $stmt->execute([':id' => $specId]);
    $specialization = $stmt->fetch();

    $stmt = db()->prepare('SELECT * FROM subjects WHERE specialization_id = :id AND is_active = 1 ORDER BY display_order, id');
    $stmt->execute([':id' => $specId]);
    $subjects = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>المواد</title>
    <link rel="stylesheet" href="../assets/css/public.css">
</head>
<body>
<div class="container">
    <a class="btn" href="index.php">عودة</a>
    <?php if ($specialization): ?>
        <h1>مواد <?= htmlspecialchars($specialization['name'], ENT_QUOTES, 'UTF-8'); ?></h1>
        <div class="grid grid-2">
            <?php foreach ($subjects as $subject): ?>
                <div class="card">
                    <h3><?= htmlspecialchars($subject['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p><?= htmlspecialchars($subject['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                    <a class="btn" href="subjects.php?id=<?= (int) $subject['id']; ?>">عرض الفصول</a>
                </div>
            <?php endforeach; ?>
            <?php if (!$subjects): ?>
                <div class="card">لا توجد مواد متاحة.</div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="card">التخصص غير موجود.</div>
    <?php endif; ?>
</div>
</body>
</html>
