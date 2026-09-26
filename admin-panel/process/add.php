<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Add Process Step';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $pdo->prepare("INSERT INTO processes (step_number,title,description,icon,status,sort_order) VALUES (?,?,?,?,?,?)")
        ->execute([
            (int)$_POST['step_number'], $_POST['title'], $_POST['description'],
            $_POST['icon'], (int)($_POST['status'] ?? 1), (int)$_POST['sort_order']
        ]);
    flashMessage('success', 'Process step added.');
    redirect(ADMIN_URL . '/process/index.php');
}

// Suggest next step number
$nextStep = (int)($pdo->query("SELECT MAX(step_number) FROM processes")->fetchColumn()) + 1;
require_once '../includes/layout-top.php';
?>
<div class="admin-form">
  <form method="POST">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <div class="form-grid">
      <div class="form-group">
        <label>Step Number *</label>
        <input type="number" name="step_number" required value="<?= $nextStep ?>" min="1">
      </div>
      <div class="form-group">
        <label>Icon Class (FontAwesome)</label>
        <input type="text" name="icon" value="fas fa-circle" placeholder="fas fa-search">
      </div>
      <div class="form-group">
        <label>Step Title *</label>
        <input type="text" name="title" required placeholder="e.g. Discover">
      </div>
      <div class="form-group">
        <label>Sort Order</label>
        <input type="number" name="sort_order" value="<?= $nextStep ?>">
      </div>
      <div class="form-group full">
        <label>Description</label>
        <textarea name="description" rows="3"></textarea>
      </div>
      <div class="form-group">
        <label>Status</label>
        <select name="status">
          <option value="1">Active</option>
          <option value="0">Hidden</option>
        </select>
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Step</button>
      <a href="index.php" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
<?php require_once '../includes/layout-bottom.php'; ?>
