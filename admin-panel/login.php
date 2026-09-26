<?php
session_start();
require_once dirname(__DIR__) . '/includes/config.php';

if (!empty($_SESSION['admin_id'])) {
    header('Location: index.php'); exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        $stmt = $pdo->prepare("SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.email = ? AND u.status = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_id']   = $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            $_SESSION['admin_role'] = $user['role_name'];
            $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);
            header('Location: index.php'); exit;
        } else {
            $error = 'Invalid email or password.';
        }
    } else {
        $error = 'Please enter your email and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login - Faheem Innovations</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#0B3C33,#1a6b5a);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
.login-box{background:#fff;border-radius:16px;padding:48px 40px;width:100%;max-width:420px;box-shadow:0 24px 64px rgba(0,0,0,0.2)}
.login-logo{text-align:center;margin-bottom:32px}
.login-logo img{height:50px}
.login-logo h2{font-size:1.1rem;color:#6b7280;margin-top:8px;font-weight:500}
.form-group{margin-bottom:20px}
.form-group label{display:block;font-size:0.85rem;font-weight:600;margin-bottom:6px;color:#374151}
.form-group input{width:100%;padding:12px 16px;border:1px solid #e5e7eb;border-radius:8px;font-family:'Inter',sans-serif;font-size:0.95rem;transition:0.3s}
.form-group input:focus{outline:none;border-color:#0B3C33;box-shadow:0 0 0 3px rgba(11,60,51,0.08)}
.btn-login{width:100%;padding:13px;background:#0B3C33;color:#fff;border:none;border-radius:8px;font-family:'Inter',sans-serif;font-size:1rem;font-weight:600;cursor:pointer;transition:0.3s}
.btn-login:hover{background:#072e27}
.error{background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:12px 16px;border-radius:8px;font-size:0.9rem;margin-bottom:20px}
.forgot-link{text-align:center;margin-top:16px;font-size:0.85rem}
.forgot-link a{color:#0B3C33;font-weight:500}
</style>
</head>
<body>
<div class="login-box">
  <div class="login-logo">
    <img src="<?= SITE_URL ?>/images/main-logo.svg" alt="Faheem Innovations" style="height:50px;width:auto">
    <h2>Admin Panel</h2>
  </div>
  <?php if ($error): ?><div class="error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>
  <form method="POST">
    <div class="form-group">
      <label>Email Address</label>
      <input type="email" name="email" required autofocus value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" required>
    </div>
    <button type="submit" class="btn-login">Sign In <i class="fas fa-arrow-right"></i></button>
  </form>
  <div class="forgot-link"><a href="forgot-password.php">Forgot password?</a></div>
</div>
</body>
</html>
