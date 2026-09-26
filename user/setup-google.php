<?php
require_once '../includes/config.php';
$msgs = [];

// Add google_id column
try {
    $pdo->exec("ALTER TABLE tool_users ADD COLUMN google_id VARCHAR(100) NULL UNIQUE AFTER email");
    $msgs[] = '✅ google_id column added';
} catch(Exception $e) { $msgs[] = 'ℹ️ google_id already exists'; }

// Add avatar column
try {
    $pdo->exec("ALTER TABLE tool_users ADD COLUMN avatar VARCHAR(255) NULL AFTER google_id");
    $msgs[] = '✅ avatar column added';
} catch(Exception $e) { $msgs[] = 'ℹ️ avatar already exists'; }

@unlink(__FILE__);
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Done</title>
<style>body{font-family:Inter,sans-serif;max-width:500px;margin:60px auto;padding:0 20px}h2{color:#0B3C33}.box{background:#ecfdf5;border:1px solid #6ee7b7;border-radius:8px;padding:16px 20px}ul{line-height:2.2}a{color:#0B3C33;font-weight:600}</style>
</head><body>
<h2>✅ Google OAuth Setup Done</h2>
<div class="box"><ul><?php foreach($msgs as $m): ?><li><?= $m ?></li><?php endforeach; ?></ul>
<p style="font-size:.8rem;color:#065f46;margin:8px 0 0">🗑️ File deleted.</p></div>
<p style="margin-top:16px">
  <a href="http://localhost/faheeminnovations/user/login">→ Test Google Login</a>
</p>
</body></html>
