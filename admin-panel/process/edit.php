<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Edit Process Step';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM processes WHERE id = ?");
$stmt->execute([$id]);
$step = $stmt->fetch();
if (!$step) { flashMessage('danger', 'Step not found.'); redirect(ADMIN_URL . '/process/index.php'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $pdo->prepare("UPDATE processes SET step_number=?,title=?,description=?,icon=?,status=?,sort_order=? WHERE id=?")
        ->execute([
            (int)$_POST['step_number'], $_POST['title'], $_POST['description'],
            $_POST['icon'], (int)($_POST['status'] ?? 1), (int)$_POST['sort_order'], $id
        ]);
    flashMessage('success', 'Process step updated.');
    redirect(ADMIN_URL . '/process/index.php');
}
require_once '../includes/layout-top.php';
?>
<div class="admin-form">
  <form method="POST">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <div class="form-grid">
      <div class="form-group">
        <label>Step Number *</label>
        <input type="number" name="step_number" required value="<?= e($step['step_number']) ?>" min="1">
      </div>
      <div class="form-group">
        <label>Icon Class (FontAwesome)</label>
        <input type="text" name="icon" value="<?= e($step['icon']) ?>">
      </div>
      <div class="form-group">
        <label>Step Title *</label>
        <input type="text" name="title" required value="<?= e($step['title']) ?>">
      </div>
      <div class="form-group">
        <label>Sort Order</label>
        <input type="number" name="sort_order" value="<?= e($step['sort_order']) ?>">
      </div>
      <div class="form-group full">
        <label>Description</label>
        <textarea name="description" rows="3"><?= e($step['description']) ?></textarea>
      </div>
      <div class="form-group">
        <label>Status</label>
        <select name="status">
          <option value="1" <?= $step['status'] ? 'selected' : '' ?>>Active</option>
          <option value="0" <?= !$step['status'] ? 'selected' : '' ?>>Hidden</option>
        </select>
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Step</button>
      <a href="index.php" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
<?php require_once '../includes/layout-bottom.php'; ?>
