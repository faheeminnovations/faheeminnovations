<?php
require_once 'includes/config.php';
$tools = $pdo->query("SELECT id, name, slug, category, is_free, price FROM ai_tools ORDER BY sort_order")->fetchAll();
@unlink(__FILE__);
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>AI Tools</title>
<style>body{font-family:Inter,sans-serif;max-width:700px;margin:40px auto;padding:0 20px}table{width:100%;border-collapse:collapse}th,td{padding:9px 14px;border:1px solid #ddd;font-size:.85rem;text-align:left}th{background:#0B3C33;color:#fff}tr:nth-child(even){background:#f9f9f9}</style>
</head><body>
<h2>AI Tools in DB</h2>
<table><thead><tr><th>ID</th><th>Name</th><th>Slug</th><th>Category</th><th>Free?</th><th>Price</th></tr></thead>
<tbody>
<?php foreach($tools as $t): ?>
<tr><td><?=$t['id']?></td><td><?=htmlspecialchars($t['name'])?></td><td><?=htmlspecialchars($t['slug'])?></td><td><?=htmlspecialchars($t['category'])?></td><td><?=$t['is_free']?'✅':'❌'?></td><td><?=$t['price']?></td></tr>
<?php endforeach; ?>
<?php if(!$tools): ?><tr><td colspan="6" style="text-align:center;color:#888">No tools found</td></tr><?php endif; ?>
</tbody></table>
</body></html>
