<?php
// ONE-TIME — auto-deletes after run
require_once 'includes/config.php';

// Get Services sort_order
$servicesOrder = (int)$pdo->query("SELECT sort_order FROM menu_items WHERE label='Services' AND parent_id=0 LIMIT 1")->fetchColumn();

// Shift all items after Services up by 1 to make room
$pdo->prepare("UPDATE menu_items SET sort_order = sort_order + 1 WHERE sort_order > ? AND parent_id = 0")
    ->execute([$servicesOrder]);

// Set Products right after Services
$pdo->prepare("UPDATE menu_items SET sort_order = ? WHERE label = 'Products' AND parent_id = 0")
    ->execute([$servicesOrder + 1]);

// Show result
$rows = $pdo->query("SELECT id, label, url, parent_id, sort_order FROM menu_items ORDER BY parent_id, sort_order")->fetchAll();
@unlink(__FILE__);
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Done</title>
<style>body{font-family:Inter,sans-serif;max-width:680px;margin:60px auto;padding:0 20px}h2{color:#0B3C33}table{width:100%;border-collapse:collapse;margin-top:16px}th,td{padding:9px 14px;border:1px solid #ddd;font-size:.85rem;text-align:left}th{background:#0B3C33;color:#fff}tr:nth-child(even){background:#f9f9f9}.sub td:nth-child(2){padding-left:28px;color:#1d4ed8}.box{background:#ecfdf5;border:1px solid #6ee7b7;border-radius:8px;padding:14px 18px;margin-bottom:20px}</style>
</head><body>
<h2>✅ Done</h2>
<div class="box">Products moved after Services. 🗑️ File deleted.</div>
<table>
  <thead><tr><th>ID</th><th>Label</th><th>URL</th><th>parent_id</th><th>Order</th></tr></thead>
  <tbody>
    <?php foreach ($rows as $r): ?>
    <tr class="<?= $r['parent_id'] > 0 ? 'sub' : '' ?>">
      <td><?= $r['id'] ?></td>
      <td><?= $r['parent_id'] > 0 ? '↳ ' : '' ?><?= htmlspecialchars($r['label']) ?></td>
      <td style="font-size:.8rem"><?= htmlspecialchars($r['url']) ?></td>
      <td><?= $r['parent_id'] ?: '—' ?></td>
      <td><?= $r['sort_order'] ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</body></html>
