<?php
require_once 'includes/config.php';
$tools = $pdo->query("SELECT id, name, slug, tool_url, is_free, status FROM ai_tools ORDER BY sort_order")->fetchAll();
@unlink(__FILE__);
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Tools Check</title>
<style>body{font-family:Inter,sans-serif;max-width:800px;margin:40px auto;padding:0 20px}table{width:100%;border-collapse:collapse}th,td{padding:9px 14px;border:1px solid #ddd;font-size:.85rem;text-align:left}th{background:#0B3C33;color:#fff}tr:nth-child(even){background:#f9f9f9}.empty{color:red;font-weight:600}</style>
</head><body>
<h2>AI Tools in DB</h2>
<table><thead><tr><th>ID</th><th>Name</th><th>Slug</th><th>Tool URL</th><th>Status</th></tr></thead>
<tbody>
<?php foreach($tools as $t): ?>
<tr>
  <td><?= $t['id'] ?></td>
  <td><?= htmlspecialchars($t['name']) ?></td>
  <td><?= htmlspecialchars($t['slug']) ?></td>
  <td class="<?= empty($t['tool_url']) ? 'empty' : '' ?>"><?= $t['tool_url'] ? htmlspecialchars($t['tool_url']) : '❌ EMPTY' ?></td>
  <td><?= $t['status'] ? '✅' : '❌' ?></td>
</tr>
<?php endforeach; ?>
</tbody></table>
</body></html>
