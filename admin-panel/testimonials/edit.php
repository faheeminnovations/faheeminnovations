<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Edit Testimonial';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM testimonials WHERE id = ?");
$stmt->execute([$id]);
$t = $stmt->fetch();
if (!$t) { flashMessage('danger', 'Not found.'); redirect(ADMIN_URL . '/testimonials/index.php'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $image = $t['image'];
    if (!empty($_FILES['image']['name'])) {
        $uploaded = uploadFile($_FILES['image'], 'testimonials');
        if ($uploaded) $image = $uploaded;
    }
    $pdo->prepare("UPDATE testimonials SET client_name=?,company=?,position=?,image=?,testimonial=?,rating=?,status=?,sort_order=? WHERE id=?")
        ->execute([
            $_POST['client_name'], $_POST['company'], $_POST['position'], $image,
            $_POST['testimonial'], (int)$_POST['rating'],
            (int)($_POST['status'] ?? 1), (int)$_POST['sort_order'], $id
        ]);
    flashMessage('success', 'Testimonial updated.');
    redirect(ADMIN_URL . '/testimonials/index.php');
}
require_once '../includes/layout-top.php';
?>
<div class="admin-form">
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <div class="form-grid">
      <div class="form-group">
        <label>Client Name *</label>
        <input type="text" name="client_name" required value="<?= e($t['client_name']) ?>">
      </div>
      <div class="form-group">
        <label>Company</label>
        <input type="text" name="company" value="<?= e($t['company']) ?>">
      </div>
      <div class="form-group">
        <label>Position / Title</label>
        <input type="text" name="position" value="<?= e($t['position']) ?>">
      </div>
      <div class="form-group">
        <label>Rating</label>
        <select name="rating">
          <?php for ($i = 5; $i >= 1; $i--): ?>
          <option value="<?= $i ?>" <?= (int)$t['rating']===$i?'selected':'' ?>><?= str_repeat('★',$i) ?> <?= $i ?> Star<?= $i>1?'s':'' ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <div class="form-group full">
        <label>Testimonial *</label>
        <textarea name="testimonial" rows="4" required><?= e($t['testimonial']) ?></textarea>
      </div>
      <div class="form-group">
        <label>Client Photo</label>
        <?php if ($t['image']): ?>
        <img src="<?= SITE_URL ?>/<?= e($t['image']) ?>" style="width:60px;height:60px;border-radius:50%;object-fit:cover;margin-bottom:8px;display:block">
        <?php endif; ?>
        <input type="file" name="image" accept="image/*" data-preview="imgPreview">
        <img id="imgPreview" style="display:none;margin-top:8px;width:60px;height:60px;border-radius:50%;object-fit:cover">
        <span class="form-hint">Upload to replace current photo.</span>
      </div>
      <div class="form-group">
        <label>Status</label>
        <select name="status">
          <option value="1" <?= $t['status'] ? 'selected' : '' ?>>Active</option>
          <option value="0" <?= !$t['status'] ? 'selected' : '' ?>>Hidden</option>
        </select>
      </div>
      <div class="form-group">
        <label>Sort Order</label>
        <input type="number" name="sort_order" value="<?= e($t['sort_order']) ?>">
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Testimonial</button>
      <a href="index.php" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
<?php require_once '../includes/layout-bottom.php'; ?>
