<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Process Steps';

if (isset($_GET['delete'])) { handleDelete('processes', $_GET['delete']); flashMessage('success','Process step deleted.'); redirect(ADMIN_URL.'/process/index.php'); }
if (isset($_GET['toggle'])) { handleStatusToggle('processes', $_GET['toggle']); redirect(ADMIN_URL.'/process/index.php'); }

$result = getPaginatedResults('processes', 1, 20, '1', [], 'sort_order ASC');
require_once '../includes/layout-top.php';
?>
<?php showFlash(); ?>
<div class="admin-table-wrap">
  <div class="table-header">
    <h3>Process Steps (<?= $result['total'] ?>)</h3>
    <a href="add.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Step</a>
  </div>
  <table>
    <thead><tr><th>Step #</th><th>Icon</th><th>Title</th><th>Status</th><th>Order</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($result['data'] as $row): ?>
      <tr>
        <td><div style="width:36px;height:36px;background:var(--primary);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700"><?= $row['step_number'] ?></div></td>
        <td><i class="<?= e($row['icon']) ?>" style="color:var(--primary);font-size:1.1rem"></i></td>
        <td><strong><?= e($row['title']) ?></strong><br><small style="color:var(--text-light)"><?= e(substr($row['description'], 0, 60)) ?>...</small></td>
        <td><span class="badge badge-<?= $row['status'] ? 'success' : 'danger' ?>"><?= $row['status'] ? 'Active' : 'Hidden' ?></span></td>
        <td><?= $row['sort_order'] ?></td>
        <td class="table-actions">
          <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-outline btn-sm btn-icon"><i class="fas fa-edit"></i></a>
          <a href="?toggle=<?= $row['id'] ?>" class="btn btn-warning btn-sm btn-icon"><i class="fas fa-eye<?= $row['status'] ? '' : '-slash' ?>"></i></a>
          <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm btn-icon" data-confirm="Delete this step?"><i class="fas fa-trash"></i></a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require_once '../includes/layout-bottom.php'; ?>
