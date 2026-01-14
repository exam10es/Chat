<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/helpers.php';

if (!is_admin()) {
    redirect('../login.php');
}

$id = (int) ($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM subjects WHERE id = :id');
$stmt->execute([':id' => $id]);
$subject = $stmt->fetch();

if (!$subject) {
    redirect('index.php');
}

$specializations = db()->query('SELECT * FROM specializations ORDER BY name')->fetchAll();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'رمز التحقق غير صالح.';
    } else {
        $name = clean_input($_POST['name'] ?? '');
        $description = clean_input($_POST['description'] ?? '');
        $order = (int) ($_POST['display_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $specId = (int) ($_POST['specialization_id'] ?? 0);

        if ($name && $specId) {
            $update = db()->prepare('UPDATE subjects SET specialization_id = :specialization_id, name = :name, description = :description, display_order = :display_order, is_active = :is_active WHERE id = :id');
            $update->execute([
                ':specialization_id' => $specId,
                ':name' => $name,
                ':description' => $description,
                ':display_order' => $order,
                ':is_active' => $isActive,
                ':id' => $id,
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
    <title>تعديل مادة</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
<div class="container">
    <h1>تعديل مادة</h1>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <form method="post" class="card">
        <input type="hidden" name="csrf_token" value="<?= csrf_token(); ?>">
        <label>التخصص</label>
        <select name="specialization_id" required>
            <?php foreach ($specializations as $spec): ?>
                <option value="<?= (int) $spec['id']; ?>" <?= $spec['id'] == $subject['specialization_id'] ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($spec['name'], ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <label>الاسم</label>
        <input type="text" name="name" value="<?= htmlspecialchars($subject['name'], ENT_QUOTES, 'UTF-8'); ?>" required>
        <label>الوصف</label>
        <textarea name="description"><?= htmlspecialchars($subject['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        <label>ترتيب العرض</label>
        <input type="number" name="display_order" value="<?= (int) $subject['display_order']; ?>">
        <label>
            <input type="checkbox" name="is_active" <?= $subject['is_active'] ? 'checked' : ''; ?>> نشط
        </label>
        <button class="btn" type="submit">تحديث</button>
    </form>
</div>
</body>
</html>
