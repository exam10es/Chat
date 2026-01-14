<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/helpers.php';

if (!is_admin()) {
    redirect('../login.php');
}

$subjects = db()->query('SELECT * FROM subjects ORDER BY name')->fetchAll();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'رمز التحقق غير صالح.';
    } else {
        $name = clean_input($_POST['name'] ?? '');
        $description = clean_input($_POST['description'] ?? '');
        $order = (int) ($_POST['display_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $subjectId = (int) ($_POST['subject_id'] ?? 0);

        if ($name && $subjectId) {
            $stmt = db()->prepare('INSERT INTO chapters (subject_id, name, description, display_order, is_active) VALUES (:subject_id, :name, :description, :display_order, :is_active)');
            $stmt->execute([
                ':subject_id' => $subjectId,
                ':name' => $name,
                ':description' => $description,
                ':display_order' => $order,
                ':is_active' => $isActive,
            ]);
            redirect('index.php');
        } else {
            $error = 'يرجى إدخال جميع البيانات.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إضافة فصل</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
<div class="container">
    <h1>إضافة فصل</h1>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <form method="post" class="card">
        <input type="hidden" name="csrf_token" value="<?= csrf_token(); ?>">
        <label>المادة</label>
        <select name="subject_id" required>
            <option value="">اختر</option>
            <?php foreach ($subjects as $subject): ?>
                <option value="<?= (int) $subject['id']; ?>"><?= htmlspecialchars($subject['name'], ENT_QUOTES, 'UTF-8'); ?></option>
            <?php endforeach; ?>
        </select>
        <label>الاسم</label>
        <input type="text" name="name" required>
        <label>الوصف</label>
        <textarea name="description"></textarea>
        <label>ترتيب العرض</label>
        <input type="number" name="display_order" value="0">
        <label>
            <input type="checkbox" name="is_active" checked> نشط
        </label>
        <button class="btn" type="submit">حفظ</button>
    </form>
</div>
</body>
</html>
