<?php
// ONE-TIME DIAGNOSTIC — auto-deletes after run
require_once 'includes/config.php';
$items = $pdo->query("SELECT id, label, url, parent_id, sort_order, status FROM menu_items ORDER BY sort_order")->fetchAll();
@unlink(__FILE__);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Menu Check</title>
<style>
  body { font-family: Inter, sans-serif; max-width: 700px; margin: 60px auto; padding: 0 20px; }
  h2 { color: #0B3C33; }
  table { width: 100%; border-collapse: collapse; }
  th, td { padding: 9px 14px; border: 1px solid #ddd; font-size: 0.88rem; text-align: left; }
  th { background: #0B3C33; color: #fff; }
  tr:nth-child(even) { background: #f5f5f5; }
  .bad { background: #fef2f2 !important; color: #b91c1c; font-weight: 600; }
</style>
</head>
<body>
<h2>Current menu_items</h2>
<table>
  <thead><tr><th>ID</th><th>Label</th><th>URL</th><th>parent_id</th><th>Order</th><th>Status</th></tr></thead>
  <tbody>
    <?php foreach ($items as $r): ?>
    <tr class="<?= $r['parent_id'] > 0 ? 'bad' : '' ?>">
      <td><?= $r['id'] ?></td>
      <td><?= htmlspecialchars($r['label']) ?></td>
      <td><?= htmlspecialchars($r['url']) ?></td>
      <td><?= $r['parent_id'] > 0 ? '⚠️ '.$r['parent_id'] : '0 (top-level)' ?></td>
      <td><?= $r['sort_order'] ?></td>
      <td><?= $r['status'] ? '✅' : '❌' ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<p style="font-size:0.8rem;color:#888;margin-top:16px">⚠️ highlighted rows have parent_id > 0 (sub-items). This file deleted itself.</p>
</body>
</html>
