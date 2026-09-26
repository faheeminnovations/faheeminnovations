<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Edit Menu Item';

$id   = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id=?");
$stmt->execute([$id]);
$item = $stmt->fetch();
if (!$item) { flashMessage('error','Menu item not found.'); redirect(ADMIN_URL.'/navigation/index.php'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $pdo->prepare("UPDATE menu_items SET label=?,url=?,target=?,status=?,sort_order=? WHERE id=?")
        ->execute([
            trim($_POST['label']),
            trim($_POST['url']),
            $_POST['target'] === '_blank' ? '_blank' : '_self',
            (int)($_POST['status'] ?? 1),
            (int)$_POST['sort_order'],
            $id
        ]);
    flashMessage('success','Menu item updated.');
    redirect(ADMIN_URL.'/navigation/index.php');
}
require_once '../includes/layout-top.php';
?>
<?php showFlash(); ?>
<div class="admin-form" style="max-width:500px">
  <form method="POST">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <div class="form-grid">
      <div class="form-group full">
        <label>Label *</label>
        <input type="text" name="label" required value="<?= e($item['label']) ?>">
      </div>
      <div class="form-group full">
        <label>URL *</label>
        <input type="text" name="url" required value="<?= e($item['url']) ?>">
      </div>
      <div class="form-group">
        <label>Open In</label>
        <select name="target">
          <option value="_self"  <?= $item['target']==='_self'  ? 'selected':'' ?>>Same Tab</option>
          <option value="_blank" <?= $item['target']==='_blank' ? 'selected':'' ?>>New Tab</option>
        </select>
      </div>
      <div class="form-group">
        <label>Status</label>
        <select name="status">
          <option value="1" <?= $item['status'] ? 'selected':'' ?>>Active</option>
          <option value="0" <?= !$item['status'] ? 'selected':'' ?>>Hidden</option>
        </select>
      </div>
      <div class="form-group">
        <label>Sort Order</label>
        <input type="number" name="sort_order" value="<?= $item['sort_order'] ?>">
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
      <a href="index.php" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
<?php require_once '../includes/layout-bottom.php'; ?>
