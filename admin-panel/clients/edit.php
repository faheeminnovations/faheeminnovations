<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Edit Client';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM clients WHERE id=?");
$stmt->execute([$id]);
$client = $stmt->fetch();
if (!$client) { flashMessage('danger','Client not found.'); redirect(ADMIN_URL.'/clients/index.php'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $logo = $client['logo'];
    if (!empty($_FILES['logo']['name'])) {
        $uploaded = uploadFile($_FILES['logo'], 'clients');
        if ($uploaded) $logo = $uploaded;
    }
    // Remove logo if requested
    if (isset($_POST['remove_logo'])) $logo = '';
    $pdo->prepare("UPDATE clients SET name=?,logo=?,website=?,industry=?,status=?,sort_order=? WHERE id=?")
        ->execute([
            trim($_POST['name']), $logo,
            trim($_POST['website']), trim($_POST['industry']),
            (int)($_POST['status'] ?? 1), (int)$_POST['sort_order'], $id
        ]);
    flashMessage('success', 'Client updated.');
    redirect(ADMIN_URL . '/clients/index.php');
}
require_once '../includes/layout-top.php';
?>
<div class="admin-form" style="max-width:600px">
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <div class="form-grid">
      <div class="form-group">
        <label>Client / Company Name *</label>
        <input type="text" name="name" required value="<?= e($client['name']) ?>">
      </div>
      <div class="form-group">
        <label>Industry</label>
        <input type="text" name="industry" value="<?= e($client['industry']) ?>">
      </div>
      <div class="form-group full">
        <label>Website URL</label>
        <input type="url" name="website" value="<?= e($client['website']) ?>">
      </div>
      <div class="form-group full">
        <label>Client Logo</label>
        <?php if ($client['logo']): ?>
        <div style="margin-bottom:10px;display:flex;align-items:center;gap:12px">
          <img src="<?= SITE_URL ?>/<?= e($client['logo']) ?>" style="max-height:50px;object-fit:contain;border:1px solid var(--border);border-radius:6px;padding:4px 8px;background:#fff">
          <label style="display:flex;align-items:center;gap:6px;font-size:0.82rem;font-weight:500;cursor:pointer">
            <input type="checkbox" name="remove_logo" value="1"> Remove current logo
          </label>
        </div>
        <?php endif; ?>
        <input type="file" name="logo" accept="image/*" data-preview="logoPreview">
        <img id="logoPreview" style="display:none;margin-top:10px;max-height:60px;object-fit:contain;border:1px solid var(--border);border-radius:6px;padding:4px 8px;background:#fff">
        <span class="form-hint">Upload to replace current logo. PNG with transparent background recommended.</span>
      </div>
      <div class="form-group">
        <label>Status</label>
        <select name="status">
          <option value="1" <?= $client['status'] ? 'selected' : '' ?>>Visible on website</option>
          <option value="0" <?= !$client['status'] ? 'selected' : '' ?>>Hidden</option>
        </select>
      </div>
      <div class="form-group">
        <label>Sort Order</label>
        <input type="number" name="sort_order" value="<?= e($client['sort_order']) ?>">
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Client</button>
      <a href="index.php" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
<?php require_once '../includes/layout-bottom.php'; ?>
