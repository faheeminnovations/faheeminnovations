<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Edit Service';
$id = (int)($_GET['id'] ?? 0);
$service = $pdo->prepare("SELECT * FROM services WHERE id = ?");
$service->execute([$id]);
$service = $service->fetch();
if (!$service) { flashMessage('danger','Service not found.'); redirect(ADMIN_URL.'/services/index.php'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $features = array_filter(array_map('trim', explode("\n", $_POST['features'] ?? '')));
    $pdo->prepare("UPDATE services SET name=?,slug=?,short_description=?,full_description=?,icon=?,features=?,cta_text=?,cta_url=?,meta_title=?,meta_description=?,status=?,sort_order=? WHERE id=?")
        ->execute([$_POST['name'],$_POST['slug'],$_POST['short_description'],$_POST['full_description'],$_POST['icon'],json_encode($features),$_POST['cta_text'],$_POST['cta_url'],$_POST['meta_title'],$_POST['meta_description'],(int)($_POST['status']??0),(int)$_POST['sort_order'],$id]);
    flashMessage('success','Service updated.');
    redirect(ADMIN_URL.'/services/index.php');
}
$featuresText = implode("\n", json_decode($service['features'] ?? '[]', true));
require_once '../includes/layout-top.php';
?>
<?php showFlash(); ?>
<div class="admin-form">
  <form method="POST">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <div class="form-grid">
      <div class="form-group"><label>Service Name *</label><input type="text" name="name" id="name" required value="<?= e($service['name']) ?>"></div>
      <div class="form-group"><label>Slug *</label><input type="text" name="slug" id="slug" required value="<?= e($service['slug']) ?>"></div>
      <div class="form-group"><label>Icon Class</label><input type="text" name="icon" value="<?= e($service['icon']) ?>"></div>
      <div class="form-group"><label>Sort Order</label><input type="number" name="sort_order" value="<?= e($service['sort_order']) ?>"></div>
      <div class="form-group full"><label>Short Description</label><textarea name="short_description" rows="2"><?= e($service['short_description']) ?></textarea></div>
      <div class="form-group full"><label>Full Description</label><textarea name="full_description" rows="4"><?= e($service['full_description']) ?></textarea></div>
      <div class="form-group full"><label>Features (one per line)</label><textarea name="features" rows="6"><?= e($featuresText) ?></textarea></div>
      <div class="form-group"><label>CTA Text</label><input type="text" name="cta_text" value="<?= e($service['cta_text']) ?>"></div>
      <div class="form-group"><label>CTA URL</label><input type="text" name="cta_url" value="<?= e($service['cta_url']) ?>"></div>
      <div class="form-group"><label>SEO Title</label><input type="text" name="meta_title" value="<?= e($service['meta_title']) ?>"></div>
      <div class="form-group"><label>SEO Description</label><input type="text" name="meta_description" value="<?= e($service['meta_description']) ?>"></div>
      <div class="form-group"><label>Status</label><select name="status"><option value="1" <?= $service['status']?'selected':'' ?>>Published</option><option value="0" <?= !$service['status']?'selected':'' ?>>Draft</option></select></div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Service</button>
      <a href="index.php" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
<?php require_once '../includes/layout-bottom.php'; ?>
