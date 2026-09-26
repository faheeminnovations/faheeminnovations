<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Our Products';

if (isset($_GET['delete'])) { handleDelete('products', $_GET['delete']); flashMessage('success','Product deleted.'); redirect(ADMIN_URL.'/products/index.php'); }
if (isset($_GET['toggle'])) { handleStatusToggle('products', $_GET['toggle']); redirect(ADMIN_URL.'/products/index.php'); }

$products = $pdo->query("SELECT * FROM products ORDER BY sort_order, created_at DESC")->fetchAll();
require_once '../includes/layout-top.php';
?>
<?php showFlash(); ?>
<div class="table-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
  <h3>All Products (<?= count($products) ?>)</h3>
  <a href="add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Product</a>
</div>
<div class="admin-table-wrap">
  <table>
    <thead>
      <tr><th>Order</th><th>Product</th><th>Category</th><th>Price</th><th>Featured</th><th>Status</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php foreach ($products as $p): ?>
      <tr>
        <td><input type="number" value="<?= $p['sort_order'] ?>" style="width:60px;padding:4px 8px;border:1px solid var(--border);border-radius:4px;font-size:.82rem" onchange="updateOrder(<?= $p['id'] ?>,this.value)"></td>
        <td>
          <div style="display:flex;align-items:center;gap:12px">
            <div style="width:40px;height:40px;background:rgba(11,60,51,.08);border-radius:8px;display:flex;align-items:center;justify-content:center">
              <i class="<?= e($p['icon']) ?>" style="color:var(--primary)"></i>
            </div>
            <div>
              <strong><?= e($p['name']) ?></strong>
              <div style="font-size:.78rem;color:var(--text-light)"><?= e($p['tagline']) ?></div>
            </div>
          </div>
        </td>
        <td><?= e($p['category']) ?></td>
        <td><?= $p['price'] > 0 ? e($p['currency']).' '.number_format($p['price'],0) : '<span style="color:var(--text-light)">On Request</span>' ?></td>
        <td><?= $p['is_featured'] ? '<span class="badge badge-success">Yes</span>' : '—' ?></td>
        <td><span class="badge badge-<?= $p['status'] ? 'success' : 'danger' ?>"><?= $p['status'] ? 'Active' : 'Hidden' ?></span></td>
        <td class="table-actions">
          <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-outline btn-sm btn-icon" title="Edit"><i class="fas fa-edit"></i></a>
          <a href="?toggle=<?= $p['id'] ?>" class="btn btn-warning btn-sm btn-icon"><i class="fas fa-eye<?= $p['status'] ? '' : '-slash' ?>"></i></a>
          <a href="?delete=<?= $p['id'] ?>" class="btn btn-danger btn-sm btn-icon" data-confirm="Delete '<?= e($p['name']) ?>'?"><i class="fas fa-trash"></i></a>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$products): ?>
      <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-light)">No products yet. <a href="add.php">Add your first product</a>.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<script>
function updateOrder(id, val) {
  fetch('', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:'csrf_token=<?= csrf_token() ?>&_order=1&id='+id+'&order='+val });
}
</script>
<?php
// Handle inline sort order update
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['_order'])) {
    handleSortOrder('products', $_POST['id'], $_POST['order']);
    exit;
}
require_once '../includes/layout-bottom.php';
?>
