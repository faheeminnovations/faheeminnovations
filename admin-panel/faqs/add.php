<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Add FAQ';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $pdo->prepare("INSERT INTO faqs (question,answer,status,sort_order) VALUES (?,?,?,?)")
        ->execute([$_POST['question'],$_POST['answer'],(int)($_POST['status']??0),(int)$_POST['sort_order']]);
    flashMessage('success','FAQ added.');
    redirect(ADMIN_URL.'/faqs/index.php');
}
require_once '../includes/layout-top.php';
?>
<div class="admin-form">
  <form method="POST">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <div class="form-group"><label>Question *</label><input type="text" name="question" required></div>
    <div class="form-group"><label>Answer *</label><textarea name="answer" rows="5" required></textarea></div>
    <div class="form-grid">
      <div class="form-group"><label>Status</label><select name="status"><option value="1">Active</option><option value="0">Hidden</option></select></div>
      <div class="form-group"><label>Sort Order</label><input type="number" name="sort_order" value="0"></div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save FAQ</button>
      <a href="index.php" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
<?php require_once '../includes/layout-bottom.php'; ?>
