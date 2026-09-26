<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Add Product';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $image = '';
    if (!empty($_FILES['image']['name'])) {
        $image = uploadFile($_FILES['image'], 'products') ?? '';
    }
    $features  = array_filter(array_map('trim', explode("\n", $_POST['features']  ?? '')));
    $techStack = array_filter(array_map('trim', explode("\n", $_POST['tech_stack'] ?? '')));
    $slug = slugify(trim($_POST['name']));
    // ensure unique slug
    $existing = $pdo->prepare("SELECT COUNT(*) FROM products WHERE slug=?"); $existing->execute([$slug]);
    if ($existing->fetchColumn()) $slug .= '-' . time();

    $pdo->prepare("INSERT INTO products (name,slug,tagline,short_description,full_description,category,icon,image,price,price_label,currency,is_featured,demo_url,purchase_url,button_text,features,tech_stack,status,sort_order,meta_title,meta_description) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")
        ->execute([
            trim($_POST['name']), $slug,
            trim($_POST['tagline']), trim($_POST['short_description']),
            trim($_POST['full_description']), trim($_POST['category']),
            trim($_POST['icon']) ?: 'fas fa-box', $image,
            (float)($_POST['price'] ?? 0), trim($_POST['price_label']),
            trim($_POST['currency']) ?: 'USD',
            isset($_POST['is_featured']) ? 1 : 0,
            trim($_POST['demo_url']), trim($_POST['purchase_url']),
            trim($_POST['button_text']) ?: 'Get Started',
            json_encode(array_values($features)),
            json_encode(array_values($techStack)),
            (int)($_POST['status'] ?? 1),
            (int)$_POST['sort_order'],
            trim($_POST['meta_title']), trim($_POST['meta_description'])
        ]);
    flashMessage('success', 'Product added successfully.');
    redirect(ADMIN_URL . '/products/index.php');
}
$nextOrder = (int)($pdo->query("SELECT MAX(sort_order) FROM products")->fetchColumn()) + 1;
require_once '../includes/layout-top.php';
?>
<div class="admin-form" style="max-width:780px">
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <div class="form-grid">

      <div class="form-group full">
        <label>Product Name *</label>
        <input type="text" name="name" required placeholder="e.g. FaheemEdu 360">
      </div>

      <div class="form-group full">
        <label>Tagline</label>
        <input type="text" name="tagline" placeholder="e.g. Cloud-Based School Management System">
      </div>

      <div class="form-group">
        <label>Category</label>
        <input type="text" name="category" placeholder="e.g. ERP Solutions, SaaS Platform">
      </div>

      <div class="form-group">
        <label>Icon <span class="form-hint">(FontAwesome class)</span></label>
        <input type="text" name="icon" value="fas fa-box" placeholder="fas fa-graduation-cap">
      </div>

      <div class="form-group full">
        <label>Short Description *</label>
        <textarea name="short_description" rows="2" placeholder="Brief one-line description shown on cards"></textarea>
      </div>

      <div class="form-group full">
        <label>Full Description</label>
        <textarea name="full_description" rows="5" placeholder="Detailed product description"></textarea>
      </div>

      <div class="form-group full">
        <label>Product Image</label>
        <input type="file" name="image" accept="image/*" data-preview="imgPreview">
        <img id="imgPreview" style="display:none;margin-top:10px;max-height:140px;border-radius:8px;border:1px solid var(--border)">
        <span class="form-hint">Recommended: 1200×630px. Max 5MB.</span>
      </div>

      <div class="form-group">
        <label>Price (0 = On Request)</label>
        <input type="number" name="price" value="0" step="0.01" min="0">
      </div>

      <div class="form-group">
        <label>Price Label</label>
        <input type="text" name="price_label" value="Starting from" placeholder="Starting from / Per month">
      </div>

      <div class="form-group">
        <label>Currency</label>
        <input type="text" name="currency" value="USD" placeholder="USD / PKR">
      </div>

      <div class="form-group">
        <label>Button Text</label>
        <input type="text" name="button_text" value="Get Started" placeholder="Get Started / Buy Now">
      </div>

      <div class="form-group full">
        <label>Demo URL</label>
        <input type="url" name="demo_url" placeholder="https://demo.example.com">
      </div>

      <div class="form-group full">
        <label>Purchase / Contact URL</label>
        <input type="url" name="purchase_url" placeholder="https://... (leave empty to use Contact page)">
      </div>

      <div class="form-group full">
        <label>Key Features <span class="form-hint">One per line</span></label>
        <textarea name="features" rows="6" placeholder="Student & Parent Portal&#10;Fee Management&#10;Attendance Tracking"></textarea>
      </div>

      <div class="form-group full">
        <label>Tech Stack <span class="form-hint">One per line</span></label>
        <textarea name="tech_stack" rows="3" placeholder="Laravel 11&#10;MySQL&#10;Bootstrap 5"></textarea>
      </div>

      <div class="form-group full">
        <label>Meta Title <span class="form-hint">(SEO)</span></label>
        <input type="text" name="meta_title" placeholder="Leave empty to auto-generate">
      </div>

      <div class="form-group full">
        <label>Meta Description <span class="form-hint">(SEO)</span></label>
        <textarea name="meta_description" rows="2" placeholder="Short SEO description"></textarea>
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

      <div class="form-group">
        <label>&nbsp;</label>
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:500">
          <input type="checkbox" name="is_featured" value="1"> Mark as Featured
        </label>
      </div>

    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Product</button>
      <a href="index.php" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
<?php require_once '../includes/layout-bottom.php'; ?>
