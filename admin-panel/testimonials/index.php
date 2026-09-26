<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Testimonials';

if (isset($_GET['delete'])) { handleDelete('testimonials', $_GET['delete']); flashMessage('success','Testimonial deleted.'); redirect(ADMIN_URL.'/testimonials/index.php'); }
if (isset($_GET['toggle'])) { handleStatusToggle('testimonials', $_GET['toggle']); redirect(ADMIN_URL.'/testimonials/index.php'); }

$page   = max(1, (int)($_GET['page'] ?? 1));
$result = getPaginatedResults('testimonials', $page, 15, '1', [], 'sort_order ASC, created_at DESC');
require_once '../includes/layout-top.php';
?>
<?php showFlash(); ?>
<div class="admin-table-wrap">
  <div class="table-header">
    <h3>Testimonials (<?= $result['total'] ?>)</h3>
    <a href="add.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Testimonial</a>
  </div>
  <table>
    <thead><tr><th>#</th><th>Client</th><th>Company / Position</th><th>Rating</th><th>Status</th><th>Order</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($result['data'] as $row): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td>
          <div style="display:flex;align-items:center;gap:10px">
            <?php if ($row['image']): ?>
            <img src="<?= SITE_URL ?>/<?= e($row['image']) ?>" style="width:36px;height:36px;border-radius:50%;object-fit:cover">
            <?php else: ?>
            <div style="width:36px;height:36px;border-radius:50%;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.85rem"><?= strtoupper(substr($row['client_name'],0,1)) ?></div>
            <?php endif; ?>
            <strong><?= e($row['client_name']) ?></strong>
          </div>
        </td>
        <td><?= e($row['position']) ?><?= $row['company'] ? ', ' . e($row['company']) : '' ?></td>
        <td><span style="color:#f59e0b"><?= str_repeat('★', (int)$row['rating']) ?><?= str_repeat('☆', 5-(int)$row['rating']) ?></span></td>
        <td><span class="badge badge-<?= $row['status'] ? 'success' : 'danger' ?>"><?= $row['status'] ? 'Active' : 'Hidden' ?></span></td>
        <td><?= $row['sort_order'] ?></td>
        <td class="table-actions">
          <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-outline btn-sm btn-icon"><i class="fas fa-edit"></i></a>
          <a href="?toggle=<?= $row['id'] ?>" class="btn btn-warning btn-sm btn-icon"><i class="fas fa-eye<?= $row['status'] ? '' : '-slash' ?>"></i></a>
          <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm btn-icon" data-confirm="Delete this testimonial?"><i class="fas fa-trash"></i></a>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$result['data']): ?>
      <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-light)">
        <i class="fas fa-star" style="font-size:2rem;display:block;margin-bottom:8px;opacity:0.2"></i>
        No testimonials yet. Add your first one.
      </td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  <?php paginationLinks($result, '?'); ?>
</div>
<?php require_once '../includes/layout-bottom.php'; ?>
