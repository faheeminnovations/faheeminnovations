<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Our Clients';

if (isset($_GET['delete'])) { handleDelete('clients', $_GET['delete']); flashMessage('success','Client deleted.'); redirect(ADMIN_URL.'/clients/index.php'); }
if (isset($_GET['toggle'])) { handleStatusToggle('clients', $_GET['toggle']); redirect(ADMIN_URL.'/clients/index.php'); }

$result = getPaginatedResults('clients', 1, 30, '1', [], 'sort_order ASC');
require_once '../includes/layout-top.php';
?>
<?php showFlash(); ?>

<div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:13px 16px;font-size:0.84rem;color:#1e40af;margin-bottom:20px">
  <i class="fas fa-info-circle"></i> Clients section is shown on the homepage. Upload a logo or just enter the name — both are displayed nicely.
</div>

<div class="admin-table-wrap">
  <div class="table-header">
    <h3>Clients (<?= $result['total'] ?>)</h3>
    <a href="add.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Client</a>
  </div>
  <table>
    <thead><tr><th>#</th><th>Logo</th><th>Name</th><th>Industry</th><th>Website</th><th>Status</th><th>Order</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($result['data'] as $row): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td>
          <?php if ($row['logo']): ?>
          <img src="<?= SITE_URL ?>/<?= e($row['logo']) ?>" style="height:36px;max-width:80px;object-fit:contain;border-radius:4px;border:1px solid var(--border);padding:2px 4px;background:#fff">
          <?php else: ?>
          <div style="width:50px;height:36px;background:var(--bg);border:1px solid var(--border);border-radius:4px;display:flex;align-items:center;justify-content:center"><i class="fas fa-building" style="color:var(--text-light);font-size:0.8rem"></i></div>
          <?php endif; ?>
        </td>
        <td><strong><?= e($row['name']) ?></strong></td>
        <td><?= e($row['industry']) ?></td>
        <td><?php if ($row['website']): ?><a href="<?= e($row['website']) ?>" target="_blank" style="color:var(--primary);font-size:0.82rem"><i class="fas fa-external-link-alt"></i> Visit</a><?php else: ?><span style="color:var(--text-light)">—</span><?php endif; ?></td>
        <td><span class="badge badge-<?= $row['status'] ? 'success' : 'danger' ?>"><?= $row['status'] ? 'Visible' : 'Hidden' ?></span></td>
        <td><?= $row['sort_order'] ?></td>
        <td class="table-actions">
          <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-outline btn-sm btn-icon"><i class="fas fa-edit"></i></a>
          <a href="?toggle=<?= $row['id'] ?>" class="btn btn-warning btn-sm btn-icon"><i class="fas fa-eye<?= $row['status'] ? '' : '-slash' ?>"></i></a>
          <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm btn-icon" data-confirm="Delete this client?"><i class="fas fa-trash"></i></a>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$result['data']): ?>
      <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-light)">
        <i class="fas fa-building" style="font-size:2rem;display:block;margin-bottom:8px;opacity:0.2"></i>No clients yet.
      </td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php require_once '../includes/layout-bottom.php'; ?>
