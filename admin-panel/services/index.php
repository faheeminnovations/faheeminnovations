<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Services';

if (isset($_GET['delete'])) { handleDelete('services', $_GET['delete']); flashMessage('success','Service deleted.'); redirect(ADMIN_URL.'/services/index.php'); }
if (isset($_GET['toggle'])) { handleStatusToggle('services', $_GET['toggle']); redirect(ADMIN_URL.'/services/index.php'); }

$page = max(1, (int)($_GET['page'] ?? 1));
$result = getPaginatedResults('services', $page, 15, '1', [], 'sort_order ASC');
require_once '../includes/layout-top.php';
?>
<?php showFlash(); ?>
<div class="admin-table-wrap">
  <div class="table-header">
    <h3>All Services (<?= $result['total'] ?>)</h3>
    <a href="add.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Service</a>
  </div>
  <table>
    <thead><tr><th>#</th><th>Name</th><th>Slug</th><th>Status</th><th>Order</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($result['data'] as $row): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><i class="<?= e($row['icon']) ?>" style="color:var(--primary);margin-right:8px"></i><?= e($row['name']) ?></td>
        <td><code><?= e($row['slug']) ?></code></td>
        <td><span class="badge badge-<?= $row['status']?'success':'danger' ?>"><?= $row['status']?'Published':'Draft' ?></span></td>
        <td><?= $row['sort_order'] ?></td>
        <td class="table-actions">
          <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-outline btn-sm btn-icon"><i class="fas fa-edit"></i></a>
          <a href="?toggle=<?= $row['id'] ?>" class="btn btn-warning btn-sm btn-icon"><i class="fas fa-eye<?= $row['status']?'':'-slash' ?>"></i></a>
          <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm btn-icon" data-confirm="Delete this service?"><i class="fas fa-trash"></i></a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php paginationLinks($result, '?'); ?>
</div>
<?php require_once '../includes/layout-bottom.php'; ?>
