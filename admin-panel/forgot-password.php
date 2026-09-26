<?php
session_start();
require_once dirname(__DIR__) . '/includes/config.php';

if (!empty($_SESSION['admin_id'])) { header('Location: index.php'); exit; }

$step    = 'request'; // request | reset
$success = '';
$error   = '';
$token   = $_GET['token'] ?? '';

// Step 2: Validate token
if ($token) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token=? AND reset_expires > NOW() AND status=1");
    $stmt->execute([$token]);
    $tokenUser = $stmt->fetch();
    if ($tokenUser) {
        $step = 'reset';
    } else {
        $error = 'This password reset link is invalid or has expired.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['request_reset'])) {
        $email = trim($_POST['email'] ?? '');
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email=? AND status=1");
            $stmt->execute([$email]);
            $u = $stmt->fetch();
            if ($u) {
                $resetToken   = bin2hex(random_bytes(32));
                $resetExpires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                $pdo->prepare("UPDATE users SET reset_token=?,reset_expires=? WHERE id=?")->execute([$resetToken,$resetExpires,$u['id']]);
                $resetUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/forgot-password.php?token=' . $resetToken;
                // In production, send via email. For now show the link.
                $success = 'Reset link generated. <a href="' . htmlspecialchars($resetUrl) . '" style="color:#0B3C33;font-weight:600">Click here to reset</a> (valid 1 hour).<br><small style="color:#6b7280">In production, configure SMTP to email this link.</small>';
            } else {
                // Don't reveal if email exists
                $success = 'If this email is registered, a reset link has been sent.';
            }
        }
    } elseif (isset($_POST['do_reset'])) {
        $t  = trim($_POST['token'] ?? '');
        $pw = $_POST['password'] ?? '';
        $cf = $_POST['confirm_password'] ?? '';
        if (strlen($pw) < 8) {
            $error = 'Password must be at least 8 characters.';
            $step  = 'reset';
            $token = $t;
            $tokenUser = $pdo->prepare("SELECT * FROM users WHERE reset_token=? AND reset_expires > NOW()")->execute([$t]) ? $pdo->prepare("SELECT * FROM users WHERE reset_token=?")->execute([$t]) : null;
        } elseif ($pw !== $cf) {
            $error = 'Passwords do not match.';
            $step  = 'reset';
            $token = $t;
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token=? AND reset_expires > NOW()");
            $stmt->execute([$t]);
            $u = $stmt->fetch();
            if ($u) {
                $pdo->prepare("UPDATE users SET password=?,reset_token=NULL,reset_expires=NULL WHERE id=?")->execute([password_hash($pw, PASSWORD_DEFAULT), $u['id']]);
                $success = 'Password reset successfully. <a href="login.php" style="color:#0B3C33;font-weight:600">Sign in now</a>';
                $step    = 'done';
            } else {
                $error = 'Invalid or expired token.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password - Faheem Innovations</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#0B3C33,#1a6b5a);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
.box{background:#fff;border-radius:16px;padding:40px;width:100%;max-width:400px;box-shadow:0 24px 64px rgba(0,0,0,0.2)}
.logo{text-align:center;margin-bottom:28px}
.logo img{height:44px}
.logo h2{font-size:1rem;color:#6b7280;margin-top:6px;font-weight:500}
.form-group{margin-bottom:18px}
label{display:block;font-size:0.83rem;font-weight:600;margin-bottom:5px;color:#374151}
input{width:100%;padding:11px 14px;border:1px solid #e5e7eb;border-radius:7px;font-family:'Inter',sans-serif;font-size:0.9rem;transition:0.2s}
input:focus{outline:none;border-color:#0B3C33;box-shadow:0 0 0 3px rgba(11,60,51,0.08)}
.btn{width:100%;padding:12px;background:#0B3C33;color:#fff;border:none;border-radius:7px;font-family:'Inter',sans-serif;font-size:0.95rem;font-weight:600;cursor:pointer;transition:0.2s}
.btn:hover{background:#072e27}
.alert{padding:12px 14px;border-radius:7px;font-size:0.85rem;margin-bottom:16px;line-height:1.5}
.alert-err{background:#fef2f2;border:1px solid #fecaca;color:#dc2626}
.alert-ok{background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a}
.back{text-align:center;margin-top:14px;font-size:0.83rem}<br>.back a{color:#0B3C33;font-weight:600}
</style>
</head>
<body>
<div class="box">
  <div class="logo">
    <img src="<?= SITE_URL ?>/images/main-logo.svg" alt="Faheem Innovations" style="height:44px;width:auto">
    <h2>Reset Password</h2>
  </div>

  <?php if ($error): ?><div class="alert alert-err"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>
  <?php if ($success): ?><div class="alert alert-ok"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>

  <?php if ($step === 'request' && !$success): ?>
  <p style="font-size:0.85rem;color:#6b7280;margin-bottom:18px">Enter your admin email to receive a password reset link.</p>
  <form method="POST">
    <div class="form-group">
      <label>Email Address</label>
      <input type="email" name="email" required autofocus value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>
    <button type="submit" name="request_reset" class="btn">Send Reset Link</button>
  </form>

  <?php elseif ($step === 'reset'): ?>
  <form method="POST">
    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
    <div class="form-group">
      <label>New Password</label>
      <input type="password" name="password" required minlength="8" autofocus>
    </div>
    <div class="form-group">
      <label>Confirm Password</label>
      <input type="password" name="confirm_password" required>
    </div>
    <button type="submit" name="do_reset" class="btn">Reset Password</button>
  </form>
  <?php endif; ?>

  <div class="back"><a href="login.php"><i class="fas fa-arrow-left"></i> Back to Login</a></div>
</div>
</body>
</html>
