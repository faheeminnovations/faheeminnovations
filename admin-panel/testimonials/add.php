<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Add Testimonial';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $image = '';
    if (!empty($_FILES['image']['name'])) {
        $image = uploadFile($_FILES['image'], 'testimonials') ?? '';
    }
    $pdo->prepare("INSERT INTO testimonials (client_name,company,position,image,testimonial,rating,status,sort_order) VALUES (?,?,?,?,?,?,?,?)")
        ->execute([
            $_POST['client_name'], $_POST['company'], $_POST['position'], $image,
            $_POST['testimonial'], (int)$_POST['rating'],
            (int)($_POST['status'] ?? 1), (int)$_POST['sort_order']
        ]);
    flashMessage('success', 'Testimonial added.');
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
        <input type="text" name="client_name" required>
      </div>
      <div class="form-group">
        <label>Company</label>
        <input type="text" name="company">
      </div>
      <div class="form-group">
        <label>Position / Title</label>
        <input type="text" name="position" placeholder="CEO, Manager, Director...">
      </div>
      <div class="form-group">
        <label>Rating</label>
        <select name="rating">
          <?php for ($i = 5; $i >= 1; $i--): ?>
          <option value="<?= $i ?>" <?= $i===5?'selected':'' ?>><?= str_repeat('★',$i) ?> <?= $i ?> Star<?= $i>1?'s':'' ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <div class="form-group full">
        <label>Testimonial *</label>
        <textarea name="testimonial" rows="4" required placeholder="What the client said about working with Faheem Innovations..."></textarea>
      </div>
      <div class="form-group">
        <label>Client Photo</label>
        <input type="file" name="image" accept="image/*" data-preview="imgPreview">
        <img id="imgPreview" style="display:none;margin-top:8px;width:60px;height:60px;border-radius:50%;object-fit:cover">
        <span class="form-hint">Optional. Square image recommended.</span>
      </div>
      <div class="form-group">
        <label>Status</label>
        <select name="status">
          <option value="1">Active (visible on website)</option>
          <option value="0">Hidden</option>
        </select>
      </div>
      <div class="form-group">
        <label>Sort Order</label>
        <input type="number" name="sort_order" value="0">
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Testimonial</button>
      <a href="index.php" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
<?php require_once '../includes/layout-bottom.php'; ?>
