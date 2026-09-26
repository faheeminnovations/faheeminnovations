<?php
require_once '../includes/config.php';

$msgs = [];

// Add credit cost columns to ai_tools
$cols = [
    "ALTER TABLE ai_tools ADD COLUMN credit_cost_720  INT DEFAULT 5  AFTER status",
    "ALTER TABLE ai_tools ADD COLUMN credit_cost_1080 INT DEFAULT 10 AFTER credit_cost_720",
    "ALTER TABLE ai_tools ADD COLUMN credit_cost_4k   INT DEFAULT 25 AFTER credit_cost_1080",
];
foreach ($cols as $sql) {
    try {
        $pdo->exec($sql);
        $msgs[] = '✅ Column added';
    } catch(Exception $e) {
        $msgs[] = 'ℹ️ Already exists (skipped)';
    }
}

// Set costs for ai-video-hd
$pdo->exec("UPDATE ai_tools SET credit_cost_720=5, credit_cost_1080=10, credit_cost_4k=25 WHERE slug='ai-video-hd'");
$msgs[] = '✅ AI Video HD credit costs set (720p=5, 1080p=10, 4K=25)';

// Also add pricing_plans column if missing
try {
    $pdo->exec("ALTER TABLE ai_tools ADD COLUMN pricing_plans JSON NULL AFTER features");
    $msgs[] = '✅ pricing_plans column added';
} catch(Exception $e) {
    $msgs[] = 'ℹ️ pricing_plans already exists';
}

@unlink(__FILE__);
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Fixed</title>
<style>body{font-family:Inter,sans-serif;max-width:500px;margin:60px auto;padding:0 20px}h2{color:#0B3C33}.box{background:#ecfdf5;border:1px solid #6ee7b7;border-radius:8px;padding:16px 20px}ul{line-height:2.2;font-size:.9rem}a{color:#0B3C33;font-weight:600}</style>
</head><body>
<h2>✅ Columns Fixed</h2>
<div class="box"><ul><?php foreach($msgs as $m): ?><li><?= $m ?></li><?php endforeach; ?></ul>
<p style="font-size:.8rem;color:#065f46;margin:8px 0 0">🗑️ File deleted.</p></div>
<p style="margin-top:20px">
  <a href="http://localhost/faheeminnovations/user/register">→ Try Register</a> &nbsp;|&nbsp;
  <a href="http://localhost/faheeminnovations/user/login">→ Try Login</a>
</p>
</body></html>
