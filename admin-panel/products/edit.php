<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Edit Product';

$id = (int)($_GET['id'] ?? 0);
$p  = $pdo->prepare("SELECT * FROM products WHERE id=?");
$p->execute([$id]);
$product = $p->fetch();
if (!$product) { flashMessage('error','Product not found.'); redirect(ADMIN_URL.'/products/index.php'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $image = $product['image'];
    if (!empty($_FILES['image']['name'])) {
        $newImg = uploadFile($_FILES['image'], 'products');
        if ($newImg) $image = $newImg;
    }
    $features  = array_filter(array_map('trim', explode("\n", $_POST['features']  ?? '')));
    $techStack = array_filter(array_map('trim', explode("\n", $_POST['tech_stack'] ?? '')));

    $pdo->prepare("UPDATE products SET name=?,tagline=?,short_description=?,full_description=?,category=?,icon=?,image=?,price=?,price_label=?,currency=?,is_featured=?,demo_url=?,purchase_url=?,button_text=?,features=?,tech_stack=?,status=?,sort_order=?,meta_title=?,meta_description=? WHERE id=?")
        ->execute([
            trim($_POST['name']), trim($_POST['tagline']),
            trim($_POST['short_description']), trim($_POST['full_description']),
            trim($_POST['category']), trim($_POST['icon']) ?: 'fas fa-box',
            $image, (float)($_POST['price'] ?? 0),
            trim($_POST['price_label']), trim($_POST['currency']) ?: 'PKR',
            isset($_POST['is_featured']) ? 1 : 0,
            trim($_POST['demo_url']), trim($_POST['purchase_url']),
            trim($_POST['button_text']) ?: 'Get Started',
            json_encode(array_values($features)),
            json_encode(array_values($techStack)),
            (int)($_POST['status'] ?? 1),
            (int)$_POST['sort_order'],
            trim($_POST['meta_title']), trim($_POST['meta_description']),
            $id
        ]);
    flashMessage('success', 'Product updated.');
    redirect(ADMIN_URL . '/products/index.php');
}

// Decode features/tech for textarea
$featuresText  = implode("\n", json_decode($product['features']   ?? '[]', true) ?: []);
$techStackText = implode("\n", json_decode($product['tech_stack'] ?? '[]', true) ?: []);
require_once '../includes/layout-top.php';
?>
<div class="admin-form" style="max-width:780px">
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <div class="form-grid">

      <div class="form-group full">
        <label>Product Name *</label>
        <input type="text" name="name" required value="<?= e($product['name']) ?>">
      </div>

      <div class="form-group full">
        <label>Tagline</label>
        <input type="text" name="tagline" value="<?= e($product['tagline']) ?>">
      </div>

      <div class="form-group">
        <label>Category</label>
        <input type="text" name="category" value="<?= e($product['category']) ?>">
      </div>

      <div class="form-group">
        <label>Icon <span class="form-hint">(FontAwesome class)</span></label>
        <input type="text" name="icon" value="<?= e($product['icon']) ?>">
      </div>

      <div class="form-group full">
        <label>Short Description</label>
        <textarea name="short_description" rows="2"><?= e($product['short_description']) ?></textarea>
      </div>

      <div class="form-group full">
        <label>Full Description</label>
        <textarea name="full_description" rows="5"><?= e($product['full_description']) ?></textarea>
      </div>

      <div class="form-group full">
        <label>Product Image</label>
        <?php if ($product['image']): ?>
        <img src="<?= SITE_URL ?>/<?= e($product['image']) ?>" style="display:block;max-height:120px;margin-bottom:10px;border-radius:8px;border:1px solid var(--border)">
        <?php endif; ?>
        <input type="file" name="image" accept="image/*" data-preview="imgPreview">
        <img id="imgPreview" style="display:none;margin-top:10px;max-height:120px;border-radius:8px;border:1px solid var(--border)">
        <span class="form-hint">Leave empty to keep current image.</span>
      </div>

      <div class="form-group">
        <label>Price (0 = On Request)</label>
        <input type="number" name="price" value="<?= $product['price'] ?>" step="0.01" min="0">
      </div>

      <div class="form-group">
        <label>Price Label</label>
        <input type="text" name="price_label" value="<?= e($product['price_label']) ?>">
      </div>

      <div class="form-group">
        <label>Currency</label>
        <input type="text" name="currency" value="<?= e($product['currency']) ?>">
      </div>

      <div class="form-group">
        <label>Button Text</label>
        <input type="text" name="button_text" value="<?= e($product['button_text']) ?>">
      </div>

      <div class="form-group full">
        <label>Demo URL</label>
        <input type="url" name="demo_url" value="<?= e($product['demo_url']) ?>">
      </div>

      <div class="form-group full">
        <label>Purchase / Contact URL</label>
        <input type="url" name="purchase_url" value="<?= e($product['purchase_url']) ?>">
      </div>

      <div class="form-group full">
        <label>Key Features <span class="form-hint">One per line</span></label>
        <textarea name="features" rows="6"><?= e($featuresText) ?></textarea>
      </div>

      <div class="form-group full">
        <label>Tech Stack <span class="form-hint">One per line</span></label>
        <textarea name="tech_stack" rows="3"><?= e($techStackText) ?></textarea>
      </div>

      <div class="form-group full">
        <label>Meta Title</label>
        <input type="text" name="meta_title" value="<?= e($product['meta_title']) ?>">
      </div>

      <div class="form-group full">
        <label>Meta Description</label>
        <textarea name="meta_description" rows="2"><?= e($product['meta_description']) ?></textarea>
      </div>

      <div class="form-group">
        <label>Status</label>
        <select name="status">
          <option value="1" <?= $product['status'] ? 'selected' : '' ?>>Visible on website</option>
          <option value="0" <?= !$product['status'] ? 'selected' : '' ?>>Hidden</option>
        </select>
      </div>

      <div class="form-group">
        <label>Sort Order</label>
        <input type="number" name="sort_order" value="<?= $product['sort_order'] ?>">
      </div>

      <div class="form-group">
        <label>&nbsp;</label>
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:500">
          <input type="checkbox" name="is_featured" value="1" <?= $product['is_featured'] ? 'checked' : '' ?>> Mark as Featured
        </label>
      </div>

    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Product</button>
      <a href="index.php" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
<?php require_once '../includes/layout-bottom.php'; ?>
