<?php
require_once '../includes/config.php';
require_once '../includes/user-auth.php';

if (isUserLoggedIn()) { header('Location: ' . SITE_URL . '/user/dashboard.php'); exit; }

$error    = '';
$redirect = $_GET['redirect'] ?? (SITE_URL . '/user/dashboard.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = 'Email and password are required.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM tool_users WHERE email=? AND status=1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            loginUser($user);
            $pdo->prepare("UPDATE tool_users SET last_login=NOW() WHERE id=?")->execute([$user['id']]);
            $back = $_POST['redirect'] ?? SITE_URL . '/user/dashboard.php';
            header('Location: ' . $back);
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sign In — Faheem Innovations</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
<style>
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:'Inter',sans-serif;background:#f0f4f8;min-height:100vh;display:flex;flex-direction:column}
  .auth-wrap{flex:1;display:flex;align-items:center;justify-content:center;padding:40px 20px}
  .auth-card{background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,.08);width:100%;max-width:420px;padding:40px}
  .auth-logo{text-align:center;margin-bottom:28px}
  .auth-logo img{height:44px}
  .auth-logo h2{font-size:1.3rem;font-weight:700;margin-top:12px;color:#0B3C33}
  .auth-logo p{font-size:.88rem;color:#666;margin-top:4px}
  .form-group{margin-bottom:18px}
  .form-group label{display:block;font-size:.85rem;font-weight:600;color:#444;margin-bottom:6px}
  .form-group input{width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font:inherit;font-size:.92rem;transition:border-color .2s}
  .form-group input:focus{outline:none;border-color:#0B3C33}
  .btn-auth{width:100%;padding:13px;background:#0B3C33;color:#fff;border:none;border-radius:8px;font:inherit;font-size:.95rem;font-weight:600;cursor:pointer;transition:background .2s}
  .btn-auth:hover{background:#072e27}
  .alert{padding:12px 16px;border-radius:8px;font-size:.88rem;margin-bottom:18px}
  .alert-error{background:#fef2f2;border:1px solid #fca5a5;color:#b91c1c}
  .auth-footer{text-align:center;margin-top:20px;font-size:.88rem;color:#666}
  .auth-footer a{color:#0B3C33;font-weight:600;text-decoration:none}
  .divider{border:none;border-top:1px solid #e2e8f0;margin:20px 0}
  .btn-google{display:flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:11px;border:1.5px solid #e2e8f0;border-radius:8px;background:#fff;font:inherit;font-size:.92rem;font-weight:600;color:#333;cursor:pointer;transition:border-color .2s,box-shadow .2s;text-decoration:none;margin-bottom:14px}
  .btn-google:hover{border-color:#4285F4;box-shadow:0 2px 8px rgba(66,133,244,.15)}
  .or-divider{display:flex;align-items:center;gap:12px;margin-bottom:16px;font-size:.82rem;color:#94a3b8}
  .or-divider::before,.or-divider::after{content:'';flex:1;height:1px;background:#e2e8f0}
</style>
</head>
<body>
<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-logo">
      <a href="<?= SITE_URL ?>"><img src="<?= SITE_URL ?>/images/main-logo.png" alt="Faheem Innovations"></a>
      <h2>Welcome Back</h2>
      <p>Sign in to access your AI tools</p>
    </div>

    <?php if ($error): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>

    <?php if (!empty($_GET['error'])): ?>
    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <a href="<?= SITE_URL ?>/user/google-login.php?redirect=<?= urlencode($redirect) ?>" class="btn-google">
      <svg width="18" height="18" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
      Continue with Google
    </a>

    <div class="or-divider"><span>or sign in with email</span></div>

    <form method="POST">
      <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
      <div class="form-group">
        <label>Email Address</label>
        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="you@example.com" required autofocus>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="Your password" required>
      </div>
      <button type="submit" class="btn-auth">Sign In <i class="fas fa-arrow-right"></i></button>
    </form>

    <hr class="divider">
    <div class="auth-footer">
      Don't have an account? <a href="<?= SITE_URL ?>/user/register.php">Create one free</a>
    </div>
    <div class="auth-footer" style="margin-top:8px">
      <a href="<?= SITE_URL ?>"><i class="fas fa-arrow-left"></i> Back to website</a>
    </div>
  </div>
</div>
</body>
</html>
