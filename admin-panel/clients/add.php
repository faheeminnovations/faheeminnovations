<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Add Client';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $logo = '';
    if (!empty($_FILES['logo']['name'])) {
        $logo = uploadFile($_FILES['logo'], 'clients') ?? '';
    }
    $pdo->prepare("INSERT INTO clients (name, logo, website, industry, status, sort_order) VALUES (?,?,?,?,?,?)")
        ->execute([
            trim($_POST['name']), $logo,
            trim($_POST['website']), trim($_POST['industry']),
            (int)($_POST['status'] ?? 1), (int)$_POST['sort_order']
        ]);
    flashMessage('success', 'Client added.');
    redirect(ADMIN_URL . '/clients/index.php');
}
$nextOrder = (int)($pdo->query("SELECT MAX(sort_order) FROM clients")->fetchColumn()) + 1;
require_once '../includes/layout-top.php';
?>
<div class="admin-form" style="max-width:600px">
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <div class="form-grid">
      <div class="form-group">
        <label>Client / Company Name *</label>
        <input type="text" name="name" required placeholder="e.g. TechCorp Solutions">
      </div>
      <div class="form-group">
        <label>Industry</label>
        <input type="text" name="industry" placeholder="e.g. Technology, Healthcare, E-Commerce">
      </div>
      <div class="form-group full">
        <label>Website URL</label>
        <input type="url" name="website" placeholder="https://example.com">
      </div>
      <div class="form-group full">
        <label>Client Logo</label>
        <input type="file" name="logo" accept="image/*" data-preview="logoPreview">
        <img id="logoPreview" style="display:none;margin-top:10px;max-height:60px;object-fit:contain;border:1px solid var(--border);border-radius:6px;padding:4px 8px;background:#fff">
        <span class="form-hint">PNG with transparent background recommended. Max 5MB.</span>
      </div>
      <div class="form-group">
        <label>Status</label>
        <select name="status">
          <option value="1">Visible on website</option>
          <option value="0">Hidden</option>
        </select>
      </div>
      <div class="form-group">
        <label>Sort Order</label>
        <input type="number" name="sort_order" value="<?= $nextOrder ?>">
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Client</button>
      <a href="index.php" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
<?php require_once '../includes/layout-bottom.php'; ?>
