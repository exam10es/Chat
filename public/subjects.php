<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/helpers.php';

$subjectId = (int) ($_GET['id'] ?? 0);
$subject = null;
$chapters = [];

if ($subjectId) {
    $stmt = db()->prepare('SELECT * FROM subjects WHERE id = :id AND is_active = 1');
    $stmt->execute([':id' => $subjectId]);
    $subject = $stmt->fetch();

    $stmt = db()->prepare('SELECT * FROM chapters WHERE subject_id = :id AND is_active = 1 ORDER BY display_order, id');
    $stmt->execute([':id' => $subjectId]);
    $chapters = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>الفصول</title>
    <link rel="stylesheet" href="../assets/css/public.css">
</head>
<body>
<div class="container">
    <a class="btn" href="index.php">عودة</a>
    <?php if ($subject): ?>
        <h1>فصول <?= htmlspecialchars($subject['name'], ENT_QUOTES, 'UTF-8'); ?></h1>
        <div class="grid grid-2">
            <?php foreach ($chapters as $chapter): ?>
                <div class="card">
                    <h3><?= htmlspecialchars($chapter['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p><?= htmlspecialchars($chapter['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                    <a class="btn" href="chapters.php?id=<?= (int) $chapter['id']; ?>">بدء الامتحان</a>
                </div>
            <?php endforeach; ?>
            <?php if (!$chapters): ?>
                <div class="card">لا توجد فصول متاحة.</div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="card">المادة غير موجودة.</div>
    <?php endif; ?>
</div>
</body>
</html>
