<?php
require_once '../includes/auth.php';
requireRole('super_admin');
require_once '../includes/crud.php';
$adminTitle = 'Add User';

$roles = $pdo->query("SELECT * FROM roles ORDER BY id")->fetchAll();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';
    $role_id  = (int)($_POST['role_id'] ?? 2);

    if (!$name || !$email || !$password) {
        $error = 'Name, email and password are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $exists = $pdo->prepare("SELECT id FROM users WHERE email=?");
        $exists->execute([$email]);
        if ($exists->fetchColumn()) {
            $error = 'An account with this email already exists.';
        } else {
            $pdo->prepare("INSERT INTO users (name,email,password,role_id,status) VALUES (?,?,?,?,1)")
                ->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $role_id]);
            flashMessage('success', 'User "' . $name . '" added successfully.');
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
      <input type="text" name="name" required value="<?= e($_POST['name'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Email Address *</label>
      <input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Role *</label>
      <select name="role_id">
        <?php foreach ($roles as $r): ?>
        <option value="<?= $r['id'] ?>" <?= ($r['id']==(int)($_POST['role_id']??2))?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$r['name'])) ?></option>
        <?php endforeach; ?>
      </select>
      <span class="form-hint">Super Admin: full access. Admin: content &amp; settings. Editor: content only.</span>
    </div>
    <div class="form-group">
      <label>Password *</label>
      <input type="password" name="password" required minlength="8">
      <span class="form-hint">Minimum 8 characters.</span>
    </div>
    <div class="form-group">
      <label>Confirm Password *</label>
      <input type="password" name="confirm_password" required>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary"><i class="fas fa-user-plus"></i> Create User</button>
      <a href="index.php" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
<?php require_once '../includes/layout-bottom.php'; ?>
