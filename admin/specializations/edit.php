<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/helpers.php';

if (!is_admin()) {
    redirect('../login.php');
}

$id = (int) ($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM specializations WHERE id = :id');
$stmt->execute([':id' => $id]);
$spec = $stmt->fetch();

if (!$spec) {
    redirect('index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'رمز التحقق غير صالح.';
    } else {
        $name = clean_input($_POST['name'] ?? '');
        $description = clean_input($_POST['description'] ?? '');
        $order = (int) ($_POST['display_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($name) {
            $update = db()->prepare('UPDATE specializations SET name = :name, description = :description, display_order = :display_order, is_active = :is_active WHERE id = :id');
            $update->execute([
                ':name' => $name,
                ':description' => $description,
                ':display_order' => $order,
                ':is_active' => $isActive,
                ':id' => $id,
            ]);
            redirect('index.php');
        } else {
            $error = 'يرجى إدخال الاسم.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تعديل تخصص</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
<div class="container">
    <h1>تعديل تخصص</h1>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <form method="post" class="card">
        <input type="hidden" name="csrf_token" value="<?= csrf_token(); ?>">
        <label>الاسم</label>
        <input type="text" name="name" value="<?= htmlspecialchars($spec['name'], ENT_QUOTES, 'UTF-8'); ?>" required>
        <label>الوصف</label>
        <textarea name="description"><?= htmlspecialchars($spec['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        <label>ترتيب العرض</label>
        <input type="number" name="display_order" value="<?= (int) $spec['display_order']; ?>">
        <label>
            <input type="checkbox" name="is_active" <?= $spec['is_active'] ? 'checked' : ''; ?>> نشط
        </label>
        <button class="btn" type="submit">تحديث</button>
    </form>
</div>
</body>
</html>
