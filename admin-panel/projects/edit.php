<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Edit Project';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$id]);
$project = $stmt->fetch();
if (!$project) { flashMessage('danger', 'Project not found.'); redirect(ADMIN_URL . '/projects/index.php'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $technologies = array_filter(array_map('trim', explode("\n", $_POST['technologies'] ?? '')));
    $image = $project['main_image'];
    if (!empty($_FILES['main_image']['name'])) {
        $uploaded = uploadFile($_FILES['main_image'], 'projects');
        if ($uploaded) $image = $uploaded;
    }
    $pdo->prepare("UPDATE projects SET name=?,slug=?,client=?,category=?,short_description=?,full_description=?,technologies=?,main_image=?,project_url=?,completion_date=?,is_featured=?,status=?,sort_order=?,meta_title=?,meta_description=? WHERE id=?")
        ->execute([
            $_POST['name'], $_POST['slug'], $_POST['client'], $_POST['category'],
            $_POST['short_description'], $_POST['full_description'],
            json_encode($technologies), $image, $_POST['project_url'],
            $_POST['completion_date'] ?: null,
            (int)($_POST['is_featured'] ?? 0), (int)($_POST['status'] ?? 1),
            (int)$_POST['sort_order'], $_POST['meta_title'], $_POST['meta_description'], $id
        ]);
    flashMessage('success', 'Project updated.');
    redirect(ADMIN_URL . '/projects/index.php');
}

$techText = implode("\n", json_decode($project['technologies'] ?? '[]', true));
require_once '../includes/layout-top.php';
?>
<?php showFlash(); ?>
<div class="admin-form">
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <div class="form-grid">
      <div class="form-group">
        <label>Project Name *</label>
        <input type="text" name="name" id="name" required value="<?= e($project['name']) ?>">
      </div>
      <div class="form-group">
        <label>Slug *</label>
        <input type="text" name="slug" id="slug" required value="<?= e($project['slug']) ?>">
      </div>
      <div class="form-group">
        <label>Client Name</label>
        <input type="text" name="client" value="<?= e($project['client']) ?>">
      </div>
      <div class="form-group">
        <label>Category</label>
        <input type="text" name="category" value="<?= e($project['category']) ?>" list="catList">
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
        <textarea name="short_description" rows="2"><?= e($project['short_description']) ?></textarea>
      </div>
      <div class="form-group full">
        <label>Full Description</label>
        <textarea name="full_description" rows="6"><?= e($project['full_description']) ?></textarea>
      </div>
      <div class="form-group full">
        <label>Technologies Used (one per line)</label>
        <textarea name="technologies" rows="4"><?= e($techText) ?></textarea>
      </div>
      <div class="form-group">
        <label>Main Image</label>
        <?php if ($project['main_image']): ?>
        <img src="<?= SITE_URL ?>/<?= e($project['main_image']) ?>" style="max-height:80px;border-radius:6px;margin-bottom:8px;display:block">
        <?php endif; ?>
        <input type="file" name="main_image" accept="image/*" data-preview="imgPreview">
        <img id="imgPreview" style="display:none;margin-top:8px;max-height:120px;border-radius:6px">
        <span class="form-hint">Upload new image to replace current.</span>
      </div>
      <div class="form-group">
        <label>Project URL (live link)</label>
        <input type="text" name="project_url" value="<?= e($project['project_url']) ?>">
      </div>
      <div class="form-group">
        <label>Completion Date</label>
        <input type="date" name="completion_date" value="<?= e($project['completion_date']) ?>">
      </div>
      <div class="form-group">
        <label>Featured Project?</label>
        <select name="is_featured">
          <option value="0" <?= !$project['is_featured'] ? 'selected' : '' ?>>No</option>
          <option value="1" <?= $project['is_featured'] ? 'selected' : '' ?>>Yes — show on homepage</option>
        </select>
      </div>
      <div class="form-group">
        <label>Status</label>
        <select name="status">
          <option value="1" <?= $project['status'] ? 'selected' : '' ?>>Published</option>
          <option value="0" <?= !$project['status'] ? 'selected' : '' ?>>Draft</option>
        </select>
      </div>
      <div class="form-group">
        <label>Sort Order</label>
        <input type="number" name="sort_order" value="<?= e($project['sort_order']) ?>">
      </div>
      <div class="form-group">
        <label>SEO Title</label>
        <input type="text" name="meta_title" value="<?= e($project['meta_title']) ?>">
      </div>
      <div class="form-group">
        <label>SEO Description</label>
        <input type="text" name="meta_description" value="<?= e($project['meta_description']) ?>">
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Project</button>
      <a href="index.php" class="btn btn-outline">Cancel</a>
      <a href="<?= SITE_URL ?>/projects/<?= e($project['slug']) ?>" target="_blank" class="btn btn-outline" style="margin-left:auto"><i class="fas fa-external-link-alt"></i> View Live</a>
    </div>
  </form>
</div>
<?php require_once '../includes/layout-bottom.php'; ?>
