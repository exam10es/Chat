<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/helpers.php';

$chapterId = (int) ($_GET['id'] ?? 0);
$chapter = null;

if ($chapterId) {
    $stmt = db()->prepare('SELECT chapters.*, subjects.name AS subject_name FROM chapters JOIN subjects ON subjects.id = chapters.subject_id WHERE chapters.id = :id AND chapters.is_active = 1');
    $stmt->execute([':id' => $chapterId]);
    $chapter = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>بدء الامتحان</title>
    <link rel="stylesheet" href="../assets/css/public.css">
</head>
<body>
<div class="container">
    <a class="btn" href="subjects.php?id=<?= $chapter ? (int) $chapter['subject_id'] : 0; ?>">عودة</a>
    <?php if ($chapter): ?>
        <h1>امتحان <?= htmlspecialchars($chapter['name'], ENT_QUOTES, 'UTF-8'); ?></h1>
        <form class="card" method="post" action="exam.php">
            <input type="hidden" name="chapter_id" value="<?= (int) $chapter['id']; ?>">
            <label>اسم الطالب</label>
            <input type="text" name="student_name" required>
            <button class="btn" type="submit">بدء الامتحان</button>
        </form>
    <?php else: ?>
        <div class="card">الفصل غير موجود.</div>
    <?php endif; ?>
</div>
</body>
</html>
