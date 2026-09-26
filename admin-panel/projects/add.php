<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Add Project';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $technologies = array_filter(array_map('trim', explode("\n", $_POST['technologies'] ?? '')));
    $image = '';
    if (!empty($_FILES['main_image']['name'])) {
        $image = uploadFile($_FILES['main_image'], 'projects') ?? '';
    }
    $pdo->prepare("INSERT INTO projects (name,slug,client,category,short_description,full_description,technologies,main_image,project_url,completion_date,is_featured,status,sort_order,meta_title,meta_description) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")
        ->execute([
            $_POST['name'], $_POST['slug'], $_POST['client'], $_POST['category'],
            $_POST['short_description'], $_POST['full_description'],
            json_encode($technologies), $image, $_POST['project_url'],
            $_POST['completion_date'] ?: null,
            (int)($_POST['is_featured'] ?? 0), (int)($_POST['status'] ?? 1),
            (int)$_POST['sort_order'], $_POST['meta_title'], $_POST['meta_description']
        ]);
    flashMessage('success', 'Project added successfully.');
    redirect(ADMIN_URL . '/projects/index.php');
}
require_once '../includes/layout-top.php';
?>
<?php showFlash(); ?>
<div class="admin-form">
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <div class="form-grid">
      <div class="form-group">
        <label>Project Name *</label>
        <input type="text" name="name" id="name" required>
      </div>
      <div class="form-group">
        <label>Slug *</label>
        <input type="text" name="slug" id="slug" required>
        <span class="form-hint">Used in URL. Auto-generated from name.</span>
      </div>
      <div class="form-group">
        <label>Client Name</label>
        <input type="text" name="client">
      </div>
      <div class="form-group">
        <label>Category</label>
        <input type="text" name="category" list="catList" placeholder="Web Development, Software, AI...">
        <datalist id="catList">
          <option value="Web Development">
          <option value="Software">
          <option value="AI">
          <option value="Business Solutions">
          <option value="Custom Applications">
        </datalist>
      </div>
      <div class="form-group full">
        <label>Short Description</label>
        <textarea name="short_description" rows="2" placeholder="Brief summary shown in listing cards..."></textarea>
      </div>
      <div class="form-group full">
        <label>Full Description</label>
        <textarea name="full_description" rows="6" placeholder="Detailed project description for the project detail page..."></textarea>
      </div>
      <div class="form-group full">
        <label>Technologies Used (one per line)</label>
        <textarea name="technologies" rows="4" placeholder="PHP&#10;MySQL&#10;JavaScript&#10;Bootstrap"></textarea>
      </div>
      <div class="form-group">
        <label>Main Image</label>
        <input type="file" name="main_image" accept="image/*" data-preview="imgPreview">
        <img id="imgPreview" style="display:none;margin-top:8px;max-height:120px;border-radius:6px">
      </div>
      <div class="form-group">
        <label>Project URL (live link)</label>
        <input type="text" name="project_url" placeholder="https://...">
      </div>
      <div class="form-group">
        <label>Completion Date</label>
        <input type="date" name="completion_date">
      </div>
      <div class="form-group">
        <label>Featured Project?</label>
        <select name="is_featured">
          <option value="0">No</option>
          <option value="1">Yes — show on homepage</option>
        </select>
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
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Project</button>
      <a href="index.php" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
<?php require_once '../includes/layout-bottom.php'; ?>
