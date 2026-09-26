<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Edit AI Tool';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM ai_tools WHERE id = ?");
$stmt->execute([$id]);
$tool = $stmt->fetch();
if (!$tool) { flashMessage('danger', 'AI Tool not found.'); redirect(ADMIN_URL . '/ai-tools/index.php'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $features = array_filter(array_map('trim', explode("\n", $_POST['features'] ?? '')));
    $image = $tool['image'];
    if (!empty($_FILES['image']['name'])) {
        $uploaded = uploadFile($_FILES['image'], 'ai-tools');
        if ($uploaded) $image = $uploaded;
    }
    $pdo->prepare("UPDATE ai_tools SET name=?,slug=?,description=?,tool_url=?,icon=?,image=?,category=?,is_free=?,price=?,features=?,button_text=?,status=?,sort_order=?,meta_title=?,meta_description=? WHERE id=?")
        ->execute([
            $_POST['name'], $_POST['slug'], $_POST['description'], $_POST['tool_url'],
            $_POST['icon'], $image, $_POST['category'],
            (int)($_POST['is_free'] ?? 1), (float)($_POST['price'] ?? 0),
            json_encode($features), $_POST['button_text'],
            (int)($_POST['status'] ?? 1), (int)$_POST['sort_order'],
            $_POST['meta_title'], $_POST['meta_description'], $id
        ]);
    flashMessage('success', 'AI Tool updated.');
    redirect(ADMIN_URL . '/ai-tools/index.php');
}

$featuresText = implode("\n", json_decode($tool['features'] ?? '[]', true));
require_once '../includes/layout-top.php';
?>
<?php showFlash(); ?>
<div class="admin-form">
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <div class="form-grid">
      <div class="form-group">
        <label>Tool Name *</label>
        <input type="text" name="name" id="name" required value="<?= e($tool['name']) ?>">
      </div>
      <div class="form-group">
        <label>Slug *</label>
        <input type="text" name="slug" id="slug" required value="<?= e($tool['slug']) ?>">
      </div>
      <div class="form-group">
        <label>Category</label>
        <input type="text" name="category" value="<?= e($tool['category']) ?>" list="categories">
        <datalist id="categories">
          <option value="Image Tools">
          <option value="Document Tools">
          <option value="Audio Tools">
          <option value="Video Tools">
        </datalist>
      </div>
      <div class="form-group">
        <label>Icon Class (FontAwesome)</label>
        <input type="text" name="icon" value="<?= e($tool['icon']) ?>">
      </div>
      <div class="form-group full">
        <label>Description</label>
        <textarea name="description" rows="3"><?= e($tool['description']) ?></textarea>
      </div>
      <div class="form-group">
        <label>Tool URL</label>
        <input type="text" name="tool_url" value="<?= e($tool['tool_url']) ?>">
      </div>
      <div class="form-group">
        <label>Button Text</label>
        <input type="text" name="button_text" value="<?= e($tool['button_text']) ?>">
      </div>
      <div class="form-group">
        <label>Free / Paid</label>
        <select name="is_free" id="isPaid" onchange="document.getElementById('priceGroup').style.display=this.value=='0'?'block':'none'">
          <option value="1" <?= $tool['is_free'] ? 'selected' : '' ?>>Free</option>
          <option value="0" <?= !$tool['is_free'] ? 'selected' : '' ?>>Paid</option>
        </select>
      </div>
      <div class="form-group" id="priceGroup" style="display:<?= $tool['is_free'] ? 'none' : 'block' ?>">
        <label>Price (USD)</label>
        <input type="number" name="price" step="0.01" value="<?= e($tool['price']) ?>">
      </div>
      <div class="form-group full">
        <label>Features (one per line)</label>
        <textarea name="features" rows="5"><?= e($featuresText) ?></textarea>
      </div>
      <div class="form-group">
        <label>Tool Image</label>
        <?php if ($tool['image']): ?>
        <img src="<?= SITE_URL ?>/<?= e($tool['image']) ?>" style="max-height:80px;border-radius:6px;margin-bottom:8px;display:block">
        <?php endif; ?>
        <input type="file" name="image" accept="image/*" data-preview="imgPreview">
        <img id="imgPreview" style="display:none;margin-top:8px;max-height:100px;border-radius:6px">
      </div>
      <div class="form-group">
        <label>Status</label>
        <select name="status">
          <option value="1" <?= $tool['status'] ? 'selected' : '' ?>>Published</option>
          <option value="0" <?= !$tool['status'] ? 'selected' : '' ?>>Draft</option>
        </select>
      </div>
      <div class="form-group">
        <label>Sort Order</label>
        <input type="number" name="sort_order" value="<?= e($tool['sort_order']) ?>">
      </div>
      <div class="form-group">
        <label>SEO Title</label>
        <input type="text" name="meta_title" value="<?= e($tool['meta_title']) ?>">
      </div>
      <div class="form-group">
        <label>SEO Description</label>
        <input type="text" name="meta_description" value="<?= e($tool['meta_description']) ?>">
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update AI Tool</button>
      <a href="index.php" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
<?php require_once '../includes/layout-bottom.php'; ?>
