<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Edit FAQ';
$id = (int)($_GET['id']??0);
$stmt = $pdo->prepare("SELECT * FROM faqs WHERE id=?"); $stmt->execute([$id]); $faq = $stmt->fetch();
if (!$faq) { flashMessage('danger','Not found.'); redirect(ADMIN_URL.'/faqs/index.php'); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $pdo->prepare("UPDATE faqs SET question=?,answer=?,status=?,sort_order=? WHERE id=?")
        ->execute([$_POST['question'],$_POST['answer'],(int)($_POST['status']??0),(int)$_POST['sort_order'],$id]);
    flashMessage('success','FAQ updated.');
    redirect(ADMIN_URL.'/faqs/index.php');
}
require_once '../includes/layout-top.php';
?>
<div class="admin-form">
  <form method="POST">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <div class="form-group"><label>Question *</label><input type="text" name="question" required value="<?= e($faq['question']) ?>"></div>
    <div class="form-group"><label>Answer *</label><textarea name="answer" rows="5" required><?= e($faq['answer']) ?></textarea></div>
    <div class="form-grid">
      <div class="form-group"><label>Status</label><select name="status"><option value="1" <?= $faq['status']?'selected':'' ?>>Active</option><option value="0" <?= !$faq['status']?'selected':'' ?>>Hidden</option></select></div>
      <div class="form-group"><label>Sort Order</label><input type="number" name="sort_order" value="<?= e($faq['sort_order']) ?>"></div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update FAQ</button>
      <a href="index.php" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
<?php require_once '../includes/layout-bottom.php'; ?>
