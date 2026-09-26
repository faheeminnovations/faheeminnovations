<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Add AI Tool';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $features = array_filter(array_map('trim', explode("\n", $_POST['features'] ?? '')));
    $image = '';
    if (!empty($_FILES['image']['name'])) {
        $image = uploadFile($_FILES['image'], 'ai-tools') ?? '';
    }
    $pdo->prepare("INSERT INTO ai_tools (name,slug,description,tool_url,icon,image,category,is_free,price,features,button_text,status,sort_order,meta_title,meta_description) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")
        ->execute([
            $_POST['name'], $_POST['slug'], $_POST['description'], $_POST['tool_url'],
            $_POST['icon'], $image, $_POST['category'],
            (int)($_POST['is_free'] ?? 1), (float)($_POST['price'] ?? 0),
            json_encode($features), $_POST['button_text'],
            (int)($_POST['status'] ?? 1), (int)$_POST['sort_order'],
            $_POST['meta_title'], $_POST['meta_description']
        ]);
    flashMessage('success', 'AI Tool added successfully.');
    redirect(ADMIN_URL . '/ai-tools/index.php');
}
require_once '../includes/layout-top.php';
?>
<?php showFlash(); ?>
<div class="admin-form">
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <div class="form-grid">
      <div class="form-group">
        <label>Tool Name *</label>
        <input type="text" name="name" id="name" required>
      </div>
      <div class="form-group">
        <label>Slug *</label>
        <input type="text" name="slug" id="slug" required>
        <span class="form-hint">Used in URL. Auto-generated from name.</span>
      </div>
      <div class="form-group">
        <label>Category</label>
        <input type="text" name="category" placeholder="Image Tools, Document Tools, Audio Tools..." list="categories">
        <datalist id="categories">
          <option value="Image Tools">
          <option value="Document Tools">
          <option value="Audio Tools">
          <option value="Video Tools">
        </datalist>
      </div>
      <div class="form-group">
        <label>Icon Class (FontAwesome)</label>
        <input type="text" name="icon" value="fas fa-robot" placeholder="fas fa-robot">
      </div>
      <div class="form-group full">
        <label>Description</label>
        <textarea name="description" rows="3"></textarea>
      </div>
      <div class="form-group">
        <label>Tool URL</label>
        <input type="text" name="tool_url" placeholder="https://... or leave blank if coming soon">
      </div>
      <div class="form-group">
        <label>Button Text</label>
        <input type="text" name="button_text" value="Try Tool">
      </div>
      <div class="form-group">
        <label>Free / Paid</label>
        <select name="is_free" id="isPaid" onchange="document.getElementById('priceGroup').style.display=this.value=='0'?'block':'none'">
          <option value="1">Free</option>
          <option value="0">Paid</option>
        </select>
      </div>
      <div class="form-group" id="priceGroup" style="display:none">
        <label>Price (PKR)</label>
        <input type="number" name="price" step="0.01" value="0">
      </div>
      <div class="form-group full">
        <label>Features (one per line)</label>
        <textarea name="features" rows="5" placeholder="Feature 1&#10;Feature 2&#10;Feature 3"></textarea>
      </div>
      <div class="form-group">
        <label>Tool Image</label>
        <input type="file" name="image" accept="image/*" data-preview="imgPreview">
        <img id="imgPreview" style="display:none;margin-top:8px;max-height:100px;border-radius:6px">
      </div>
      <div class="form-group">
        <label>Status</label>
        <select name="status">
          <option value="1">Published</option>
          <option value="0">Draft</option>
        </select>
      </div>
      <div class="form-group">
        <label>Sort Order</label>
        <input type="number" name="sort_order" value="0">
      </div>
      <div class="form-group">
        <label>SEO Title</label>
        <input type="text" name="meta_title">
      </div>
      <div class="form-group">
        <label>SEO Description</label>
        <input type="text" name="meta_description">
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save AI Tool</button>
      <a href="index.php" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
<?php require_once '../includes/layout-bottom.php'; ?>
