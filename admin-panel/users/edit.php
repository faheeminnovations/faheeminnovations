<?php
require_once '../includes/auth.php';
requireRole('super_admin');
require_once '../includes/crud.php';
$adminTitle = 'Edit User';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) { flashMessage('danger','User not found.'); redirect(ADMIN_URL.'/users/index.php'); }

$roles = $pdo->query("SELECT * FROM roles ORDER BY id")->fetchAll();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $role_id = (int)($_POST['role_id'] ?? 2);
    $status  = (int)($_POST['status'] ?? 1);
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (!$name || !$email) {
        $error = 'Name and email are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } elseif ($password && strlen($password) < 8) {
        $error = 'New password must be at least 8 characters.';
    } elseif ($password && $password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        // Check email uniqueness (excluding self)
        $chk = $pdo->prepare("SELECT id FROM users WHERE email=? AND id!=?");
        $chk->execute([$email, $id]);
        if ($chk->fetchColumn()) {
            $error = 'Another account already uses this email.';
        } else {
            if ($password) {
                $pdo->prepare("UPDATE users SET name=?,email=?,role_id=?,status=?,password=? WHERE id=?")
                    ->execute([$name, $email, $role_id, $status, password_hash($password, PASSWORD_DEFAULT), $id]);
            } else {
                $pdo->prepare("UPDATE users SET name=?,email=?,role_id=?,status=? WHERE id=?")
                    ->execute([$name, $email, $role_id, $status, $id]);
            }
            flashMessage('success', 'User updated.');
            redirect(ADMIN_URL . '/users/index.php');
        }
    }
}
require_once '../includes/layout-top.php';
?>
<?php if ($error): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= e($error) ?></div><?php endif; ?>
<div class="admin-form" style="max-width:560px">
  <form method="POST">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <div class="form-group">
      <label>Full Name *</label>
      <input type="text" name="name" required value="<?= e($user['name']) ?>">
    </div>
    <div class="form-group">
      <label>Email Address *</label>
      <input type="email" name="email" required value="<?= e($user['email']) ?>">
    </div>
    <div class="form-group">
      <label>Role *</label>
      <select name="role_id">
        <?php foreach ($roles as $r): ?>
        <option value="<?= $r['id'] ?>" <?= $user['role_id']==$r['id']?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$r['name'])) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label>Status</label>
      <select name="status">
        <option value="1" <?= $user['status']?'selected':'' ?>>Active</option>
        <option value="0" <?= !$user['status']?'selected':'' ?>>Inactive</option>
      </select>
    </div>
    <div style="background:#f8fafb;border:1px solid var(--border);border-radius:8px;padding:16px;margin-bottom:20px">
      <p style="font-size:0.82rem;font-weight:700;margin-bottom:12px;color:var(--text-light)">CHANGE PASSWORD (leave blank to keep current)</p>
      <div class="form-group" style="margin-bottom:12px">
        <label>New Password</label>
        <input type="password" name="password" minlength="8" placeholder="Min 8 characters">
      </div>
      <div class="form-group" style="margin-bottom:0">
        <label>Confirm New Password</label>
        <input type="password" name="confirm_password">
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update User</button>
      <a href="index.php" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
<?php require_once '../includes/layout-bottom.php'; ?>
