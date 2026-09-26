<?php
require_once 'includes/config.php';
$msgs = [];

// Fix AI Video HD — set tool_url and activate
$pdo->exec("UPDATE ai_tools SET 
    tool_url='http://localhost/faheeminnovations/ai-tools/video-to-hd/',
    button_text='Try Free',
    is_free=0,
    status=1
WHERE slug='ai-video-hd'");
$msgs[] = '✅ AI Video HD — tool_url set, status activated';

// Show current state
$tools = $pdo->query("SELECT id, name, slug, tool_url, status FROM ai_tools ORDER BY sort_order")->fetchAll();

@unlink(__FILE__);
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Fixed</title>
<style>body{font-family:Inter,sans-serif;max-width:700px;margin:60px auto;padding:0 20px}h2{color:#0B3C33}.box{background:#ecfdf5;border:1px solid #6ee7b7;border-radius:8px;padding:14px 18px;margin-bottom:20px}table{width:100%;border-collapse:collapse;margin-top:16px}th,td{padding:9px 14px;border:1px solid #ddd;font-size:.85rem;text-align:left}th{background:#0B3C33;color:#fff}tr:nth-child(even){background:#f9f9f9}a{color:#0B3C33;font-weight:600}</style>
</head><body>
<h2>✅ Tools Fixed</h2>
<div class="box"><ul style="line-height:2"><?php foreach($msgs as $m): ?><li><?= $m ?></li><?php endforeach; ?></ul>
<p style="font-size:.8rem;color:#065f46;margin:4px 0 0">🗑️ File deleted.</p></div>
<table>
  <thead><tr><th>ID</th><th>Name</th><th>Tool URL</th><th>Status</th></tr></thead>
  <tbody>
    <?php foreach($tools as $t): ?>
    <tr>
      <td><?= $t['id'] ?></td>
      <td><?= htmlspecialchars($t['name']) ?></td>
      <td style="font-size:.78rem;color:<?= $t['tool_url'] ? '#16a34a' : '#dc2626' ?>"><?= $t['tool_url'] ? htmlspecialchars($t['tool_url']) : '❌ EMPTY' ?></td>
      <td><?= $t['status'] ? '✅ Active' : '❌ Hidden' ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<p style="margin-top:20px">
  <a href="http://localhost/faheeminnovations/ai-tools">→ View AI Tools Page</a> &nbsp;|&nbsp;
  <a href="http://localhost/faheeminnovations/user/dashboard">→ My Dashboard</a>
</p>
</body></html>
